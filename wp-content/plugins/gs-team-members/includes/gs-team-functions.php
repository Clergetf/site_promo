<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! function_exists('gs_team_is_divi_active') ) {
    
    function gs_team_is_divi_active() {

        if ( defined('ET_BUILDER_PLUGIN_ACTIVE') && ET_BUILDER_PLUGIN_ACTIVE ) return et_core_is_builder_used_on_current_request();

        return false;

    }

}

if ( ! function_exists('gs_team_echo_return') ) {
    
    function gs_team_echo_return( $content, $echo = false ) {

        if ( $echo ) {
            echo $content;
        } else {
            return $content;
        }

    }

}

if ( ! function_exists('get_gs_team_query') ) {

    function get_gs_team_query( $atts ) {

        $args = shortcode_atts([
            'order'			    => 'DESC',
            'orderby'		    => 'date',
            'posts_per_page'    => -1,
            'paged'             => 1,
            'tax_query'         => [],
        ], $atts );

        $args['post_type'] = 'gs_team';

        return new WP_Query( apply_filters( 'gs_team_wp_query_args', $args ) );
        
    }

}

if ( ! function_exists('gs_team_getoption') ) {

    function gs_team_getoption( $option, $default = '' ) {

        $options = get_option( 'gs_team_shortcode_prefs' );
        
        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }
        
        return $default;
        
    }

}

if ( ! function_exists('gs_team_get_translation') ) {

    function gs_team_get_translation( $translation_name ) {

        $translations = [
            'gs_teamfliter_designation' => __('Show All Designation', 'gsteam'),
            'gs_teamfliter_name' => __('Search By Name', 'gsteam'),
            'gs_teamcom_meta' => __('Company', 'gsteam'),
            'gs_teamadd_meta' => __('Address', 'gsteam'),
            'gs_teamlandphone_meta' => __('Land Phone', 'gsteam'),
            'gs_teamcellPhone_meta' => __('Cell Phone', 'gsteam'),
            'gs_teamemail_meta' => __('Email', 'gsteam'),
            'gs_teamlocation_meta' => __('Location', 'gsteam'),
            'gs_teamlanguage_meta' => __('Language', 'gsteam'),
            'gs_teamspecialty_meta' => __('Specialty', 'gsteam'),
            'gs_teamgender_meta' => __('Gender', 'gsteam'),
            'gs_team_read_on' => __('Read On', 'gsteam'),
            'gs_team_more' => __('More', 'gsteam'),
            'gs_team_vcard_txt' => __('Download vCard', 'gsteam'),
            'gs_team_reset_filters_txt' => __('Reset Filters', 'gsteam'),
        ];
        
        if ( ! array_key_exists($translation_name, $translations) ) return '';

        if ( gs_team_getoption( 'gs_member_enable_multilingual', 'off' ) == 'on' ) return $translations[$translation_name];

        return gs_team_getoption( $translation_name, $translations[$translation_name] );
        
    }

}

if ( ! function_exists('gs_team_member_description') ) {
    
    function gs_team_member_description( $max_length = 100, $echo = false, $is_excerpt = true, $has_link = true, $gs_member_link_type = 'single_page' ) {

        $description = $is_excerpt ? get_the_excerpt() : get_the_content();

        $gs_team_more = gs_team_get_translation( 'gs_team_more' );

        $gs_more_link = '';

        if ( $has_link ) {
            
            if ( $gs_member_link_type == 'single_page' ) {
    
                $gs_more_link = sprintf( '...<a href="%s">%s</a>', get_the_permalink(), $gs_team_more );
    
            } else if ( $gs_member_link_type == 'popup' ) {
    
                $gs_more_link = sprintf( '...<a class="gs_team_pop open-popup-link" data-mfp-src="#gs_team_popup_%s" href="javascript:void(0);">%s</a>', get_the_ID(), $gs_team_more );
    
            }

        }

        // Reduce the description length
        if ( $max_length > 0 && strlen($description) > $max_length ) {
            $description = substr( $description, 0, $max_length ) . $gs_more_link;
        }

        return gs_team_echo_return( $description, $echo );

    }

}

if ( ! function_exists('gs_team_member_thumbnail') ) {
    
    function gs_team_member_thumbnail( $size, $echo = false ) {

        $disable_lazy_load = gs_team_getoption( 'disable_lazy_load', 'off' );
        $lazy_load_class = gs_team_getoption( 'lazy_load_class', 'skip-lazy' );

        $member_id = get_the_ID();

        if ( has_post_thumbnail() ) {

            $size = apply_filters( 'gs_team_member_thumbnail_size', $size, $member_id );
            if ( empty($size) ) $size = 'large';

            $classes = ['gs_team_member--image'];

            if ( $disable_lazy_load == 'on' && !empty($lazy_load_class) ) {
                $classes[] = $lazy_load_class;
            }

            $classes = (array) apply_filters( 'gs_team_thumbnail_classes', $classes );

            $thumbnail = get_the_post_thumbnail( $member_id, $size, [
                'class' => implode(' ', $classes),
                'alt' => get_the_title(),
                'itemprop' => 'image'
            ]);

        } else {

            $thumbnail = sprintf( '<img src="%s" alt="%s" itemprop="image"/>', GSTEAM_PLUGIN_URI . '/assets/img/no_img.png', get_the_title() );

        }

        $thumbnail = apply_filters( 'gs_team_member_thumbnail_html', $thumbnail, $member_id );

        return gs_team_echo_return( $thumbnail, $echo );

    }

}

if ( ! function_exists('gs_team_member_thumbnail_with_link') ) {
    
    function gs_team_member_thumbnail_with_link( $size, $has_link = false, $gs_member_link_type = 'single_page', $extra_link_class = '' ) {

        $image_html = gs_team_member_thumbnail( $size, false );

        $before = $after = '';
        
        if ( $has_link ) {
            
            if ( $gs_member_link_type == 'single_page' ) {
    
                $before = sprintf( '<a class="%s" href="%s">', $extra_link_class, get_the_permalink() );
    
            } else if ( $gs_member_link_type == 'popup' ) {
    
                $before = sprintf( '<a class="gs_team_pop open-popup-link %s" data-mfp-src="#gs_team_popup_%s" href="javascript:void(0);">', $extra_link_class, get_the_ID() );
    
            }

            $after = '</a>';

        }

        return $before . $image_html . $after;

    }

}

if ( ! function_exists('gs_team_member_name') ) {
    
    function gs_team_member_name( $echo = false, $has_link = true, $gs_member_link_type = 'single_page', $tag = 'div', $extra_classes = '', $no_default_class = false ) {

        $member_id = get_the_ID();

        if ( empty($tag) || !in_array($tag, ['div', 'p', 'span', 'td', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ) $tag = 'div';
    
        $content = get_the_title();
        
        if ( $has_link ) {
            
            if ( $gs_member_link_type == 'single_page' ) {
    
                $content = sprintf( '<a href="%s">%s</a>', get_the_permalink(), $content );
    
            } else if ( $gs_member_link_type == 'popup' ) {
    
                $content = sprintf( '<a class="gs_team_pop open-popup-link" data-mfp-src="#gs_team_popup_%s" href="javascript:void(0);">%s</a>', get_the_ID(), $content );
    
            }

        }

        $classes = $no_default_class ? '' : 'gs-member-name ';

        $classes .= $extra_classes;
    
        $name = sprintf( '<%s class="%s" itemprop="name">%s</%s>', $tag, $classes, $content, $tag );

        $name = apply_filters( 'gs_team_member_name_html', $name, $member_id );

        return gs_team_echo_return(  $name, $echo );

    }

}

if ( ! function_exists( 'getGsShortcodeAttributes' ) ) {
    /**
     * Returns all required shortcode attributes for this plugin.
     * 
     * @since  1.10.14
     * @return array   Shortcode attributes.
     */
    function getGsShortcodeAttributes() {
        return [
            'id'            				  => '',
            'is_preview'            		  => false,
            'num' 		                      => -1,
            'order'		                      => 'DESC',
            'orderby'	                      => 'date',
            'gs_team_theme'                   => 'gs_tm_theme1',
            'gs_team_cols'                    => '3',
            'gs_team_cols_tablet'             => '4',
            'gs_team_cols_mobile_portrait'    => '6',
            'gs_team_cols_mobile'		      => '12',
            'group'		                      => '',
            'exclude_group'		              => '',
            'panel'		                      => 'right',
            'gs_teammembers_pop_clm'		  => 'two',
            'gs_member_connect'               => 'on',
            'gs_member_name'                  => 'on',
            'gs_member_name_is_linked'        => 'on',
            'gs_member_link_type'        	  => 'default',
            'gs_member_role'                  => 'on',
            'gs_member_pagination'            => 'off',
            'gs_member_details'               => 'on',
            'gs_tm_details_contl'             => 100,
            'gs_member_srch_by_name'          => 'on',
            'gs_member_filter_by_desig'       => 'on',
            'gs_member_filter_by_location'    => 'on',
            'gs_member_filter_by_language'    => 'on',
            'gs_member_filter_by_gender'      => 'on',
            'gs_member_filter_by_speciality'  => 'on',
            'gs_member_enable_clear_filters'  => 'off',
            'gs_member_enable_multi_select'   => 'off',
            'gs_member_multi_select_ellipsis' => 'off',
            'gs_filter_all_enabled' 		  => 'on',
            'enable_child_cats' 		  	  => 'off',
            'enable_scroll_animation' 		  => 'on',
            'fitler_all_text' 		  		  => 'All',
            'gs_team_filter_columns'  		  => 'two',
            'gs_tm_m_fz' 						=> 18,
            'gs_tm_m_fntw' 						=> 400,
            'gs_tm_m_fnstyl' 					=> 'normal',
            'gs_tm_mname_color' 				=> '#141412',
            'gs_tm_mname_background' 			=> 'rgba(0,185,235,0.8)',
            'gs_tm_info_background' 			=> 'rgba(255, 255, 255, 0.9)',
            'gs_tm_tooltip_background' 			=> '#2196f3',
            'gs_tm_hover_icon_background' 		=> '#00B9EB',
            'gs_tm_ribon_color' 				=> '#1DA642',
            'gs_tm_arrow_color' 				=> '#1d9ff3',
            'gs_tm_role_fz' 					=> 15,
            'gs_tm_role_fntw' 					=> 400,
            'gs_tm_role_fnstyl' 				=> 'italic',
            'gs_tm_role_color' 					=> '#141412',
            'gs_tm_filter_cat_pos' 				=> 'center',
            'gs_member_thumbnail_sizes' 	    => 'large',
            'show_acf_fields' 		            => 'off',
            'acf_fields_position' 	            => 'after_skills'
        ];
    }
}

if ( ! function_exists('gs_team_member_secondary_thumbnail') ) {
    
    function gs_team_member_secondary_thumbnail( $size, $echo = false ) {

        $member_id = get_the_ID();

        $thumbnail_id = get_post_meta( $member_id, 'second_featured_img', true );

        $size = apply_filters( 'gs_team_member_secondary_thumbnail_size', $size, $member_id );
        if ( empty($size) ) $size = 'large';

        $thumbnail = '';

        if ( $thumbnail_id ) {

            $classes = (array) apply_filters( 'gs_team_secondary_thumbnail_classes', ['gs_team_member--image'] );
            
            $thumbnail = wp_get_attachment_image( $thumbnail_id, $size, false, [
                'class' => implode( ' ', $classes ),
                'alt' => get_the_title(),
                'itemprop' => 'image'
            ]);

        }

        $thumbnail = apply_filters( 'gs_team_member_secondary_thumbnail_html', $thumbnail, $member_id );

        return gs_team_echo_return( $thumbnail, $echo );

    }

}

if ( ! function_exists('gs_team_format_phone') ) {

    function gs_team_format_phone( $num ) {

        $num = preg_replace( '/[^0-9]/', '', $num );
        $len = strlen( $num );

        if ( $len == 7 ) $num = preg_replace( '/([0-9]{3})([0-9]{2})([0-9]{1})/', '($1) $2$3-', $num );
        elseif ( $len == 8 ) $num = preg_replace( '/([0-9]{3})([0-9]{2})([0-9]{1})/', '($1) $2$3-', $num );
        elseif ( $len == 9 ) $num = preg_replace( '/([0-9]{3})([0-9]{2})([0-9]{1})([0-9]{2})/', '($1) $2$3-$4', $num );
        elseif ( $len == 10 ) $num = preg_replace( '/([0-9]{3})([0-9]{2})([0-9]{1})([0-9]{3})/', '($1) $2$3-$4', $num );

        return $num;

    }

}

if ( ! function_exists('gs_team_get_meta_values') ) {

    function gs_team_get_meta_values( $meta_key = '', $post_type = 'gs_team', $status = 'publish', $order_by = true, $order = 'ASC' ) {

        global $wpdb;

        if ( empty( $meta_key ) ) return [];

        if ( $order_by ) {
            $order == 'ASC' ? $order : 'DESC';
            $order_by = sprintf( 'ORDER BY pm.meta_value %s', $order );
        } else {
            $order_by = '';
        }

        $result = $wpdb->get_col( $wpdb->prepare("
            SELECT pm.meta_value FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = %s 
            AND p.post_status = %s 
            AND p.post_type = %s 
            {$order_by}
        ", $meta_key, $status, $post_type) );

        $result = array_values(array_unique($result));

        return $result;
    }

}

if ( ! function_exists('gs_team_get_meta_values_options') ) {

    function gs_team_get_meta_values_options( $meta_key = '', $post_type = 'gs_team', $status = 'publish', $echo = true ) {

        $meta_values = gs_team_get_meta_values( $meta_key, $post_type, $status );

        $html = '';

        foreach ( $meta_values as $meta_value ) {
            $html.= sprintf( '<option value=".%s">%s</option>', sanitize_title($meta_value), $meta_value );
        }

        return gs_team_echo_return( $html, $echo );

    }

}

function gs_cols_to_number( $cols ) {

    return ( 12 / (int) str_replace( '_', '.', $cols ) );
    
}

if ( ! function_exists('gs_team_get_carousel_data') ) {

    function gs_team_get_carousel_data( $cols_desktop, $cols_tablet, $cols_mobile_portrait, $cols_mobile, $echo = true ) {

        $carousel_data = [
            'data-carousel-desktop' 		=> gs_cols_to_number( $cols_desktop ),
            'data-carousel-tablet' 			=> gs_cols_to_number( $cols_tablet ),
            'data-carousel-mobile-portrait' => gs_cols_to_number( $cols_mobile_portrait ),
            'data-carousel-mobile' 			=> gs_cols_to_number( $cols_mobile )
        ];

        $carousel_data = array_map( function($key, $val) {
            return $key . '=' . $val;
        }, array_keys($carousel_data), array_values($carousel_data) );
    
        $carousel_data = implode( ' ', $carousel_data );

        return gs_team_echo_return( $carousel_data, $echo );

    }

}

if ( ! function_exists('gs_team_get_col_classes') ) {

    function gs_team_get_col_classes( $desktop = '3', $tablet = '4', $mobile_portrait = '6', $mobile = '12' ) {
        return sprintf('col-lg-%s col-md-%s col-sm-%s col-xs-%s', $desktop, $tablet, $mobile_portrait, $mobile );
    }

}

if ( ! function_exists('gs_team_get_terms') ) {

    function gs_team_get_terms( $term_name, $order = 'ASC', $orderby = 'name', $exclude = [] ) {

        $terms = get_terms([
            'taxonomy' => $term_name,
            'orderby'  => $orderby,
            'order'    => $order,
            'exclude' => (array) $exclude,
            'hide_empty' => false
        ]);

        return wp_list_pluck( $terms, 'name', 'slug' );

    }

}

if ( ! function_exists('gs_team_string_to_array') ) {

    function gs_team_string_to_array( $terms = '' ) {
        if ( empty($terms) ) return [];
        return (array) array_filter( explode( ',', $terms ) );
    }

}

if ( ! function_exists('gs_team_get_terms_options') ) {

    function gs_team_get_terms_options( $term_name, $echo = true, $order = 'ASC', $orderby = 'name' ) {

        $terms = gs_team_get_terms( $term_name, $order, $orderby );
        
        $html = '';

        foreach ( $terms as $term_slug => $term_name ) {
            $html.= sprintf( '<option value=".%s">%s</option>', $term_slug, $term_name );
        }

        return gs_team_echo_return( $html, $echo );

    }

}

if ( ! function_exists('get_team_setup_group_to_posts') ) {

    function get_team_setup_group_to_posts( $query ) {

        if ( empty($query->posts) ) return;
        
        foreach( $query->posts as $post_key => $post ) {

            $terms = get_the_terms( $post->ID, 'team_group' );
            $terms = empty($terms) ? [] : wp_list_pluck( $terms, 'slug' );
            $query->posts[$post_key]->team_group = (array) $terms;

        }

    }

}

if ( ! function_exists('get_team_filter_posts_by_term') ) {

    function get_team_filter_posts_by_term( $group_slug, $query_posts ) {

        $posts = array_filter( $query_posts, function( $post ) use($group_slug) {
            return in_array( $group_slug, $post->team_group );
        });

        return array_values($posts);

    }

}

if ( ! function_exists('gs_team_get_member_terms_slugs') ) {

    function gs_team_get_member_terms_slugs( $term_name, $separator = ' ' ) {

        global $post;

        $terms = get_the_terms( $post->ID, $term_name );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $terms = implode( $separator, wp_list_pluck( $terms, 'slug' ) );
            return $terms;
        }

    }

}

if ( ! function_exists('gs_team_pagination') ) {

    function gs_team_pagination( $echo = true ) {

        $gs_tm_paged = get_query_var('paged') ? get_query_var('paged') : get_query_var('page');
        $gsbig = 999999999; // need an unlikely integer

        $paginate_params = [
            'base' => str_replace( $gsbig, '%#%', esc_url( get_pagenum_link( $gsbig ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, $gs_tm_paged ),
            'total' => $GLOBALS['gs_team_loop']->max_num_pages
        ];
        $paginate_params = (array) apply_filters( 'gs_team_paginate_params', $paginate_params );
        
        $paginate_links = paginate_links( $paginate_params );
        $paginate_links = apply_filters( 'gs_team_paginate_links', $paginate_links );

        $html = '';

        if ( !empty($paginate_links) ) {
            $html = '<div class="gs-roow"><div class="col-md-12 gs-pagination">'.$paginate_links.'</div></div>';
        }
        
        return gs_team_echo_return( $html, $echo );

    }

}

if ( ! function_exists('gs_team_get_shortcodes') ) {

    function gs_team_get_shortcodes() {

        return GS_Team_Shortcode_Builder::get_instance()->_get_shortcodes( null, false, true );

    }

}

if ( ! function_exists('gs_team_select_builder') ) {

    function gs_team_select_builder( $name, $options, $selected = "", $selecttext = "", $class = "", $optionvalue = 'value' ) {

        if ( is_array($options) ) {

            $select_html = "<select name=\"$name\" id=\"$name\" class=\"$class\">";

            if ( $selecttext ) {
                $select_html .= '<option value="">' . $selecttext . '</option>';
            }

            foreach ( $options as $key => $option ) {

                if ( $optionvalue == 'value' ) {
                    $value = $option;
                } else {
                    $value = $key;
                }

                $select_html .= "<option value=\"$value\"";

                if ( $value == $selected ) {
                    $select_html .= ' selected="selected"';
                }

                $select_html .= ">$option</option>\n";

            }

            $select_html .= '</select>';
            echo $select_html;

        }

    }
    
}

function gs_team_add_fs_script( $handler ) {

    $data = [
		'is_paying_or_trial' => wp_validate_boolean( gtm_fs()->is_paying_or_trial() )
	];

    wp_localize_script( $handler, 'gs_team_fs', $data );

}

function gs_team_terms_hierarchically( Array &$cats, Array &$into, $parentId = 0, $exclude_group = [] ) {

    foreach ($cats as $i => $cat) {
        if ( in_array( $cat->term_id, $exclude_group ) ) continue;
        if ($cat->parent == $parentId) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $topCat) {
        $topCat->children = array();
        gs_team_terms_hierarchically( $cats, $topCat->children, $topCat->term_id, $exclude_group );
    }
}

function gs_team_term_walker( $term ) {

    if ( !empty($term->children) ) : ?>
        <ul class="filter-cats--sub">
            <?php foreach ( $term->children as $_term ) :

                $has_child = !empty( $_term->children );

                ?>

                <li class="filter <?php echo $has_child ? 'has-child' : ''; ?>">
                    <a href="javascript:void(0);" data-filter=".<?php echo $_term->slug; ?>">
                        <span><?php echo $_term->name; ?></span>
                        <?php if ( $has_child ) : ?>
                            <span class="sub-arrow fa fa-angle-right"></span>
                        <?php endif; ?>
                    </a>
                    <?php gs_team_term_walker( $_term ); ?>
                </li>

            <?php endforeach; ?>
        </ul>
    <?php endif;

}

if ( gtm_fs()->is_paying_or_trial() ) {

    if ( ! function_exists('gs_team_get_terms_names') ) {

        function gs_team_get_terms_names( $term_name, $separator = ', ' ) {

            global $post;

            $terms = get_the_terms( $post->ID, $term_name );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $terms = implode( $separator, wp_list_pluck( $terms, 'name' ) );
                return $terms;
            }

        }

    }
  
    if ( ! function_exists('gs_team_member_location') ) { 

        function gs_team_member_location( $separator = ', ' ) {
            return gs_team_get_terms_names( 'team_location', $separator );
        }

    }
  
    if ( ! function_exists('gs_team_member_language') ) { 

        function gs_team_member_language( $separator = ', ' ) {
            return gs_team_get_terms_names( 'team_language', $separator );
        }

    }
  
    if ( ! function_exists('gs_team_member_specialty') ) { 

        function gs_team_member_specialty( $separator = ', ' ) {
            return gs_team_get_terms_names( 'team_specialty', $separator );
        }

    }
  
    if ( ! function_exists('gs_team_member_gender') ) { 

        function gs_team_member_gender( $separator = ', ' ) {
            return gs_team_get_terms_names( 'team_gender', $separator );
        }

    }
  
}