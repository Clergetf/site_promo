<?php
/**
 * GS Team - Layout Skills
 * @author GS Plugins <hello@gsplugins.com>
 * 
 * This template can be overridden by copying it to yourtheme/gs-team/partials/gs-team-layout-skills.php
 * 
 * @package GS_Team/Templates
 * @version 1.0.0
 */

$member_id = get_the_id();

$skills = get_post_meta( $member_id, 'gs_skill', true );
$skills = apply_filters( 'gs_team_member_skills', $skills, $member_id );

$is_skills_title = empty($is_skills_title) ? false : wp_validate_boolean($is_skills_title);
$is_skills_title = apply_filters( 'gs_team_member_is_skills_title', $is_skills_title, $skills, $member_id );

if ( !empty($skills) ) : ?>

    <div class="member-skill">

        <?php if ( $is_skills_title ) : ?>
            <h3><?php _e('Skills', 'gsteam'); ?></h3>
        <?php endif; ?>

        <?php foreach( $skills as $skill ) : ?>
            
            <?php if ( !empty($skill['percent']) ) : ?>

                <span class="progressText">
                    <b><?php echo $skill['skill']; ?></b>
                </span>

                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $skill['percent']; ?>%"></div>
                    <span class="progress-completed"><?php echo $skill['percent']; ?>%</span>
                </div>
                
            <?php endif; ?>

        <?php endforeach; ?>

    </div>

    <?php do_action( 'gs_team_after_member_skills' ); ?>

<?php endif; ?>