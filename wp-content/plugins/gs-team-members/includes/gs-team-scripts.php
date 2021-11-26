<?php
/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Scripts' ) ) {

    final class GS_Team_Scripts {

		private static $_instance = null;
		
		public $styles = [];

		public $scripts = [];
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Scripts();
            }

            return self::$_instance;
            
        }

        public function __construct() {

			$this->add_assets();

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_script' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_team_scripts' ] );
			
			add_action( 'admin_head', [ $this, 'print_plugin_icon_css' ] );

			if ( ! gtm_fs()->is_paying_or_trial() ) {

				add_action( 'admin_head', [ $this, 'disable_term_pages_css' ], 1 );

			}

			return $this;
            
		}

		public function add_assets() {

			// Styles
			$this->add_style( 'gs-select2', GSTEAM_PLUGIN_URI . '/assets/libs/select2/select2.min.css', [], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-font-awesome', GSTEAM_PLUGIN_URI . '/assets/libs/font-awesome/css/font-awesome.min.css', [], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-team-sort', GSTEAM_PLUGIN_URI . '/assets/admin/css/gs-team-sort.min.css', ['gs-font-awesome'], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-team-admin', GSTEAM_PLUGIN_URI . '/assets/admin/css/gs-team-admin.min.css', ['gs-select2', 'gs-font-awesome'], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-bootstrap-grid', GSTEAM_PLUGIN_URI . '/assets/libs/bootstrap-grid/bootstrap-grid.min.css', [], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-bootstrap-table', GSTEAM_PLUGIN_URI . '/assets/libs/bootstrap-table/bootstrap-table.min.css', [], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-magnific-popup', GSTEAM_PLUGIN_URI . '/assets/libs/magnific-popup/magnific-popup.min.css', [], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-owl-carousel', GSTEAM_PLUGIN_URI . '/assets/libs/owl-carousel/owl-carousel.min.css', [], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-team-public', GSTEAM_PLUGIN_URI . '/assets/css/gs-team.min.css', ['gs-bootstrap-grid'], GSTEAM_VERSION, 'all' );
			$this->add_style( 'gs-team-divi-public', GSTEAM_PLUGIN_URI . '/assets/css/gs-team-divi.min.css', ['gs-team-public'], GSTEAM_VERSION, 'all' );
			
			// Scripts
			$this->add_script( 'gs-select2', GSTEAM_PLUGIN_URI . '/assets/libs/select2/select2.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-team-sort', GSTEAM_PLUGIN_URI . '/assets/admin/js/gs-team-sort.min.js', ['jquery', 'jquery-ui-sortable'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-team-sort-group', GSTEAM_PLUGIN_URI . '/assets/admin/js/gs-team-sort-group.min.js', ['jquery', 'jquery-ui-sortable'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-team-admin', GSTEAM_PLUGIN_URI . '/assets/admin/js/gs-team-admin.min.js', ['jquery', 'jquery-ui-sortable', 'gs-select2'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-bootstrap-table', GSTEAM_PLUGIN_URI . '/assets/libs/bootstrap-table/bootstrap-table.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-cpb-scroller', GSTEAM_PLUGIN_URI . '/assets/libs/cpb-scroller/cpb-scroller.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-gridder', GSTEAM_PLUGIN_URI . '/assets/libs/gridder/gridder.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-isotope', GSTEAM_PLUGIN_URI . '/assets/libs/isotope/isotope.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-jquery-flip', GSTEAM_PLUGIN_URI . '/assets/libs/jquery-flip/jquery-flip.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-jquery-panelslider', GSTEAM_PLUGIN_URI . '/assets/libs/jquery-panelslider/jquery-panelslider.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-magnific-popup', GSTEAM_PLUGIN_URI . '/assets/libs/magnific-popup/magnific-popup.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-owl-carousel', GSTEAM_PLUGIN_URI . '/assets/libs/owl-carousel/owl-carousel.min.js', ['jquery'], GSTEAM_VERSION, true );
			$this->add_script( 'gs-team-public', GSTEAM_PLUGIN_URI . '/assets/js/gs-team.min.js', ['jquery'], GSTEAM_VERSION, true );

		}

		public function add_style( $handler, $src, $deps = [], $ver = false, $media ='all' ) {

			$this->styles[$handler] = [
				'src' => $src,
				'deps' => $deps,
				'ver' => $ver,
				'media' => $media
			];

		}

		public function add_script( $handler, $src, $deps = [], $ver = false, $in_footer = false ) {

			$this->scripts[$handler] = [
				'src' => $src,
				'deps' => $deps,
				'ver' => $ver,
				'in_footer' => $in_footer
			];

		}

		public function get_style( $handler ) {

			if ( empty( $style = $this->styles[$handler] ) ) return false;

			return $style;

		}

		public function get_script( $handler ) {

			if ( empty( $script = $this->scripts[$handler] ) ) return false;

			return $script;

		}

		public function wp_register_style( $handler ) {

			$style = $this->get_style( $handler );

			if ( ! $style ) return;

			$deps = (array) apply_filters( $handler . '--style', $style['deps'] );

			wp_register_style( $handler, $style['src'], $deps, $style['ver'], $style['media'] );

		}

		public function wp_register_script( $handler ) {

			$script = $this->get_script( $handler );

			if ( ! $script ) return;

			$deps = (array) apply_filters( $handler . '--script', $script['deps'] );

			wp_register_script( $handler, $script['src'], $deps, $script['ver'], $script['in_footer'] );

		}

		public function _get_public_style_all() {

			return [
				'gs-bootstrap-grid',
				'gs-bootstrap-table',
				'gs-font-awesome',
				'gs-magnific-popup',
				'gs-owl-carousel',
				'gs-team-public',
				'gs-team-divi-public'
			];

		}

		public function _get_public_script_all() {

			return [
				'gs-bootstrap-table',
				'gs-cpb-scroller',
				'gs-gridder',
				'gs-isotope',
				'gs-jquery-flip',
				'gs-jquery-panelslider',
				'gs-magnific-popup',
				'gs-owl-carousel',
				'gs-team-public'
			];

		}

		public function _get_admin_style_all() {

			return [
				'gs-select2',
				'gs-font-awesome',
				'gs-team-admin'
			];

		}

		public function _get_admin_script_all() {

			return [
				'gs-select2',
				'gs-team-admin',
			];

		}

		public function _get_assets_all( $asset_type, $group, $excludes = [] ) {

			if ( !in_array($asset_type, ['style', 'script']) || !in_array($group, ['public', 'admin']) ) return;

			$get_assets = sprintf( '_get_%s_%s_all', $group, $asset_type );

			$assets = $this->$get_assets();

			if ( ! empty($excludes) ) $assets = array_diff( $assets, $excludes );

			return (array) apply_filters( sprintf( 'gs_team_%s__%s_all', $group, $asset_type ), $assets );

		}

		public function _wp_load_assets_all( $function, $asset_type, $group, $excludes = [] ) {

			if ( !in_array($function, ['enqueue', 'register']) || !in_array($asset_type, ['style', 'script']) ) return;

			$assets = $this->_get_assets_all( $asset_type, $group, $excludes );

			$function = sprintf( 'wp_%s_%s', $function, $asset_type );

			foreach( $assets as $asset ) $this->$function( $asset );

		}

		public function wp_register_style_all( $group, $excludes = [] ) {

			$this->_wp_load_assets_all( 'register', 'style', $group, $excludes );

		}

		public function wp_enqueue_style_all( $group, $excludes = [] ) {

			$this->_wp_load_assets_all( 'enqueue', 'style', $group, $excludes );

		}

		public function wp_register_script_all( $group, $excludes = [] ) {

			$this->_wp_load_assets_all( 'register', 'script', $group, $excludes );

		}

		public function wp_enqueue_script_all( $group, $excludes = [] ) {

			$this->_wp_load_assets_all( 'enqueue', 'script', $group, $excludes );

		}

		// Use to direct enqueue
		public function wp_enqueue_style( $handler ) {

			$style = $this->get_style( $handler );

			if ( ! $style ) return;

			$deps = (array) apply_filters( $handler . '--style-enqueue', $style['deps'] );

			wp_enqueue_style( $handler, $style['src'], $deps, $style['ver'], $style['media'] );

		}

		public function wp_enqueue_script( $handler ) {

			$script = $this->get_script( $handler );

			if ( ! $script ) return;

			$deps = (array) apply_filters( $handler . '--script-enqueue', $script['deps'] );

			wp_enqueue_script( $handler, $script['src'], $deps, $script['ver'], $script['in_footer'] );

		}
		
		public function enqueue_admin_script( $hook ) {

			global $post;
	
			$load_script = false;
	
			// Register Styles
			$this->wp_register_style_all( 'admin' );
	
			// Register Scripts
			$this->wp_register_script_all( 'admin' );
			
			// Allow scripts loading in new gs_team member page
			if ( $hook == 'post-new.php' && $_GET['post_type'] == 'gs_team' ) $load_script = true;
	
			// Allow scripts loading in gs_team member edit page
			if ( $hook == 'post.php' && $post->post_type == 'gs_team' ) $load_script = true;
			
			// Abort load script if not allowed
			if ( ! $load_script ) return;
	
			// Enqueue Styles
			wp_enqueue_style( 'gs-team-admin' );
			
			// Enqueue Scripts
			wp_enqueue_script( 'gs-team-admin' );

			gs_team_add_fs_script( 'gs-team-admin' );
			
		}

		public function print_plugin_icon_css() {

			echo "<style>#adminmenu .toplevel_page_gs-team-members .wp-menu-image img, #adminmenu .menu-icon-gs_team .wp-menu-image img{padding-top:7px;width:20px;opacity:.8;height:auto;}</style>";

		}

		public function enqueue_team_scripts() {
			
			$enqueue_style = false;
			
			// Register Styles
			$this->wp_register_style_all( 'public' );
		
			// Register Scripts
			$this->wp_register_script_all( 'public' );
	
			// Allow loading script on gs_team single page
			if ( is_singular('gs_team') ) $enqueue_style = true;
			
			// Support for Archive page
			if ( ! $enqueue_style && is_post_type_archive( 'gs_team' ) ) $enqueue_style = true;
			
			// Support for Taxonomy Archive pages
			if ( ! $enqueue_style && is_tax(['team_group', 'team_gender', 'team_location', 'team_language', 'team_specialty']) ) $enqueue_style = true;
	
			// Abort loading script if not allowed
			if ( ! $enqueue_style ) return;
			
			// Enqueue Styles - This should get called through add_shortcode
			self::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
			wp_enqueue_style( 'gs-team-public' );
	
		}

		public static function add_dependency_scripts( $handle, $scripts ) {

			add_action( 'wp_footer', function() use( $handle, $scripts ) {
				
				global $wp_scripts;
	
				if ( empty($scripts) || empty($handle) ) return;
				if ( ! isset($wp_scripts->registered[$handle]) ) return;
	
				$wp_scripts->registered[$handle]->deps = array_unique( array_merge( $wp_scripts->registered[$handle]->deps, $scripts ) );
	
			});
	
		}

		public static function add_dependency_styles( $handle, $styles ) {
            
			global $wp_styles;
			
			if ( empty($styles) || empty($handle) ) return;
			if ( ! isset($wp_styles->registered[$handle]) ) return;
			
			$wp_styles->registered[$handle]->deps = array_unique( array_merge( $wp_styles->registered[$handle]->deps, $styles ) );
	
		}

		public function echo_gtm_fs_conditions() {
			?>
			<script>
				window.gs_team_fs = {
					is_paying_or_trial: Boolean( <?php echo gtm_fs()->is_paying_or_trial(); ?> )
				}
			</script>
			<?php
		}

		public function disable_term_pages_css() {
			?>
			<style>

				td.name.column-name.has-row-actions.column-primary {
					height: 41px;
				}
				
				.gs-team-disable--term-pages {
					background: rgba(227, 228, 230, .7);
					position: fixed;
					width: -webkit-calc(100% - 160px);
					width: calc(100% - 160px);
					margin-left: 160px;
					height: 100%;
					top: 0;
					left: 0;
					z-index: 999;
					display: -webkit-box;
					display: -webkit-flex;
					display: -ms-flexbox;
					display: flex;
					-webkit-box-align: center;
					-webkit-align-items: center;
						-ms-flex-align: center;
							align-items: center;
					-webkit-box-pack: center;
					-webkit-justify-content: center;
						-ms-flex-pack: center;
							justify-content: center;
				}

				.gs-team-disable--term-inner {
					font-size: 18px;
					background: #6472ef;
					padding: 20px 100px;
					-webkit-box-shadow: 0 0 50px rgba(89, 97, 109, .1);
							box-shadow: 0 0 50px rgba(89, 97, 109, .1);
					border-radius: 3px;
					color: #fff;
					letter-spacing: 1px;
				}

			</style>
			<?php
		}

    }

}

GS_Team_Scripts::get_instance();