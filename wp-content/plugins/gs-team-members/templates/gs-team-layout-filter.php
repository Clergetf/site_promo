<?php
/**
 * GS Team - Layout Filter
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-filter.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.4
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">

	<!-- Cat Filters Template -->
	<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-cat-filters.php' ); ?>

	<!-- Filters Template -->
	<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-filters.php' ); ?>

	<?php if ( $gs_team_loop->have_posts() ): ?>

		<?php do_action( 'gs_team_before_team_members' ); ?>

		<div class="gs-all-items-filter-wrapper gs-roow">

			<?php while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();
		
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

				if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';
				if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>

			<div class="<?php echo implode( ' ', $classes ); ?>" data-category="<?php echo gs_team_get_member_terms_slugs( 'team_group' ); ?>">
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">
					<div class="single-member ccbp-so-side ccbp-so-side-left">

						<?php

						if ( $gs_member_name_is_linked == 'on' ) {
							if ( $gs_member_link_type == 'single_page' ) {
								printf( '<a href="%s">', get_the_permalink() );
							} else if ( $gs_member_link_type == 'popup' ) {
								printf( '<a class="gs_team_pop open-popup-link" data-mfp-src="#gs_team_popup_%s" href="javascript:void(0);">', get_the_ID() );
							}
						}

							do_action( 'gs_team_before_member_content' ); ?>

							<!-- Ribbon -->
							<?php if ( !empty($ribon) ): ?>
								<span class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></span>
								<?php do_action( 'gs_team_after_member_ribbon' ); ?>
							<?php endif; ?>
							
							<!-- Team Image -->
							<div class="gs_team_image__wrapper">
								<?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
							</div>
							<?php do_action( 'gs_team_after_member_thumbnail' ); ?>
							
							<!-- Indicator -->
							<?php if ( $gs_member_name_is_linked == 'on' ) : ?>
								<div class="gs_team_overlay"><i class="fa fa-external-link"></i></div>
							<?php endif; ?>

							<div class="single-member-name-desig cbp-so-side cbp-so-side-right">

								<!-- Single member name -->
								<?php if ( 'on' ==  $gs_member_name ): ?>
									<?php gs_team_member_name( true, false ); ?>
									<?php do_action( 'gs_team_after_member_name' ); ?>
								<?php endif; ?>
								
								<!-- Single member designation -->
								<?php if ( !empty( $designation ) && 'on' == $gs_member_role ): ?>
									<div class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
									<?php do_action( 'gs_team_after_member_designation' ); ?>
								<?php endif; ?>

							</div>

							<?php do_action( 'gs_team_after_member_content' ); ?>

						<?php if ( $gs_member_name_is_linked == 'on' ) echo '</a>'; ?>
						
						<!-- Popup -->
						<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>
					
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