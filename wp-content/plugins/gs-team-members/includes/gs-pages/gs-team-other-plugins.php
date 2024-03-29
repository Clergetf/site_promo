<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

if ( ! class_exists( 'GS_Team_Other_Plugins' ) ) {
    class GS_Team_Other_Plugins {
        
        private static $_instance;
        
        public function __construct() {

            add_action( 'admin_menu', array( $this, 'gs_other_plugins' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'scripts') );

        }
        
        public static function get_instance() {

            if ( ! self::$_instance instanceof GS_Team_Other_Plugins ) {
                self::$_instance = new GS_Team_Other_Plugins();
            }
            
            return self::$_instance;
        }

        public function scripts( $hook ) {

            if ( ! in_array( $hook, ['gs_team_page_gs-team-help', 'gs_team_page_gs-plugins-free'] ) ) return;

            wp_enqueue_style( 'gs-plugins-free', GSTEAM_PLUGIN_URI . '/assets/admin/css/gs-plugins-free.min.css', [], GSTEAM_VERSION, 'all' );

        }
                
        public function gs_other_plugins() {

            // Pro Plugins
            add_submenu_page( 
                'edit.php?post_type=gs_team', 
                'GS Plugins', 
                'GS Plugins', 
                'manage_options', 
                'gstm-add-ons', 
                array( $this, 'gsteam_display_pro_plugins' )
            );

            // Free Plugins
            add_submenu_page( 
                'edit.php?post_type=gs_team', 
                'GS Plugins', 
                'GS Plugins Lite', 
                'manage_options', 
                'gs-plugins-free', 
                array( $this, 'gsteam_display_free_plugins' )
            );

        }
        
        public function gsteam_display_pro_plugins() {

			// Load from cache
			$promo_content = get_transient( 'gsteam-pro-plugins' );

			// If not found in cache then load from remote server and cache it for 1 week
			if ( $promo_content === false ) {

				$protocol = is_ssl() ? 'https' : 'http';
				$promo_content = wp_remote_get( $protocol . '://www.gsplugins.com/gs_plugins_list/index.php' );
	
				if ( !empty($promo_content) && $promo_content['response']['code'] == 200 ) {
					$promo_content = json_encode($promo_content['body']);
					set_transient( 'gsteam-pro-plugins', $promo_content, WEEK_IN_SECONDS );
				}

			}

			// Show the data
			echo json_decode( $promo_content );

        }

        public function gsteam_display_free_plugins() {

        ?>

        <div class="row">
	
			<div class="panel panel-primary panel-default content-panel">
				<div class="panel-body">
				<table class="form-table2">
					<tr class="radio-span" style="border-bottom:none;">
						<td>
						<?php
						include( ABSPATH . "wp-admin/includes/plugin-install.php" );
						global $tabs, $tab, $paged, $type, $term;
						$tabs 		= array();
						$tab 		= "search";
						$per_page 	= 20;
						$args 		= array
						(
							"author" 	=> "samdani",
							"page" 		=> $paged,
							"per_page" 	=> $per_page,
							"fields" 	=> array( "last_updated" => true, "downloaded" => true, "icons" => true ),
							"locale" 	=> get_locale(),
						);
						$arges 	= apply_filters( "install_plugins_table_api_args_$tab", $args );
						$api 	= plugins_api( "query_plugins", $arges );
						$item 	= $api->plugins;
						if ( !function_exists("wp_star_rating") ) {
							function wp_star_rating( $args = array() ) {
								$defaults = array(
									'rating' 	=> 0,
									'type' 		=> 'rating',
									'number' 	=> 0,
								);
								$r = wp_parse_args( $args, $defaults );
						
								// Non-english decimal places when the $rating is coming from a string
								$rating = str_replace( ',', '.', $r['rating'] );
						
								// Convert Percentage to star rating, 0..5 in .5 increments
								if ( 'percent' == $r['type'] ) {
									$rating = round( $rating / 10, 0 ) / 2;
								}
						
								// Calculate the number of each type of star needed
								$full_stars = floor( $rating );
								$half_stars = ceil( $rating - $full_stars );
								$empty_stars = 5 - $full_stars - $half_stars;
						
								if ( $r['number'] ) {
									/* translators: 1: The rating, 2: The number of ratings */
									$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'] );
									$title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
								} else {
									/* translators: 1: The rating */
									$title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
								}
						
								echo '<div class="star-rating" title="' . esc_attr( $title ) . '">';
								echo '<span class="screen-reader-text">' . $title . '</span>';
								echo str_repeat( '<div class="star star-full"></div>', $full_stars );
								echo str_repeat( '<div class="star star-half"></div>', $half_stars );
								echo str_repeat( '<div class="star star-empty"></div>', $empty_stars);
								echo '</div>';
							}
						}
						?>
						<form id="gsp-recommended" class="layout-form">
							<div id="poststuff">
								<div id="post-body" class="metabox-holder">
									<div id="postbox-container-2" class="postbox-container">
										<div id="advanced" class="meta-box-sortables">
											<div id="gs_plugins_free" class="postbox" >
												<div class="handlediv" data-target="ux_recommendation" title="Click to toggle" data-toggle="collapse"><br></div>
												<h2 class="hndle">GS Plugins - recommended more free plugins</h3>
												<div class="inside">
													<div id="gsp_recommendation" class="gsp-recommendation">
														
														<div class="fluid-layout">
															<div class="layout-span12">
																<div class="wp-list-table plugin-install">
																	<div id="the-list">

																		<?php foreach ((array) $item as $plugin) {
																			
																			if (is_object( $plugin)) {
																				$plugin = (array) $plugin;
																			}
																			if ( !empty($plugin["icons"]["svg"]) ) {
																				$plugin_icon_url = $plugin["icons"]["svg"];
																			} elseif ( !empty( $plugin["icons"]["2x"]) ) {
																				$plugin_icon_url = $plugin["icons"]["2x"];
																			} elseif (!empty( $plugin["icons"]["1x"])) {
																				$plugin_icon_url = $plugin["icons"]["1x"];
																			} else {
																				$plugin_icon_url = $plugin["icons"]["default"];
																			}

																			$plugins_allowedtags = array(
																				"a" => array( "href" => array(),"title" => array(), "target" => array() ),
																				"abbr" => array( "title" => array() ),"acronym" => array( "title" => array() ),
																				"code" => array(), "pre" => array(), "em" => array(),"strong" => array(),
																				"ul" => array(), "ol" => array(), "li" => array(), "p" => array(), "br" => array()
																			);

																			$title = wp_kses($plugin["name"], $plugins_allowedtags);
																			$description = strip_tags($plugin["short_description"]);
																			$author = wp_kses($plugin["author"], $plugins_allowedtags);
																			$version = wp_kses($plugin["version"], $plugins_allowedtags);
																			$name = strip_tags( $title . " " . $version );
																			$details_link   = self_admin_url( "plugin-install.php?tab=plugin-information&amp;plugin=" . $plugin["slug"] .
																			"&amp;TB_iframe=true&amp;width=600&amp;height=550" );
																			
																			/* translators: 1: Plugin name and version. */
																			$action_links[] = '<a href="' . esc_url( $details_link ) . '" class="thickbox" aria-label="' . esc_attr( sprintf("More information about %s", $name ) ) . '" data-title="' . esc_attr( $name ) . '">' . __( 'More Details' ) . '</a>';
																			$action_links = array();
																			if (current_user_can( "install_plugins") || current_user_can("update_plugins")) {
																				$status = install_plugin_install_status( $plugin );
																				switch ($status["status"]) {
																					case "install":
																						if ( $status["url"] )
																						{
																							/* translators: 1: Plugin name and version. */
																							$action_links[] = '<a class="install-now button" href="' . $status['url'] . '" aria-label="' . esc_attr( sprintf("Install %s now", $name ) ) . '">' . __( 'Install Now' ) . '</a>';
																						}
																					break;
																					case "update_available":
																						if ($status["url"])
																						{
																							/* translators: 1: Plugin name and version */
																							$action_links[] = '<a class="button" href="' . $status['url'] . '" aria-label="' . esc_attr( sprintf( "Update %s now", $name ) ) . '">' . __( 'Update Now' ) . '</a>';
																						}
																					break;
																					case "latest_installed":
																					case "newer_installed":
																						$action_links[] = '<span class="button button-disabled" title="' . esc_attr__( "This plugin is already installed and is up to date" ) . ' ">' . _x( 'Installed', 'plugin' ) . '</span>';
																					break;
																				}
																			}
																			?>
																			<div class="plugin-div plugin-div-settings">
																				<div class="plugin-div-top plugin-div-settings-top">
																					<div class="plugin-div-inner-content">
																						<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox plugin-icon plugin-icon-custom">
																							<img class="custom_icon" src="<?php echo esc_attr( $plugin_icon_url ) ?>" />
																						</a>
																						<div class="name column-name">
																							<h4>
																								<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox"><?php echo $title; ?></a>
																							</h4>
																						</div>
																						<div class="desc column-description">
																							<p><?php echo $description; ?></p>
																							<p class="authors"><cite>By <?php echo $author;?></cite></p>
																						</div>
																					</div>
																					<div class="action-links">
																						<ul class="plugin-action-buttons-custom">
																							<li><?php if ( $action_links ) echo implode("</li><li>", $action_links); ?></li>
																						</ul>
																					</div>
																				</div>
																				<div class="plugin-card-bottom plugin-card-bottom_settings">
																					<div class="vers column-rating">
																						<?php wp_star_rating( array( "rating" => $plugin["rating"], "type" => "percent", "number" => $plugin["num_ratings"] ) ); ?>
																						<span class="num-ratings">
																							(<?php echo number_format_i18n( $plugin["num_ratings"] ); ?>)
																						</span>
																					</div>
																					<div class="column-updated">
																						<strong><?php _e("Last Updated:"); ?></strong> <span title="<?php echo esc_attr($plugin["last_updated"]); ?>">
																							<?php printf("%s ago", human_time_diff(strtotime($plugin["last_updated"]))); ?>
																						</span>
																					</div>
																					<div class="column-downloaded">
																						<?php echo sprintf( _n("%s download", "%s downloads", $plugin["downloaded"]), number_format_i18n($plugin["downloaded"])); ?>
																					</div>
																					<div class="column-compatibility">
																						<?php
																						if ( !empty($plugin["tested"]) && version_compare(substr($GLOBALS["wp_version"], 0, strlen($plugin["tested"])), $plugin["tested"], ">")) {
																							echo '<span class="compatibility-untested">' . __( "<strong>Untested</strong> with your version of WordPress" ) . '</span>';
																						} elseif (!empty($plugin["requires"]) && version_compare(substr($GLOBALS["wp_version"], 0, strlen($plugin["requires"])), $plugin["requires"], "<"))  {
																							echo '<span class="compatibility-incompatible">' . __("Incompatible with your version of WordPress") . '</span>';
																						} else {
																							echo '<span class="compatibility-compatible">' . __("Compatible with your version of WordPress") . '</span>';
																						}
																						?>
																					</div>
																				</div>
																			</div>
																		<?php
																		}
																		?>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
						</td>
					</tr>
				</table>
				</div>
			</div>
			
		</div> <!-- /row -->
    
        <?php

        }

    }
}

$tmev = GS_Team_Other_Plugins::get_instance();