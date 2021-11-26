<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSTEAM_HACK_MSG );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
if ( ! function_exists('add_gs_team_metaboxes') ) {
	
	function add_gs_team_metaboxes() {
		add_meta_box('gsTeamSection', 'Member\'s Additioinal Info' ,'gs_team_cmb_cb', 'gs_team', 'normal', 'high');
		add_meta_box('gsTeamSectionSocial', 'Member\'s Social Links' ,'gs_team_cmb_social_cb', 'gs_team', 'normal', 'high');
		add_meta_box('gsTeamSectionSkill', 'Member\'s Skills' ,'gs_team_cmb_skill_cb', 'gs_team', 'normal', 'high');
	}

}
add_action( 'add_meta_boxes', 'add_gs_team_metaboxes' );


function gs_image_uploader_field( $name, $value = '' ) {

	$image = ' button">Upload Image';
	$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
	$display = 'none'; // display state ot the "Remove image" button
 
	if ( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
 
		// $image_attributes[0] - image URL
		// $image_attributes[1] - image width
		// $image_attributes[2] - image height
 
		$image = '"><img src="' . $image_attributes[0] . '" />';
		$display = 'inline-block';
 
	} 
 
	return '
	<div class="form-group">
		<label for="gsDes">Flip Image:</label>
		<div class="gs-image-uploader-area">
			<a href="#" class="gs_upload_image_button' . $image . '</a>
			<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
			<a href="#" class="gs_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
		</div>
	</div>';
}

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
if ( ! function_exists('gs_team_cmb_cb') ) {
	function gs_team_cmb_cb( $post ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'gs_team_nonce_name', 'gs_team_cmb_token' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$gs_des = get_post_meta( $post->ID, '_gs_des', true );
		$gs_com = get_post_meta( $post->ID, '_gs_com', true );
		$gs_land = get_post_meta( $post->ID, '_gs_land', true );
		$gs_cell = get_post_meta( $post->ID, '_gs_cell', true );
		$gs_email = get_post_meta( $post->ID, '_gs_email', true );
		$gs_address = get_post_meta( $post->ID, '_gs_address', true );
		$gs_ribon = get_post_meta( $post->ID, '_gs_ribon', true );
		$gs_vcard = get_post_meta( $post->ID, '_gs_vcard', true );
		
		?>

		<div class="gs_team-metafields">

			<div style="height: 20px;"></div>

			<div class="form-group">
				<label for="gsDes"><?php _e( 'Designation', 'gsteam' ); ?></label>
				<input type="text" id="gsDes" class="form-control" name="gs_des" value="<?php echo isset($gs_des) ? esc_attr($gs_des) : ''; ?>">
			</div>

			<div class="gs-team-pro-field">

				<div class="form-group">
					<label for="gsCom"><?php _e( 'Company', 'gsteam' ); ?></label>
					<input type="text" id="gsCom" class="form-control" name="gs_com" value="<?php echo isset($gs_com) ? esc_attr($gs_com) : ''; ?>">
				</div>

				<div class="form-group">
					<label for="gsLand"><?php _e( 'Land Phone', 'gsteam' ); ?></label>
					<input type="text" id="gsLand" class="form-control" name="gs_land" value="<?php echo isset($gs_land) ? esc_attr($gs_land) : ''; ?>">
				</div>

				<div class="form-group">
					<label for="gsCell"><?php _e( 'Cell Phone', 'gsteam' ); ?></label>
					<input type="text" id="gsCell" class="form-control" name="gs_cell" value="<?php echo isset($gs_cell) ? esc_attr($gs_cell) : ''; ?>">
				</div>

				<div class="form-group">
					<label for="gsEm"><?php _e( 'Email', 'gsteam' ); ?></label>
					<input type="text" id="gsEm" class="form-control" name="gs_email" value="<?php echo isset($gs_email) ? esc_attr($gs_email) : ''; ?>">
				</div>

				<div class="form-group">
					<label for="gsAdd"><?php _e( 'Address', 'gsteam' ); ?></label>
					<input type="text" id="gsAdd" class="form-control" name="gs_address" value="<?php echo isset($gs_address) ? esc_attr($gs_address) : ''; ?>">
				</div>
			
				<div class="form-group">
					<label for="gsribon"><?php _e( 'Ribbon', 'gsteam' ); ?></label>
					<input type="text" id="gsribon" class="form-control" name="gs_ribon" value="<?php echo isset($gs_ribon) ? esc_attr($gs_ribon) : ''; ?>">
				</div>
			
				<div class="form-group">
					<label for="gsvcard"><?php _e( 'vCard', 'gsteam' ); ?></label>
					<input type="url" id="gsvcard" class="form-control" name="gs_vcard" placeholder="<?php _e( 'Add any external or internal link', 'gsteam' ); ?>" value="<?php echo isset($gs_vcard) ? esc_attr($gs_vcard) : ''; ?>">
				</div>

				<?php
					$meta_key = 'second_featured_img';
					echo gs_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
				?>

			</div>

		</div>

		<?php
	}
}
if ( ! function_exists('gs_team_cmb_social_cb') ) {
	function gs_team_cmb_social_cb( $post ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'gs_team_nonce_name', 'gs_team_cmb_token' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$gs_social  = get_post_meta($post->ID, 'gs_social', true);
 		$social_icons  = array('envelope', 'link', 'google-plus','facebook', 'instagram', 'whatsapp', 'twitter', 'youtube', 'vimeo-square', 'flickr', 'dribbble', 'behance', 'dropbox', 'wordpress',  'tumblr', 'skype', 'linkedin', 'stack-overflow','pinterest', 'foursquare','github','xing', 'stumbleupon',  'delicious', 'lastfm','hacker-news', 'reddit', 'soundcloud', 'yahoo', 'trello','steam', 'deviantart', 'twitch', 'feed','renren', 'vk', 'vine', 'spotify', 'digg', 'slideshare');
		
		?>

		<div class="gs_team-metafields">

			<div style="height: 20px;"></div>

			<div class="gs-team-social--section">
				
				<div class="member-details-section">
	
					<table id="repeatable-fieldset-two" width="100%" class="gstm-sorable-table">
						<thead>
							<tr>
								<td width="3%"></td>
								<td width="45%"><?php _e( 'Icon', 'gsteam' ); ?></td>
								<td width="42%"><?php _e( 'Link', 'gsteam' ); ?></td>
								<td width="10%"></td>
							</tr>
						</thead>
						<tbody>
							
							<?php if ( $gs_social ) : foreach ( $gs_social as $field ) : ?>
	
								<tr>
									<td><i class="fa fa-arrows" aria-hidden="true"></i></td>
									<td>
										<?php gs_team_select_builder('gstm-team-icon[]', $social_icons, $field['icon'], __('Select icon', 'gsteam'), 'widefat gstm-icon-select'); ?>
									</td>
									<td><input type="text" placeholder="<?php _e('ex: https://twitter.com/gsplugins', 'gsteam'); ?>" class="widefat" name="gstm-team-link[]" value="<?php if(isset($field['link'])) echo esc_attr( $field['link'] ); ?>"/></td>
									<td><a class="button remove-row" href="#"><?php _e('Remove', 'gsteam'); ?></a></td>
								</tr>
	
							<?php endforeach; else: ?> 
	
								<tr>
									<td><i class="fa fa-arrows" aria-hidden="true"></i></td>
									<td>
										<?php gs_team_select_builder('gstm-team-icon[]', $social_icons, '', __('Select icon', 'gsteam'), 'widefat gstm-icon-select'); ?>
									</td>
									<td><input type="text" placeholder="<?php _e('ex: https://twitter.com/gsplugins', 'gsteam'); ?>" class="widefat" name="gstm-team-link[]" value=""/></td>
									<td><a class="button remove-row" href="#"><?php _e('Remove', 'gsteam'); ?></a></td>
								</tr>
	
							<?php endif; ?>
	
							<tr class="empty-row screen-reader-text">
								<td><i class="fa fa-arrows" aria-hidden="true"></i></td>
								<td>
									<?php gs_team_select_builder('gstm-team-icon[]', $social_icons, '', __('Select icon', 'gsteam'), 'widefat'); ?>
								</td>
								<td><input type="text" placeholder="<?php _e('ex: https://twitter.com/gsplugins', 'gsteam'); ?>" class="widefat" name="gstm-team-link[]" value=""/></td>
								<td><a class="button remove-row" href="#"><?php _e('Remove', 'gsteam'); ?></a></td>
							</tr>
	
						</tbody>
					</table>
	
					<p><a class="button gstm-add-row" href="#" data-table="repeatable-fieldset-two"><?php _e('Add Row', 'gsteam'); ?></a></p>
	
				</div>

			</div>

		</div>

		<?php
	}
}
if ( ! function_exists('gs_team_cmb_skill_cb') ) {
	function gs_team_cmb_skill_cb( $post ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'gs_team_nonce_name', 'gs_team_cmb_token' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$gs_skill = get_post_meta($post->ID, 'gs_skill', true);
		
		?>

		<div class="gs_team-metafields">

			<div style="height: 20px;"></div>

			<div class="gs-team-skills--section gs-team-pro-field">
	
				<div class="member-details-section">
					<table id="repeatable-fieldset-skill" width="100%" class="gstm-sorable-table">
						<thead>
							<tr>
								<td width="3%"></td>
								<td width="45%"><?php _e( 'Title', 'gsteam' );?></td>
								<td width="42%"><?php _e( 'Percent', 'gsteam' );?></td>
								<td width="10%"></td>
							</tr>
						</thead>
						<tbody>
	
							<?php if($gs_skill) : foreach ( $gs_skill as $field ) : ?>
							
								<tr>
									<td><i class="fa fa-arrows" aria-hidden="true"></i></td>
									<td>
										<input type="text" placeholder="html" class="widefat" name="gstm-skill-name[]" value="<?php if(isset($field['skill'])) echo esc_attr( $field['skill'] ); ?>"/>
									</td>
									<td><input type="text" placeholder="85" class="widefat" name="gstm-skill-percent[]" value="<?php if(isset($field['percent'])) echo esc_attr( $field['percent'] ); ?>"/></td>
									<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'gsteam' ); ?></a></td>
								</tr>
	
							<?php endforeach; else: ?> 
	
								<tr>
									<td><i class="fa fa-arrows" aria-hidden="true"></i></td>
									<td>
										<input type="text" placeholder="html" class="widefat" name="gstm-skill-name[]" value="<?php if(isset($field['skill'])) echo esc_attr( $field['skill'] ); ?>"/>
									</td>
									<td><input type="text" placeholder="85" class="widefat" name="gstm-skill-percent[]" value="<?php if(isset($field['percent'])) echo esc_attr( $field['percent'] ); ?>"/></td>
									<td><a class="button remove-row" href="#"><?php _e( 'Remove', 'gsteam' );?></a></td>
								</tr>
	
							<?php endif; ?>	
	
							<tr class="empty-skill screen-reader-text">
								<td><i class="fa fa-arrows" aria-hidden="true"></i></td>
								<td>
									<input type="text" placeholder="<?php _e('ex: Wordpress', 'gsteam'); ?>" class="widefat" name="gstm-skill-name[]" value="<?php if(isset($field['link'])) echo esc_attr( $field['link'] ); ?>"/>
								</td>
								<td><input type="text" placeholder="<?php _e('ex: 90', 'gsteam'); ?>" class="widefat" name="gstm-skill-percent[]" value=""/></td>
								<td><a class="button remove-row" href="#"><?php _e('Remove', 'gsteam'); ?></a></td>
							</tr>
	
						</tbody>
					</table>
	
					<p><a class="button gstm-add-skill" href="#" data-table="repeatable-fieldset-skill"><?php _e('Add Row', 'gsteam'); ?></a></p>
	
				</div>

			</div>


		</div>

		<?php
	}
}


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */

if ( ! function_exists('save_gs_team_metadata') ) {

	function save_gs_team_metadata( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['gs_team_cmb_token'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['gs_team_cmb_token'], 'gs_team_nonce_name' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		$team_repeater = array(
       
	        'gs_social' => array(
	            'icon' => 'gstm-team-icon',
	            'link' => 'gstm-team-link'
	        )
	    );

		foreach ( $team_repeater as $key => $value ) {

			$olddata = get_post_meta($post_id, $key, true);

			$newdata = $item = array();

			foreach ( $value as $k => $v ) {
				$item[$k] = $_POST[$v];
			}

			$count = count( reset($item) );

			for ( $i = 0; $i < $count; $i++ ) {
				foreach ($value as $k => $v) {
					if ($item[$k][$i] != '') {
						$newdata[$i][$k] = stripslashes(strip_tags($item[$k][$i]));
					}
				}
			}

			if ( !empty($newdata) && $newdata != $olddata ) {
				update_post_meta( $post_id, $key, $newdata );
			} elseif ( empty($newdata) && $olddata ) {
				delete_post_meta($post_id, $key, $olddata);
			}
			
		}
				
		if ( gtm_fs()->is_paying_or_trial() ) { 

			$team_skill = array(
				'gs_skill' => array(
					'skill' => 'gstm-skill-name',
					'percent' => 'gstm-skill-percent'
				)
			);
			
			foreach ($team_skill as $key => $value) {

				$olddata = get_post_meta($post_id, $key, true);
				$newdata = $item = array();

				foreach ($value as $k => $v) {
					$item[$k] = $_POST[$v];
				}

				$count = count(reset($item));

				for ( $i = 0; $i < $count; $i++ ) {
					foreach ($value as $k => $v) {
						if ($item[$k][$i] != '') {
							$newdata[$i][$k] = stripslashes(strip_tags($item[$k][$i]));
						}
					}
				}

				if (!empty($newdata) && $newdata != $olddata) {
					update_post_meta($post_id, $key, $newdata);
				} elseif (empty($newdata) && $olddata) {
					delete_post_meta($post_id, $key, $olddata);
				}
					
			}
			
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! isset( $_POST['gs_des'] ) ) {
			return;
		}	

		// Sanitize user input.
		$gs_des_data = sanitize_text_field( $_POST['gs_des'] );
		update_post_meta( $post_id, '_gs_des', $gs_des_data );

		if ( gtm_fs()->is_paying_or_trial() ) { 
			
			$gs_com = sanitize_text_field( $_POST['gs_com'] );
			$gs_land = sanitize_text_field( $_POST['gs_land'] );
			$gs_cell = sanitize_text_field( $_POST['gs_cell'] );
			$gs_email = sanitize_text_field( $_POST['gs_email'] );
			$gs_address = sanitize_text_field( $_POST['gs_address'] );
			$gs_ribon = sanitize_text_field( $_POST['gs_ribon'] );
			$gs_vcard = esc_url_raw( $_POST['gs_vcard'], array('http', 'https', 'ftp', 'ftps') );

			update_post_meta( $post_id, '_gs_com', $gs_com );
			update_post_meta( $post_id, '_gs_land', $gs_land );
			update_post_meta( $post_id, '_gs_cell', $gs_cell );
			update_post_meta( $post_id, '_gs_email', $gs_email );
			update_post_meta( $post_id, '_gs_address', $gs_address);
			update_post_meta( $post_id, '_gs_ribon', $gs_ribon );
			update_post_meta( $post_id, '_gs_vcard', $gs_vcard );

			$meta_key = 'second_featured_img';
			update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );

		}

	}

	add_action( 'save_post', 'save_gs_team_metadata');

}