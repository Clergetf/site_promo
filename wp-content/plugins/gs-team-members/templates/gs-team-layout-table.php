<?php
/**
 * GS Team - Layout Table
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-table.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.2
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer">
	
	<div class="gs_team">
		
		<?php if ( $gs_team_loop->have_posts() ): ?>
			
			<?php do_action( 'gs_team_before_team_members' ); ?>

			<div class="gs-team-table">

				<div class="gs-team-table-row gsc-table-head">

					<?php do_action( 'gs_team_before_member_content_table_heads' ); ?>

					<div class="gs-team-table-cell"><?php _e( 'Image', 'gsteam' ); ?></div>
					<div class="gs-team-table-cell"><?php _e( 'Name', 'gsteam' ); ?></div>
					<div class="gs-team-table-cell"><?php _e( 'Position', 'gsteam' ); ?></div>
					<div class="gs-team-table-cell"><?php _e( 'Description', 'gsteam' ); ?></div>

					<?php if ( 'on' == $gs_member_connect ) : ?>
						<div class="gs-team-table-cell"><?php _e( 'Social Links', 'gsteam' ); ?></div>
					<?php endif; ?>

					<?php do_action( 'gs_team_after_member_content_table_heads' ); ?>

				</div>

				<?php while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

					$designation = get_post_meta( get_the_id(), '_gs_des', true );
					$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );

					$classes = ['gs-team-table-row single-member-div'];

					if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';

					?>

					<div class="<?php echo implode( ' ', $classes ); ?>">

						<?php do_action( 'gs_team_before_member_content' ); ?>

						<!-- Team Image -->
						<div class="gs-team-table-cell gsc-image">
							<?php echo gs_team_member_thumbnail_with_link( $gs_member_thumbnail_sizes, $gs_member_name_is_linked == 'on', $gs_member_link_type, 'gs_team_image__wrapper' ); ?>
							<?php do_action( 'gs_team_after_member_thumbnail' ); ?>
						</div>

						<!-- Single member name -->
						<div class="gs-team-table-cell gsc-name">
							<div class="gs-team-table-cell-inner">
								<?php gs_team_member_name( true, $gs_member_name_is_linked == 'on', $gs_member_link_type ); ?>
								<?php do_action( 'gs_team_after_member_name' ); ?>
							</div>
						</div>

						<!-- Single member designation -->
						<div class="gs-team-table-cell gsc-desig">
							<div class="gs-team-table-cell-inner">
								<div class="gs-member-profession" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
								<?php do_action( 'gs_team_after_member_designation' ); ?>
							</div>
						</div>

						<!-- Description -->
						<div class="gs-team-table-cell gsc-desc">
							<div class="gs-team-table-cell-inner">
								<div class="gs-member-details justify" itemprop="description"><?php gs_team_member_description( $gs_tm_details_contl, true, true, $gs_member_name_is_linked == 'on', $gs_member_link_type ); ?></div>
								<?php do_action( 'gs_team_after_member_details' ); ?>
							</div>
						</div>

						<?php if ( 'on' == $gs_member_connect ) : ?>
							<!-- Social Links -->
							<div class="gs-team-table-cell socialicon gs-tm-sicons">
								<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>
							</div>
						<?php endif; ?>

						<?php do_action( 'gs_team_after_member_content' ); ?>

					</div>

					<!-- Popup -->
					<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>
				
				<?php endwhile; ?>

			</div>

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