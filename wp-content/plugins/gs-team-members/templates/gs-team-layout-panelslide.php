<?php
/**
 * GS Team - Layout Panel Slide
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-panelslide.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.4
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">

	<?php if ( $gs_team_loop->have_posts() ): ?>

		<!-- Search by Name Filter -->
		<div class="search-filter">
			<div class="gs-roow justify-content-center">

				<?php if ( 'on' ==  $gs_member_srch_by_name ) : ?>

					<?php do_action( 'gs_team_before_search_filter' ); ?>

					<div class="col-lg-4 col-md-6 search-fil-nbox">
						<input type="text" class="search-by-name" placeholder="<?php echo $gs_teamfliter_name; ?>" />
					</div>

				<?php endif; ?>

			</div>
		</div>

		<?php do_action( 'gs_team_before_team_members' ); ?>

		<div class="gs-roow clearfix gs_team gs-all-items-filter-wrapper" id="gs_team<?php echo get_the_id(); ?>">

			<?php while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();
		
			$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );
			$designation = get_post_meta( get_the_id(), '_gs_des', true );

			$classes = ['gs-filter-single-item single-member-div gs-filter-single-item', gs_team_get_col_classes( $gs_team_cols, $gs_team_cols_tablet, $gs_team_cols_mobile_portrait, $gs_team_cols_mobile ) ];
			if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>

			<div class="<?php echo implode( ' ', $classes ); ?>">
				
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">
					<div class="single-member cbp-so-side cbp-so-side-left">

						<a class="gs_team_pop gs_team_panelslide_link" id="gsteamlink<?php echo get_the_id(); ?>" href="#gsteam<?php echo get_the_id(); ?>">

							<?php do_action( 'gs_team_before_member_content' ); ?>

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
							<div class="gs_team_overlay"><i class="fa fa-bolt"></i></div>

							<div class="single-member-name-desig">

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

						</a>
					
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

	<!-- Pagination -->
	<?php if ( 'on' == $gs_member_pagination ) : ?>
		<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-pagination.php' ); ?>
	<?php endif; ?>

	<!-- Popups -->
	<?php if ( $gs_team_loop->have_posts() ):

		gs_team_load_acf_fields( $show_acf_fields, $acf_fields_position );
	
		while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();
			$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );
			$designation = get_post_meta( get_the_id(), '_gs_des', true );
		?>
	
		<div id="gsteam<?php echo get_the_id(); ?>" class="gstm-panel">
			<div class="panel-container">
				
				<div class="gstm-panel-left gs-tm-sicons">
					<!-- Social Links -->
					<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>
				</div>
	
				<div class="gstm-panel-right">
					
					<div class="gstm-panel-title">
						<!-- Member Name -->
						<?php the_title(); ?>
						<?php do_action( 'gs_team_after_member_name' ); ?>
						<div class="close-gstm-panel-bt"><i class="fa fa-times" aria-hidden="true"></i></div>
					</div>
					
					<!-- Member Designation -->
					<div class="gstm-panel-info" itemprop="jobtitle"><?php echo wp_kses_post($designation); ?></div>
					<?php do_action( 'gs_team_after_member_designation' ); ?>
	
					<!-- Team Image -->
					<div class="gs_team_image__wrapper">
						<?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
						<?php do_action( 'gs_team_after_member_thumbnail_popup' ); ?>
					</div>
	
					<!-- Description -->
					<div class="gs-member-desc" itemprop="description"><?php echo wpautop(gs_team_member_description( 300, false )); ?></div>
					<?php do_action( 'gs_team_after_member_details' ); ?>
	
					<!-- Meta Details -->
					<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup-details.php' ); ?>
	
					<!-- Skills -->
					<?php $is_skills_title = true; ?>
					<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-skills.php' ); ?>
	
				</div>
	
	
			</div>
		</div>
	
		<?php endwhile; ?>
	
	<?php endif; ?>

</div>

<div id="gstm-overlay"></div>