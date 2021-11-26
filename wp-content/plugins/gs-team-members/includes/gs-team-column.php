<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

// ============== Displaying Additional Columns ===============
if ( ! function_exists('gs_team_screen_columns') ) {

    function gs_team_screen_columns( $columns ) {

        unset( $columns['date'] );
        unset( $columns['taxonomy-team_group'] );

        $columns['title'] = __( 'Member Name', 'gsteam' );
        $columns['gsteam_featured_image'] = __( 'Member Image', 'gsteam' );
        $columns['_gs_des'] = __( 'Designation', 'gsteam' );
        $columns['taxonomy-team_group'] = __( 'Team Group', 'gsteam' );
        $columns['date'] = __( 'Date', 'gsteam' );

        return $columns;

    }

}
add_filter( 'manage_edit-gs_team_columns', 'gs_team_screen_columns' );

// GET FEATURED IMAGE
if ( ! function_exists('gs_team_featured_image') ) {

    function gs_team_featured_image( $post_ID ) {

        $post_thumbnail_id = get_post_thumbnail_id( $post_ID );

        if ( $post_thumbnail_id ) {
            $post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id );
            return $post_thumbnail_img[0];
        }

    }

}

// SHOW THE FEATURED IMAGE
if ( ! function_exists('gs_team_columns_content') ) {
    function gs_team_columns_content($column_name, $post_ID) {
        if ( $column_name == 'gsteam_featured_image' ) {
            $post_featured_image = gs_team_featured_image( $post_ID );
            if ( $post_featured_image ) {
                echo '<img src="' . $post_featured_image . '" width="34"/>';
            }
        }
    }
}
add_action( 'manage_posts_custom_column', 'gs_team_columns_content', 10, 2 );

// Populating the Columns
if ( ! function_exists('gs_team_populate_columns') ) {

    function gs_team_populate_columns( $column ) {
        if ( '_gs_des' == $column ) {
            $tm_m_desig = get_post_meta( get_the_ID(), '_gs_des', true );
            echo $tm_m_desig;
        }
    }

}
add_action( 'manage_posts_custom_column', 'gs_team_populate_columns' );

// Columns as Sortable
if ( ! function_exists('gs_team_sort') ) {

    function gs_team_sort( $columns ) {
        $columns['taxonomy-team_group'] = 'taxonomy-team_group';
        return $columns;
    }

}
add_filter( 'manage_edit-gs_team_sortable_columns', 'gs_team_sort' );