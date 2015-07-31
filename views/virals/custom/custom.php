<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$customName = '';
	$customCategories = '';
	$customTags = '';
	$customAuthors = '';
	$customDisplay = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['custom'] ) && !empty( $_GET['custom'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['custom'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$customName = $viral->name;
			$customCategories = $viral->categories;
			$customTags = $viral->tags;
			$customAuthors = $viral->authors;
			$customDisplay = $viral->display;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=custom" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=custom&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}
?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-welcome-widgets-menus"></span> Custom Article
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_custom" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="custom_name" id="custom_name" class="vcs_field_input_30" value="<?php echo $customName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Categories</span></td>
							<td class="vcs_td_after_label">
								<select name="custom_categories[]" id="custom_categories" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $customCategories ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Categories</option> -->
									<?php
										$argscustomcategories = array(
											'orderby' => 'name',
											'hide_empty' => 1,
											'order' => 'ASC'
											);
										$getcategories = get_categories( $argscustomcategories );
										if ( $getcategories || count( $getcategories ) ) {
											foreach ( $getcategories as $cat ) {
												$selected = "";
												if ( in_array( '{' . $cat->cat_ID . '}', explode( ',', $customCategories ) ) ) {
													$selected = "selected='selected'";
												}
												echo "<option value='" . $cat->cat_ID . "' " . $selected . ">" . $cat->name . "</option>";
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Tags</span></td>
							<td class="vcs_td_after_label">
								<select name="custom_tags[]" id="custom_tags" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $customTags ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Tags</option> -->
									<?php
										$gettags = get_tags();
										if ( $gettags || count( $gettags ) ) {
											foreach ( $gettags as $tag ) {
												$selected = "";
												if ( in_array( '{' . $tag->term_id . '}', explode( ',', $customTags ) ) ) {
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
							<td class="vcs_td_label"><span class="vcs_form_label">Authors</span></td>
							<td class="vcs_td_after_label">
								<select name="custom_authors[]" id="custom_authors" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $customAuthors ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Authors</option> -->
									<?php
										$getauthors = get_users();
										if ( $getauthors || count( $getauthors ) ) {
											foreach ( $getauthors as $author ) {
												$selected = "";
												if ( in_array( '{' . $author->ID . '}', explode( ',', $customAuthors ) ) ) {
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
								<input type="number" name="custom_display" id="custom_display" class="vcs_field_input_number" value="<?php echo $customDisplay; ?>" min="1"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_custom', 'viralcontentslider_save_custom_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_custom" id="viralcontentslider_save_custom" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>