<?php
/**
 * GS Team - Layout Table Filter
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-table-filter.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.1
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer">

	<div class="gs-roow clearfix gs_team">
	
		<?php if ( $gs_team_loop->have_posts() ): ?>

			<?php do_action( 'gs_team_before_team_members' ); ?>
			
			<div class="table-responsive col-md-12 table-responsive--dense">

				<table data-toggle="table" data-search="true" class="table table-striped table-hover" style="display: none;">

					<thead class="thead-dark">

						<tr>
							<?php do_action( 'gs_team_before_member_content_table_heads' ); ?>

							<th data-sortable="true"><?php _e( 'Name', 'gsteam' ); ?></th>
							<th data-sortable="true"><?php _e( 'Phone', 'gsteam' ); ?></th>
							<th data-sortable="true"><?php _e( 'Email', 'gsteam' ); ?></th>

							<?php do_action( 'gs_team_after_member_content_table_heads' ); ?>
						</tr>

					</thead>

					<tbody>

						<?php while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

							$email = get_post_meta( get_the_id(), '_gs_email', true );

							$cell = get_post_meta( get_the_id(), '_gs_cell', true );
							$cell = gs_team_format_phone($cell);

							$classes = ['single-member-div'];
				
							if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';

							?>

							<tr class="<?php echo implode( ' ', $classes ); ?>">
								<?php do_action( 'gs_team_before_member_content' ); ?>

								<?php gs_team_member_name( true, $gs_member_name_is_linked == 'on', $gs_member_link_type, 'td', '', true ); ?>
								<td><?php echo sanitize_text_field( $cell ); ?></td>
								<td><?php echo sanitize_email($email); ?></td>
								
								<?php do_action( 'gs_team_after_member_content' ); ?>
							</tr>

							<!-- Popup -->
							<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>
						
						<?php endwhile; ?>

					</tbody>
				
				</table>

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