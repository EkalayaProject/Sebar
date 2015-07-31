<?php
	$idViral = sanitize_text_field( $_GET['video'] );
	$idObj = sanitize_text_field( $_GET['obj'] );
	$video = $this->get_video( $idObj, $idViral );
	$viral = $this->get_viral( $idViral );
	$name = '';
	$title = '';
	$description = '';
	$thumbnail = '';

	if ( !empty( $video ) ) {
		if ( !empty( $viral ) ) {
			$name = $viral->name;
		}

		$title = $video->title;
		$description = $video->description;
		$thumbnail = $video->thumbnail;
	}
	$manageVideoUrl = admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=video&video=' . $idViral . '&action=manage';
?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<h2><span class="dashicons dashicons-edit"></span> Update <a href="<?php echo $manageVideoUrl; ?>" class="add-new-h2">Back</a></h2>
			<p class="about-description"><?php echo $name; ?></p>
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<form action="" method="POST" id="form_update_video" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Title</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="video_update_title" class="vcs_field_input_50" value="<?php echo $title; ?>"/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Description</span></td>
							<td class="vcs_td_after_label">
								<textarea name="video_update_description" class="vcs_field_textarea"><?php echo $description; ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Thumbnail</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="video_update_thumbnail" id="video_update_thumbnail" class="vcs_field_input_50" value="<?php echo $thumbnail; ?>"/>
								<button type="button" class="button action vcs_browse_update_video_thumbnail">Browse</button>
								<?php
								if ( !empty( $thumbnail ) ) {
									echo '<p><img src="' . $thumbnail . '" class="vcs_thumbnail_image_admin"></img></p>';
								}
							?>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_update_video', 'viralcontentslider_save_update_video_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<input type="hidden" name="viralcontentslider_hidden_obj" value="<?php echo $idObj; ?>"/>
						<button name="viralcontentslider_save_update_video" id="viralcontentslider_save_update_video" class="button-primary">Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>