<?php

/**
 *
 * @package   GS_Team
 * @author    GS Plugins <hello@gsplugins.com>
 * @license   GPL-2.0+
 * @link      https://www.gsplugins.com
 * @copyright 2016 GS Plugins
 *
 * @wordpress-plugin
 * Plugin Name:		GS Team Members
 * Plugin URI:		https://www.gsplugins.com/wordpress-plugins
 * Description:     Best Responsive Team member plugin for Wordpress to showcase member Image, Name, Designation, Social connectivity links. Display anywhere at your site using generated shortcode like [gsteam id=1] & widgets. Check more shortcode examples and documentation at <a href="http://team.gsplugins.com">GS Team PRO Demos & Docs</a>
 * Version:         1.10.18
 * Author:       	GS Plugins
 * Author URI:      https://www.gsplugins.com
 * Text Domain:     gsteam
 * Domain Path:     /languages
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 */
if ( !defined( 'GSTEAM_HACK_MSG' ) ) {
    define( 'GSTEAM_HACK_MSG', __( 'Sorry cowboy! This is not your place', 'gsteam' ) );
}
/**
 * Protect direct access
 */
if ( !defined( 'ABSPATH' ) ) {
    die( GSTEAM_HACK_MSG );
}
/**
 * Defining constants
 */
if ( !defined( 'GSTEAM_VERSION' ) ) {
    define( 'GSTEAM_VERSION', '1.10.18' );
}
if ( !defined( 'GSTEAM_MENU_POSITION' ) ) {
    define( 'GSTEAM_MENU_POSITION', 39 );
}
if ( !defined( 'GSTEAM_PLUGIN_DIR' ) ) {
    define( 'GSTEAM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'GSTEAM_PLUGIN_URI' ) ) {
    define( 'GSTEAM_PLUGIN_URI', plugins_url( '', __FILE__ ) );
}

if ( !function_exists( 'gtm_fs' ) ) {
    // Create a helper function for easy SDK access.
    function gtm_fs()
    {
        global  $gtm_fs ;
        
        if ( !isset( $gtm_fs ) ) {
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $gtm_fs = fs_dynamic_init( array(
                'id'              => '1851',
                'slug'            => 'gs-team-members',
                'type'            => 'plugin',
                'public_key'      => 'pk_e88759b9ba026403ad505a5877eac',
                'is_premium'      => false,
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'has_affiliation' => 'selected',
                'trial'           => array(
                'days'               => 14,
                'is_require_payment' => true,
            ),
                'menu'            => array(
                'slug'    => 'edit.php?post_type=gs_team',
                'support' => false,
            ),
                'is_live'         => true,
            ) );
        }
        
        return $gtm_fs;
    }
    
    // Init Freemius.
    gtm_fs();
    // Signal that SDK was initiated.
    do_action( 'gtm_fs_loaded' );
}

$status = get_option( 'GS_TEAM_LICENSE_STATUS' );
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-functions.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-cpt.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-meta-fields.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-column.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-shortcode.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-template-loader.php';
// Shortcode builder
require_once GSTEAM_PLUGIN_DIR . 'includes/shortcode-builder/gs-team-shortcode-builder.php';
// Dummy data, make sure it is loaded after 'gs-team-shortcode-builder.php'
require_once GSTEAM_PLUGIN_DIR . 'includes/demo-data/gs-team-dummy-data.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-sortable.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-term-sort.php';
if ( gtm_fs()->is_paying_or_trial() ) {
    require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-widgets.php';
}
if ( gtm_fs()->is_not_paying() && !gtm_fs()->is_trial() ) {
    require_once GSTEAM_PLUGIN_DIR . 'includes/gs-pages/gs-team-trial.php';
}
require_once GSTEAM_PLUGIN_DIR . 'includes/integrations/gs-team-integrations.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-scripts.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-pages/gs-team-other-plugins.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-pages/gs-team-help.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/bulk-importer/gs-team-bulk-importer.php';
require_once GSTEAM_PLUGIN_DIR . 'includes/gs-team-disable-notices.php';

if ( !function_exists( 'gs_team_change_image_box' ) ) {
    function gs_team_change_image_box()
    {
        remove_meta_box( 'postimagediv', 'gs_team', 'side' );
        add_meta_box(
            'postimagediv',
            __( 'Team Member Image' ),
            'post_thumbnail_meta_box',
            'gs_team',
            'side',
            'low'
        );
    }
    
    add_action( 'do_meta_boxes', 'gs_team_change_image_box' );
}

function gs_team_img_size_note( $content )
{
    global  $post_type, $post ;
    if ( $post_type == 'gs_team' ) {
        if ( !has_post_thumbnail( $post->ID ) ) {
            $content .= '<p>' . __( 'Recommended image size 400px X 400px for perfect view on various devices.', 'gsteam' ) . '</p>';
        }
    }
    return $content;
}

add_filter( 'admin_post_thumbnail_html', 'gs_team_img_size_note' );
function gs_team_display_acf_fields()
{
    include GS_Team_Template_Loader::locate_template( 'partials/gs-team-layout-acf-fields.php' );
}

function gs_team_load_acf_fields( $show = 'off', $position = 'after_skills' )
{
    if ( $show != 'on' ) {
        return;
    }
    switch ( $position ) {
        case 'after_meta_details':
            $action = 'gs_team_after_member_details_popup';
            break;
        case 'after_description':
            $action = 'gs_team_after_member_details';
            break;
        default:
            $action = 'gs_team_after_member_skills';
    }
    add_action( $action, 'gs_team_display_acf_fields' );
}


if ( gtm_fs()->is_paying_or_trial() ) {
    if ( !function_exists( 'gs_team_single_template' ) ) {
        function gs_team_single_template( $single_team_template )
        {
            global  $post ;
            
            if ( $post->post_type == 'gs_team' ) {
                $show_acf_fields = gs_team_getoption( 'show_acf_fields', 'off' );
                $acf_fields_position = gs_team_getoption( 'acf_fields_position', 'after_skills' );
                gs_team_load_acf_fields( $show_acf_fields, $acf_fields_position );
                $single_team_template = GS_Team_Template_Loader::locate_template( 'gs-team-template-single.php' );
            }
            
            return $single_team_template;
        }
    
    }
    add_filter( 'single_template', 'gs_team_single_template' );
    if ( !function_exists( 'gs_team_archive_template' ) ) {
        function gs_team_archive_template( $archive_template )
        {
            if ( is_post_type_archive( 'gs_team' ) ) {
                $archive_template = GS_Team_Template_Loader::locate_template( 'gs-team-template-archive.php' );
            }
            if ( is_tax( [
                'team_group',
                'team_gender',
                'team_location',
                'team_language',
                'team_specialty'
            ] ) ) {
                $archive_template = GS_Team_Template_Loader::locate_template( 'gs-team-template-archive.php' );
            }
            return $archive_template;
        }
    
    }
    add_filter( 'archive_template', 'gs_team_archive_template' );
}

function gs_team_pro_link( $gsTeam_links )
{
    $gsTeam_links[] = '<a href="https://www.gsplugins.com/wordpress-plugins" target="_blank">GS Plugins</a>';
    return $gsTeam_links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'gs_team_pro_link' );
function gs_team_plugin_update_version()
{
    $old_version = get_option( 'gs_team_plugin_version' );
    if ( GSTEAM_VERSION === $old_version ) {
        return;
    }
    update_option( 'gs_team_plugin_version', GSTEAM_VERSION );
    GS_Team_Shortcode_Builder::get_instance()->maybe_upgrade_data( $old_version );
}

add_action( 'init', 'gs_team_plugin_update_version', 0 );
// Plugin On Activation
function gs_team_plugin_activate()
{
    GS_Team();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'gs_team_plugin_activate' );
// Plugin On Loaded
function gs_team_plugin_loaded()
{
    GS_Team_Shortcode_Builder::get_instance()->maybe_create_shortcodes_table();
}

add_action( 'plugins_loaded', 'gs_team_plugin_loaded' );
// Reset Permalinks
function GS_flush_rewrite_rules()
{
    
    if ( !get_option( 'GS_Team_plugin_permalinks_flushed' ) ) {
        flush_rewrite_rules();
        update_option( 'GS_Team_plugin_permalinks_flushed', 1 );
    }

}

add_action( 'init', 'GS_flush_rewrite_rules' );
// Excerpt Length
function gsteam_excerpt_length( $length )
{
    global  $post ;
    if ( $post->post_type == 'gs_team' ) {
        $length = 150;
    }
    return $length;
}

add_filter( 'excerpt_length', 'gsteam_excerpt_length' );
// Load translations
function gs_team_i18n()
{
    load_plugin_textdomain( 'gsteam', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'gs_team_i18n' );