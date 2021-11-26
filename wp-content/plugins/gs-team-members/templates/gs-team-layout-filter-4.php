<?php
/**
 * GS Team - Layout Filter Four
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-filter-4.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.7
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
				
				$vcard = get_post_meta( get_the_id(), '_gs_vcard', true );

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
	
				if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';
				if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>
			
			<div class="<?php echo implode( ' ', $classes ); ?>" data-category="<?php echo gs_team_get_member_terms_slugs( 'team_group' ); ?>">
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">
					<div class="card">

						<?php do_action( 'gs_team_before_member_content' ); ?>

						<div class="single-member cbp-so-side cbp-so-side-left">

							<!-- Team Image -->
							<div class="gs_team_image__wrapper">
								<?php echo gs_team_member_thumbnail_with_link( $gs_member_thumbnail_sizes, $gs_member_name_is_linked == 'on', $gs_member_link_type ); ?>
							</div>
							<?php do_action( 'gs_team_after_member_thumbnail' ); ?>
							
						</div>
							
						<!-- Single member name -->
						<?php if ( 'on' ==  $gs_member_name ): ?>
							<?php gs_team_member_name( true, $gs_member_name_is_linked == 'on', $gs_member_link_type, 'h5' ); ?>
							<?php do_action( 'gs_team_after_member_name' ); ?>
						<?php endif; ?>

						<!-- Single member designation -->
						<?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
							<p class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></p>
							<?php do_action( 'gs_team_after_member_designation' ); ?>
						<?php endif; ?>

						<?php if ( !empty($cell) ) : ?>
							<div class="gs-member-cphon">

								<?php if ( is_rtl() ) : ?>
									<span class="level-info-cphon"><a href="tel:<?php echo $cell; ?>"><?php echo $cell; ?></a> : </span>
								<?php endif; ?>
								
								<span class="levels"><?php echo $gs_teamcellPhone_meta; ?></span>

								<?php if ( ! is_rtl() ) : ?>
									<span class="level-info-cphon"> : <a href="tel:<?php echo $cell; ?>"><?php echo $cell; ?></a></span>
								<?php endif; ?>

							</div>
						<?php endif; ?>

						<?php if ( !empty($email) ) : ?>
							<div class="gs-member-email">

								<?php if ( is_rtl() ) : ?>
									<span class="level-info-email"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a> : </span>
								<?php endif; ?>
								
								<span class="levels"><?php echo $gs_teamemail_meta; ?></span>

								<?php if ( ! is_rtl() ) : ?>
									<span class="level-info-email"> : <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></span>
								<?php endif; ?>

							</div>
						<?php endif; ?>

						<?php if ( !empty($vcard) && $gs_team_vcard_txt ) : ?>
							<a class="gs_secondary_button" rel="noopener noreferrer nofollow" target="_blank" href="<?php echo $vcard; ?>">
								<?php echo $gs_team_vcard_txt; ?>
							</a>
						<?php endif; ?>
							
						<?php do_action( 'gs_team_after_member_content' ); ?>
					
					</div>
				</div>
			
				<!-- Popup -->
				<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>

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