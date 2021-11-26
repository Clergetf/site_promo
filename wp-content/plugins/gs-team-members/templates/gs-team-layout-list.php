<?php
/**
 * GS Team - Layout List
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-list.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.4
 */

global $gs_team_loop;

gs_team_load_acf_fields( $show_acf_fields, $acf_fields_position );

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">
	
	<div class="gs-roow clearfix gs_team">
	
		<?php if ( $gs_team_loop->have_posts() ):

			do_action( 'gs_team_before_team_members' );

			while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

			$designation = get_post_meta( get_the_id(), '_gs_des', true );
			$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );

			$classes = ['col-xs-12 single-member-div'];

			if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';
			if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>

			<!-- Start single member -->
			<div class="<?php echo implode( ' ', $classes ); ?>">
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">
					<div class="single-member fullcolumn">

						<div class="gs-roow">

							<?php do_action( 'gs_team_before_member_content' ); ?>
							
							<div class="col-md-4 col-sm-4 col-xs-12 cbp-so-side cbp-so-side-left gstm-img-div">

								<!-- Team Image -->
								<div class="zoomin image">

									<!-- Ribbon -->
									<?php if ( !empty($ribon) ): ?>
										<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
										<?php do_action( 'gs_team_after_member_ribbon' ); ?>
									<?php endif; ?>

									<?php echo gs_team_member_thumbnail_with_link( $gs_member_thumbnail_sizes, $gs_member_name_is_linked == 'on', $gs_member_link_type, 'gs_team_image__wrapper' ); ?>

								</div>
								<?php do_action( 'gs_team_after_member_thumbnail' ); ?>

							</div>

							<div class="col-md-8 col-sm-8 col-xs-12 cbp-so-side cbp-so-side-right gstm-img-div">
								<div class="single-team-rightinfo">
									<div class="gs-team-info gs-tm-sicons">

										<!-- Single member name -->
										<?php gs_team_member_name( true, $gs_member_name_is_linked == 'on', $gs_member_link_type, 'div', 'gs-team-name' ); ?>
										<?php do_action( 'gs_team_after_member_name' ); ?>

										<!-- Single member designation -->
										<span class="gs-team-profession" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></span>
										<?php do_action( 'gs_team_after_member_designation' ); ?>

										<!-- Description -->
										<div class="gs-team-details justify" itemprop="description"><?php gs_team_member_description( 9999, true, false ); ?></div>
										<?php do_action( 'gs_team_after_member_details' ); ?>

										<!-- Social Links -->
										<div class="socialicon">
											<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>
										</div>

									</div>
								</div>
							</div>

							<?php do_action( 'gs_team_after_member_content' ); ?>

						</div>
						
					</div>
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