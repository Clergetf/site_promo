<?php
/**
 * GS Team - Layout Popup Details
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/gs-team-layout-popup-details.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.0
 */

$address    = get_post_meta( get_the_id(), '_gs_address', true );
$email      = get_post_meta( get_the_id(), '_gs_email', true );
$land       = get_post_meta( get_the_id(), '_gs_land', true );
$cell       = get_post_meta( get_the_id(), '_gs_cell', true );
$company    = get_post_meta( get_the_id(), '_gs_com', true );
$location   = gs_team_member_location();
$language   = gs_team_member_language();
$specialty  = gs_team_member_specialty();
$gender     = gs_team_member_gender();

?>

<div class="gstm-details">
    
    <?php if ( !empty($company) ) : ?>
        <div class="gs-member-company">
            <span class="levels"><?php echo $gs_teamcom_meta; ?></span>
            <span class="level-info-company"><?php echo $company; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty($address) ) : ?>
        <div class="gs-member-address">
            <span class="levels"><?php echo $gs_teamadd_meta; ?></span>
            <span class="level-info-address"><?php echo $address; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty($land) ) : ?>
        <div class="gs-member-lphon">
            <span class="levels"><?php echo $gs_teamlandphone_meta; ?></span>
            <span class="level-info-lphon"><?php echo $land; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty($cell) ) : ?>
        <div class="gs-member-cphon">
            <span class="levels"><?php echo $gs_teamcellPhone_meta; ?></span>
            <span class="level-info-cphon"><?php echo $cell; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty($email) ) : ?>
        <div class="gs-member-email">
            <span class="levels"><?php echo $gs_teamemail_meta; ?></span>
            <span class="level-info-email"><?php echo $email; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty( $location )) : ?>
        <div class="gs-member-loc">
            <span class="levels"><?php echo $gs_teamlocation_meta; ?></span>
            <span class="level-info-loc"><?php echo $location; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty( $language ) )  : ?>
        <div class="gs-member-lang">
            <span class="levels"><?php echo $gs_teamlanguage_meta; ?></span>
            <span class="level-info-lang"><?php echo $language; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty( $specialty ) )  : ?>
        <div class="gs-member-specialty">
            <span class="levels"><?php echo $gs_teamspecialty_meta; ?> </span>
            <span class="level-info-specialty"><?php echo $specialty; ?></span>
        </div>
    <?php endif; ?>

    <?php if ( !empty( $gender ) ) : ?>
        <div class="gs-member-gender">
            <span class="levels"><?php echo $gs_teamgender_meta; ?></span>
            <span class="level-info-gender"><?php echo $gender; ?></span>
        </div>
    <?php endif; ?>
    
</div>

<?php do_action( 'gs_team_after_member_details_popup' ); ?>