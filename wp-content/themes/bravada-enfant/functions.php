<?php
require_once( get_stylesheet_directory(). '/includes/landing-page.php' );
function wpm_enqueue_styles(){
wp_enqueue_style( 'bravada', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'wpm_enqueue_styles' );

function my_custom_scripts() {
    wp_enqueue_script( 'bravada', get_stylesheet_directory_uri() . '/script.js');
}
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );
