<?php
/**
 * GS Team - Layout Drawer
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-drawer.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.4
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">
	
	<div class="gs-roow clearfix gs_team gstm-gridder">

		<?php if ( $gs_team_loop->have_posts() ):

			do_action( 'gs_team_before_team_members' ); ?>

			<div class="gridder">

				<?php while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

					$designation = get_post_meta( get_the_id(), '_gs_des', true );
					$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );

					$classes = ['gridder-list gs-filter-single-item single-member-div', gs_team_get_col_classes( $gs_team_cols, $gs_team_cols_tablet, $gs_team_cols_mobile_portrait, $gs_team_cols_mobile ) ];
					if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

				?>
				
				<div class="<?php echo implode( ' ', $classes ); ?>" data-griddercontent="#<?php echo get_the_id(); ?>" itemscope itemtype="http://schema.org/Organization">
					
					<div class="single-member--wraper cbp-so-side cbp-so-side-left">
						<div class="overlay-area single-member">

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
							
							<!-- Overlay Contents -->
							<div class="overlay">
								<?php gs_team_member_name( true, false, '', 'h2', 'title', true ); ?>
								<?php do_action( 'gs_team_after_member_name' ); ?>

								<p class="desig"><?php echo wp_kses_post($designation); ?></p>
								<?php do_action( 'gs_team_after_member_designation' ); ?>
							</div>

							<?php do_action( 'gs_team_after_member_content' ); ?>

						</div>
					</div>

				</div>
		
				<?php endwhile; ?>

			</div>

			<?php do_action( 'gs_team_after_team_members' ); ?>

		<?php else: ?>

			<!-- Members not found - Load no-team-member template -->
			<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-no-team-member.php' ); ?>

		<?php endif; ?>

		<!-- Panel Contents -->
		<?php if ( $gs_team_loop->have_posts() ): while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

			$designation = get_post_meta( get_the_id(), '_gs_des', true );

			gs_team_load_acf_fields( $show_acf_fields, $acf_fields_position );

			?>
			
			<div id="<?php echo get_the_id(); ?>" class="gridder-content">

				<div class="gs-roow">

					<div class="col-md-6 team-description">

						<!-- Single member name -->
						<?php gs_team_member_name( true, $gs_member_name_is_linked == 'on', $gs_member_link_type, 'h2', 'title', true ); ?>
						<?php do_action( 'gs_team_after_member_name' ); ?>
						
						<!-- Single member designation -->
						<p class="gs-member-desig" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></p>
						<?php do_action( 'gs_team_after_member_designation' ); ?>

						<!-- Description -->
						<p class="gs-member-desc" itemprop="description"><?php gs_team_member_description( $gs_tm_details_contl, true ); ?></p>
						<?php do_action( 'gs_team_after_member_details' ); ?>

					</div>

					<div class="col-md-6 gs-tm-sicons">

						<!-- Social Links -->
						<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>
						
						<!-- Skills -->
						<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-skills.php' ); ?>

					</div>

				</div>

			</div>
			
		<?php endwhile; endif; ?>

	</div>

	<!-- Pagination -->
	<?php if ( 'on' == $gs_member_pagination ) : ?>
		<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-pagination.php' ); ?>
	<?php endif; ?>

</div>