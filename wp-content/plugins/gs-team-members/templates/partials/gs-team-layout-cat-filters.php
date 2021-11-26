<?php
/**
 * GS Team - Layout Category Filter
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/partials/gs-team-layout-cat-filters.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.3
 */

do_action( 'gs_team_before_cats_filters' );

$_group = (array) gs_team_string_to_array($group);
$_exclude_group = [];

$terms = get_terms([
    'taxonomy'  => 'team_group',
    'orderby'   => 'name',
    'order'     => 'ASC',
    'hide_empty' => false
]);

if ( !empty($_group) ) {
    $term_ids = wp_list_pluck( $terms, 'term_id' );
    $_exclude_group = array_diff( $term_ids, $_group );
} else {
    $_exclude_group = (array) gs_team_string_to_array($exclude_group);
}

$_terms = [];
gs_team_terms_hierarchically( $terms, $_terms, 0, $_exclude_group ); // it will override $_terms variable.

$classes = 'gs-team-filter-cats';

$with_child_cats = $enable_child_cats == 'on';

if ( $with_child_cats ) $classes .= ' gs-filter--with-child';

if ( empty($_terms) || count($_terms) < 2 ) return;

?>

<ul class="<?php echo $classes; ?>" style="text-align: <?php echo $gs_tm_filter_cat_pos; ?>">
    
    <?php if ( $gs_filter_all_enabled == 'on' ) : ?>
        <li class="filter"><a href=javascript:void(0);" data-filter="*"><?php echo $fitler_all_text; ?></a></li>
    <?php endif; ?>

    <?php foreach ( $_terms as $term ) :
        
        $has_child = !empty( $term->children );

        ?>

        <li class="filter <?php echo $has_child ? 'has-child' : ''; ?>">
            <a href="javascript:void(0);" data-filter=".<?php echo $term->slug; ?>">
                <span><?php echo $term->name; ?></span>
                <?php if ( $has_child && 'on' === $enable_child_cats ) : ?>
                    <span class="sub-arrow fa fa-angle-down"></span>
                <?php endif; ?>
            </a>
            <?php if ( $with_child_cats ) gs_team_term_walker( $term ); ?>
        </li>

    <?php endforeach; ?>
</ul>