<?php
/**
 * GS Team - Layout Filters
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/partials/gs-team-layout-filters.php
 * 
 * @package GS_Team/Templates
 * @version 1.1.0
 */

do_action( 'gs_team_before_filters' );

$filter_col_class = $gs_team_filter_columns == 'three' ? 'col-md-4 col-sm-6' : 'col-md-6 col-sm-6';
$filter_col_class .= ' col-xs-12';

ob_start(); ?>

<?php if ( 'on' ==  $gs_member_srch_by_name ) : ?>
    <?php do_action( 'gs_team_before_search_filter' ); ?>
    <div class="<?php echo $filter_col_class; ?> search-fil-nbox">
        <input type="text" class="search-by-name" placeholder="<?php echo $gs_teamfliter_name; ?>" />
    </div>
<?php endif; ?>

<?php if ( 'on' == $gs_member_filter_by_desig ) : ?>
    <?php do_action( 'gs_team_before_designation_filter' ); ?>
    <div class="<?php echo $filter_col_class; ?> search-fil-nbox">
        <select class="filters-select-designation">
            <option value="*"><?php echo $gs_teamfliter_designation ?></option>
            <?php gs_team_get_meta_values_options( '_gs_des' ); ?>
        </select>
    </div>
<?php endif; ?>

<?php if ( 'on' == $gs_member_filter_by_location ) : ?>
    <?php do_action( 'gs_team_before_location_filter' ); ?>
    <div class="<?php echo $filter_col_class; ?> search-fil-nbox">
        <select class="filters-select-location">
            <option value="*"><?php echo $gs_teamlocation_meta; ?></option>
            <?php gs_team_get_terms_options( 'team_location' ); ?>
        </select>
    </div>
<?php endif; ?>

<?php if ( 'on' == $gs_member_filter_by_language ) : ?>
    <?php do_action( 'gs_team_before_language_filter' ); ?>
    <div class="<?php echo $filter_col_class; ?> search-fil-nbox">
        <select class="filters-select-language">
            <option value="*"><?php echo $gs_teamlanguage_meta; ?></option>
            <?php gs_team_get_terms_options( 'team_language' ); ?>
        </select>
    </div>
<?php endif; ?>

<?php if ( 'on' == $gs_member_filter_by_gender ) : ?>
    <?php do_action( 'gs_team_before_gender_filter' ); ?>
    <div class="<?php echo $filter_col_class; ?> search-fil-nbox">
        <select class="filters-select-gender">
            <option value="*"><?php echo $gs_teamgender_meta; ?></option>
            <?php gs_team_get_terms_options( 'team_gender', true, 'DESC' ); ?>
        </select>
    </div>
<?php endif; ?>

<?php if ( 'on' == $gs_member_filter_by_speciality ) : ?>
    <?php do_action( 'gs_team_before_speciality_filter' ); ?>
    <div class="<?php echo $filter_col_class; ?> search-fil-nbox">
        <select class="filters-select-speciality">
            <option value="*"><?php echo $gs_teamspecialty_meta; ?></option>
            <?php gs_team_get_terms_options( 'team_specialty' ); ?>
        </select>	
    </div>
<?php endif; ?>

<?php $filters_html = ob_get_clean();

if ( !empty(trim($filters_html)) ) : ?>
    <div class="search-filter"><div class="gs-roow"><?php echo $filters_html; ?></div></div>
<?php endif; ?>