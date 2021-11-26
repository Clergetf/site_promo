<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

function gs_team_free_vs_pro_page() {

	add_submenu_page(
	    'edit.php?post_type=gs_team',
	    'Free Pro Trial',
	    'Free Pro Trial',
	    'delete_posts',
	    gtm_fs()->get_trial_url()
	);

}

add_action( 'admin_menu', 'gs_team_free_vs_pro_page', 20 );