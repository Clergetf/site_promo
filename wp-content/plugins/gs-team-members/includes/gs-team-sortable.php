<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Sortable' ) ) :

class GS_Team_Sortable {

	var $posttype = 'gs_team';
	var $title = '';
	var $ppp = '-1'; // posts per page

	public function __construct( $posttype, $title, $ppp = -1 ) {

		$this->posttype = $posttype;
		$this->title = $title;
		$this->ppp = $ppp;

		add_filter( 'posts_orderby', array( $this, 'gs_team_order_posts' ) );
		add_action( 'admin_menu' , array( $this, 'gs_team_enable_sort' ) ); 
		add_action( 'admin_enqueue_scripts', array( $this, 'gs_team_sort_scripts' ) );
		add_action( 'wp_ajax_sort_team_members', array( $this, 'gs_team_save_sort_order' ) );
	}

	public function is_pro() {
		return gtm_fs()->is_paying_or_trial();
	}

	/**
	 * Alter the query on front and backend to order posts as desired.
	 */
	public function gs_team_order_posts( $orderby ) {
	    global $wpdb;
	
	    if ( is_post_type_archive( array($this->posttype)) ) {
			$orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
		}

	    return($orderby);
	}

	/**
	 * Add Sort menu
	 */
	public function gs_team_enable_sort() {
		add_submenu_page('edit.php?post_type=' . $this->posttype, 'Sort Members', 'Sort Order', 'edit_posts', 'sort_' . $this->posttype, array( $this, 'sort_team_members'));
	}

	/**
	 * Display Sort admin page
	 */
	public function sort_team_members() {
	
		$sortable = new WP_Query('post_type=' . $this->posttype . '&posts_per_page=' . $this->ppp . '&orderby=menu_order&order=ASC');

		if ( ! $this->is_pro() ) : ?>

			<div class="gs-team-disable--term-pages">
				<div class="gs-team-disable--term-inner">
					<div class="gs-team-disable--term-message">Pro Only</div>
				</div>
			</div>

		<?php endif; ?>

		<div class="wrap">
	
			<div id="icon-edit" class="icon32"></div>
			<h2><?php echo __('Custom Order for', 'gsteam') .': '. $this->title; ?> <img src="<?php bloginfo('url'); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h2>

			<?php if ( $sortable->have_posts() ) : ?>
	
				<ul id="sortable-list">
					<?php while ( $sortable->have_posts() ) :
							
						$sortable->the_post();
						$term_obj_list = get_the_terms( get_the_ID(), 'team_group' );
						$terms_string = '';

						if ( is_array($term_obj_list) || is_object($term_obj_list) ) {
							$terms_string = join('</span><span>', wp_list_pluck($term_obj_list, 'name'));
						}

						if ( !empty($terms_string) ) $terms_string = '<span>' . $terms_string . '</span>';
					
						?>
						
						<li id="<?php the_id(); ?>">
							<div class="sortable-content sortable-icon"><i class="fa fa-arrows" aria-hidden="true"></i></div>
							<div class="sortable-content sortable-thumbnail"><span><?php gs_team_member_thumbnail( 'thumbnail', true ); ?></span></div>
							<div class="sortable-content sortable-title"><?php the_title(); ?></div>
							<div class="sortable-content sortable-group"><?php echo $terms_string; ?></div>
						</li>
			
					<?php endwhile; ?>
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

	/**
	 * Add JS and CSS to admin
	 */
	public function gs_team_sort_scripts( $hook ) {

		if ( $hook != 'gs_team_page_sort_gs_team' ) return;

		GS_Team_Scripts::get_instance()->wp_enqueue_style( 'gs-team-sort' );
		GS_Team_Scripts::get_instance()->wp_enqueue_script( 'gs-team-sort' );

		if ( $this->is_pro() ) {
			$data = [
				'nonce' => wp_create_nonce( '_gsteam_save_sort_order_gs_' )
			];
			wp_localize_script( 'gs-team-sort', '_gsteam_sort_data', $data );
		}

		gs_team_add_fs_script( 'gs-team-sort' );

	}

	/**
	 * Save the sort order to database
	 */
	public function gs_team_save_sort_order() {

		if ( empty($_POST['_nonce']) || ! wp_verify_nonce( $_POST['_nonce'], '_gsteam_save_sort_order_gs_') ) {
			wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );
		}

		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
	
		foreach ( $order as $post_id ) {
			$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $post_id) );
			$counter++;
		}

		return true;

	}

}

endif;

$gs_team_custom_order = new GS_Team_Sortable( 'gs_team', 'GS Team Members' );