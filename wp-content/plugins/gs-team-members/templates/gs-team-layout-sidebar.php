<?php
/**
 * GS Team - Layout Sidebar
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-sidebar.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.2
 */

global $gs_team_loop_side;

$gs_member_name = gs_team_getoption( 'gs_member_name', 'on' );
$gs_member_role = gs_team_getoption( 'gs_member_role', 'on' );
$gs_member_connect = gs_team_getoption( 'gs_member_connect', 'on' );
$gs_member_name_is_linked = gs_team_getoption( 'gs_member_name_is_linked', 'on' );

if ( $gs_team_loop_side->have_posts() ) : while ( $gs_team_loop_side->have_posts() ) : $gs_team_loop_side->the_post();

    $gs_team_id = get_post_thumbnail_id();
    $gs_team_url = wp_get_attachment_image_src($gs_team_id, 'full', true);
    $team_thumb = $gs_team_url[0];
    $gs_team_alt = get_post_meta($gs_team_id,'_wp_attachment_image_alt',true);
    $gs_member_desc_link = get_the_permalink();
    $gs_tm_meta = get_post_meta( get_the_id() );
    $designation = !empty($gs_tm_meta['_gs_des'][0]) ? $gs_tm_meta['_gs_des'][0] : '';
    $gs_social  = get_post_meta( get_the_id(), 'gs_social', true);

    ?>

    <div class="gs-team-widget--single-item" itemscope="" itemtype="http://schema.org/Person">

        <div class="gs-team-widget">

            <div class="gs-team-widget--member-image">
                
                <!-- Team Image -->
                <a class="gs_team_image__wrapper" href="<?php the_permalink(); ?>">
                    <?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
                </a>

            </div>

            <div class="gs-team-widget--member-info">

                <!-- Single member name -->
                <?php if ( 'on' ==  $gs_member_name ): ?>
                    <?php gs_team_member_name( true, $gs_member_name_is_linked == 'on' ); ?>
                <?php endif; ?>

                <!-- Single member designation -->
                <?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
                    <div class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
                <?php endif; ?>

                <!-- Social Links -->
                <div class="gs-team-table-cell gs-tm-sicons">
                    <?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>
                </div>

            </div>

        </div>

    </div>

<?php endwhile; else: ?>

    <!-- Members not found - Load no-team-member template -->
	<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-no-team-member.php' ); ?>

<?php endif; ?>