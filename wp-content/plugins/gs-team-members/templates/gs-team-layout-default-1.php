<?php
/**
 * GS Team - Layout One
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-default-1.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.4
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">
	
	<div class="gs-roow clearfix gs_team">
	
		<?php if ( $gs_team_loop->have_posts() ):

			do_action( 'gs_team_before_team_members' );

			while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

			$designation = get_post_meta( get_the_id(), '_gs_des', true );
			$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );

			$classes = ['single-member-div', gs_team_get_col_classes( $gs_team_cols, $gs_team_cols_tablet, $gs_team_cols_mobile_portrait, $gs_team_cols_mobile ) ];

			if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';
			if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>

			<!-- Start single member -->
			<div class="<?php echo implode( ' ', $classes ); ?>">
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">

					<!-- Single member thumbnail and more -->
					<div class="single-member cbp-so-side cbp-so-side-left">

						<?php do_action( 'gs_team_before_member_content' ); ?>

						<!-- Ribbon -->
						<?php if ( !empty($ribon) ): ?>
							<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
							<?php do_action( 'gs_team_after_member_ribbon' ); ?>
						<?php endif; ?>
						
						<!-- Team Image -->
						<div class="gs_team_image__wrapper">
							<?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
						</div>
						<?php do_action( 'gs_team_after_member_thumbnail' ); ?>
						
						<!-- Description & Social Links -->
						<div class="single-mem-desc-social flex-align-justify-center">

							<div class="single-mem-desc-social--inner">
								
								<!-- Description -->
								<?php if ( 'on' ==  $gs_member_details ) : ?>
									<p class="gs-member-desc" itemprop="description"><?php gs_team_member_description( $gs_tm_details_contl, true, true, $gs_member_name_is_linked == 'on', $gs_member_link_type ); ?></p>
									<?php do_action( 'gs_team_after_member_details' ); ?>
								<?php endif; ?>
									
								<!-- Social Links -->
								<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>

							</div>

						</div>

						<?php do_action( 'gs_team_after_member_content' ); ?>
						
					</div>

					<!-- Single member name -->
					<?php if ( 'on' ==  $gs_member_name ): ?>
						<?php gs_team_member_name( true, $gs_member_name_is_linked == 'on', $gs_member_link_type ); ?>
						<?php do_action( 'gs_team_after_member_name' ); ?>
					<?php endif; ?>
					
					<!-- Single member designation -->
					<?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
						<div class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
						<?php do_action( 'gs_team_after_member_designation' ); ?>
					<?php endif; ?>

				</div>
				
				<!-- Popup -->
				<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>

			</div>

		<?php endwhile; ?>

		<?php do_action( 'gs_team_after_team_members' ); ?>

		<?php else: ?>

			<!-- Members not found - Load no-team-member template -->
			<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-no-team-member.php' ); ?>

		<?php endif; ?>

	</div>

	<!-- Pagination -->
	<?php if ( 'on' == $gs_member_pagination ) : ?>
		<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-pagination.php' ); ?>
	<?php endif; ?>

</div>