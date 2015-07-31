<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$authorName = '';
	$authorAuthors = '';
	$authorDisplay = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['author'] ) && !empty( $_GET['author'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['author'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$authorName = $viral->name;
			$authorAuthors = $viral->authors;
			$authorDisplay = $viral->display;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=author" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=author&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}

?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-admin-users"></span> Author
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_author" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="author_name" id="author_name" class="vcs_field_input_30" value="<?php echo $authorName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Authors</span></td>
							<td class="vcs_td_after_label">
								<select name="author_authors[]" id="author_authors" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $authorAuthors ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Authors</option> -->
									<?php
										$getauthors = get_users();
										if ( $getauthors || count( $getauthors ) ) {
											foreach ( $getauthors as $author ) {
												$selected = "";
												if ( in_array( '{' . $author->ID . '}', explode( ',', $authorAuthors ) ) ) {
													$selected = "selected='selected'";
												}
												echo "<option value='" . $author->ID . "' " . $selected . ">" . ucfirst( $author->display_name ) . "</option>";
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Display</span></td>
							<td class="vcs_td_after_label">
								<input type="number" name="author_display" id="author_display" class="vcs_field_input_number" value="<?php echo $authorDisplay; ?>" min="1"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_author', 'viralcontentslider_save_author_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_author" id="viralcontentslider_save_author" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>