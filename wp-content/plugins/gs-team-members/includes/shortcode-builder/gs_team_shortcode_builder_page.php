<div class="app-container">
	<div class="main-container">
		
		<div id="gs-team-shortcode-app">

			<header class="gs-team-header">
				<div class="gs-containeer-f">
					<div class="gs-roow">
						<div class="logo-area col-xs-6 col-sm-5 col-md-3">
							<router-link to="/"><img src="<?php echo GSTEAM_PLUGIN_URI . '/assets/img/logo.svg'; ?>" alt="GS Team Members Logo"></router-link>
						</div>
						<div class="menu-area col-xs-6 col-sm-7 col-md-9 text-right">
							<ul>
								<router-link to="/" tag="li"><a><?php _e( 'Shortcodes', 'gsteam' ); ?></a></router-link>
								<router-link to="/shortcode" tag="li"><a><?php _e( 'Create New', 'gsteam' ); ?></a></router-link>
								<router-link to="/preferences" tag="li"><a><?php _e( 'Preferences', 'gsteam' ); ?></a></router-link>
								<router-link to="/demo-data" tag="li"><a><?php _e( 'Demo Data', 'gsteam' ); ?></a></router-link>
								<router-link to="/bulk-import" tag="li"><a><?php _e( 'Bulk Import', 'gsteam' ); ?></a></router-link>
							</ul>
						</div>
					</div>
				</div>
			</header>

			<div class="gs-team-app-view-container">
				<router-view :key="$route.fullPath"></router-view>
			</div>

		</div>
		
	</div>
</div>