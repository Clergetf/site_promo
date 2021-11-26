<?php
/**
 * GS Team - Layout Flip
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-flip.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.3
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">
	
	<div class="gs-roow clearfix gs_team">
	
		<?php if ( $gs_team_loop->have_posts() ):

			do_action( 'gs_team_before_team_members' );

			while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

			$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );
			$land_number = get_post_meta( get_the_id(), '_gs_land', true );
			$cell_number = get_post_meta( get_the_id(), '_gs_cell', true );
			$member_email = get_post_meta( get_the_id(), '_gs_email', true );
			
			$designation = get_post_meta( get_the_id(), '_gs_des', true );
			if ( empty($designation) ) $designation = '';

			$designation_slug = sanitize_title( $designation );
			
			$classes = [
				'gs-filter-single-item single-member-div',
				gs_team_get_col_classes( $gs_team_cols, $gs_team_cols_tablet, $gs_team_cols_mobile_portrait, $gs_team_cols_mobile ),
				gs_team_get_member_terms_slugs( 'team_group' ),
				$designation_slug
			];
		
			if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';
			if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>

			<!-- Start single member -->
			<div class="<?php echo implode( ' ', $classes ); ?>" data-category="<?php echo gs_team_get_member_terms_slugs( 'team_group' ); ?>">

				<?php do_action( 'gs_team_before_member_content' ); ?>
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper flip-vertical cbp-so-side cbp-so-side-left" itemscope itemtype="http://schema.org/Organization">
				
					<div class="single-member front">
						
						<!-- Team Image -->
						<div class="gs_team_image__wrapper">
							<?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
						</div>
						<?php do_action( 'gs_team_after_member_thumbnail' ); ?>
					
						<!-- Ribbon -->
						<?php if ( !empty($ribon) ): ?>
							<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
							<?php do_action( 'gs_team_after_member_ribbon' ); ?>
						<?php endif; ?>

					</div>
					
					<div class="back">
						
						<!-- Team Flip Image -->
						<div class="gs_team_image__wrapper gs_team_image__wrapper-secondary">
							<?php gs_team_member_secondary_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
						</div>
						<?php do_action( 'gs_team_after_member_secondary_thumbnail' ); ?>

						<!-- Ribbon -->
						<?php if ( !empty($ribon) ): ?>
							<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
							<?php do_action( 'gs_team_after_member_ribbon' ); ?>
						<?php endif; ?>
						
						<!-- Other Information -->
						<div class="flip-info">

							<?php if ( !empty($member_email) ) : ?>
								<div class="flip-info-email"><?php echo $member_email; ?></div>
								<?php do_action( 'gs_team_after_member_email' ); ?>
							<?php endif; ?>

							<?php if ( !empty($land_number) ) : ?>
								<div><?php echo $land_number; ?></div>
								<?php do_action( 'gs_team_after_member_lang_number' ); ?>
							<?php endif; ?>

							<?php if ( !empty($cell_number) ) : ?>
								<div><?php echo $cell_number; ?></div>
								<?php do_action( 'gs_team_after_member_cell_number' ); ?>
							<?php endif; ?>
							
						</div>

					</div>
					
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

				<?php do_action( 'gs_team_after_member_content' ); ?>

			</div>
				
			<!-- Popup -->
			<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>

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