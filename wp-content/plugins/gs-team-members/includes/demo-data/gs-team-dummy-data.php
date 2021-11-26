<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Dummy_Data' ) ) {

    final class GS_Team_Dummy_Data {

        private static $_instance = null;

        private $is_pro;
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Dummy_Data();
            }

            return self::$_instance;
            
        }

        public function __construct() {

            $this->is_pro = gtm_fs()->is_paying_or_trial();

            add_action( 'admin_notices', array($this, 'gsteam_dummy_data_admin_notice') );

            add_action( 'wp_ajax_gsteam_dismiss_demo_data_notice', array($this, 'gsteam_dismiss_demo_data_notice') );

            add_action( 'wp_ajax_gsteam_import_team_data', array($this, 'import_team_data') );

            add_action( 'wp_ajax_gsteam_remove_team_data', array($this, 'remove_team_data') );

            add_action( 'wp_ajax_gsteam_import_shortcode_data', array($this, 'import_shortcode_data') );

            add_action( 'wp_ajax_gsteam_remove_shortcode_data', array($this, 'remove_shortcode_data') );

            add_action( 'wp_ajax_gsteam_import_all_data', array($this, 'import_all_data') );

            add_action( 'wp_ajax_gsteam_remove_all_data', array($this, 'remove_all_data') );

            add_action( 'gs_after_shortcode_submenu', array($this, 'register_sub_menu') );

            // Remove dummy indicator
            add_action( 'edit_post_gs_team', array($this, 'remove_dummy_indicator'), 10 );

            // Import Process
            add_action( 'gsteam_dummy_attachments_process_start', function() {

                // Force delete option if have any
                delete_option( 'gsteam_dummy_team_data_created' );

                // Force update the process
                set_transient( 'gsteam_dummy_team_data_creating', 1, 3 * MINUTE_IN_SECONDS );

            });
            
            add_action( 'gsteam_dummy_attachments_process_finished', function() {

                $this->create_dummy_terms();

            });
            
            add_action( 'gsteam_dummy_terms_process_finished', function() {

                $this->create_dummy_members();

            });
            
            add_action( 'gsteam_dummy_members_process_finished', function() {

                // clean the record that we have started a process
                delete_transient( 'gsteam_dummy_team_data_creating' );

                // Add a track so we never duplicate the process
                update_option( 'gsteam_dummy_team_data_created', 1 );

            });
            
            // Shortcodes
            add_action( 'gsteam_dummy_shortcodes_process_start', function() {

                // Force delete option if have any
                delete_option( 'gsteam_dummy_shortcode_data_created' );

                // Force update the process
                set_transient( 'gsteam_dummy_shortcode_data_creating', 1, 3 * MINUTE_IN_SECONDS );

            });

            add_action( 'gsteam_dummy_shortcodes_process_finished', function() {

                // clean the record that we have started a process
                delete_transient( 'gsteam_dummy_shortcode_data_creating' );

                // Add a track so we never duplicate the process
                update_option( 'gsteam_dummy_shortcode_data_created', 1 );

            });
            
        }

        public function register_sub_menu() {

            $builder = GS_Team_Shortcode_Builder::get_instance();

            add_submenu_page( 
                'edit.php?post_type=gs_team', 'Install Demo', 'Install Demo', 'manage_options', 'gs-team-shortcode#/demo-data', array( $builder, 'view' )
            );

        }

        public function get_taxonomy_list() {

            if ( $this->is_pro ) {
                return ['team_group', 'team_language', 'team_location', 'team_gender', 'team_specialty'];
            }

            return ['team_group'];

        }

        public function remove_dummy_indicator( $post_id ) {

            if ( empty( get_post_meta($post_id, 'gsteam-demo_data', true) ) ) return;
            
            $taxonomies = $this->get_taxonomy_list();

            // Remove dummy indicator from texonomies
            $dummy_terms = wp_get_post_terms( $post_id, $taxonomies, [
                'fields' => 'ids',
                'meta_key' => 'gsteam-demo_data',
                'meta_value' => 1,
            ]);

            if ( !empty($dummy_terms) ) {
                foreach( $dummy_terms as $term_id ) {
                    delete_term_meta( $term_id, 'gsteam-demo_data', 1 );
                }
                delete_transient( 'gsteam_dummy_terms' );
            }

            // Remove dummy indicator from attachments
            $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
            $thumbnail_flip_id = get_post_meta( $post_id, 'second_featured_img', true );
            if ( !empty($thumbnail_id) ) delete_post_meta( $thumbnail_id, 'gsteam-demo_data', 1 );
            if ( !empty($thumbnail_flip_id) ) delete_post_meta( $thumbnail_flip_id, 'gsteam-demo_data', 1 );
            delete_transient( 'gsteam_dummy_attachments' );
            
            // Remove dummy indicator from post
            delete_post_meta( $post_id, 'gsteam-demo_data', 1 );
            delete_transient( 'gsteam_dummy_members' );

        }

        public function import_all_data() {

            // Validate nonce && check permission
            if ( !check_admin_referer('_gsteam_simport_gsteam_demo_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            // Hide the notice
            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

            $response = [
                'team' => $this->_import_team_data( false ),
                'shortcode' => $this->_import_shortcode_data( false )
            ];

            if ( wp_doing_ajax() ) wp_send_json_success( $response, 200 );

            return $response;

        }

        public function remove_all_data() {

            // Validate nonce && check permission
            if ( !check_admin_referer('_gsteam_simport_gsteam_demo_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            // Hide the notice
            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

            $response = [
                'team' => $this->_remove_team_data( false ),
                'shortcode' => $this->_remove_shortcode_data( false )
            ];

            if ( wp_doing_ajax() ) wp_send_json_success( $response, 200 );

            return $response;

        }

        public function import_team_data() {

            // Validate nonce && check permission
            if ( !check_admin_referer('_gsteam_simport_gsteam_demo_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            // Hide the notice
            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

            // Start importing
            $this->_import_team_data();

        }

        public function remove_team_data() {

            // Validate nonce && check permission
            if ( !check_admin_referer('_gsteam_simport_gsteam_demo_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            // Hide the notice
            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

            // Remove team data
            $this->_remove_team_data();

        }

        public function import_shortcode_data() {

            // Validate nonce && check permission
            if ( !check_admin_referer('_gsteam_simport_gsteam_demo_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            // Hide the notice
            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

            // Start importing
            $this->_import_shortcode_data();

        }

        public function remove_shortcode_data() {

            // Validate nonce && check permission
            if ( !check_admin_referer('_gsteam_simport_gsteam_demo_gs_') || !current_user_can('manage_options') ) wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            // Hide the notice
            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

            // Remove team data
            $this->_remove_shortcode_data();

        }

        public function _import_team_data( $is_ajax = null ) {

            if ( $is_ajax === null ) $is_ajax = wp_doing_ajax();

            // Data already imported
            if ( get_option('gsteam_dummy_team_data_created') !== false || get_transient('gsteam_dummy_team_data_creating') !== false ) {

                $message_202 = __( 'Dummy Team members already imported', 'gsteam' );

                if ( $is_ajax ) wp_send_json_success( $message_202, 202 );
                
                return [
                    'status' => 202,
                    'message' => $message_202
                ];

            }
            
            // Importing demo data
            $this->create_dummy_attachments();

            $message = __( 'Dummy Team members imported', 'gsteam' );

            if ( $is_ajax ) wp_send_json_success( $message, 200 );

            return [
                'status' => 200,
                'message' => $message
            ];

        }

        public function _remove_team_data( $is_ajax = null ) {

            if ( $is_ajax === null ) $is_ajax = wp_doing_ajax();

            $this->delete_dummy_attachments();
            $this->delete_dummy_terms();
            $this->delete_dummy_members();

            delete_option( 'gsteam_dummy_team_data_created' );
            delete_transient( 'gsteam_dummy_team_data_creating' );

            $message = __( 'Dummy Team members deleted', 'gsteam' );

            if ( $is_ajax ) wp_send_json_success( $message, 200 );

            return [
                'status' => 200,
                'message' => $message
            ];

        }

        public function _import_shortcode_data( $is_ajax = null ) {

            if ( $is_ajax === null ) $is_ajax = wp_doing_ajax();

            // Data already imported
            if ( get_option('gsteam_dummy_shortcode_data_created') !== false || get_transient('gsteam_dummy_shortcode_data_creating') !== false ) {

                $message_202 = __( 'Dummy Shortcodes already imported', 'gsteam' );

                if ( $is_ajax ) wp_send_json_success( $message_202, 202 );
                
                return [
                    'status' => 202,
                    'message' => $message_202
                ];

            }
            
            // Importing demo shortcodes
            $this->create_dummy_shortcodes();

            $message = __( 'Dummy Shortcodes imported', 'gsteam' );

            if ( $is_ajax ) wp_send_json_success( $message, 200 );

            return [
                'status' => 200,
                'message' => $message
            ];

        }

        public function _remove_shortcode_data( $is_ajax = null ) {

            if ( $is_ajax === null ) $is_ajax = wp_doing_ajax();

            $this->delete_dummy_shortcodes();

            delete_option( 'gsteam_dummy_shortcode_data_created' );
            delete_transient( 'gsteam_dummy_shortcode_data_creating' );

            $message = __( 'Dummy Shortcodes deleted', 'gsteam' );

            if ( $is_ajax ) wp_send_json_success( $message, 200 );

            return [
                'status' => 200,
                'message' => $message
            ];

        }

        public function get_taxonomy_ids_by_slugs( $taxonomy_group, $taxonomy_slugs = [] ) {

            $_terms = $this->get_dummy_terms();

            if ( empty($_terms) ) return [];
            
            $_terms = wp_filter_object_list( $_terms, [ 'taxonomy' => $taxonomy_group ] );
            $_terms = array_values( $_terms );      // reset the keys
            
            if ( empty($_terms) ) return [];
            
            $term_ids = [];
            
            foreach ( $taxonomy_slugs as $slug ) {
                $key = array_search( $slug, array_column($_terms, 'slug') );
                if ( $key !== false ) $term_ids[] = $_terms[$key]['term_id'];
            }

            return $term_ids;

        }

        public function get_attachment_id_by_filename( $filename ) {

            $attachments = $this->get_dummy_attachments();
            
            if ( empty($attachments) ) return '';
            
            $attachments = wp_filter_object_list( $attachments, [ 'post_name' => $filename ] );
            if ( empty($attachments) ) return '';
            
            $attachments = array_values( $attachments );
            
            return $attachments[0]->ID;

        }

        public function get_tax_inputs( $tax_inputs = [] ) {

            if ( empty($tax_inputs) ) return $tax_inputs;

            if ( ! $this->is_pro ) {

                if ( isset($tax_inputs['team_language']) ) unset( $tax_inputs['team_language'] );
                if ( isset($tax_inputs['team_location']) ) unset( $tax_inputs['team_location'] );
                if ( isset($tax_inputs['team_gender']) ) unset( $tax_inputs['team_gender'] );
                if ( isset($tax_inputs['team_specialty']) ) unset( $tax_inputs['team_specialty'] );

            }

            foreach( $tax_inputs as $tax_input => $tax_params ) {

                $tax_inputs[$tax_input] = $this->get_taxonomy_ids_by_slugs( $tax_input, $tax_params );

            }

            return $tax_inputs;

        }

        public function get_meta_inputs( $meta_inputs = [] ) {

            if ( ! $this->is_pro ) {

                if ( isset($meta_inputs['_gs_com']) ) unset( $meta_inputs['_gs_com'] );
                if ( isset($meta_inputs['_gs_land']) ) unset( $meta_inputs['_gs_land'] );
                if ( isset($meta_inputs['_gs_cell']) ) unset( $meta_inputs['_gs_cell'] );
                if ( isset($meta_inputs['_gs_email']) ) unset( $meta_inputs['_gs_email'] );
                if ( isset($meta_inputs['_gs_address']) ) unset( $meta_inputs['_gs_address'] );
                if ( isset($meta_inputs['_gs_ribon']) ) unset( $meta_inputs['_gs_ribon'] );
                if ( isset($meta_inputs['gs_skill']) ) unset( $meta_inputs['gs_skill'] );
                if ( isset($meta_inputs['second_featured_img']) ) unset( $meta_inputs['second_featured_img'] );

            }

            $meta_inputs['_thumbnail_id'] = $this->get_attachment_id_by_filename( $meta_inputs['_thumbnail_id'] );
            if ( $this->is_pro )  $meta_inputs['second_featured_img'] = $this->get_attachment_id_by_filename( $meta_inputs['second_featured_img'] );

            return $meta_inputs;

        }

        // Members
        public function create_dummy_members() {

            do_action( 'gsteam_dummy_members_process_start' );

            $post_status = 'publish';
            $post_type = 'gs_team';

            $members = [];

            $members[] = array(
                'post_title'    => 'William Dean',
                'post_content'  => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed lectus. Aenean massa. Phasellus gravida semper nisi. Praesent metus tellus, elementum eu, semper a, adipiscing nec, purus. Vestibulum turpis sem, aliquet eget, lobortis pellentesque, rutrum eu, nisl. Quisque libero metus, condimentum nec, tempor a, commodo mollis, magna. Cras varius. Suspendisse nisl elit, rhoncus eget, elementum ac, condimentum eget, diam. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Donec vitae orci sed dolor rutrum auctor. Aenean vulputate eleifend tellus. Vestibulum suscipit nulla quis orci. Integer tincidunt.',
                'post_status'   => $post_status,
                'post_type' => $post_type,
                'post_date' => '2020-08-10 07:01:44',
                'tax_input' => $this->get_tax_inputs([
                    'team_group' => ['group-one', 'group-three'],
                    'team_language' => ['english'],
                    'team_location' => ['paris'],
                    'team_gender' => ['male'],
                    'team_specialty' => ['web-development', 'networking']
                ]),
                'meta_input' => $this->get_meta_inputs([
                    '_thumbnail_id' => 'gsteam-member-1',
                    '_gs_des' => 'Product Manager',
                    '_gs_com' => 'Sanira Inc',
                    '_gs_land' => '406-324-6585',
                    '_gs_cell' => '619-770-9056',
                    '_gs_email' => 'WilliamMDean@dummy.com',
                    '_gs_address' => '1158 Hartland Avenue, Fond Du Lac, WI 54935',
                    '_gs_ribon' => 'Rising Star',
                    'second_featured_img' => 'gsteam-member-flip-1',
                    'gs_social' => [
                        ['icon' => 'twitter', 'link' => 'https://twitter.com/WilliamMDean'],
                        ['icon' => 'google-plus', 'link' => 'https://google.com/WilliamMDean'],
                        ['icon' => 'facebook', 'link' => 'https://facebook.com/WilliamMDean'],
                        ['icon' => 'linkedin', 'link' => 'https://linkedin.com/WilliamMDean'],
                    ],
                    'gs_skill' => [
                        ['skill' => 'Communication', 'percent' => 100],
                        ['skill' => 'Growth Process', 'percent' => 90],
                        ['skill' => 'Analysis', 'percent' => 95],
                    ],
                ])
            );

            $members[] = array(
                'post_title'    => 'Michael Dehaven',
                'post_content'  => 'Quisque rutrum. Nunc nec neque. Sed magna purus, fermentum eu, tincidunt eu, varius ut, felis. Etiam vitae tortor. Vivamus elementum semper nisi. Quisque rutrum. Nam adipiscing. Curabitur vestibulum aliquam leo. Praesent nec nisl a purus blandit viverra. Nam at tortor in tellus interdum sagittis. Nunc nonummy metus. Nam ipsum risus, rutrum vitae, vestibulum eu, molestie vel, lacus. In turpis. Donec vitae sapien ut libero venenatis faucibus. Quisque ut nisi.',
                'post_status'   => $post_status,
                'post_type' => $post_type,
                'post_date' => '2020-08-11 07:01:44',
                'tax_input' => $this->get_tax_inputs([
                    'team_group' => ['group-one', 'group-two'],
                    'team_language' => ['spanish'],
                    'team_location' => ['london'],
                    'team_gender' => ['female'],
                    'team_specialty' => ['graphic-design']
                ]),
                'meta_input' => $this->get_meta_inputs([
                    '_thumbnail_id' => 'gsteam-member-2',
                    '_gs_des' => 'Web Designer',
                    '_gs_com' => 'GsPlugins',
                    '_gs_land' => '301-346-3447',
                    '_gs_cell' => '719-382-2900',
                    '_gs_email' => 'MichaelDDehaven@clora.com',
                    '_gs_address' => '1394 Flanigan Oaks Drive, Washington, MD 20005',
                    '_gs_ribon' => 'Top Talent',
                    'second_featured_img' => 'gsteam-member-flip-2',
                    'gs_social' => [
                        ['icon' => 'twitter', 'link' => 'https://twitter.com/MichaelDDehaven'],
                        ['icon' => 'google-plus', 'link' => 'https://google.com/MichaelDDehaven'],
                        ['icon' => 'facebook', 'link' => 'https://facebook.com/MichaelDDehaven'],
                        ['icon' => 'linkedin', 'link' => 'https://linkedin.com/MichaelDDehaven'],
                    ],
                    'gs_skill' => [
                        ['skill' => 'Graphic Design', 'percent' => 95],
                        ['skill' => 'UI/UX Design', 'percent' => 100],
                        ['skill' => 'Design Tools', 'percent' => 95],
                    ],
                ])
            );

            $members[] = array(
                'post_title'    => 'Herman Willis',
                'post_content'  => 'Sed in libero ut nibh placerat accumsan. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Quisque libero metus, condimentum nec, tempor a, commodo mollis, magna. Nunc nec neque. Nulla sit amet est. Praesent nonummy mi in odio. Vestibulum suscipit nulla quis orci. Nunc egestas, augue at pellentesque laoreet, felis eros vehicula leo, at malesuada velit leo quis pede. Fusce vulputate eleifend sapien. Praesent egestas neque eu enim. Sed hendrerit. Praesent ac sem eget est egestas volutpat. Duis lobortis massa imperdiet quam. Etiam imperdiet imperdiet orci.',
                'post_status'   => $post_status,
                'post_type' => $post_type,
                'post_date' => '2020-08-12 07:01:44',
                'tax_input' => $this->get_tax_inputs([
                    'team_group' => ['group-three'],
                    'team_language' => ['spanish', 'french'],
                    'team_location' => ['rome', 'london'],
                    'team_gender' => ['male'],
                    'team_specialty' => ['networking']
                ]),
                'meta_input' => $this->get_meta_inputs([
                    '_thumbnail_id' => 'gsteam-member-3',
                    '_gs_des' => 'Network Manager',
                    '_gs_com' => 'Oracle Org',
                    '_gs_land' => '949-250-0110',
                    '_gs_cell' => '646-281-3348',
                    '_gs_email' => 'HermanEWillis@teleworm.us',
                    '_gs_address' => '4970 University Drive, Chicago, IL 60606',
                    '_gs_ribon' => 'Best Employee',
                    'second_featured_img' => 'gsteam-member-flip-3',
                    'gs_social' => [
                        ['icon' => 'twitter', 'link' => 'https://twitter.com/HermanEWillis'],
                        ['icon' => 'google-plus', 'link' => 'https://google.com/HermanEWillis'],
                        ['icon' => 'facebook', 'link' => 'https://facebook.com/HermanEWillis'],
                        ['icon' => 'linkedin', 'link' => 'https://linkedin.com/HermanEWillis'],
                    ],
                    'gs_skill' => [
                        ['skill' => 'Empathy', 'percent' => 100],
                        ['skill' => 'Social Skills', 'percent' => 80],
                        ['skill' => 'Active Listening', 'percent' => 85],
                    ]
                ])
            );

            $members[] = array(
                'post_title'    => 'Joseph Barren',
                'post_content'  => 'Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nam eget dui. Vivamus in erat ut urna cursus vestibulum. Duis leo. Donec venenatis vulputate lorem. Pellentesque commodo eros a enim. Nullam quis ante. Proin magna. Quisque rutrum. Pellentesque auctor neque nec urna. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi. Aliquam erat volutpat. Vestibulum dapibus nunc ac augue. Phasellus gravida semper nisi. Nunc nec neque.',
                'post_status'   => $post_status,
                'post_type' => $post_type,
                'post_date' => '2020-08-13 07:01:44',
                'tax_input' => $this->get_tax_inputs([
                    'team_group' => ['group-two'],
                    'team_language' => ['english', 'spanish'],
                    'team_location' => ['paris', 'rome'],
                    'team_gender' => ['female'],
                    'team_specialty' => ['web-development', 'networking']
                ]),
                'meta_input' => $this->get_meta_inputs([
                    '_thumbnail_id' => 'gsteam-member-4',
                    '_gs_des' => 'Senior Developer',
                    '_gs_com' => 'Heartana',
                    '_gs_land' => '785-416-8903',
                    '_gs_cell' => '212-694-2286',
                    '_gs_email' => 'JosephPBarren@rhyta.com',
                    '_gs_address' => '4614 Birch Street, Indianapolis, IN 46229',
                    '_gs_ribon' => 'Top Talent',
                    'second_featured_img' => 'gsteam-member-flip-4',
                    'gs_social' => [
                        ['icon' => 'twitter', 'link' => 'https://twitter.com/JosephPBarren'],
                        ['icon' => 'google-plus', 'link' => 'https://google.com/JosephPBarren'],
                        ['icon' => 'facebook', 'link' => 'https://facebook.com/JosephPBarren'],
                        ['icon' => 'linkedin', 'link' => 'https://linkedin.com/JosephPBarren'],
                    ],
                    'gs_skill' => [
                        ['skill' => 'FrontEnd Development', 'percent' => 100],
                        ['skill' => 'BackEnd Development', 'percent' => 95],
                        ['skill' => 'Server Management', 'percent' => 90],
                    ],
                ])
            );

            $members[] = array(
                'post_title'    => 'Sidney Buckley',
                'post_content'  => 'Nullam cursus lacinia erat. Pellentesque egestas, neque sit amet convallis pulvinar, justo nulla eleifend augue, ac auctor orci leo non est. In hac habitasse platea dictumst. Phasellus magna. Fusce neque. Pellentesque auctor neque nec urna. Curabitur a felis in nunc fringilla tristique. Nam pretium turpis et arcu. Nam pretium turpis et arcu. Etiam ut purus mattis mauris sodales aliquam. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce id purus. Nulla sit amet est. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.',
                'post_status'   => $post_status,
                'post_type' => $post_type,
                'post_date' => '2020-08-14 07:01:44',
                'tax_input' => $this->get_tax_inputs([
                    'team_group' => ['group-two', 'group-three'],
                    'team_language' => ['french'],
                    'team_location' => ['rome'],
                    'team_gender' => ['male'],
                    'team_specialty' => ['graphic-design', 'web-development', 'networking']
                ]),
                'meta_input' => $this->get_meta_inputs([
                    '_thumbnail_id' => 'gsteam-member-5',
                    '_gs_des' => 'Product Architecture',
                    '_gs_com' => 'Modera',
                    '_gs_land' => '952-855-3834',
                    '_gs_cell' => '865-635-1895',
                    '_gs_email' => 'SidneyMBuckley@armyspy.com',
                    '_gs_address' => '2589 Cheshire Road, Stamford, CT 06901',
                    '_gs_ribon' => 'Rising Star',
                    'second_featured_img' => 'gsteam-member-flip-5',
                    'gs_social' => [
                        ['icon' => 'twitter', 'link' => 'https://twitter.com/SidneyMBuckley'],
                        ['icon' => 'google-plus', 'link' => 'https://google.com/SidneyMBuckley'],
                        ['icon' => 'facebook', 'link' => 'https://facebook.com/SidneyMBuckley'],
                        ['icon' => 'linkedin', 'link' => 'https://linkedin.com/SidneyMBuckley'],
                    ],
                    'gs_skill' => [
                        ['skill' => 'Product Design', 'percent' => 95],
                        ['skill' => 'Competitor Analysis', 'percent' => 100],
                        ['skill' => 'Product Interaction', 'percent' => 95],
                    ],
                ])
            );

            $members[] = array(
                'post_title'    => 'Dante Hicks',
                'post_content'  => 'Donec interdum, metus et hendrerit aliquet, dolor diam sagittis ligula, eget egestas libero turpis vel mi. Phasellus magna. Curabitur turpis. Nullam tincidunt adipiscing enim. Nunc egestas, augue at pellentesque laoreet, felis eros vehicula leo, at malesuada velit leo quis pede. Mauris sollicitudin fermentum libero. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Aenean tellus metus, bibendum sed, posuere ac, mattis non, nunc. Praesent egestas neque eu enim. Aenean imperdiet. Vivamus aliquet elit ac nisl. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. Etiam ut purus mattis mauris sodales aliquam. Praesent venenatis metus at tortor pulvinar varius.',
                'post_status'   => $post_status,
                'post_type' => $post_type,
                'post_date' => '2020-08-15 07:01:44',
                'tax_input' => $this->get_tax_inputs([
                    'team_group' => ['group-three'],
                    'team_language' => ['english', 'french'],
                    'team_location' => ['rome', 'london'],
                    'team_gender' => ['female'],
                    'team_specialty' => ['graphic-design', 'networking']
                ]),
                'meta_input' => $this->get_meta_inputs([
                    '_thumbnail_id' => 'gsteam-member-6',
                    '_gs_des' => 'Graphics Designer',
                    '_gs_com' => 'Coream Inc',
                    '_gs_land' => '419-255-5857',
                    '_gs_cell' => '507-513-6174',
                    '_gs_email' => 'DanteKHicks@coream.com',
                    '_gs_address' => '251 Smith Street, Taunton, MA 02780',
                    '_gs_ribon' => 'Top Rated',
                    'second_featured_img' => 'gsteam-member-flip-6',
                    'gs_social' => [
                        ['icon' => 'twitter', 'link' => 'https://twitter.com/DanteKHicks'],
                        ['icon' => 'google-plus', 'link' => 'https://google.com/DanteKHicks'],
                        ['icon' => 'facebook', 'link' => 'https://facebook.com/DanteKHicks'],
                        ['icon' => 'linkedin', 'link' => 'https://linkedin.com/DanteKHicks'],
                    ],
                    'gs_skill' => [
                        ['skill' => 'Cartoon Design', 'percent' => 85],
                        ['skill' => 'Product Mockup', 'percent' => 100],
                        ['skill' => 'Graphic Elements', 'percent' => 95],
                    ],
                ])
            );

            foreach ( $members as $member ) {
                // Insert the post into the database
                $post_id = wp_insert_post( $member );
                // Add meta value for demo
                if ( $post_id ) add_post_meta( $post_id, 'gsteam-demo_data', 1 );
            }

            do_action( 'gsteam_dummy_members_process_finished' );

        }

        public function delete_dummy_members() {
            
            $members = $this->get_dummy_members();

            if ( empty($members) ) return;

            foreach ($members as $member) {
                wp_delete_post( $member->ID, true );
            }

            delete_transient( 'gsteam_dummy_members' );

        }

        public function get_dummy_members() {

            $members = get_transient( 'gsteam_dummy_members' );

            if ( false !== $members ) return $members;

            $members = get_posts( array(
                'numberposts' => -1,
                'post_type'   => 'gs_team',
                'meta_key' => 'gsteam-demo_data',
                'meta_value' => 1,
            ));
            
            if ( is_wp_error($members) || empty($members) ) {
                delete_transient( 'gsteam_dummy_members' );
                return [];
            }
            
            set_transient( 'gsteam_dummy_members', $members, 3 * MINUTE_IN_SECONDS );

            return $members;

        }

        public function http_request_args( $args ) {
            
            $args['sslverify'] = false;

            return $args;

        }

        // Attachments
        public function create_dummy_attachments() {

            do_action( 'gsteam_dummy_attachments_process_start' );

            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            $attachment_files = [
                'gsteam-member-1.jpg',
                'gsteam-member-2.jpg',
                'gsteam-member-3.jpg',
                'gsteam-member-4.jpg',
                'gsteam-member-5.jpg',
                'gsteam-member-6.jpg',
            ];

            if ( $this->is_pro ) {

                $attachment_files = array_merge( $attachment_files, [
                    'gsteam-member-flip-1.jpg',
                    'gsteam-member-flip-2.jpg',
                    'gsteam-member-flip-3.jpg',
                    'gsteam-member-flip-4.jpg',
                    'gsteam-member-flip-5.jpg',
                    'gsteam-member-flip-6.jpg'
                ]);

            }

            add_filter( 'http_request_args', [ $this, 'http_request_args' ] );

            wp_raise_memory_limit( 'image' );

            foreach ( $attachment_files as $file ) {

                $file = GSTEAM_PLUGIN_URI . '/assets/img/dummy-data/' . $file;

                $filename = basename($file);

                $get = wp_remote_get( $file );
                $type = wp_remote_retrieve_header( $get, 'content-type' );
                $mirror = wp_upload_bits( $filename, null, wp_remote_retrieve_body( $get ) );
                
                // Prepare an array of post data for the attachment.
                $attachment = array(
                    'guid'           => $mirror['url'],
                    'post_mime_type' => $type,
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );
                
                // Insert the attachment.
                $attach_id = wp_insert_attachment( $attachment, $mirror['file'] );
                
                // Generate the metadata for the attachment, and update the database record.
                $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                add_post_meta( $attach_id, 'gsteam-demo_data', 1 );

            }

            remove_filter( 'http_request_args', [ $this, 'http_request_args' ] );

            do_action( 'gsteam_dummy_attachments_process_finished' );

        }

        public function delete_dummy_attachments() {
            
            $attachments = $this->get_dummy_attachments();

            if ( empty($attachments) ) return;

            foreach ($attachments as $attachment) {
                wp_delete_attachment( $attachment->ID, true );
            }

            delete_transient( 'gsteam_dummy_attachments' );

        }

        public function get_dummy_attachments() {

            $attachments = get_transient( 'gsteam_dummy_attachments' );

            if ( false !== $attachments ) return $attachments;

            $attachments = get_posts( array(
                'numberposts' => -1,
                'post_type'   => 'attachment',
                'post_status' => 'inherit',
                'meta_key' => 'gsteam-demo_data',
                'meta_value' => 1,
            ));
            
            if ( is_wp_error($attachments) || empty($attachments) ) {
                delete_transient( 'gsteam_dummy_attachments' );
                return [];
            }
            
            set_transient( 'gsteam_dummy_attachments', $attachments, 3 * MINUTE_IN_SECONDS );

            return $attachments;

        }
        
        // Terms
        public function create_dummy_terms() {

            do_action( 'gsteam_dummy_terms_process_start' );
            
            $terms = [
                // 3 Groups
                [
                    'name' => 'Group One',
                    'slug' => 'group-one',
                    'group' => 'team_group'
                ],
                [
                    'name' => 'Group Two',
                    'slug' => 'group-two',
                    'group' => 'team_group'
                ],
                [
                    'name' => 'Group Three',
                    'slug' => 'group-three',
                    'group' => 'team_group'
                ]
            ];

            if ( $this->is_pro ) {

                $pro_terms = [
                    // 3 Language
                    [
                        'name' => 'English',
                        'slug' => 'english',
                        'group' => 'team_language'
                    ],
                    [
                        'name' => 'Spanish',
                        'slug' => 'spanish',
                        'group' => 'team_language'
                    ],
                    [
                        'name' => 'French',
                        'slug' => 'french',
                        'group' => 'team_language'
                    ],
    
                    // 3 Location
                    [
                        'name' => 'Paris',
                        'slug' => 'paris',
                        'group' => 'team_location'
                    ],
                    [
                        'name' => 'Rome',
                        'slug' => 'rome',
                        'group' => 'team_location'
                    ],
                    [
                        'name' => 'London',
                        'slug' => 'london',
                        'group' => 'team_location'
                    ],
    
                    // 2 Gender
                    [
                        'name' => 'Male',
                        'slug' => 'male',
                        'group' => 'team_gender'
                    ],
                    [
                        'name' => 'Female',
                        'slug' => 'female',
                        'group' => 'team_gender'
                    ],
    
                    // 3 Speciality
                    [
                        'name' => 'Graphic Design',
                        'slug' => 'graphic-design',
                        'group' => 'team_specialty'
                    ],
                    [
                        'name' => 'Web Development',
                        'slug' => 'web-development',
                        'group' => 'team_specialty'
                    ],
                    [
                        'name' => 'Networking',
                        'slug' => 'networking',
                        'group' => 'team_specialty'
                    ]
                ];

                $terms = array_merge( $terms, $pro_terms );

            }


            foreach( $terms as $term ) {

                $response = wp_insert_term( $term['name'], $term['group'], array('slug' => $term['slug']) );
    
                if ( ! is_wp_error($response) ) {
                    add_term_meta( $response['term_id'], 'gsteam-demo_data', 1 );
                }

            }

            do_action( 'gsteam_dummy_terms_process_finished' );

        }
        
        public function delete_dummy_terms() {
            
            $terms = $this->get_dummy_terms();

            if ( empty($terms) ) return;
    
            foreach ( $terms as $term ) {
                wp_delete_term( $term['term_id'], $term['taxonomy'] );
            }

            delete_transient( 'gsteam_dummy_terms' );

        }

        public function get_dummy_terms() {

            $terms = get_transient( 'gsteam_dummy_terms' );

            if ( false !== $terms ) return $terms;

            $taxonomies = $this->get_taxonomy_list();

            $terms = get_terms( array(
                'taxonomy' => $taxonomies,
                'hide_empty' => false,
                'meta_key' => 'gsteam-demo_data',
                'meta_value' => 1,
            ));

            $terms = json_decode( json_encode( $terms ), true ); // Object to Array
            
            if ( is_wp_error($terms) || empty($terms) ) {
                delete_transient( 'gsteam_dummy_terms' );
                return [];
            }

            set_transient( 'gsteam_dummy_terms', $terms, 3 * MINUTE_IN_SECONDS );

            return $terms;

        }

        // Shortcode
        public function create_dummy_shortcodes() {

            do_action( 'gsteam_dummy_shortcodes_process_start' );

            $GS_Team_Shortcode_Builder = GS_Team_Shortcode_Builder::get_instance();
            $GS_Team_Shortcode_Builder->create_dummy_shortcodes();

            do_action( 'gsteam_dummy_shortcodes_process_finished' );

        }

        public function delete_dummy_shortcodes() {
            
            $GS_Team_Shortcode_Builder = GS_Team_Shortcode_Builder::get_instance();
            $GS_Team_Shortcode_Builder->delete_dummy_shortcodes();

        }

        // Notice
        function gsteam_dummy_data_admin_notice() {

            // delete_option('gsteam_dismiss_demo_data_notice');

            if ( get_option('gsteam_dismiss_demo_data_notice') ) return;

            if ( get_current_screen()->id == 'gs_team_page_gs-team-shortcode' ) return;

            ?>
            <div id="gsteam-dummy-data-install--notice" class="notice notice-success is-dismissible">

                <h3>GS Team - Install Demo Data!</h3>

                <p><b>Gs Team</b> plugin offers to install <b>demo data</b> with just one click.</p>
                <p>You can remove the data anytime if you want by another click.</p>

                <p style="margin-top: 16px; margin-bottom: 18px;">

                    <a href="<?php echo admin_url( 'edit.php?post_type=gs_team&page=gs-team-shortcode#/demo-data' ); ?>" class="button button-primary" style="margin-right: 10px;">Install Demo Data</a>

                    <a href="javascript:void(0)" onclick="jQuery('#gsteam-dummy-data-install--notice').slideUp(); jQuery.post(ajaxurl, {action: 'gsteam_dismiss_demo_data_notice', nonce: '<?php echo wp_create_nonce('_gsteam_dismiss_demo_data_notice_gs_'); ?>' });">
                        <?php _e( "Don't show this message again", 'gsteam'); ?>
                    </a>

                </p>

            </div>
            <?php

        }

        function gsteam_dismiss_demo_data_notice() {

            $nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : null;

            if ( ! wp_verify_nonce( $nonce, '_gsteam_dismiss_demo_data_notice_gs_') ) {

                wp_send_json_error( __('Unauthorised Request', 'gsteam'), 401 );

            }

            update_option( 'gsteam_dismiss_demo_data_notice', 1 );

        }

    }

}

GS_Team_Dummy_Data::get_instance();