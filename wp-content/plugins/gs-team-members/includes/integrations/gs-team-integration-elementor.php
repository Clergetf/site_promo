<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

// Integration Class
if ( ! class_exists( 'GS_Team_Integration_Elementor' ) ) :

    class GS_Team_Integration_Elementor {

        private static $_instance = null;
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Integration_Elementor();
            }

            return self::$_instance;
            
        }

        public function __construct() {

            add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_elementor_widget' ] );
            add_action( 'elementor/elements/categories_registered', [$this, 'add_elementor_widget_category'] );
            
            add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'print_elementor_editor_scripts' ] );
            add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'print_elementor_editor_styles' ] );

            add_action( 'elementor/preview/enqueue_styles', [ $this, 'print_elementor_preview_styles' ] );
            add_action( 'elementor/preview/enqueue_scripts', [ $this, 'print_elementor_preview_scripts' ] );
            
        }
    
        public function register_elementor_widget() {

            gs_team_load_elementor_widget_class();
    
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new GS_Team_Elementor_Widget() );
    
        }
    
        public function add_elementor_widget_category( $elements_manager ) {
    
            $elements_manager->add_category(
                'gs-plugins',
                [
                    'title' => 'GS Plugins',
                    'icon' => 'fa fa-plug',
                ]
            );
        
        }
    
        public function print_elementor_editor_scripts() {
    
            ?>
            <script>
                
                window.onload = function() {
    
                    elementor.hooks.addAction( 'panel/open_editor/widget/gs-team-members', function( panel, model, view ) {
    
                        var $shortcode_field = jQuery('.elementor-control-gs_team_shortcode .elementor-control-input-wrapper select');
                        var $edit_link = jQuery('.elementor-control-gs_team_shortcode .gs-team-edit-link');
                        var shortcode_id = $shortcode_field.val();
                        var href = $edit_link.attr('href');
                        href = href.substring(0, href.indexOf('/shortcode/')+11);
    
                        $edit_link.attr( 'href', href + shortcode_id );
    
                        $shortcode_field.on('change', function() {
                            shortcode_id = jQuery(this).val();
                            $edit_link.attr( 'href', href + shortcode_id );
                        });
    
                    });
    
                }
    
            </script>
    
            <?php
    
        }
    
        public function print_elementor_editor_styles() {

            $icon = GSTEAM_PLUGIN_URI . '/assets/img/icon-colored.svg';
    
            ?>
    
            <style>

                body #elementor-controls .elementor-control-gs_team_shortcode .elementor-control-field-description {
                    font-size: 12px;
                    line-height: 1.8;
                }

                body #elementor-panel-elements-wrapper .icon .gs-team-members {
                    background: url('<?php echo $icon; ?>') no-repeat center center;
                    background-size: contain;
                    height: 29px;
                    display: block;
                }

            </style>
    
            <?php
    
        }

        public function print_elementor_preview_styles() {

            GS_Team_Scripts::get_instance()->wp_enqueue_style_all( 'public', ['gs-team-divi-public'] );

        }

        public function print_elementor_preview_scripts() {

            GS_Team_Scripts::get_instance()->wp_enqueue_script_all( 'public' );

            wp_enqueue_script( 'gs-team-elementor-preview-js', GSTEAM_PLUGIN_URI . '/includes/integrations/assets/elementor/gs-team-elementor-preview.min.js', ['jquery'], GSTEAM_VERSION, true );

        }
    
    }
    
endif;

function gs_team_load_elementor_widget_class() {

    // Elementor Widget Class
    if ( ! class_exists( 'GS_Team_Elementor_Widget' ) ) :
    
        class GS_Team_Elementor_Widget extends \Elementor\Widget_Base {
    
            public function get_name() {
                return 'gs-team-members';
            }
    
            public function get_title() {
                return __( 'GS Team Members', 'gsteam' );
            }
    
            public function get_icon() {
                return 'gs-team-members';
            }
    
            public function get_categories() {
                return [ 'gs-plugins', 'general' ];
            }
    
            protected function _register_controls() {
    
                $this->start_controls_section(
                    'content_section',
                    [
                        'label' => __( 'Content', 'gsteam' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]
                );
    
                $this->add_control(
                    'gs_team_shortcode',
                    [
                        'label' => __( 'Team Shortcode', 'gsteam' ),
                        'description' => $this->get_field_description(),
                        'label_block' => true,
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'options' => $this->get_shortcode_list(),
                        'default' => $this->get_default_item()
                    ]
                );
    
                $this->end_controls_section();
    
            }
    
            protected function get_field_description() {
    
                $eidt_link = sprintf( '%s: <a class="gs-team-edit-link" href="%s" target="_blank">%s</a>',
                    __('Edit this shortcode', 'gsteam'),
                    admin_url( "edit.php?post_type=gs_team&page=gs-team-shortcode#/shortcode/" ),
                    __('Edit', 'gsteam')
                );
    
                $create_link = sprintf( '%s: <a class="gs-team-create-link" href="%s" target="_blank">%s</a>',
                    __('Create new shortcode', 'gsteam'),
                    admin_url( 'edit.php?post_type=gs_team&page=gs-team-shortcode#/shortcode' ),
                    __('Craete', 'gsteam')
                );
    
                return implode( '<br />', [$eidt_link, $create_link] );
    
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
    
            protected function render() {
    
                $shortcode_id = $this->get_settings_for_display( 'gs_team_shortcode' );
    
                if ( empty($shortcode_id) ) return;
                
                echo do_shortcode( "[gsteam id={$shortcode_id}]" );
    
            }
    
        }
    
    endif;

}