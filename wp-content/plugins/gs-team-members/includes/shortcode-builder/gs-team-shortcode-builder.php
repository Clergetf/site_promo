<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Shortcode_Builder' ) ) {

    final class GS_Team_Shortcode_Builder {

        private $option_name = 'gs_team_shortcode_prefs';

        private static $_instance = null;
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Shortcode_Builder();
            }

            return self::$_instance;
            
        }

        public function __construct() {
            
            add_action( 'admin_menu', array( $this, 'register_sub_menu') );
            add_action( 'admin_enqueue_scripts', array( $this, 'scripts') );
            add_action( 'wp_enqueue_scripts', array( $this, 'preview_scripts') );

            add_action( 'init', array($this, 'init') );
            add_action( 'wp_ajax_gsteam_create_shortcode', array($this, 'create_shortcode') );
            add_action( 'wp_ajax_gsteam_clone_shortcode', array($this, 'clone_shortcode') );
            add_action( 'wp_ajax_gsteam_get_shortcode', array($this, 'get_shortcode') );
            add_action( 'wp_ajax_gsteam_update_shortcode', array($this, 'update_shortcode') );
            add_action( 'wp_ajax_gsteam_delete_shortcodes', array($this, 'delete_shortcodes') );
            add_action( 'wp_ajax_gsteam_temp_save_shortcode_settings', array($this, 'temp_save_shortcode_settings') );
            add_action( 'wp_ajax_gsteam_get_shortcodes', array($this, 'get_shortcodes') );

            add_action( 'wp_ajax_gsteam_get_shortcode_pref', array($this, 'get_shortcode_pref') );
            add_action( 'wp_ajax_gsteam_save_shortcode_pref', array($this, 'save_shortcode_pref') );

            add_action( 'template_include', array($this, 'populate_shortcode_preview') );
            add_action( 'show_admin_bar', array($this, 'hide_admin_bar_from_preview') );

            // add_filter( 'body_class', array($this, 'add_shortcode_body_class') );

            return $this;

        }

        public function init() {
            // Register Shortcode
            include GSTEAM_PLUGIN_DIR . 'includes/shortcode-builder/gs_team_shortcode_builder_shortcode.php';
        }

        public function is_gsteam_shortcode_preview() {

            return isset( $_REQUEST['gsteam_shortcode_preview'] ) && !empty($_REQUEST['gsteam_shortcode_preview']);

        }

        public function hide_admin_bar_from_preview( $visibility ) {

            if ( $this->is_gsteam_shortcode_preview() ) return false;

            return $visibility;

        }

        public function add_shortcode_body_class( $classes ) {

            if ( $this->is_gsteam_shortcode_preview() ) return array_merge( $classes, array( 'gsteam-shortcode-preview--page' ) );

            return $classes;

        }

        public function populate_shortcode_preview( $template ) {

            global $wp, $wp_query;
            
            if ( $this->is_gsteam_shortcode_preview() ) {

                // Create our fake post
                $post_id = rand( 1, 99999 ) - 9999999;
                $post = new stdClass();
                $post->ID = $post_id;
                $post->post_author = 1;
                $post->post_date = current_time( 'mysql' );
                $post->post_date_gmt = current_time( 'mysql', 1 );
                $post->post_title = __('Shortcode Preview', 'gsteam');
                $post->post_content = '[gsteam preview="yes" id="'.$_REQUEST['gsteam_shortcode_preview'].'"]';
                $post->post_status = 'publish';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->post_name = 'fake-page-' . rand( 1, 99999 ); // append random number to avoid clash
                $post->post_type = 'page';
                $post->filter = 'raw'; // important!


                // Convert to WP_Post object
                $wp_post = new WP_Post( $post );


                // Add the fake post to the cache
                wp_cache_add( $post_id, $wp_post, 'posts' );


                // Update the main query
                $wp_query->post = $wp_post;
                $wp_query->posts = array( $wp_post );
                $wp_query->queried_object = $wp_post;
                $wp_query->queried_object_id = $post_id;
                $wp_query->found_posts = 1;
                $wp_query->post_count = 1;
                $wp_query->max_num_pages = 1; 
                $wp_query->is_page = true;
                $wp_query->is_singular = true; 
                $wp_query->is_single = false; 
                $wp_query->is_attachment = false;
                $wp_query->is_archive = false; 
                $wp_query->is_category = false;
                $wp_query->is_tag = false; 
                $wp_query->is_tax = false;
                $wp_query->is_author = false;
                $wp_query->is_date = false;
                $wp_query->is_year = false;
                $wp_query->is_month = false;
                $wp_query->is_day = false;
                $wp_query->is_time = false;
                $wp_query->is_search = false;
                $wp_query->is_feed = false;
                $wp_query->is_comment_feed = false;
                $wp_query->is_trackback = false;
                $wp_query->is_home = false;
                $wp_query->is_embed = false;
                $wp_query->is_404 = false; 
                $wp_query->is_paged = false;
                $wp_query->is_admin = false; 
                $wp_query->is_preview = false; 
                $wp_query->is_robots = false; 
                $wp_query->is_posts_page = false;
                $wp_query->is_post_type_archive = false;


                // Update globals
                $GLOBALS['wp_query'] = $wp_query;
                $wp->register_globals();


                include GSTEAM_PLUGIN_DIR . 'includes/shortcode-builder/gs_team_shortcode_builder_preview.php';

                return;

            }

            return $template;

        }

        public function register_sub_menu() {

            add_submenu_page( 
                'edit.php?post_type=gs_team', 'Team Shortcode', 'Team Shortcode', 'manage_options', 'gs-team-shortcode', array( $this, 'view' )
            );

            do_action( 'gs_after_shortcode_submenu' );

        }

        public function view() {

            include GSTEAM_PLUGIN_DIR . 'includes/shortcode-builder/gs_team_shortcode_builder_page.php';

        }

        public static function get_team_groups( $idsOnly = false ) {

            $_terms = get_terms( 'team_group', [
                'hide_empty' => false,
            ]);

            if ( empty($_terms) ) return [];
            
            if ( $idsOnly ) return wp_list_pluck( $_terms, 'term_id' );

            $terms = [];

            foreach ( $_terms as $term ) {
                $terms[] = [
                    'label' => $term->name,
                    'value' => $term->term_id
                ];
            }

            return $terms;

        }

        public function scripts( $hook ) {

            if ( 'gs_team_page_gs-team-shortcode' != $hook ) {
                return;
            }

            wp_register_style( 'gs-zmdi-fonts', GSTEAM_PLUGIN_URI . '/assets/libs/material-design-iconic-font/css/material-design-iconic-font.min.css', '', GSTEAM_VERSION, 'all' );

            wp_enqueue_style( 'gs-team-shortcode', GSTEAM_PLUGIN_URI . '/assets/admin/css/gs-team-shortcode.min.css', array('gs-zmdi-fonts'), GSTEAM_VERSION, 'all' );

            $data = array(
                "nonce" => array(
                    "create_shortcode" 		        => wp_create_nonce( "_gsteam_create_shortcode_gs_" ),
                    "clone_shortcode" 		        => wp_create_nonce( "_gsteam_clone_shortcode_gs_" ),
                    "update_shortcode" 	            => wp_create_nonce( "_gsteam_update_shortcode_gs_" ),
                    "delete_shortcodes" 	        => wp_create_nonce( "_gsteam_delete_shortcodes_gs_" ),
                    "temp_save_shortcode_settings" 	=> wp_create_nonce( "_gsteam_temp_save_shortcode_settings_gs_" ),
                    "save_shortcode_pref" 	        => wp_create_nonce( "_gsteam_save_shortcode_pref_gs_" ),
                    "import_gsteam_demo" 	        => wp_create_nonce( "_gsteam_simport_gsteam_demo_gs_" ),
                    "bulk_import" 	                => wp_create_nonce( "_gsteam_bulk_import_" ),
                ),
                "ajaxurl" => admin_url( "admin-ajax.php" ),
                "adminurl" => admin_url(),
                "siteurl" => home_url()
            );

            $data['shortcode_settings'] = $this->get_shortcode_default_settings();
            $data['shortcode_options']  = $this->get_shortcode_default_options();
            $data['translations']       = $this->get_translation_srtings();
            $data['preference']         = $this->get_shortcode_default_prefs();
            $data['preference_options'] = $this->get_shortcode_prefs_options();
            $data['enabled_plugins']    = $this->get_enabled_plugins();

            $data['demo_data'] = [
                'team_data'      => wp_validate_boolean( get_option('gsteam_dummy_team_data_created') ),
                'shortcode_data' => wp_validate_boolean( get_option('gsteam_dummy_shortcode_data_created') )
            ];

            wp_enqueue_script( 'gs-team-shortcode', GSTEAM_PLUGIN_URI . '/assets/admin/js/gs-team-shortcode.min.js', array('jquery'), GSTEAM_VERSION, true );

            wp_localize_script( 'gs-team-shortcode', '_gsteam_data', $data );

            gs_team_add_fs_script( 'gs-team-shortcode' );
            
        }

        public function get_enabled_plugins() {
            
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            $plugins = [];

            if ( is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
                
                $team_groups = acf_get_field_groups([
                    'post_type'	=> 'gs_team'
                ]);
                
                if ( !empty($team_groups) ) {
                    $plugins[] = 'advanced-custom-fields';
                }

            }

            return $plugins;

        }

        public function preview_scripts( $hook ) {
            
            if ( ! $this->is_gsteam_shortcode_preview() ) return;

            wp_enqueue_style( 'gs-team-shortcode-preview', GSTEAM_PLUGIN_URI . '/assets/css/gs-team-shortcode-preview.min.css', '', GSTEAM_VERSION, 'all' );
            
        }

        public function gsteam_get_wpdb() {

            global $wpdb;
            
            if ( wp_doing_ajax() ) $wpdb->show_errors = false;

            return $wpdb;

        }

        public function gsteam_check_db_error() {

            $wpdb = $this->gsteam_get_wpdb();

            if ( $wpdb->last_error === '') return false;

            return true;

        }

        public function validate_shortcode_settings( $shortcode_settings ) {
            
            return (array) $shortcode_settings;

        }

        protected function get_gsteam_shortcode_db_columns() {

            return array(
                'shortcode_name' => '%s',
                'shortcode_settings' => '%s',
                'created_at' => '%s',
                'updated_at' => '%s',
            );

        }

        public function _get_shortcode( $shortcode_id, $is_ajax = false ) {

            if ( empty($shortcode_id) ) {
                if ( $is_ajax ) wp_send_json_error( __('Shortcode ID missing', 'gsteam'), 400 );
                return false;
            }

            $wpdb = $this->gsteam_get_wpdb();


            $shortcode = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}gs_team WHERE id = %d LIMIT 1", absint($shortcode_id) ), ARRAY_A );

            if ( $shortcode ) {

                $shortcode["shortcode_settings"] = json_decode( $shortcode["shortcode_settings"], true );

                if ( $is_ajax ) wp_send_json_success( $shortcode );

                return $shortcode;

            }

            if ( $is_ajax ) wp_send_json_error( __('No shortcode found', 'gsteam'), 404 );

            return false;

        }

        public function _update_shortcode( $shortcode_id, $nonce, $fields, $is_ajax ) {

            if ( ! wp_verify_nonce( $nonce, '_gsteam_update_shortcode_gs_') ) {
                if ( $is_ajax ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );
                return false;
            }

            if ( empty($shortcode_id) ) {
                if ( $is_ajax ) wp_send_json_error( __('Shortcode ID missing', 'gsteam'), 400 );
                return false;
            }
        
            $_shortcode = $this->_get_shortcode( $shortcode_id, false );
        
            if ( empty($_shortcode) ) {
                if ( $is_ajax ) wp_send_json_error( __('No shortcode found to update', 'gsteam'), 404 );
                return false;
            }
        
            $shortcode_name = !empty( $fields['shortcode_name'] ) ? $fields['shortcode_name'] : $_shortcode['shortcode_name'];
            $shortcode_settings  = !empty( $fields['shortcode_settings']) ? $fields['shortcode_settings'] : $_shortcode['shortcode_settings'];

            // Remove dummy indicator on update
            if ( isset($shortcode_settings['gsteam-demo_data']) ) unset($shortcode_settings['gsteam-demo_data']);
        
            $shortcode_settings = $this->validate_shortcode_settings( $shortcode_settings );

        
            $wpdb = $this->gsteam_get_wpdb();
        
            $data = array(
                "shortcode_name" 	    => $shortcode_name,
                "shortcode_settings" 	=> json_encode($shortcode_settings),
                "updated_at" 		    => current_time( 'mysql')
            );
        
            $num_row_updated = $wpdb->update( "{$wpdb->prefix}gs_team" , $data, array( 'id' => absint( $shortcode_id ) ),  $this->get_gsteam_shortcode_db_columns() );
        
            if ( $this->gsteam_check_db_error() ) {
                if ( $is_ajax ) wp_send_json_error( sprintf( __( 'Database Error: %1$s', 'gsteam'), $wpdb->last_error), 500 );
                return false;
            }
        
            if ($is_ajax) wp_send_json_success( array(
                'message' => __('Shortcode updated', 'gsteam'),
                'shortcode_id' => $num_row_updated
            ));
        
            return $num_row_updated;

        }
        
        public function _get_shortcodes( $shortcode_ids = [], $is_ajax = false, $minimal = false ) {

            $wpdb = $this->gsteam_get_wpdb();
            $fields = $minimal ? 'id, shortcode_name' : '*';

            if ( empty( $shortcode_ids ) ) {

                $shortcodes = $wpdb->get_results( "SELECT {$fields} FROM {$wpdb->prefix}gs_team ORDER BY id DESC", ARRAY_A );

            } else {

                $how_many = count($shortcode_ids);
                $placeholders = array_fill(0, $how_many, '%d');
                $format = implode(', ', $placeholders);
                $query = "SELECT {$fields} FROM {$wpdb->prefix}gs_team WHERE id IN($format)";
                $shortcodes = $wpdb->get_results( $wpdb->prepare($query, $shortcode_ids), ARRAY_A );

            }

            // check for database error
            if ( $this->gsteam_check_db_error() ) wp_send_json_error(sprintf(__('Database Error: %s'), $wpdb->last_error));

            if ( $is_ajax ) {
                wp_send_json_success( $shortcodes );
            }

            return $shortcodes;

        }

        public function create_shortcode() {

            // validate nonce && check permission
            if ( !check_admin_referer('_gsteam_create_shortcode_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            $shortcode_settings  = !empty( $_POST['shortcode_settings']) ? $_POST['shortcode_settings'] : '';
            $shortcode_name  = !empty( $_POST['shortcode_name']) ? $_POST['shortcode_name'] : __('Undefined', 'gsteam');

            if ( empty($shortcode_settings) || !is_array($shortcode_settings) ) {
                wp_send_json_error( __('Please configure the settings properly', 'gsteam'), 206 );
            }

            $shortcode_settings = $this->validate_shortcode_settings( $shortcode_settings );

            $wpdb = $this->gsteam_get_wpdb();

            $data = array(
                "shortcode_name" => $shortcode_name,
                "shortcode_settings" => json_encode($shortcode_settings),
                "created_at" => current_time( 'mysql'),
                "updated_at" => current_time( 'mysql'),
            );

            $wpdb->insert( "{$wpdb->prefix}gs_team", $data, $this->get_gsteam_shortcode_db_columns() );

            // check for database error
            if ( $this->gsteam_check_db_error() ) wp_send_json_error( sprintf(__('Database Error: %s'), $wpdb->last_error), 500 );

            // send success response with inserted id
            wp_send_json_success( array(
                'message' => __('Shortcode created successfully', 'gsteam'),
                'shortcode_id' => $wpdb->insert_id
            ));
        }

        public function clone_shortcode() {

            // validate nonce && check permission
            if ( !check_admin_referer('_gsteam_clone_shortcode_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            $clone_id  = !empty( $_POST['clone_id']) ? $_POST['clone_id'] : '';

            if ( empty($clone_id) ) wp_send_json_error( __('Clone Id not provided', 'gsteam'), 400 );

            $clone_shortcode = $this->_get_shortcode( $clone_id, false );

            if ( empty($clone_shortcode) ) wp_send_json_error( __('Clone shortcode not found', 'gsteam'), 404 );


            $shortcode_settings  = $clone_shortcode['shortcode_settings'];
            $shortcode_name  = $clone_shortcode['shortcode_name'] .' '. __('- Cloned', 'gsteam');

            $shortcode_settings = $this->validate_shortcode_settings( $shortcode_settings );

            $wpdb = $this->gsteam_get_wpdb();

            $data = array(
                "shortcode_name" => $shortcode_name,
                "shortcode_settings" => json_encode($shortcode_settings),
                "created_at" => current_time( 'mysql'),
                "updated_at" => current_time( 'mysql'),
            );

            $wpdb->insert( "{$wpdb->prefix}gs_team", $data, $this->get_gsteam_shortcode_db_columns() );

            // check for database error
            if ( $this->gsteam_check_db_error() ) wp_send_json_error( sprintf(__('Database Error: %s'), $wpdb->last_error), 500 );

            // Get the cloned shortcode
            $shotcode = $this->_get_shortcode( $wpdb->insert_id, false );

            // send success response with inserted id
            wp_send_json_success( array(
                'message' => __('Shortcode cloned successfully', 'gsteam'),
                'shortcode' => $shotcode,
            ));
        }

        public function get_shortcode() {

            $shortcode_id = !empty( $_GET['id']) ? absint( $_GET['id'] ) : null;

            $this->_get_shortcode( $shortcode_id, wp_doing_ajax() );

        }

        public function update_shortcode( $shortcode_id = null, $nonce = null ) {

            if ( ! $shortcode_id ) {
                $shortcode_id = !empty( $_POST['id']) ? $_POST['id'] : null;
            }
    
            if ( ! $nonce ) {
                $nonce = wp_create_nonce('_gsteam_update_shortcode_gs_');
            }
    
            $this->_update_shortcode( $shortcode_id, $nonce, $_POST, true );

        }

        public function delete_shortcodes() {

            if ( !check_admin_referer('_gsteam_delete_shortcodes_gs_') || !current_user_can('manage_options') )
                wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );
    
            $ids = isset( $_POST['ids'] ) ? $_POST['ids'] : null;
    
            if ( empty( $ids ) ) {
                wp_send_json_error( __('No shortcode ids provided', 'gsteam'), 400 );
            }
    
            $wpdb = $this->gsteam_get_wpdb();
    
            $count = count( $ids );
    
            $ids = implode( ',', array_map('absint', $ids) );
            $wpdb->query( "DELETE FROM {$wpdb->prefix}gs_team WHERE ID IN($ids)" );
    
            if ( $this->gsteam_check_db_error() ) wp_send_json_error( sprintf(__('Database Error: %s'), $wpdb->last_error), 500 );
    
            $m = _n( "Shortcode has been deleted", "Shortcodes have been deleted", $count, 'gsteam' ) ;
    
            wp_send_json_success( ['message' => $m] );

        }

        public function get_shortcodes() {

            $this->_get_shortcodes( null, wp_doing_ajax() );

        }

        public function temp_save_shortcode_settings() {

            if ( !check_admin_referer('_gsteam_temp_save_shortcode_settings_gs_') || !current_user_can('manage_options') )
                wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );
            
            $temp_key = isset( $_POST['temp_key'] ) ? $_POST['temp_key'] : null;
            $shortcode_settings = isset( $_POST['shortcode_settings'] ) ? $_POST['shortcode_settings'] : null;

            if ( empty($temp_key) ) wp_send_json_error( __('No temp key provided', 'gsteam'), 400 );
            if ( empty($shortcode_settings) ) wp_send_json_error( __('No temp settings provided', 'gsteam'), 400 );

            delete_transient( $temp_key );
            set_transient( $temp_key, $shortcode_settings, 86400 ); // save the transient for 1 day

            wp_send_json_success([
                'message' => __('Temp data saved', 'gsteam'),
            ]);

        }

        public function get_translation_srtings() {
            return [

                'install-demo-data' => __('Install Demo Data', 'gsteam'),
                'install-demo-data-description' => __('Quick start with GS Plugins by installing the demo data', 'gsteam'),

                'bulk-import' => __('Bulk Import', 'gsteam'),
                'bulk-import-description' => __('Add team members faster by GS Bulk Import feature', 'gsteam'),

                'preference' => __('Preference', 'gsteam'),
                'save-preference' => __('Save Preference', 'gsteam'),
                'team-members-slug' => __('Team Members Slug', 'gsteam'),
                'team-members-slug-details' => __('After updating GS Team Members slug, Member may NOT found with 404 error. In this senario go to Settings > Permalinks. It\'ll flush URL. Clear cache if needed & refresh Single Team Member page to display.', 'gsteam'),
                
                'show-acf-fields' => __('Display ACF Fields', 'gsteam'),
                'show-acf-fields-details' => __('Display ACF fields in the single pages', 'gsteam'),
                
                'disable_lazy_load' => __('Disable Lazy Load', 'gsteam'),
                'disable_lazy_load-details' => __('Disable Lazy Load for team member images', 'gsteam'),
                
                'lazy_load_class' => __('Lazy Load Class', 'gsteam'),
                'lazy_load_class-details' => __('Add class to disable lazy loading, multiple classes should be separated by space', 'gsteam'),

                'acf-fields-position' => __('ACF Fields Position', 'gsteam'),
                'acf-fields-position-details' => __('Position to display ACF fields', 'gsteam'),
                
                'enable-multilingual' => __('Enable Multilingual', 'gsteam'),
                'enable-multilingual--details' => __('Enable Multilingual mode to translate below strings using any Multilingual plugin like wpml or loco translate.', 'gsteam'),
                
                'pref-filter-designation-text' => __('Filter Designation Text', 'gsteam'),
                'pref-serach-text' => __('Serach Text', 'gsteam'),
                'pref-search-all-fields' => __('Include fields when search', 'gsteam'),
                'pref-company' => __('Company', 'gsteam'),
                'pref-address' => __('Address', 'gsteam'),
                'pref-land-phone' => __('Land Phone', 'gsteam'),
                'pref-cell-phone' => __('Cell Phone', 'gsteam'),
                'pref-email' => __('Email', 'gsteam'),
                'pref-location' => __('Location', 'gsteam'),
                'pref-language' => __('Language', 'gsteam'),
                'pref-specialty' => __('Specialty', 'gsteam'),
                'pref-gender' => __('Gender', 'gsteam'),
                'pref-read-on' => __('Read On', 'gsteam'),
                'pref-more' => __('More', 'gsteam'),
                'custom-css' => __('Custom CSS', 'gsteam'),
                
                'pref-filter-designation-text-details' => __('Replace with preferred text for Designation', 'gsteam'),
                'pref-serach-text-details' => __('Replace with preferred text for Search', 'gsteam'),
                'pref-company-details' => __('Replace with preferred text for Company', 'gsteam'),
                'pref-address-details' => __('Replace with preferred text for Address', 'gsteam'),
                'pref-land-phone-details' => __('Replace with preferred text for Land Phone', 'gsteam'),
                'pref-cell-phone-details' => __('Replace with preferred text for Cell Phone', 'gsteam'),
                'pref-email-details' => __('Replace with preferred text for Email', 'gsteam'),
                'pref-location-details' => __('Replace with preferred text for Location', 'gsteam'),
                'pref-language-details' => __('Replace with preferred text for Language', 'gsteam'),
                'pref-specialty-details' => __('Replace with preferred text for Specialty', 'gsteam'),
                'pref-gender-details' => __('Replace with preferred text for Gender', 'gsteam'),
                'pref-read-on-details' => __('Replace with preferred text for Read On', 'gsteam'),
                'pref-more-details' => __('Replace with preferred text for More', 'gsteam'),
                'pref-search-all-fields-details' => __('Enable searching through all fields', 'gsteam'),


                'vcard-txt' => __('vCard Text', 'gsteam'),
                'vcard-txt-details' => __('Replace with preferred text for vCard Text', 'gsteam'),

                'reset-filters' => __('Reset Filters Text', 'gsteam'),
                'reset-filters-details' => __('Replace with preferred text for Reset Filters button text', 'gsteam'),

                'shortcodes' => __('Shortcodes', 'gsteam'),
                'shortcode' => __('Shortcode', 'gsteam'),
                'global-settings-for-gs-team-members' => __('Global Settings for GS Team Members', 'gsteam'),
                'all-shortcodes-for-gs-team-member' => __('All shortcodes for GS Team Member', 'gsteam'),
                'create-shortcode' => __('Create Shortcode', 'gsteam'),
                'create-new-shortcode' => __('Create New Shortcode', 'gsteam'),
                'name' => __('Name', 'gsteam'),
                'action' => __('Action', 'gsteam'),
                'actions' => __('Actions', 'gsteam'),
                'edit' => __('Edit', 'gsteam'),
                'clone' => __('Clone', 'gsteam'),
                'delete' => __('Delete', 'gsteam'),
                'delete-all' => __('Delete All', 'gsteam'),
                'create-a-new-shortcode-and' => __('Create a new shortcode & save it to use globally in anywhere', 'gsteam'),
                'edit-shortcode' => __('Edit Shortcode', 'gsteam'),
                'general-settings' => __('General Settings', 'gsteam'),
                'style-settings' => __('Style Settings', 'gsteam'),
                'query-settings' => __('Query Settings', 'gsteam'),
                'general-settings-short' => __('General', 'gsteam'),
                'style-settings-short' => __('Style', 'gsteam'),
                'query-settings-short' => __('Query', 'gsteam'),
                'columns' => __('Columns', 'gsteam'),
                'columns_desktop' => __('Desktop Slides', 'gsteam'),
                'columns_desktop_details' => __('Enter the slides number for desktop', 'gsteam'),
                'columns_tablet' => __('Tablet Slides', 'gsteam'),
                'columns_tablet_details' => __('Enter the slides number for tablet', 'gsteam'),
                'columns_mobile_portrait' => __('Portrait Mobile Slides', 'gsteam'),
                'columns_mobile_portrait_details' => __('Enter the slides number for portrait or large display mobile', 'gsteam'),
                'columns_mobile' => __('Mobile Slides', 'gsteam'),
                'columns_mobile_details' => __('Enter the slides number for mobile', 'gsteam'),
                'style-theming' => __('Style & Theming', 'gsteam'),
                'member-name' => __('Member Name', 'gsteam'),
                'gs_member_name_is_linked' => __('Link Team Members', 'gsteam'),
                'gs_member_name_is_linked__details' => __('Add links to the Member\'s name, description & image to display popup or to single member page', 'gsteam'),
                'gs_member_thumbnail_sizes' => __('Thumbnail Sizes', 'gsteam'),
                'gs_member_thumbnail_sizes_details' => __('Select a thumbnail size', 'gsteam'),
                'gs_member_link_type' => __('Link Type', 'gsteam'),
                'gs_member_link_type__details' => __('Choose the link type of team members', 'gsteam'),
                
                'member-designation' => __('Member Designation', 'gsteam'),
                'member-details' => __('Member Details', 'gsteam'),
                'social-connection' => __('Social Connection', 'gsteam'),
                'pagination' => __('Pagination', 'gsteam'),
                'next-prev-member' => __('Next / Prev Member', 'gsteam'),
                'instant-search-by-name' => __('Instant Search by Name', 'gsteam'),
                'filter-by-designation' => __('Filter by Designation', 'gsteam'),
                'filter-by-location' => __('Filter by Location', 'gsteam'),
                'filter-by-language' => __('Filter by Language', 'gsteam'),
                'filter-by-gender' => __('Filter by Gender', 'gsteam'),
                'filter-by-speciality' => __('Filter by Specialty', 'gsteam'),
                'gs_team_filter_columns' => __('Filter Columns', 'gsteam'),
                'social-link-target' => __('Social Link Target', 'gsteam'),
                'details-control' => __('Details Control', 'gsteam'),
                'popup-column' => __('Popup Column', 'gsteam'),
                'filter-category-position' => __('Filter Category Position', 'gsteam'),
                'panel' => __('Panel', 'gsteam'),
                'name-font-size' => __('Name Font Size', 'gsteam'),
                'name-font-weight' => __('Name Font Weight', 'gsteam'),
                'name-font-style' => __('Name Font Style', 'gsteam'),
                'name-color' => __('Name Color', 'gsteam'),
                'name-bg-color' => __('Name BG Color', 'gsteam'),
                'tooltip-bg-color' => __('Tooltip BG Color', 'gsteam'),
                'info-bg-color' => __('Info BG Color', 'gsteam'),
                'hover-icon-bg-color' => __('Hover Icon BG Color', 'gsteam'),
                'ribon-background-color' => __('Ribbon Background Color', 'gsteam'),
                'role-font-size' => __('Role Font Size', 'gsteam'),
                'role-font-weight' => __('Role Font Weight', 'gsteam'),
                'role-font-style' => __('Role Font Style', 'gsteam'),
                'role-color' => __('Role Color', 'gsteam'),
                'popup-arrow-color' => __('Popup Arrow Color', 'gsteam'),
                'team-members' => __('Team Members', 'gsteam'),
                'order' => __('Order', 'gsteam'),
                'order-by' => __('Order By', 'gsteam'),
                'group' => __('Group', 'gsteam'),
                'exclude_group' => __('Exclude Group', 'gsteam'),
                'exclude_group__help' => __('Select specific team group to hide that specific group members', 'gsteam'),

                'theme' => __('Theme', 'gsteam'),
                'font-size' => __('Font Size', 'gsteam'),
                'font-weight' => __('Font Weight', 'gsteam'),
                'font-style' => __('Font Style', 'gsteam'),
                'shortcode-name' => __('Shortcode Name', 'gsteam'),

                'select-number-of-team-columns' => __('Select number of Team columns', 'gsteam'),
                'select-preffered-style-theme' => __('Select preffered Style & Theme', 'gsteam'),
                'show-or-hide-team-member-name' => __('Show or Hide Team Member Name', 'gsteam'),
                'show-or-hide-team-member-designation' => __('Show or Hide Team Member Designation', 'gsteam'),
                'show-or-hide-team-member-details' => __('Show or Hide Team Member Details', 'gsteam'),
                'show-or-hide-team-member-social-connections' => __('Show or Hide Team Member Social Connections', 'gsteam'),
                'show-or-hide-team-member-paginations' => __('Show or Hide Team Member paginations', 'gsteam'),
                'show-or-hide-next-prev-member-link-at-single-team-template' => __('Show or Hide Next / Prev Member link at Single Team Template', 'gsteam'),
                'show-or-hide-instant-search-applicable-for-theme-9' => __('Show or Hide Instant Search', 'gsteam'),
                'show-or-hide-filter-by-designation-applicable-for-theme-9' => __('Show or Hide Filter by Designation', 'gsteam'),
                'show-or-hide-filter-by-location-applicable-for-theme-9' => __('Show or Hide Filter by Location', 'gsteam'),
                'show-or-hide-filter-by-language-applicable-for-theme-9' => __('Show or Hide Filter by Language', 'gsteam'),
                'show-or-hide-filter-by-gender-applicable-for-theme-9' => __('Show or Hide Filter by gender', 'gsteam'),
                'show-or-hide-filter-by-speciality-applicable-for-theme-9' => __('Show or Hide Filter by Specialty', 'gsteam'),
                'specify-target-to-load-the-links' => __('Specify target to load the Links, Default New Tab', 'gsteam'),
                'specify-target-to-load-the-links' => __('Specify target to load the Links, Default New Tab', 'gsteam'),
                'define-maximum-number-of-characters' => __('Define maximum number of characters in Member details. Default 100', 'gsteam'),
                'set-column-for-popup' => __('Set column for popup', 'gsteam'),
                'set-max-team-numbers-you-want-to-show' => __('Set max team numbers you want to show, set -1 for all members', 'gsteam'),
                'select-specific-team-group-to' => __('Select specific team group to show that specific group members', 'gsteam'),

                'enable-multi-select' => __('Enable Multi Select', 'gsteam'),
                'enable-multi-select--help' => __('Enable multi selection on the filters, Default is Off', 'gsteam'),
                'multi-select-ellipsis' => __('Multi Select Ellipsis', 'gsteam'),
                'multi-select-ellipsis--help' => __('Show multi selected values in ellipsis mode, Default is Off', 'gsteam'),

                'filter-all-enabled' => __('Enable All Filter', 'gsteam'),
                'filter-all-enabled--help' => __('Enable All filter in the filter templates, Default is On', 'gsteam'),

                'enable-child-cats' => __('Enable Child Filters', 'gsteam'),
                'enable-child-cats--help' => __('Enable child group filters, Default is Off', 'gsteam'),

                'enable-scroll-animation' => __('Enable Scroll Animation', 'gsteam'),
                'enable-scroll-animation--help' => __('Enable scroll animation, Default is On', 'gsteam'),

                'fitler-all-text' => __('All filter text', 'gsteam'),
                'fitler-all-text--help' => __('All filter text for filter templates, Default is All', 'gsteam'),

                'enable-clear-filters' => __('Reset Filters Button', 'gsteam'),
                'enable-clear-filters--help' => __('Enable Reset all filters button in filter themes, Default is Off ', 'gsteam'),

                'shortcode-name' => __('Shortcode Name', 'gsteam'),
                'save-shortcode' => __('Save Shortcode', 'gsteam'),
                'preview-shortcode' => __('Preview Shortcode', 'gsteam')
            ];
        }

        public function get_shortcode_options_themes() {

            $free_themes = [
                [
                    'label' => __( 'Grid 1 (Hover) - Free', 'gsteam' ),
                    'value' => 'gs_tm_theme1'
                ],
                [
                    'label' => __( 'Circle 1 (Hover) - Free', 'gsteam' ),
                    'value' => 'gs_tm_theme2'
                ],
                [
                    'label' => __( 'Horizontal 1 (Square Right Info) - Free', 'gsteam' ),
                    'value' => 'gs_tm_theme3'
                ],
                [
                    'label' => __( 'Horizontal 2 (Square Left Info) - Free', 'gsteam' ),
                    'value' => 'gs_tm_theme4'
                ],
                [
                    'label' => __( 'Horizontal 3 (Circle Right Info) - Free', 'gsteam' ),
                    'value' => 'gs_tm_theme5'
                ],
                [
                    'label' => __( 'Horizontal 4 (Circle Left Info) - Free', 'gsteam' ),
                    'value' => 'gs_tm_theme6'
                ]
            ];

            $pro_themes = [
                [
                    'label' => __( 'Grid 2 (Tooltip) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_grid2'
                ],
                [
                    'label' => __( 'Grid 3 (Static) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme20'
                ],
                [
                    'label' => __( 'Flip - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme23'
                ],
                [
                    'label' => __( 'Drawer 1 - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme13'
                ],
                [
                    'label' => __( 'Drawer 2 - Pro', 'gsteam' ),
                    'value' => 'gs_tm_drawer2'
                ],
                [
                    'label' => __( 'Table 1 (Underline) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme14'
                ],
                [
                    'label' => __( 'Table 2 (Box Border) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme15'
                ],
                [
                    'label' => __( 'Table 3 (Odd Even) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme16'
                ],
                [
                    'label' => __( 'Table & Filter - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme21'
                ],
                [
                    'label' => __( 'Table & Filter Dense - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme21_dense'
                ],
                [
                    'label' => __( 'Filter Grid & To Single - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme22'
                ],
                [
                    'label' => __( 'List 1 (Square Right Info) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme17'
                ],
                [
                    'label' => __( 'List 2 (Square Left Info) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme18'
                ],
                [
                    'label' => __( 'Slider 1 (Hover) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme7'
                ],
                [
                    'label' => __( 'Popup 1 - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme8'
                ],
                [
                    'label' => __( 'To Single - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme11'
                ],
                [
                    'label' => __( 'Filter 1 (Hover & Pop) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme9'
                ],
                [
                    'label' => __( 'Filter 2 (Selected Cats) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme12'
                ],
                [
                    'label' => __( 'Filter Grid with vcard - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme24'
                ],
                [
                    'label' => __( 'Group Filter One - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme25'
                ],
                [
                    'label' => __( 'Panel Slide - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme19'
                ],
                [
                    'label' => __( 'Gray 1 (Square) - Pro', 'gsteam' ),
                    'value' => 'gs_tm_theme10'
                ]
            ];

            if ( ! gtm_fs()->is_paying_or_trial() ) {
                
                $pro_themes = array_map( function( $item ) {
                    $item['pro'] = true;
                    return $item;
                }, $pro_themes);
                
            }

            return array_merge( $free_themes, $pro_themes );

        }

        public function get_shortcode_options_link_types() {

            $free_options = [
                [
                    'label' => __( 'Default', 'gsteam' ),
                    'value' => 'default'
                ],
                [
                    'label' => __( 'Single Page', 'gsteam' ),
                    'value' => 'single_page'
                ],
            ];

            $pro_options = [
                [
                    'label' => __( 'Popup', 'gsteam' ),
                    'value' => 'popup'
                ],
            ];

            if ( ! gtm_fs()->is_paying_or_trial() ) {
                
                $pro_options = array_map( function( $item ) {
                    $item['pro'] = true;
                    return $item;
                }, $pro_options);
                
            }

            return array_merge( $free_options, $pro_options );

        }

        public function get_shortcode_default_options() {
            return [
                'group' => $this->get_team_groups(),
                'exclude_group' => $this->get_team_groups(),
                'gs_team_cols' => $this->get_columns(),
                'gs_member_thumbnail_sizes' => $this->getPossibleThumbnailSizes(),
                'gs_team_cols_tablet' => $this->get_columns(),
                'gs_team_cols_mobile_portrait' => $this->get_columns(),
                'gs_team_cols_mobile' => $this->get_columns(),
                'gs_team_theme' => $this->get_shortcode_options_themes(),
                'gs_member_link_type' => $this->get_shortcode_options_link_types(),
                'acf_fields_position' => $this->get_acf_fields_position(),
                'gs_teammembers_pop_clm' => [
                    [
                        'label' => __( 'One', 'gsteam' ),
                        'value' => 'one'
                    ],
                    [
                        'label' => __( 'Two', 'gsteam' ),
                        'value' => 'two'
                    ],
                ],
                'gs_team_filter_columns' => [
                    [
                        'label' => __( 'Two', 'gsteam' ),
                        'value' => 'two'
                    ],
                    [
                        'label' => __( 'Three', 'gsteam' ),
                        'value' => 'three'
                    ],
                ],
                'gs_tm_filter_cat_pos' => [
                    [
                        'label' => __( 'Left', 'gsteam' ),
                        'value' => 'left'
                    ],
                    [
                        'label' => __( 'Center', 'gsteam' ),
                        'value' => 'center'
                    ],
                    [
                        'label' => __( 'Right', 'gsteam' ),
                        'value' => 'right'
                    ]
                ],
                'panel' => [
                    [
                        'label' => __( 'Left', 'gsteam' ),
                        'value' => 'left'
                    ],
                    [
                        'label' => __( 'Center', 'gsteam' ),
                        'value' => 'center'
                    ],
                    [
                        'label' => __( 'Right', 'gsteam' ),
                        'value' => 'right'
                    ]
                ],
                'orderby' => [
                    [
                        'label' => __( 'Custom Order', 'gsteam' ),
                        'value' => 'menu_order'
                    ],
                    [
                        'label' => __( 'Team ID', 'gsteam' ),
                        'value' => 'ID'
                    ],
                    [
                        'label' => __( 'Team Name', 'gsteam' ),
                        'value' => 'title'
                    ],
                    [
                        'label' => __( 'Date', 'gsteam' ),
                        'value' => 'date'
                    ],
                    [
                        'label' => __( 'Random', 'gsteam' ),
                        'value' => 'rand'
                    ],
                ],
                'order' => [
                    [
                        'label' => __( 'DESC', 'gsteam' ),
                        'value' => 'DESC'
                    ],
                    [
                        'label' => __( 'ASC', 'gsteam' ),
                        'value' => 'ASC'
                    ],
                ],

                // Style Options
                'gs_tm_m_fntw' => [
                    [
                        'label' => __( '100 - Thin', 'gsteam' ),
                        'value' => 100
                    ],
                    [
                        'label' => __( '200 - Extra Light', 'gsteam' ),
                        'value' => 200
                    ],
                    [
                        'label' => __( '300 - Light', 'gsteam' ),
                        'value' => 300
                    ],
                    [
                        'label' => __( '400 - Regular', 'gsteam' ),
                        'value' => 400
                    ],
                    [
                        'label' => __( '500 - Medium', 'gsteam' ),
                        'value' => 500
                    ],
                    [
                        'label' => __( '600 - Semi-Bold', 'gsteam' ),
                        'value' => 600
                    ],
                    [
                        'label' => __( '700 - Bold', 'gsteam' ),
                        'value' => 700
                    ],
                    [
                        'label' => __( '800 - Extra Bold', 'gsteam' ),
                        'value' => 800
                    ],
                    [
                        'label' => __( '900 - Black', 'gsteam' ),
                        'value' => 900
                    ],
                ],
                'gs_tm_m_fnstyl' => [
                    [
                        'label' => __( 'Normal', 'gsteam' ),
                        'value' => 'normal'
                    ],
                    [
                        'label' => __( 'Italic', 'gsteam' ),
                        'value' => 'italic'
                    ],
                ],
                'gs_tm_role_fntw' => [
                    [
                        'label' => __( '100 - Thin', 'gsteam' ),
                        'value' => 100
                    ],
                    [
                        'label' => __( '200 - Extra Light', 'gsteam' ),
                        'value' => 200
                    ],
                    [
                        'label' => __( '300 - Light', 'gsteam' ),
                        'value' => 300
                    ],
                    [
                        'label' => __( '400 - Regular', 'gsteam' ),
                        'value' => 400
                    ],
                    [
                        'label' => __( '500 - Medium', 'gsteam' ),
                        'value' => 500
                    ],
                    [
                        'label' => __( '600 - Semi-Bold', 'gsteam' ),
                        'value' => 600
                    ],
                    [
                        'label' => __( '700 - Bold', 'gsteam' ),
                        'value' => 700
                    ],
                    [
                        'label' => __( '800 - Extra Bold', 'gsteam' ),
                        'value' => 800
                    ],
                    [
                        'label' => __( '900 - Black', 'gsteam' ),
                        'value' => 900
                    ],
                ],
                'gs_tm_role_fnstyl' => [
                    [
                        'label' => __( 'Normal', 'gsteam' ),
                        'value' => 'normal'
                    ],
                    [
                        'label' => __( 'Italic', 'gsteam' ),
                        'value' => 'italic'
                    ],
                ],
            ];
        }

        public function get_shortcode_default_settings() {
            return getGsShortcodeAttributes();
        }

        public function get_shortcode_default_prefs() {
            return [
                'gs_member_nxt_prev' => 'off',
                'gs_member_search_all_fields' => 'off',
                'gs_member_enable_multilingual' => 'off',
                'gs_teammembers_slug' => 'team-members',
                'show_acf_fields' => 'off',
                'disable_lazy_load' => 'off',
                'lazy_load_class' => 'skip-lazy',
                'acf_fields_position' => 'after_skills',
                'gs_teamfliter_designation' => gs_team_get_translation('gs_teamfliter_designation'),
                'gs_teamfliter_name' => gs_team_get_translation('gs_teamfliter_name'),
                'gs_teamcom_meta' => gs_team_get_translation('gs_teamcom_meta'),
                'gs_teamadd_meta' => gs_team_get_translation('gs_teamadd_meta'),
                'gs_teamlandphone_meta' => gs_team_get_translation('gs_teamlandphone_meta'),
                'gs_teamcellPhone_meta' => gs_team_get_translation('gs_teamcellPhone_meta'),
                'gs_teamemail_meta' => gs_team_get_translation('gs_teamemail_meta'),
                'gs_teamlocation_meta' => gs_team_get_translation('gs_teamlocation_meta'),
                'gs_teamlanguage_meta' => gs_team_get_translation('gs_teamlanguage_meta'),
                'gs_teamspecialty_meta' => gs_team_get_translation('gs_teamspecialty_meta'),
                'gs_teamgender_meta' => gs_team_get_translation('gs_teamgender_meta'),
                'gs_team_read_on' => gs_team_get_translation('gs_team_read_on'),
                'gs_team_more' => gs_team_get_translation('gs_team_more'),
                'gs_team_vcard_txt' => gs_team_get_translation('gs_team_vcard_txt'),
                'gs_team_reset_filters_txt' => gs_team_get_translation('gs_team_reset_filters_txt'),
                'gs_team_custom_css' => ''
            ];
        }

        public function get_columns() {

            return [
                [
                    'label' => __( '1 Column', 'gsteam' ),
                    'value' => '12'
                ],
                [
                    'label' => __( '2 Columns', 'gsteam' ),
                    'value' => '6'
                ],
                [
                    'label' => __( '3 Columns', 'gsteam' ),
                    'value' => '4'
                ],
                [
                    'label' => __( '4 Columns', 'gsteam' ),
                    'value' => '3'
                ],
                [
                    'label' => __( '5 Columns', 'gsteam' ),
                    'value' => '2_4'
                ],
                [
                    'label' => __( '6 Columns', 'gsteam' ),
                    'value' => '2'
                ],
            ];

        }

        public function get_acf_fields_position() {

            return [
                [
                    'label' => __( 'After Skills', 'gsteam' ),
                    'value' => 'after_skills'
                ],
                [
                    'label' => __( 'After Description', 'gsteam' ),
                    'value' => 'after_description'
                ],
                [
                    'label' => __( 'After Meta Details', 'gsteam' ),
                    'value' => 'after_meta_details'
                ],
            ];

        }

        /**
         * Retrives WP registered possible thumbnail sizes.
         * 
         * @since  1.10.14
         * @return array   image sizes.
         */
        public function getPossibleThumbnailSizes() {
            
            $sizes = get_intermediate_image_sizes();

            if ( empty($sizes) ) return [];

            $result = [];

            foreach ( $sizes as $size ) {
                $result[] = [
                    'label' => ucwords( preg_replace('/_|-/', ' ', $size) ),
                    'value' => $size
                ];
            }
            
            return $result;
        }

        public function get_shortcode_prefs_options() {
            return [
                'acf_fields_position' => $this->get_acf_fields_position()
            ];
        }

        public function _save_shortcode_pref( $nonce, $settings, $is_ajax ) {

            if ( ! wp_verify_nonce( $nonce, '_gsteam_save_shortcode_pref_gs_') ) {
                if ( $is_ajax ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );
                return false;
            }

            // Maybe add validation?
            update_option( $this->option_name, $settings, 'yes' );
            
            // Clean permalink flush
            delete_option( 'GS_Team_plugin_permalinks_flushed' );
        
            if ( $is_ajax ) wp_send_json_success( __('Preference saved', 'gsteam') );

        }

        public function save_shortcode_pref( $nonce = null ) {
    
            if ( ! $nonce ) {
                $nonce = wp_create_nonce('_gsteam_save_shortcode_pref_gs_');
            }
    
            if ( empty($_POST['prefs']) ) {
                wp_send_json_error( __('No preference provided', 'gsteam'), 400 );
            }
    
            $this->_save_shortcode_pref( $nonce, $_POST['prefs'], true );

        }

        public function _get_shortcode_pref( $is_ajax ) {

            $pref = get_option( $this->option_name );

            if ( empty($pref) ) {
                $pref = $this->get_shortcode_default_prefs();
                $this->_save_shortcode_pref( wp_create_nonce('_gsteam_save_shortcode_pref_gs_'), $pref, false );
            }

            if ( $is_ajax ) {
                wp_send_json_success( $pref );
            }

            return $pref;

        }

        public function get_shortcode_pref() {
    
            $this->_get_shortcode_pref( wp_doing_ajax() );

        }

        static function maybe_create_shortcodes_table() {

            global $wpdb;

            $gs_team_db_version = '1.0';

            if ( get_option("{$wpdb->prefix}gs_team_db_version") == $gs_team_db_version ) return; // vail early
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}gs_team (
            	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
            	shortcode_name TEXT NOT NULL,
            	shortcode_settings LONGTEXT NOT NULL,
            	created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            	updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            	PRIMARY KEY (id)
            )".$wpdb->get_charset_collate().";";
                
            if ( get_option("{$wpdb->prefix}gs_team_db_version") < $gs_team_db_version ) {
                dbDelta( $sql );
            }

            update_option( "{$wpdb->prefix}gs_team_db_version", $gs_team_db_version );
            
        }

        public function create_dummy_shortcodes() {

            $GS_Team_Dummy_Data = GS_Team_Dummy_Data::get_instance();

            $request = wp_remote_get( GSTEAM_PLUGIN_URI . '/includes/demo-data/gs-team-dummy-shortcodes.json', array('sslverify' => false) );

            if ( is_wp_error($request) ) return false;

            $shortcodes = wp_remote_retrieve_body( $request );

            $shortcodes = json_decode( $shortcodes, true );

            $wpdb = $this->gsteam_get_wpdb();

            if ( ! $shortcodes || ! count($shortcodes) ) return;

            foreach ( $shortcodes as $shortcode ) {

                $shortcode['shortcode_settings'] = json_decode( $shortcode['shortcode_settings'], true );
                $shortcode['shortcode_settings']['gsteam-demo_data'] = true;

                if ( !empty( $group = $shortcode['shortcode_settings']['group']) ) {
                    $shortcode['shortcode_settings']['group'] = (array) $GS_Team_Dummy_Data->get_taxonomy_ids_by_slugs( 'team_group', explode(',', $group) );
                }

                if ( !empty( $exclude_group = $shortcode['shortcode_settings']['exclude_group']) ) {
                    $shortcode['shortcode_settings']['exclude_group'] = (array) $GS_Team_Dummy_Data->get_taxonomy_ids_by_slugs( 'team_group', explode(',', $exclude_group) );
                }
    
                $data = array(
                    "shortcode_name" => $shortcode['shortcode_name'],
                    "shortcode_settings" => json_encode($shortcode['shortcode_settings']),
                    "created_at" => current_time( 'mysql'),
                    "updated_at" => current_time( 'mysql'),
                );
    
                $wpdb->insert( "{$wpdb->prefix}gs_team", $data, $this->get_gsteam_shortcode_db_columns() );

            }

        }

        public function delete_dummy_shortcodes() {

            $wpdb = $this->gsteam_get_wpdb();

            $needle = 'gsteam-demo_data';

            $wpdb->query( "DELETE FROM {$wpdb->prefix}gs_team WHERE shortcode_settings like '%$needle%'" );

        }

        public function maybe_upgrade_data( $old_version ) {
            if ( version_compare( $old_version, '1.10.8' ) < 0 ) $this->upgrade_to_1_10_8();
            if ( version_compare( $old_version, '1.10.16' ) < 0 ) $this->upgrade_to_1_10_16();
        }

        public function upgrade_to_1_10_8() {

            $shortcodes = $this->_get_shortcodes();
            
            foreach ( $shortcodes as $shortcode ) {

                $shortcode_id = $shortcode['id'];
                $shortcode_settings = json_decode( $shortcode["shortcode_settings"], true );

                if ( !in_array( $shortcode_settings['gs_team_theme'], ['gs_tm_theme3', 'gs_tm_theme4', 'gs_tm_theme5', 'gs_tm_theme6'] ) ) {

                    $shortcode_settings['gs_team_cols']                 = 3;
                    $shortcode_settings['gs_team_cols_tablet']          = 4;
                    $shortcode_settings['gs_team_cols_mobile_portrait'] = 6;
                    $shortcode_settings['gs_team_cols_mobile']          = 12;

                } else {

                    $shortcode_settings['gs_team_cols']                 = 4;
                    $shortcode_settings['gs_team_cols_tablet']          = 6;
                    $shortcode_settings['gs_team_cols_mobile_portrait'] = 6;
                    $shortcode_settings['gs_team_cols_mobile']          = 12;

                }

                if ( empty($shortcode_settings['gs_member_link_type']) ) $shortcode_settings['gs_member_link_type'] = 'default';

                unset( $shortcode_settings['gs_team_cols_desktop'] );

                $shortcode_settings = $this->validate_shortcode_settings( $shortcode_settings );
        
                $wpdb = $this->gsteam_get_wpdb();
            
                $data = array(
                    "shortcode_settings" 	=> json_encode($shortcode_settings),
                    "updated_at" 		    => current_time( 'mysql')
                );
            
                $wpdb->update( "{$wpdb->prefix}gs_team" , $data, array( 'id' => absint( $shortcode_id ) ), [
                    'shortcode_settings' => '%s',
                    'updated_at' => '%s',
                ]);

            }

        }

        public function upgrade_to_1_10_16() {

            $shortcodes = $this->_get_shortcodes();
            
            foreach ( $shortcodes as $shortcode ) {

                $update             = false;
                $shortcode_id       = $shortcode['id'];
                $shortcode_settings = json_decode( $shortcode["shortcode_settings"], true );
                $group              = $shortcode_settings['group'];
                $exclude_group      = $shortcode_settings['exclude_group'];

                if ( !empty($group) && is_string($group) ) {
                    
                    $update = true;
                    $group = explode( ',', $group );
                    
                    $terms = array_map( function( $group_slug ) {
                        return get_term_by( 'slug', $group_slug, 'team_group' );
                    }, $group );

                    $shortcode_settings['group'] = wp_list_pluck( $terms, 'term_id' );

                }

                if ( !empty($exclude_group) && is_string($exclude_group) ) {
                    
                    $update = true;
                    $exclude_group  = explode( ',', $exclude_group );

                    $terms = array_map( function( $group_slug ) {
                        return get_term_by( 'slug', $group_slug, 'team_group' );
                    }, $exclude_group );

                    $shortcode_settings['exclude_group'] = wp_list_pluck( $terms, 'term_id' );

                }

                if ( ! $update ) continue;

                $shortcode_settings = $this->validate_shortcode_settings( $shortcode_settings );
        
                $wpdb = $this->gsteam_get_wpdb();
            
                $data = array(
                    "shortcode_settings" 	=> json_encode($shortcode_settings),
                    "updated_at" 		    => current_time( 'mysql')
                );
            
                $wpdb->update( "{$wpdb->prefix}gs_team" , $data, array( 'id' => absint( $shortcode_id ) ), [
                    'shortcode_settings' => '%s',
                    'updated_at' => '%s',
                ]);

            }

        }

    }

}

GS_Team_Shortcode_Builder::get_instance();