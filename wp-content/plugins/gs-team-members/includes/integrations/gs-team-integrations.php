<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Integration' ) ) {

    final class GS_Team_Integration {

        private static $_instance = null;
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Integration();
            }

            return self::$_instance;
            
        }

        public function __construct() {

            // Elementor
            if ( apply_filters( 'gs_team_integration_elementor', true ) ) $this->integration_with_elementor();

            // WP Bakery Visual Composer
            if ( apply_filters( 'gs_team_integration_wpb_vc', true ) ) $this->integration_with_wpbakery_vc();

            // Gutenberg
            if ( apply_filters( 'gs_team_integration_gutenberg', true ) ) $this->integration_with_gutenberg();

            // Divi
            if ( apply_filters( 'gs_team_integration_divi', true ) ) $this->integration_with_divi();
            
        }

        public function integration_with_elementor() {

            require_once GSTEAM_PLUGIN_DIR . 'includes/integrations/gs-team-integration-elementor.php';

            GS_Team_Integration_Elementor::get_instance();

        }
        
        public function integration_with_wpbakery_vc() {

            require_once GSTEAM_PLUGIN_DIR . 'includes/integrations/gs-team-integration-wpb-vc.php';

            GS_Team_Integration_WPB_VC::get_instance();

        }

        public function integration_with_gutenberg() {

            require_once GSTEAM_PLUGIN_DIR . 'includes/integrations/gs-team-integration-gutenberg.php';

            GS_Team_Integration_Gutenberg::get_instance();

        }

        public function integration_with_divi() {

            require_once GSTEAM_PLUGIN_DIR . 'includes/integrations/gs-team-integration-divi.php';

            GS_Team_Integration_Divi::get_instance();

        }

    }

}

GS_Team_Integration::get_instance();