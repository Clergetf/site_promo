<?php
/**
 * GS Team - Layout Navigation
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/partials/gs-team-layout-navigation.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.0
 */

do_action( 'gs_team_before_navigation' );

if ( 'on' ==  $gs_member_nxt_prev ) : ?>
    
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="prev-next-navigation">
            <?php previous_post_link( '<div class="previous">%link</div>', '%title' );  ?>
            <?php next_post_link( '<div class="next">%link</div>', '%title' );  ?>
        </div>
    </div>

<?php endif;

do_action( 'gs_team_after_navigation' );