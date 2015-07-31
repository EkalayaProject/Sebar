<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$tableVideos = $wpdb->prefix . 'viralcs_videos';

	$idViral = '';
	$videoName = '';
	$videos = array();

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['video'] ) && !empty( $_GET['video'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['video'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$videoName = $viral->name;
			$videos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableVideos WHERE id_viral = %d AND deleted_at IS NULL", $idViral ) );

			$manageVideoUrl = admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=video&video=' . $idViral . '&action=manage';

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=video" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=video&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>&nbsp;';
			$suggestionLink .= '<a href="' . $manageVideoUrl . '" class="add-new-h2">Manage videos</a>';
		}
	}

	$pluginSlug = VIRALCONTENTSLIDER_PLUGIN_SLUG;
	$googleAPIKey = get_option( 'viralcontentslider_settings_google_api_key' );
	if ( empty( $googleAPIKey ) ) {
		echo <<<HTML
<div class="error">
	<p>
		Please set your YouTube API Key before using this feature. Click <a href="?page={$pluginSlug}&tab=settings">here</a> to configure.
	</p>
</div>
HTML;
	}
?>
<style type="text/css">
	.viralcontentslider_youtube_thumbnail_lists {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.viralcontentslider_youtube_thumbnail_lists li {
		display: inline-block;
		vertical-align: top;
		padding: 2%;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
	}
	.viralcontentslider_youtube_thumbnail_lists img {
		display: block;
	}
</style>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-format-video"></span> Video
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<?php if ( !empty( $googleAPIKey ) ) : ?>
				<form action="" method="POST" id="form_video" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="video_name" id="video_name" class="vcs_field_input_25" value="<?php echo $videoName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Videos</span></td>
							<td class="vcs_td_after_label">
								<div class="theme-browser">
									<div class="themes" id="viralcontentslider_add_new_video">
										<?php
											if ( !empty( $videos ) ) {
												foreach( $videos as $video ) {
													$trimmedTitle = wp_trim_words( $video->title, 3 );
													echo '<div class="theme viralcontentslider_youtube_video_thumbnail_' . $video->video_id . '">';
													echo '	<div class="theme-screenshot">';
													echo '		<img alt="" src="' . $video->thumbnail . '" title="' . $video->title . ' - ' . $video->duration . '">';
													echo '	</div>';
													echo '	<h3 class="theme-name" title="' . $video->title . ' - ' . $video->duration . '">' . $trimmedTitle . '</h3>';
													echo '	<div class="theme-actions">';
													echo '		<a class="button button-secondary viralcontentslider_trash_video" title="Trash" data-videoid="' . $video->video_id . '"><span class="dashicons dashicons-trash" style="padding-top:3px;"></span></a>&nbsp;';
													echo '		<a href="' . $video->link . '?TB_iframe=true&width=700&height=450" class="thickbox button button-primary" title="View"><span class="dashicons dashicons-visibility" style="padding-top:3px;"></span></a>';
													echo '	</div>';
													echo '	<input type="hidden" class="viralcontentslider_yvideos_' . $video->video_id . '" name="viralcontentslider_yvideos[]" value="' . $video->video_id . '_VCS_' . $video->title . '_VCS_' . $video->duration . '_VCS_' . $video->str_duration . '_VCS_' . $video->link . '_VCS_' . $video->thumbnail . '_VCS_' . $video->published . '_VCS_' . $video->description . '">';
													echo '</div>';
												}
											}
										?>
										<div class="theme add-new-theme">
											<a onclick="browseYoutubeVideos()">
												<div class="theme-screenshot"><span></span></div>
												<h3 class="theme-name">Browse Youtube Videos</h3>
											</a>
										</div>
									</div>
								<br class="clear">
								</div>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_video', 'viralcontentslider_save_video_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_video" id="viralcontentslider_save_video" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php add_thickbox(); ?>
<div id="viralcontentslider_browse_youtube_videos" style="display:none;">
	<p>
		<form action="" method="POST" id="viralcontentslider_form_browse_youtube_videos">
			<table style="border:1px groove #eeeeee;width:100%;padding:5px;margin-bottom:5px;">
    		<tr>
					<td style="text-align:center;">
						<input type="text" id="viralcontentslider_youtube_keyword" style="width:100%;" placeholder="Keyword, Playlist URL, Channel ID URL or Channel Username URL"/>
					</td>
					<td style="width:50px;">
						<button class="button-primary" id="search_youtube_videos" type="submit"/><strong><i>Search</i></strong></button>
					</td>
				</tr>
			</table>
			<button class="button-primary" id="viralcontentslider_pick_selected_videos" style="float:right;" type="button">Pick Selected Video</button>
			<em id="viralcontentslider_loader_youtube" style="display:none;"></em>
		</form>
		<div style="clear:both;"></div>
		<form id="viralcontentslider_youtube_videos">
			<div id="viralcontentslider_display_youtube_thumbnail"></div>
		</form>
	</p>
</div>
