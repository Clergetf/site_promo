<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

add_shortcode( 'gsteam', 'register_gsteam_shortcode_builder' );

function gsteam_get_temp_settings( $id, $is_preview = false ) {

    if ( $is_preview ) return get_transient( $id );

    $gsteam_sb = GS_Team_Shortcode_Builder::get_instance();

    $shortcode = $gsteam_sb->_get_shortcode( $id, false );

    if ( $shortcode ) return $shortcode['shortcode_settings'];

    return [];
    
}

function gsteam_get_shortcode_params( $settings ) {

    $params = [];

    foreach( $settings as $key => $val ) {
        $params[] = $key.'="'.$val.'"';
    }

    return implode( ' ', $params );

}

function register_gsteam_shortcode_builder( $atts ) {

    if ( empty($atts['id']) ) {
        return __( 'No shortcode ID found', 'gsteam' );
    }

    $is_preview = ! empty($atts['preview']);

    $settings = (array) gsteam_get_temp_settings( $atts['id'], $is_preview );

    $settings['id'] = $atts['id'];
    $settings['is_preview'] = $is_preview;

    $shortcode_params = gsteam_get_shortcode_params( $settings );

    return do_shortcode("[gs_team $shortcode_params]");

}