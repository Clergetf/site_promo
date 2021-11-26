<?php

if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

function gs_team_disable_admin_notices() {

    global $parent_file;

    if ( $parent_file != 'edit.php?post_type=gs_team' ) return;

    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');

}

add_action( 'in_admin_header', 'gs_team_disable_admin_notices', 1000 );