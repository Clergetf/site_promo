<?php
/**
 * GS Team - Layout Popup Two
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-popup-2.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.3
 */

global $gs_team_loop;

?>

<!-- Container for Team members -->
<div class="gs-containeer cbp-so-scroller">
	
	<div class="gs-roow clearfix gs_team" id="gs_team<?php echo get_the_id(); ?>">

		<?php if ( $gs_team_loop->have_posts() ):

			do_action( 'gs_team_before_team_members' );

			while ( $gs_team_loop->have_posts() ): $gs_team_loop->the_post();

			$designation = get_post_meta( get_the_id(), '_gs_des', true );
			$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );

			$classes = ['single-member-div', gs_team_get_col_classes( $gs_team_cols, $gs_team_cols_tablet, $gs_team_cols_mobile_portrait, $gs_team_cols_mobile ) ];

			if ( $gs_member_link_type == 'popup' ) $classes[] = 'single-member-pop';
			if ( $enable_scroll_animation == 'on' ) $classes[] = 'cbp-so-section';

			?>
			
			<div class="<?php echo implode( ' ', $classes ); ?>">
			    
				<!-- Sehema & Single member wrapper -->
				<div class="single-member--wraper" itemscope itemtype="http://schema.org/Organization">
					<div class="single-member cbp-so-side cbp-so-side-right">
						
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
								<div class="gs_team_overlay"><i class="fa fa-eye"></i></div>
							<?php endif; ?>

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

						<?php if ( $gs_member_name_is_linked == 'on' ) echo '</a>'; ?>

						<!-- Popup -->
						<?php if ( $gs_member_link_type == 'popup' ) include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-popup.php' ); ?>
			        
			        </div>
				</div>

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