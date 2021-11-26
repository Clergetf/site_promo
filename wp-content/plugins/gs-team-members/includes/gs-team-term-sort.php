<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Term_Sort' ) ) :

class GS_Team_Term_Sort {

	var $posttype = 'gs_team';
	var $title = '';
	var $ppp = '-1'; // posts per page

	public function __construct( $posttype, $title, $ppp = -1 ) {

		$this->posttype = $posttype;
		$this->title = $title;
		$this->ppp = $ppp;

		add_filter( 'plugins_loaded', array( $this, 'alter_terms_table' ), 0 );

		add_action( 'admin_menu' , array( $this, 'gs_team_enable_sort' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'gs_team_sort_scripts' ) );

		add_filter( 'get_terms_orderby', array( $this, 'get_terms_orderby' ), 1, 2 );
		add_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10, 3 );
		add_action( 'wp_ajax_update_taxonomy_order', array( $this, 'update_taxonomy_order' ) );
	}

	public function is_pro() {
		return gtm_fs()->is_paying_or_trial();
	}

	public function alter_terms_table() {

		if ( ! $this->is_pro() ) return;

		if ( get_site_option( 'gsp_terms_table_altered', false ) !== false ) return;
		
		global $wpdb;
		
		//check if the menu_order column exists;
		$query = "SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'";
		$result = $wpdb->query($query);
		
		if ( $result == 0 ) {
			$query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
			$result = $wpdb->query($query); 
		
			update_site_option( 'gsp_terms_table_altered', true );
		}
		
	}

	/**
	 * Add Sort menu
	 */
	public function gs_team_enable_sort() {
		add_submenu_page( 'edit.php?post_type=' . $this->posttype, 'Sort Groups', 'Sort Group Order', 'edit_posts', 'sort_group_' . $this->posttype, array( $this, 'sort_member_groups') );
	}

	/**
	 * Add JS and CSS to admin
	 */
	public function gs_team_sort_scripts( $hook ) {

		if ( $hook != 'gs_team_page_sort_group_gs_team' ) return;

		GS_Team_Scripts::get_instance()->wp_enqueue_style( 'gs-team-sort' );
		GS_Team_Scripts::get_instance()->wp_enqueue_script( 'gs-team-sort-group' );

		if ( $this->is_pro() ) {
			$data = [
				'nonce' => wp_create_nonce( '_gsteam_save_sort_order_gs_' )
			];
			wp_localize_script( 'gs-team-sort-group', '_gsteam_sort_group_data', $data );
		}

		gs_team_add_fs_script( 'gs-team-sort-group' );

	}

	/**
	 * Display Sort admin page
	 */
	public function sort_member_groups() {

		if ( ! $this->is_pro() ) : ?>

			<div class="gs-team-disable--term-pages">
				<div class="gs-team-disable--term-inner">
					<div class="gs-team-disable--term-message">Pro Only</div>
				</div>
			</div>

		<?php endif; ?>

		<div class="wrap gs-team--sortable_group">
	
			<div id="icon-edit" class="icon32"></div>
			<h2><?php echo __('Custom Order for', 'gsteam') .': '. $this->title; ?> <img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h2>

			<?php
			
			$terms = get_terms( 'team_group' );
			
			if ( !empty($terms) ) : ?>
	
				<ul id="sortable-list" style="max-width: 600px;">
					<?php foreach ( $terms as $term ) : ?>
						
						<li id="<?php echo $term->term_id; ?>">
							<div class="sortable-content sortable-icon"><i class="fa fa-arrows" aria-hidden="true"></i></div>
							<div class="sortable-content sortable-title"><?php echo $term->name; ?></div>
							<div class="sortable-content sortable-group"><span><?php echo $term->count . ' ' . 'Members'; ?></span></div>
						</li>
			
					<?php endforeach; ?>
				</ul>
			
			<?php else: ?>
				
				<div class="notice notice-warning" style="margin-top: 30px;">
					<h3><?php _e( 'No Team Memebr Found!', 'gsteam' ); ?></h3>
					<p style="font-size: 14px;"><?php _e( 'We didn\'t find any team member.</br>Please add some team members to sort them.', 'gsteam' ); ?></p>
					<a href="<?php echo admin_url('post-new.php?post_type=gs_team'); ?>" style="margin-top: 10px; margin-bottom: 20px;" class="button button-primary button-large"><?php _e( 'Add Member', 'gsteam' ); ?></a>
				</div>

			<?php endif; ?>

			<?php if ( $this->ppp != -1 ) echo '<p>Latest ' . $this->ppp . ' shown</p>'; ?>
	
		</div><!-- #wrap -->
	
		<?php
	}

	public function get_terms_orderby( $orderby, $args ) {

		if ( empty($args['taxonomy']) ) return $orderby;
		
		if ( $this->is_pro() && in_array( 'team_group', $args['taxonomy'] ) ) {
			if ( isset($args['orderby']) && $args['orderby'] == "term_order" && $orderby != "term_order" ) return "t.term_order";
		}

		return $orderby;

	}

	public function terms_clauses( $clauses, $taxonomies, $args ) {

		if ( empty($args['taxonomy']) ) return $clauses;

		if ( ! $this->is_pro() || ! in_array( 'team_group', $args['taxonomy'] ) ) return $clauses;
        
		$options = [
			'adminsort' => '1',
			'autosort' => '1',
		];
			
		// if admin make sure use the admin setting
		if ( is_admin() ) {
				
			// return if use orderby columns
			if ( isset($_GET['orderby']) && $_GET['orderby'] !=  'term_order' ) return $clauses;
			
			if ( $options['adminsort'] == "1" ) $clauses['orderby'] = 'ORDER BY t.term_order';
				
			return $clauses;

		}
		
		// if autosort, then force the menu_order
		if ( $options['autosort'] == 1   &&  (!isset($args['ignore_term_order']) ||  (isset($args['ignore_term_order'])  &&  $args['ignore_term_order']  !== TRUE) ) ) {
			$clauses['orderby'] = 'ORDER BY t.term_order';
		}
			
		return $clauses;

	}

	public function update_taxonomy_order() {

		if ( ! $this->is_pro() ) return;

		if ( empty($_POST['_nonce']) || ! wp_verify_nonce( $_POST['_nonce'], '_gsteam_save_sort_order_gs_') ) {
			wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );
		}

		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
	
		foreach ( $order as $term_id ) {
			$wpdb->update($wpdb->terms, array( 'term_order' => $counter ), array( 'term_id' => $term_id) );
			$counter++;
		}

		return true;

	}


}

endif;

$gs_team_custom_order = new GS_Team_Term_Sort( 'gs_team', 'GS Team Member Groups' );