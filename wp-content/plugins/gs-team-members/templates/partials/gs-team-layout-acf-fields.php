<?php
/**
 * GS Team - Layout ACF Fields
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/partials/gs-team-layout-acf-fields.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.1
 */

$field_groups = acf_get_field_groups([
    'post_id'	=> get_the_ID(),
    'post_type'	=> get_post_type()
]);

if ( empty($field_groups) ) return;

foreach( $field_groups as $field_group ) {

    $title = $field_group['title'];

    $fields = array_map( function($field) {
        return [
            'name' => $field['name'],
            'label' => $field['label'],
            'value' => get_post_meta( get_the_ID(), $field['name'], true )
        ];
    }, (array) acf_get_fields($field_group) );

    if ( empty($fields) ) continue;

    $fields_values = array_filter( wp_list_pluck( $fields, 'value' ) );

    if ( empty($fields_values) ) continue;

    ?>

    <div class="gs-team--acf_group">

        <?php if ( !empty($title) ) printf('<h3 class="gs-team--acf_group-title">%s</h3>', sanitize_text_field( $title )); ?>
        
        <div class="gs-team--acf_group-fields gstm-details">
            
            <?php foreach ($fields as $field) : ?>
                
                <div class="gs-member-<?php echo $field['name']; ?>">
                    <span class="levels"><?php echo $field['label']; ?></span>
                    <span class="level-info-company"><?php echo $field['value']; ?></span>
                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <?php
    
}