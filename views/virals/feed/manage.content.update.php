<?php
	$idViral = sanitize_text_field( $_GET['feed'] );
	$idObj = sanitize_text_field( $_GET['obj'] );
	$feed = $this->get_feed( $idObj, $idViral );
	$viral = $this->get_viral( $idViral );
	$name = '';
	$feedLink = '';
	$link = '';
	$title = '';
	$description = '';
	$image = '';

	if ( !empty( $feed ) ) {
		if ( !empty( $viral ) ) {
			$name = $viral->name;
			$feedLink = $viral->feed;
		}
		$link = $feed->link;
		$title = $feed->title;
		$description = $feed->description;
		$image = $feed->image;
	}
	$manageContentUrl = admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=feed&feed=' . $idViral . '&action=manage';
?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<h2><span class="dashicons dashicons-edit"></span> Update <a href="<?php echo $manageContentUrl; ?>" class="add-new-h2">Back</a></h2>
			<p class="about-description"><?php echo $name . ' (' . $feedLink . ')'; ?></p>
			<div class="welcome-panel-column-container">
				<form action="" method="POST" id="form_update_feed" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Title</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="feed_update_title" class="vcs_field_input_50" value="<?php echo $title; ?>"/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Link</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="feed_update_link" class="vcs_field_input_50" value="<?php echo $link; ?>"/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Description</span></td>
							<td class="vcs_td_after_label">
								<textarea name="feed_update_description" class="vcs_field_textarea"><?php echo $description; ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Image</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="feed_update_image" id="feed_update_image" class="vcs_field_input_50" value="<?php echo $image; ?>"/>
								<button type="button" class="button action vcs_browse_thumbnail_update_image">Browse</button>
								<?php
									if ( !empty( $image ) ) {
										echo '<p><img src="' . $image . '" class="vcs_thumbnail_image_admin"></img></p>';
									}
								?>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_update_feed', 'viralcontentslider_save_update_feed_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<input type="hidden" name="viralcontentslider_hidden_obj" value="<?php echo $idObj; ?>"/>
						<button name="viralcontentslider_save_update_feed" id="viralcontentslider_save_update_feed" class="button-primary">Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>