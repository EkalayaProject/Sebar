<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$links = array();
	$customLinkName = '';

	$buttonText = '<strong><i>Continue</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['customlink'] ) && !empty( $_GET['customlink'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['customlink'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$links = $this->get_links( $idViral );
			$customLinkName = $viral->name;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=customlink" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=customlink&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}

?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-admin-links"></span> Custom Link
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_custom_link" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="custom_link_name" id="custom_link_name" class="vcs_field_input_30" value="<?php echo $customLinkName; ?>" autofocus/>
							</td>
						</tr>
					</table>
					<?php
						if ( !empty( $links ) ) {
							foreach ( $links as $link ) {
								$trashLink = wp_nonce_url( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=trash&viral=' . $idViral . '&customlink=' . $link->id, 'viralcontentslider_trash_custom_link', 'viralcontentslider_trash_custom_link_nonce' );
								echo '<table style="width:100%;box-shadow:0 0 3px #A3D79C;margin-bottom:10px;margin-top:5px;" id="customlink_table_' . $link->id . '">';
								echo '<tr>';
								echo '	<td class="vcs_td_label">';
								echo '		<span class="vcs_form_label">Link</span>';
								echo '	</td>';
								echo '	<td class="vcs_td_after_label">';
								echo '		<input type="text" name="custom_link_titles[' . $link->id . ']" class="vcs_field_input_50" value="' . $link->link . '"/>';
								echo '		<a href="' . $trashLink . '" style="float:right;" class="button action vcs_custom_link_trash_link" title="Trash link ' . $link->title . ' (' . $link->link . ')" data-id="' . $link->id . '">';
								echo '			<span class="dashicons dashicons-trash" style="padding-top:3px;"></span>';
								echo '		</a>';
								echo '	</td>';
								echo '</tr>';

								echo '<tr>';
								echo '	<td class="vcs_td_label">';
								echo '		<span class="vcs_form_label">Title</span>';
								echo '	</td>';
								echo '	<td class="vcs_td_after_label">';
								echo '		<input type="text" name="custom_link_titles[' . $link->id . ']" class="vcs_field_input_50" value="' . $link->title . '"/>';
								echo '	</td>';
								echo '</tr>';

								echo '<tr>';
								echo '	<td class="vcs_td_label">';
								echo '		<span class="vcs_form_label">Description</span>';
								echo '	</td>';
								echo '	<td class="vcs_td_after_label">';
								echo '		<textarea name="custom_link_descriptions[' . $link->id . ']" class="vcs_field_textarea">' . $link->description . '</textarea>';
								echo '	</td>';
								echo '</tr>';

								echo '<tr>';
								echo '	<td class="vcs_td_label">';
								echo '		<span class="vcs_form_label">Image</span>';
								echo '	</td>';
								echo '	<td class="vcs_td_after_label">';
								echo '		<input type="text" name="custom_link_images[' . $link->id . ']" id="custom_link_images_' . $link->id . '" class="vcs_field_input_50" value="' . $link->image . '"/>';
								echo '		<button type="button" class="button action vcs_custom_link_browse_thumbnail" data-id="' . $link->id . '">Browse</button>';
								if ( !empty( $link->image ) ) {
									echo '	<p><img src="' . $link->image . '" class="vcs_thumbnail_image_admin"></p>';
								}
								echo '	</td>';
								echo '</tr>';

								echo '</table>';
							}
						}
					?>
					<?php
						if ( !empty( $idViral ) ) {
							?>
								<table style="width:100%;box-shadow:0 0 3px #A3D79C;margin-top:20px;">
									<tr>
										<td class="vcs_td_label"><span class="vcs_form_label">Link</span></td>
										<td class="vcs_td_after_label">
											<input type="text" name="custom_link_link" id="custom_link_link" class="vcs_field_input_50" value=""/>
										</td>
									</tr>
									<tr>
										<td class="vcs_td_label"><span class="vcs_form_label">Title</span></td>
										<td class="vcs_td_after_label">
											<input type="text" name="custom_link_title" id="custom_link_title" class="vcs_field_input_50" value=""/>
										</td>
									</tr>
									<tr>
										<td class="vcs_td_label"><span class="vcs_form_label">Description</span></td>
										<td class="vcs_td_after_label">
											<textarea name="custom_link_description" id="custom_link_description" class="vcs_field_textarea"></textarea>
										</td>
									</tr>
									<tr>
										<td class="vcs_td_label"><span class="vcs_form_label">Image</span></td>
										<td class="vcs_td_after_label">
											<input type="text" name="custom_link_image" id="custom_link_image" class="vcs_field_input_50"/>
											<button type="button" class="button action vcs_custom_link_browse_thumbnail" data-id="-1">Browse</button>
										</td>
									</tr>
								</table>
							<?php
						}
					?>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_custom_link', 'viralcontentslider_save_custom_link_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_custom_link" id="viralcontentslider_save_custom_link" class="button-primary" value="<?php echo $buttonText; ?>"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>