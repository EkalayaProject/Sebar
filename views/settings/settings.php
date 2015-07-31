<?php
	//echo '<pre>'; print_r(get_option('cron')); echo '</pre>';
?>
<div class="wrap">
	<?php require_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/menus/tab.menus.php' ); ?>
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<h2><span class="dashicons dashicons-admin-settings"></span>Settings</h2>
			<div class="welcome-panel-column-container">
				<ul id="vcs_tabs">
			    <li><a href="#" name="tab1">Cron</a></li>
			    <li><a href="#" name="tab2">Thumbnail</a></li>
			    <li><a href="#" name="tab3">API</a></li>
			    <?php if ( class_exists( 'VcsExtension' ) ) : ?>
			    <li><a href="#" name="tab4">Advance Settings</a></li>
			  	<?php endif; ?>
				</ul>

				<form action="" method="POST" class="vcs_awesome_form">
					<div id="vcs_content">
			    	<div id="tab1">
			    		<table style="width:100%;">
								<tr>
									<td class="vcs_td_label">Cron interval</td>
									<td class="vcs_td_after_label">
										<input type="number" name="settings_cron_interval" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_cron_interval' ); ?>" min="1"/>&nbsp;
										<select name="settings_cron_type" class="vcs_field_input_select">
											<option value="minutes" <?php selected( get_option( 'viralcontentslider_settings_cron_type' ), 'minutes' ); ?>>Minutes</option>
											<option value="hours" <?php selected( get_option( 'viralcontentslider_settings_cron_type' ), 'hours' ); ?>>Hours</option>
											<option value="days" <?php selected( get_option( 'viralcontentslider_settings_cron_type' ), 'days' ); ?>>Days</option>
										</select>
										<br/><em>Set the specific schedule to run WP Cron for fetching new content from feeds. By default, cron will run every hour.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label"><span style="color:red;">Delete old data</span></td>
									<td class="vcs_td_after_label">
										Automatically delete data (analytics tracking & feeds) for more than <input type="number" name="settings_cron_purge_old_data_interval" value="<?php echo get_option( 'settings_cron_purge_old_data_interval', 0 ); ?>" class="vcs_field_input_number"/> days old.
										<br/>
										<em>Set to zero (0) if you don't want to delete any data.</em>
									</td>
								</tr>
							</table>
				    </div>
				    <div id="tab2">
				    	<table style="width:100%;">
				    		<tr>
									<td class="vcs_td_label">Default thumbnail</td>
									<td class="vcs_td_after_label">
										<input type="text" name="settings_default_thumbnail" id="settings_default_thumbnail" class="vcs_field_input_50" value="<?php echo get_option( 'viralcontentslider_settings_default_thumbnail' ); ?>"/>
										<button type="button" class="button action vcs_browse_thumbnail">Browse</button>
										<br/><em>Set the default thumbnail if image is not available.</em>
										<?php
											$defaultThumbnail = get_option( 'viralcontentslider_settings_default_thumbnail' );
											if ( !empty( $defaultThumbnail ) ) {
												echo '<p><img src="' . $defaultThumbnail . '" class="vcs_thumbnail_image_admin"></img></p>';
											}
										?>
									</td>
								</tr>
				    	</table>
				    </div>
				    <div id="tab3">
			    		<table style="width:100%;">
								<tr>
									<td class="vcs_td_label">YouTube API Key</td>
									<td class="vcs_td_after_label">
										<input type="text" name="settings_google_api_key" class="vcs_field_input_30" value="<?php echo get_option( 'viralcontentslider_settings_google_api_key' ); ?>"/>&nbsp;
									</td>
								</tr>
							</table>
				    </div>
				    <?php if ( class_exists( 'VcsExtension' ) ) : ?>
				    <div id="tab4">
				    	<table style="width:100%;">
				    		<tr>
									<td colspan="2">
										<h3>Widget</h3>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Margin</td>
									<td class="vcs_td_after_label">
										Top : <input type="number" name="settings_widget_margin_top" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_margin_top', 0 ); ?>" min="0"/>
										Bottom : <input type="number" name="settings_widget_margin_bottom" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_margin_bottom', 0 ); ?>" min="0"/>
										Left : <input type="number" name="settings_widget_margin_left" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_margin_left', 10 ); ?>" min="0"/>
										Right : <input type="number" name="settings_widget_margin_right" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_margin_right', 10 ); ?>" min="0"/> <em>in pixel</em>
										<br>
										<em>Default value top = 0px, bottom = 0px, left = 10px and right = 10px.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Title font size</td>
									<td class="vcs_td_after_label">
										<input type="number" name="settings_widget_title_font_size" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_title_font_size', 14 ); ?>" min="0"/> <em>in pixel</em>
										<br>
										<em>Default title font size is 14px.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Description font size</td>
									<td class="vcs_td_after_label">
										<input type="number" name="settings_widget_description_font_size" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_description_font_size', 12 ); ?>" min="0"/> <em>in pixel</em>
										<br>
										<em>Default description font size is 12px.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Readmore font size</td>
									<td class="vcs_td_after_label">
										<input type="number" name="settings_widget_readmore_font_size" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_readmore_font_size', 11 ); ?>" min="0"/> <em>in pixel</em>
										<br>
										<em>Default readmore font size is 11px.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Thumbnail dimension</td>
									<td class="vcs_td_after_label">
										Width : <input type="number" name="settings_widget_thumbnail_width" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_thumbnail_width', 85 ); ?>" min="0"/>
										<select name="settings_widget_thumbnail_width_type" class="vcs_field_input_select">
											<option value="px" <?php selected( get_option( 'viralcontentslider_settings_widget_thumbnail_width_type', 'px' ), 'px' ); ?>>px (pixel)</option>
											<option value="%" <?php selected( get_option( 'viralcontentslider_settings_widget_thumbnail_width_type', 'px' ), '%' ); ?>>% (percent)</option>
										</select>
										height : <input type="number" name="settings_widget_thumbnail_height" class="vcs_field_input_number" value="<?php echo get_option( 'viralcontentslider_settings_widget_thumbnail_height', 100 ); ?>" min="0"/>
										<select name="settings_widget_thumbnail_height_type" class="vcs_field_input_select">
											<option value="px" <?php selected( get_option( 'viralcontentslider_settings_widget_thumbnail_height_type', '%' ), 'px' ); ?>>px (pixel)</option>
											<option value="%" <?php selected( get_option( 'viralcontentslider_settings_widget_thumbnail_height_type', '%' ), '%' ); ?>>% (percent)</option>
										</select>
										<br>
										<em>Default width is 85px and height is 100%.</em>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<hr/><h3>Social Media</h3><i>Global social media configuration if you don't provide specific value from the Options page.</i>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Facebook App ID</td>
									<td class="vcs_td_after_label">
										<input type="text" name="settings_social_media_facebook_app_id" class="vcs_field_input_25" value="<?php echo get_option( 'viralcontentslider_settings_social_media_facebook_app_id', '' ); ?>"/>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label vcs_td_label_child">Twitter username</td>
									<td class="vcs_td_after_label">
										<input type="text" name="settings_social_media_twitter_username" class="vcs_field_input_25" value="<?php echo get_option( 'viralcontentslider_settings_social_media_twitter_username', '' ); ?>"/>
									</td>
								</tr>
				    	</table>
				    </div>
				    <?php endif; ?>
					</div>
					<?php wp_nonce_field( 'viralcontentslider_settings_form', 'viralcontentslider_settings_form_nonce' ); ?>
					<button name="viralcontentslider_settings_form" class="button-primary">Save settings</button>
				</form>
			</div>
		</div>
	</div>

</div>
