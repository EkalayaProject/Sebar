<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$tagName = '';
	$tagTags = '';
	$tagDisplay = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['tag'] ) && !empty( $_GET['tag'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['tag'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$tagName = $viral->name;
			$tagTags = $viral->tags;
			$tagDisplay = $viral->display;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=tag" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=tag&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}

?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-tag"></span> Tag
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_tag" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="tag_name" id="tag_name" class="vcs_field_input_50" value="<?php echo $tagName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Tags</span></td>
							<td class="vcs_td_after_label">
								<select name="tag_tags[]" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $tagTags ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Tags</option> -->
									<?php
										$gettags = get_tags();
										if ( $gettags || count( $gettags ) ) {
											foreach ( $gettags as $tag ) {
												$selected = "";
												if ( in_array( '{' . $tag->term_id . '}', explode( ',', $tagTags ) ) ) {
													$selected = "selected='selected'";
												}
												echo "<option value='" . $tag->term_id . "' " . $selected . ">" . $tag->name . "</option>";
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Display</span></td>
							<td class="vcs_td_after_label">
								<input type="number" name="tag_display" id="tag_display" class="vcs_field_input_number" value="<?php echo $tagDisplay; ?>" min="1"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_tag', 'viralcontentslider_save_tag_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_tag" id="viralcontentslider_save_tag" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>