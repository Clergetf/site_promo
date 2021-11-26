<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

function gs_team_shortcode( $atts ) {

	$gs_member_nxt_prev 			= gs_team_getoption( 'gs_member_nxt_prev', 'off' );
	$gs_member_search_all_fields 	= gs_team_getoption( 'gs_member_search_all_fields', 'off' );
	$gs_member_enable_multilingual 	= gs_team_getoption( 'gs_member_enable_multilingual', 'off' );

	$gs_teamfliter_designation 	= gs_team_get_translation( 'gs_teamfliter_designation' );
	$gs_teamfliter_name 		= gs_team_get_translation( 'gs_teamfliter_name' );
	$gs_teamcom_meta 			= gs_team_get_translation( 'gs_teamcom_meta' );
	$gs_teamadd_meta 			= gs_team_get_translation( 'gs_teamadd_meta' );
	$gs_teamlandphone_meta 		= gs_team_get_translation( 'gs_teamlandphone_meta' );
	$gs_teamcellPhone_meta 		= gs_team_get_translation( 'gs_teamcellPhone_meta' );
	$gs_teamemail_meta 			= gs_team_get_translation( 'gs_teamemail_meta' );
	$gs_teamlocation_meta 		= gs_team_get_translation( 'gs_teamlocation_meta' );
	$gs_teamlanguage_meta 		= gs_team_get_translation( 'gs_teamlanguage_meta' );
	$gs_teamspecialty_meta 		= gs_team_get_translation( 'gs_teamspecialty_meta' );
	$gs_teamgender_meta 		= gs_team_get_translation( 'gs_teamgender_meta' );
	
	$gs_team_read_on 			= gs_team_get_translation( 'gs_team_read_on' );
	$gs_team_more 				= gs_team_get_translation( 'gs_team_more' );
	$gs_team_vcard_txt 			= gs_team_get_translation( 'gs_team_vcard_txt' );

	$gs_team_reset_filters_txt 	= gs_team_get_translation( 'gs_team_reset_filters_txt' );
	
	if ( get_query_var('paged') ) {
    	$gs_tm_paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) { // 'page' is used instead of 'paged' on Static Front Page
	    $gs_tm_paged = get_query_var('page');
	} else {
	    $gs_tm_paged = 1;
	}

	// Extracting shortcode attributes.
	extract(
		shortcode_atts(
			getGsShortcodeAttributes(),
			$atts
		)
	);

	if ( empty($fitler_all_text) ) $fitler_all_text = 'All';

	if ( ! gtm_fs()->is_paying_or_trial() && $gs_member_link_type == 'popup' ) {
		$gs_member_link_type = 'default';
	}

	$args = [
		'order'          => sanitize_text_field( $order ),
		'orderby'        => sanitize_text_field( $orderby ),
		'posts_per_page' => (int) $num,
		'paged'          => (int) $gs_tm_paged
	];

	$publicTerms   = GS_Team_Shortcode_Builder::get_team_groups( true );
	$includedTerms = gs_team_string_to_array( $group );
	$excludedTerms = gs_team_string_to_array( $exclude_group );

	if ( $includedTerms !== $excludedTerms ) {
		if ( empty( $excludedTerms ) && ! empty( $includedTerms ) ) {
			$categoryIn = $includedTerms;
		}

		if ( empty( $includedTerms ) && ! empty( $excludedTerms ) ) {
			$categoryIn = array_diff( $publicTerms, $excludedTerms );
		}
	
		if ( ! empty( $includedTerms ) && ! empty( $excludedTerms ) ) {
			$categoryIn = array_diff( $includedTerms, $excludedTerms );
		}

		$categoryIn = ! empty( $categoryIn ) ? $categoryIn : [];

		$args['tax_query'][] = [
			'taxonomy' => 'team_group',
			'field'    => 'id',
			'terms'    => $categoryIn,
			'operator' => 'IN',
		];
	} else {
		if ( ! empty( $includedTerms ) && ! empty( $excludedTerms ) ) {
			$args['tax_query'][] = [
				'taxonomy' => 'team_group',
				'field'    => 'id',
				'terms'    => [],
				'operator' => 'IN',
			];
		}
	}

	$GLOBALS['gs_team_loop'] = get_gs_team_query( $args );

	ob_start(); ?>
		
	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gs-member-name,
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gs-member-name a,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .gs-member-name,
	<?php echo '#gs_team_area_' . $id; ?> .gs-member-name a {
		font-size: <?php echo $gs_tm_m_fz;?>px;
		font-weight: <?php echo $gs_tm_m_fntw; ?>;
		font-style: <?php echo $gs_tm_m_fnstyl; ?>;
		color: <?php echo $gs_tm_mname_color; ?>;
	}

	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gs_team_ribbon,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .gs_team_ribbon {
		background: <?php echo $gs_tm_ribon_color; ?>;
	}

	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .info-card,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .info-card {
		background: <?php echo $gs_tm_info_background; ?>;
	}

	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .staff-meta,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .staff-meta {
		background: <?php echo $gs_tm_tooltip_background; ?>;
	}
	
	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .staff-meta:after,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .staff-meta:after {
		border-top-color: <?php echo $gs_tm_tooltip_background; ?>;
	}

	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gs-member-name,
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gstm-panel-title,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .gs-member-name,
	<?php echo '#gs_team_area_' . $id; ?> .gstm-panel-title {
		background-color: <?php echo $gs_tm_mname_background; ?>;
	}

	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .single-member .gs_team_overlay i,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .single-member .gs_team_overlay i {
		background-color: <?php echo $gs_tm_hover_icon_background; ?>;
	}
	
	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gs-member-desig,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .gs-member-desig {
		font-size: <?php echo $gs_tm_role_fz; ?>px;
		font-weight: <?php echo $gs_tm_role_fntw; ?>;
		font-style: <?php echo $gs_tm_role_fnstyl; ?>;
		color: <?php echo $gs_tm_role_color; ?>;   
	}

	<?php if ( gs_team_is_divi_active() ) : ?>
		#et-boc .et-l div <?php echo '#gs_team_area_' . $id; ?> .gs-team-filter-cats,
	<?php endif; ?>
	<?php echo '#gs_team_area_' . $id; ?> .gs-team-filter-cats {
		text-align: <?php echo $gs_tm_filter_cat_pos; ?>;
	}

	.mfp-gsteam .mfp-container .mfp-arrow,
	.mfp-gsteam .mfp-container .mfp-arrow:hover {
		background-color: <?php echo $gs_tm_arrow_color; ?>!important;
	}
		
	<?php

	$custom_css = ob_get_clean();
	$custom_css .= gs_team_getoption( 'gs_team_custom_css', null );

	if ( ! gtm_fs()->is_paying_or_trial() ) {
		
		$free_themes = ['gs_tm_theme1', 'gs_tm_theme2', 'gs_tm_theme3', 'gs_tm_theme5', 'gs_tm_theme4', 'gs_tm_theme6'];
		$initial_theme = $gs_team_theme;

		if ( ! in_array( $initial_theme, $free_themes ) ) {
			$gs_team_theme		             = 'gs_tm_theme1';
			$gs_member_connect               = 'on';
			$gs_member_name                  = 'on';
			$gs_member_role                  = 'on';
			$gs_member_details               = 'on';
		}

	}

	$data_options = [
		'search_through_all_fields' => $gs_member_search_all_fields,
		'enable_clear_filters' => $gs_member_enable_clear_filters,
		'reset_filters_text' => $gs_team_reset_filters_txt,
		'enable_multi_select' => $gs_member_enable_multi_select,
		'multi_select_ellipsis' => $gs_member_multi_select_ellipsis
	];

	$theme_class = $gs_team_theme;

	if ( $gs_team_theme == 'gs_tm_theme25' ) {
		$theme_class .= ' gs_tm_theme22';
	}

	ob_start(); ?>
	
	<div id="gs_team_area_<?php echo $id; ?>" class="wrap gs_team_area gs_team_loading <?php echo $theme_class; ?>" data-options='<?php echo json_encode($data_options); ?>' style="visibility: hidden; opacity: 0;">

		<?php

		do_action( 'gs_team_template_before__loaded', $gs_team_theme );

		if ( ! gtm_fs()->is_paying_or_trial() ) {
			require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-restrict-template.php';
		}

		$scroll_animation = false;

		if ( $gs_team_theme == 'gs_tm_theme1' || $gs_team_theme == 'gs_tm_theme2' ) {
			
			if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
			if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';
			
			include GS_Team_Template_Loader::locate_template( 'gs-team-layout-default-1.php' );

			$scroll_animation = true;
			GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

		}

		if ( $gs_team_theme == 'gs_tm_theme3' || $gs_team_theme == 'gs_tm_theme5' ) {
			
			if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
			if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

			include GS_Team_Template_Loader::locate_template( 'gs-team-layout-default-2.php' );

			$scroll_animation = true;
			GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

		}

		if ( $gs_team_theme == 'gs_tm_theme4' || $gs_team_theme == 'gs_tm_theme6' ) {
			
			if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
			if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

			include GS_Team_Template_Loader::locate_template( 'gs-team-layout-default-3.php' );

			$scroll_animation = true;
			GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
			
		}

		if ( gtm_fs()->is_paying_or_trial() ) {

			if ( $gs_team_theme == 'gs_tm_grid2' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-grid-2.php' );

				$scroll_animation = true;

			}
			
			if ( $gs_team_theme == 'gs_tm_theme7' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-slider.php' );

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-owl-carousel'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome', 'gs-owl-carousel'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme8' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'popup';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-popup.php' );

				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme9' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'popup';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-filter.php' );

				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-isotope'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme10' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-greyscale.php' );

				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme11' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-popup-2.php' );

				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme12' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'popup';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-filter-2.php' );

				$scroll_animation = true;

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-isotope'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme13' || $gs_team_theme == 'gs_tm_drawer2' ) {
			
				$gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-drawer.php' );

				$scroll_animation = true;

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-gridder'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme14' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-table.php' );

				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme15' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-table-box.php' );

				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme16') {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-table-odd-even.php' );

				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme17' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-list.php' );

				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme18' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-list-2.php' );

				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme19' ) {
				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-panelslide.php' );
				$scroll_animation = true;
				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-isotope', 'gs-jquery-panelslider'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
			}

			if ( $gs_team_theme == 'gs_tm_theme20' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-grid.php' );

				$scroll_animation = true;

			}

			if ( $gs_team_theme == 'gs_tm_theme21' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-table-filter.php' );

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-bootstrap-table'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-bootstrap-table'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme21_dense' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-table-filter-dense.php' );

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-bootstrap-table'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-bootstrap-table'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme22' ) {

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-filter-3.php' );

				$scroll_animation = true;

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-isotope'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );

			}

			if ( $gs_team_theme == 'gs_tm_theme23' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-flip.php' );

				$scroll_animation = true;

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-jquery-flip'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme24' ) {
			
				if ( $gs_member_link_type == 'default' ) $gs_member_link_type = 'single_page';
				if ( $gs_member_name_is_linked != 'on' ) $gs_member_link_type = '';

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-filter-4.php' );

				$scroll_animation = true;

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-isotope'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_team_theme == 'gs_tm_theme25' ) {

				include GS_Team_Template_Loader::locate_template( 'gs-team-layout-group-filter.php' );

				$scroll_animation = true;

				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-isotope'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
				
			}

			if ( $gs_member_link_type == 'popup' ) {
				GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-magnific-popup'] );
				GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-magnific-popup', 'gs-font-awesome'] );
			}

		}

		if ( $scroll_animation && $enable_scroll_animation == 'on' ) {
			GS_Team_Scripts::add_dependency_scripts( 'gs-team-public', ['gs-cpb-scroller'] );
		}

		do_action( 'gs_team_template_after__loaded', $gs_team_theme );

		wp_reset_postdata();

		?>

	</div>
	
	<?php

	wp_enqueue_style( 'gs-team-public' );
	wp_enqueue_script( 'gs-team-public' );
	gs_team_add_fs_script( 'gs-team-public' );

	if ( gs_team_is_divi_active() ) {
		wp_enqueue_style( 'gs-team-divi-public' );
	}

	wp_add_inline_style( 'gs-team-public', $custom_css );

	return ob_get_clean();

}
add_shortcode( 'gs_team', 'gs_team_shortcode' );

if ( gtm_fs()->is_paying_or_trial() ) {

	// -- Shortcode for widget [gs_team_sidebar]
	function gs_team_gs_team_sidebar_shortcode( $atts ) {

		extract(shortcode_atts([
			'total_mem' => -1,
			'group_mem' => ''
		], $atts ));

		$GLOBALS['gs_team_loop_side'] = get_gs_team_query([
			'posts_per_page'	=> (int) $total_mem,
			'team_group'		=> sanitize_text_field( $group_mem )
		]);

		ob_start();

		include GS_Team_Template_Loader::locate_template( 'gs-team-layout-sidebar.php' );
		
		wp_reset_postdata();

		GS_Team_Scripts::add_dependency_styles( 'gs-team-public', ['gs-font-awesome'] );
		wp_enqueue_style( 'gs-team-public' );

		return ob_get_clean();

	}

	add_shortcode( 'gs_team_sidebar', 'gs_team_gs_team_sidebar_shortcode' );

}