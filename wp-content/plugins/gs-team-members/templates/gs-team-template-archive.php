<?php
/**
 * GS Team - Archive Template
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-archive.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.1
 */

remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );

get_header(); ?>

<div class="gs-containeer gs-archive-container">
	
	<h1 class="arc-title"><?php the_archive_title(); ?></h1>

	<div class="gs-roow clearfix gs_team" id="gs_team_archive">

		<?php while ( have_posts() ) : the_post();
		
		$designation = get_post_meta( get_the_id(), '_gs_des', true );
		$ribon = get_post_meta( get_the_id(), '_gs_ribon', true );
		
		?>
		
		<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
			<div itemscope="" itemtype="http://schema.org/Person"> <!-- Start sehema -->
			
				<!-- Team Image -->
				<div class="gs-arc-mem-img gs_ribon_wrapper">
					<a href="<?php the_permalink(); ?>">
						<?php gs_team_member_thumbnail( $gs_member_thumbnail_sizes, true ); ?>
					</a>
					<!-- Ribbon -->
					<?php if ( !empty($ribon) ): ?>
						<div class="gs_team_ribbon"><?php echo esc_html( $ribon ); ?></div>
						<?php do_action( 'gs_team_after_member_ribbon' ); ?>
					<?php endif; ?>
				</div>
				<?php do_action( 'gs_team_after_member_thumbnail' ); ?>


				<div class="gs_member_details gs-tm-sicons">

					<a href="<?php the_permalink(); ?>"><h3 class="gs-arc-mem-name" itemprop="name"><?php the_title(); ?></h3></a>
					<?php do_action( 'gs_team_after_member_name' ); ?>
					
					<div class="gs-arc-mem-desig" itemprop="jobtitle"><?php echo $designation; ?></div>
					<?php do_action( 'gs_team_after_member_designation' ); ?>

					<!-- Social Links -->
					<?php $gs_member_connect = 'on'; ?>
					<?php include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-social-links.php' ); ?>

				</div>
			
			</div> <!-- end sehema -->
		</div> <!-- end col -->
	
		<?php endwhile; ?>

	</div>
	
</div>

<?php get_footer(); ?>