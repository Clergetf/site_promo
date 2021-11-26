<?php
/**
 * GS Team - Single Template 
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-single.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.2
 */

remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );

get_header(); ?>

<div class="gs-containeer gs-single-container">
	
	<div class="gs_team" id="gs_team_single">

		<?php while ( have_posts() ) : the_post();

		$designation 			= get_post_meta( get_the_id(), '_gs_des', true );
		$ribon 					= get_post_meta( get_the_id(), '_gs_ribon', true );
		$gs_member_nxt_prev 	= gs_team_getoption( 'gs_member_nxt_prev', 'on' );
		$gs_teamcom_meta 		= gs_team_get_translation( 'gs_teamcom_meta' );
		$gs_teamadd_meta 		= gs_team_get_translation( 'gs_teamadd_meta' );
		$gs_teamlandphone_meta 	= gs_team_get_translation( 'gs_teamlandphone_meta' );
		$gs_teamcellPhone_meta 	= gs_team_get_translation( 'gs_teamcellPhone_meta' );
		$gs_teamemail_meta 		= gs_team_get_translation( 'gs_teamemail_meta' );
		$gs_teamlocation_meta 	= gs_team_get_translation( 'gs_teamlocation_meta' );
		$gs_teamlanguage_meta 	= gs_team_get_translation( 'gs_teamlanguage_meta' );
		$gs_teamspecialty_meta 	= gs_team_get_translation( 'gs_teamspecialty_meta' );
		$gs_teamgender_meta 	= gs_team_get_translation( 'gs_teamgender_meta' );
		
		?>

		<div class="gs-team-single-content" itemscope="" itemtype="http://schema.org/Person"> <!-- Start sehema -->
			<div class="gs_member_img">
				
				<!-- Team Image -->
				<div class="gs_ribon_wrapper">

					<?php gs_team_member_thumbnail( 'full', true ); ?>
					<?php do_action( 'gs_team_after_member_thumbnail' ); ?>

					<!-- Ribbon -->
					<?php if ( !empty($ribon) ): ?>
						<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
						<?php do_action( 'gs_team_after_member_ribbon' ); ?>
					<?php endif; ?>
					
				</div>

				<!-- Meta Details -->
				<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup-details.php' ); ?>

			</div>

			<div class="gs_member_details gs-tm-sicons">

				<!-- Member Name -->
				<h1 class="gs-sin-mem-name" itemprop="name"><?php the_title(); ?></h1>
				<?php do_action( 'gs_team_after_member_name' ); ?>

				<!-- Member Designation -->
				<div class="gs-sin-mem-desig" itemprop="jobtitle"><?php echo $designation; ?></div>
				<?php do_action( 'gs_team_after_member_designation' ); ?>

				<!-- Social Links -->
				<?php $gs_member_connect = 'on'; ?>
				<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>

				<!-- Description -->
				<div class="gs-member-desc" itemprop="description"><?php echo wpautop( do_shortcode( get_the_content() ) ); ?></div>
				<?php do_action( 'gs_team_after_member_details' ); ?>
				
				<!-- Skills -->
				<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-skills.php' ); ?>
				
			</div>
		</div> <!-- end sehema -->
	
		<?php endwhile; ?>

		<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-navigation.php' ); ?>

	</div>
</div>

<?php get_footer(); ?>