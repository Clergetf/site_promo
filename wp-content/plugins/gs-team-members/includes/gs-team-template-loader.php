<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Template_Loader' ) ) {

    final class GS_Team_Template_Loader {

        private static $template_path = '';

        private static $theme_path = '';

        private static $_instance = null;
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Team_Template_Loader();
            }

            return self::$_instance;
            
        }

        public function __construct() {

            self::$template_path = GSTEAM_PLUGIN_DIR . 'templates/';

            add_action( 'init', [$this, 'set_theme_template_path'] );

        }

        public function set_theme_template_path() {

            $path = apply_filters( 'gsteam_templates_folder', 'gs-team' );

            if ( $path ) {
                $path = '/' . trailingslashit( ltrim( $path, '/\\' ) );
                self::$theme_path = get_stylesheet_directory() . $path;
            }

        }

        public static function locate_template( $template_file ) {

            // Default path
            $path = self::$template_path;

            // Check requested file exist
            if ( ! file_exists( $path . $template_file ) ) return new WP_Error( 'gsteam_template_not_found', __( 'Template file not found - GsPlugins', 'gsteam' ) );

            // Override default template if exist from theme
            if ( file_exists( self::$theme_path . $template_file ) ) $path = self::$theme_path;

            // Return template path, it can be default or overridden by theme
            return $path . $template_file;

        }

    }

}

GS_Team_Template_Loader::get_instance();