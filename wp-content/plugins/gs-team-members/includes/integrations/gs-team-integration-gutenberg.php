<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Integration_Gutenberg' ) ) :

class GS_Team_Integration_Gutenberg {

	private static $_instance = null;
        
    public static function get_instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new GS_Team_Integration_Gutenberg();
        }

        return self::$_instance;
        
    }

    public function __construct() {

        add_action( 'init', [ $this, 'load_block_script' ] );

        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
        
    }

    public function enqueue_block_editor_assets() {

        GS_Team_Scripts::get_instance()->echo_gtm_fs_conditions();
		
		// Register Styles
		GS_Team_Scripts::get_instance()->wp_enqueue_style_all( 'public', ['gs-team-divi-public'] );
		
		// Register Scripts
        GS_Team_Scripts::get_instance()->wp_enqueue_script_all( 'public', ['gs-cpb-scroller'] );

    }

    public function load_block_script() {

        wp_add_inline_style( 'wp-block-editor', $this->get_block_css() );

        wp_register_script( 'gs-team-block', GSTEAM_PLUGIN_URI . '/includes/integrations/assets/gutenberg/gs-team-gutenberg-widget.min.js', ['wp-blocks', 'wp-editor'], GSTEAM_VERSION );

        $gs_team_block = array(
            'title' => __( 'GS Team Members', 'gsteam' ),
            'description' => __( 'Show Team Members by GS Team Plugin', 'gsteam' ),
            'select_shortcode' => __( 'GS Team Shortcode', 'gsteam' ),
            'edit_description_text' => __( 'Edit this shortcode', 'gsteam' ),
            'edit_link_text' => __( 'Edit', 'gsteam' ),
            'create_description_text' => __( 'Create new shortcode', 'gsteam' ),
            'create_link_text' => __( 'Create', 'gsteam' ),
            'edit_link' => admin_url( "edit.php?post_type=gs_team&page=gs-team-shortcode#/shortcode/" ),
            'create_link' => admin_url( 'edit.php?post_type=gs_team&page=gs-team-shortcode#/shortcode' ),
            'gs_team_shortcodes' => $this->get_shortcode_list()
		);
		wp_localize_script( 'gs-team-block', 'gs_team_block', $gs_team_block );

        register_block_type( 'gsteam/shortcodes', array(
            'editor_script' => 'gs-team-block',
            'attributes' => [
                'shortcode' => [
                    'type'    => 'string',
                    'default' => $this->get_default_item()
                ],
                'align' => [
                    'type'=> 'string',
                    'default'=> 'wide'
                ]
            ],
            'render_callback' => [$this, 'shortcodes_dynamic_render_callback']
        ) );

    }

    public function shortcodes_dynamic_render_callback( $block_attributes ) {

        $shortcode_id = ( ! empty($block_attributes) && ! empty($block_attributes['shortcode']) ) ? $block_attributes['shortcode'] : $this->get_default_item();

        return do_shortcode( sprintf( '[gsteam id="%u"]', $shortcode_id ) );

    }

    public function get_block_css() {

        ob_start(); ?>
    
        .gsteam-members--toolbar {
            padding: 20px;
            border: 1px solid #1f1f1f;
            border-radius: 2px;
        }

        .gsteam-members--toolbar label {
            display: block;
            margin-bottom: 6px;
            margin-top: -6px;
        }

        .gsteam-members--toolbar select {
            width: 250px;
            max-width: 100% !important;
            line-height: 42px !important;
        }

        .gsteam-members--toolbar .gs-team-block--des {
            margin: 10px 0 0;
            font-size: 16px;
        }

        .gsteam-members--toolbar .gs-team-block--des span {
            display: block;
        }

        .gsteam-members--toolbar p.gs-team-block--des a {
            margin-left: 4px;
        }
    
        <?php return ob_get_clean();
    
    }

    protected function get_shortcode_list() {

        return gs_team_get_shortcodes();

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