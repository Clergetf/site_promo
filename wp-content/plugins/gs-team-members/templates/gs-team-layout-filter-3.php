<?php
/**
 * GS Team - Layout Filter Three
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-filter-3.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.5
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">

	<!-- Filters Template -->
	<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-filters.php' ); ?>
	
	<?php do_action( 'gs_team_before_team_members' ); ?>

	<?php if ( $gs_team_loop->have_posts() ): ?>

		<?php do_action( 'gs_team_before_team_members' ); ?>

		<div class="gs-all-items-filter-wrapper gs-roow">

			<?php while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

				$email = get_post_meta( get_the_id(), '_gs_email', true );
				$cell = get_post_meta( get_the_id(), '_gs_cell', true );
				$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );

				$designation = get_post_meta( get_the_id(), '_gs_des', true );
				if ( empty($designation) ) $designation = '';

				$designation_slug = sanitize_title( $designation );

				$classes = [
					'gs-filter-single-item single-member-div',
					$designation_slug,
					gs_team_get_col_classes( $gs_team_cols, $gs_team_cols_tablet, $gs_team_cols_mobile_portrait, $gs_team_cols_mobile ),
					gs_team_get_member_terms_slugs( 'team_group' ),
					gs_team_get_member_terms_slugs( 'team_location' ),
					gs_team_get_member_terms_slugs( 'team_language' ),
					gs_team_get_member_terms_slugs( 'team_gender' ),
					gs_team_get_member_terms_slugs( 'team_specialty' )
				];

				if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>
			
			<div class="<?php echo implode( ' ', $classes ); ?>" data-category="<?php echo gs_team_get_member_terms_slugs( 'team_group' ); ?>">
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">
					<div class="single-member cbp-so-side cbp-so-side-left">
						<div class="card">

							<?php do_action( 'gs_team_before_member_content' ); ?>

							<div class="banner">

								<?php if ( !empty($ribon) ): ?>
									<!-- Ribbon -->
									<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
									<?php do_action( 'gs_team_after_member_ribbon' ); ?>
								<?php endif; ?>

								<!-- Team Image -->
								<div class="gs_team_image__wrapper">
									<?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
								</div>
								<?php do_action( 'gs_team_after_member_thumbnail' ); ?>
								
								<div class="tittle_container">

									<!-- Single member name -->
									<?php if ( 'on' ==  $gs_member_name ): ?>
										<?php gs_team_member_name( true, true, 'single_page', 'h5', 'card-title' ); ?>
										<?php do_action( 'gs_team_after_member_name' ); ?>
									<?php endif; ?>
									
									<!-- Single member designation -->
									<?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
										<p class="card-text gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></p>
										<?php do_action( 'gs_team_after_member_designation' ); ?>
									<?php endif; ?>

								</div>

							</div>


							<div class="card-body social_cont">
									
								<?php if ( !empty($email) ) : ?>
									<a class="social_contact" href="mailto:<?php echo $email; ?>">
										<i class="fa fa-envelope"></i>
										<?php echo $email; ?>
									</a>
								<?php endif; ?>

								<?php if ( !empty($cell) ) : ?>
									<a class="social_contact" href="tel:<?php echo $cell; ?>">
										<i class="fa fa-phone-square"></i>
										<?php echo $cell; ?>
									</a>
								<?php endif; ?>

								<a class="social_contact social_contact_mt gs_read_more_button" href="<?php the_permalink(); ?>">
									<i class="fa fa-arrow-right"></i>
									<?php echo $gs_team_read_on; ?>
								</a>

							</div>
								
							<?php do_action( 'gs_team_after_member_content' ); ?>

						</div>
					</div>
				</div>

			</div>
		
			<?php endwhile; ?>

		</div>

		<?php do_action( 'gs_team_after_team_members' ); ?>
	
	<?php else: ?>

		<div class="gs-roow clearfix gs_team">

			<!-- Members not found - Load no-team-member template -->
			<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-no-team-member.php' ); ?>

		</div>

	<?php endif; ?>

	<!-- Pagination -->
	<?php if ( 'on' == $gs_member_pagination ) : ?>
		<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-pagination.php' ); ?>
	<?php endif; ?>

</div>