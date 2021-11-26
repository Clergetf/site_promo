<?php
/**
 * GS Team - Layout Popup
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/partials/gs-team-layout-popup.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.2
 */

gs_team_load_acf_fields( $show_acf_fields, $acf_fields_position );

?>

<div id="gs_team_popup_<?php echo get_the_id(); ?>" class="gs_team_popup_shortcode_<?php echo $id; ?> white-popup mfp-hide mfp-with-anim gs_team_popup">
    <div class="mfp-content--container">
        <?php if ( $gs_teammembers_pop_clm == 'one' ) : ?>

            <div class="gs_team_popup_details gs-tm-sicons popup-one-column">
                
                <!-- Team Image -->
                <div class="clearfix">
                    <?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
                </div>
                <?php do_action( 'gs_team_after_member_thumbnail_popup' ); ?>

                <!-- Member Name -->
                <?php if ( 'on' ==  $gs_member_name ): ?>
                    <?php gs_team_member_name( true, $gs_member_name_is_linked == 'on' ); ?>
                    <?php do_action( 'gs_team_after_member_name' ); ?>
                <?php endif; ?>

                <!-- Member Designation -->
                <?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
                    <div class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
                    <?php do_action( 'gs_team_after_member_designation' ); ?>
                <?php endif; ?>

                <!-- Social Links -->
                <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>

                <!-- Description -->
                <?php if ( 'on' ==  $gs_member_details ) : ?>
                    <div class="gs-member-desc" itemprop="description"><?php echo wpautop(gs_team_member_description( 0, false, false )); ?></div>
                    <?php do_action( 'gs_team_after_member_details' ); ?>
                <?php endif; ?>
                
                <!-- Meta Details -->
                <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup-details.php' ); ?>

                <!-- Skills -->
                <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-skills.php' ); ?>

            </div>

        <?php else: ?>

            <div class="gs_team_popup_img">
                <!-- Team Image -->
                <?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
                <?php do_action( 'gs_team_after_member_thumbnail_popup' ); ?>

                <!-- Meta Details -->
                <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup-details.php' ); ?>

            </div>

            <div class="gs_team_popup_details gs-tm-sicons">
                
                <!-- Single member name -->
                <?php if ( 'on' ==  $gs_member_name ): ?>
                    <?php gs_team_member_name( true, $gs_member_name_is_linked == 'on' ); ?>
                    <?php do_action( 'gs_team_after_member_name' ); ?>
                <?php endif; ?>

                <!-- Single member designation -->
                <?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
                    <div class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
                    <?php do_action( 'gs_team_after_member_designation' ); ?>
                <?php endif; ?>

                <!-- Social Links -->
                <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>

                <!-- Description -->
                <?php if ( 'on' ==  $gs_member_details ) : ?>
                    <div class="gs-member-desc" itemprop="description"><?php echo do_shortcode( wpautop( gs_team_member_description( 0, false, false ) ) ); ?></div>
                    <?php do_action( 'gs_team_after_member_details' ); ?>
                <?php endif; ?>

                <!-- Skills -->
                <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-skills.php' ); ?>

            </div>

        <?php endif; ?>
    </div>
</div>