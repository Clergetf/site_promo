<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

// Integration Class
if ( ! class_exists( 'GS_Team_Integration_Divi' ) ) :

    class GS_Team_Integration_Divi {

        private static $_instance = null;
        private $name;
        private $plugin_dir_url;
        protected $_bundle_dependencies = array();
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Integration_Divi();
            }

            return self::$_instance;
            
        }

        public function __construct() {

            add_action( 'divi_extensions_init', array( $this, 'init' ) );
            
        }

        public function init() {

            $this->name = 'gs-team-divi';
            $this->plugin_dir_url = GSTEAM_PLUGIN_URI . '/includes/integrations/assets/divi';

            add_action( 'et_builder_modules_loaded', 'gs_team_divi_widget_class' );
            add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
            add_action( 'wp_head', array( $this, 'editor_style' ) );

        }


        public function editor_style() {

            if ( ! et_core_is_fb_enabled() ) return;

            $icon = GSTEAM_PLUGIN_URI . '/assets/img/icon.svg';

            ob_start();

            ?>
            <style>

                .et-db #et-boc .et-l .et-fb-modules-list ul > li.gs_team_members:before {
                    background: url('<?php echo $icon; ?>') no-repeat center center;
                    background-size: contain;
                    content: "";
                    height: 28px;
                }
                
                .et-db #et-boc .et-l .et-fb-modules-list ul > li.gs_team_members {
                    height: 67px;
                }

            </style>
            <?php

            echo ob_get_clean();

        }

        public function wp_hook_enqueue_scripts() {

            if ( et_core_is_fb_enabled() ) {

                // Load Styles
                GS_Team_Scripts::get_instance()->wp_enqueue_style_all( 'public' );

                // Load Scripts
                GS_Team_Scripts::get_instance()->wp_enqueue_script_all( 'public' );

                $bundle_url   = "{$this->plugin_dir_url}/gs-team-divi-builder.min.js";
                wp_enqueue_script( "{$this->name}-builder", $bundle_url, ['react-dom'], GSTEAM_VERSION, true );

            }

            $bundle_url   = "{$this->plugin_dir_url}/gs-team-divi-frontend.min.js";
            wp_enqueue_script( "{$this->name}-frontend", $bundle_url, ['jquery'], GSTEAM_VERSION, true );

        }
    
    }
    
endif;

if ( ! function_exists('gs_team_divi_widget_class') ) {

    function gs_team_divi_widget_class() {
    
        // Elementor Widget Class
        if ( ! class_exists( 'GS_Team_Divi_Widget' ) ) :
    
            class GS_Team_Divi_Widget extends ET_Builder_Module {
    
                public $slug       = 'gs_team_members';
                public $vb_support = 'on';
            
                public function init() {
                    $this->name = esc_html__( 'GS Team Members', 'gsteam' );
                }
            
                public function get_fields() {
            
                    return array(
                        'shortcode'     => array(
                            'label'           => esc_html__( 'Select Shortcode', 'gsteam' ),
                            'type'            => 'select',
                            'option_category' => 'basic_option',
                            'description'     => esc_html__( 'Show Team Members by GS Team Plugin', 'gsteam' ),
                            'toggle_slug'     => 'main_content',
                            'default'         => $this->get_default_item(),
                            'options'         => $this->get_shortcode_list(),
                            'computed_affects'   => array(
                                '__shortcode',
                            ),
                        ),
                        '__shortcode' => array(
                            'type'                => 'computed',
                            'computed_callback'   => array( 'GS_Team_Divi_Widget', 'get_shortcode' ),
                            'computed_depends_on' => array(
                                'shortcode',
                            ),
                            'computed_minimum' => array(
                                'shortcode',
                            ),
                        )
                    );
            
                }
            
                static function get_shortcode( $args ) {
            
                    $defaults = array(
                        'shortcode' => ''
                    );
            
                    $args = wp_parse_args( $args, $defaults );
            
                    return do_shortcode( sprintf( '[gsteam id="%s" /]', $args['shortcode'] ) );
            
                }
            
                public function render( $unprocessed_props, $content = null, $render_slug ) {
                    
                    $shortcode_id = $this->props['shortcode'];
            
                    $output = sprintf(
                        '<div id="%2$s" class="%3$s">
                            %1$s
                        </div>',
                        self::get_shortcode([
                            'shortcode' => $shortcode_id
                        ]),
                        $this->module_id(),
                        $this->module_classname( $render_slug )
                    );
            
                    // Enqueue Styles - Only load on non builder pages
                    wp_enqueue_style( 'gs-team-divi-public' );
                    
                    // Enqueue Scripts - Only load on non builder pages
                    wp_enqueue_script( 'gs-team-public' );
            
                    return $output;
            
                }
            
                protected function get_shortcode_list() {
                
                    $shortcodes = gs_team_get_shortcodes();
            
                    if ( !empty($shortcodes) ) {
                        return wp_list_pluck( $shortcodes, 'shortcode_name', 'id' );
                    }
                    
                    return [];
            
                }
            
                protected function get_default_item() {
            
                    $shortcodes = gs_team_get_shortcodes();
            
                    if ( !empty($shortcodes) ) {
                        return $shortcodes[0]['id'];
                    }
            
                    return '';
            
                }
            
            }
        
        endif;
    
        new GS_Team_Divi_Widget();
    
    }

}