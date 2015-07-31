<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$categoryName = '';
	$categoryCategories = '';
	$categoryDisplay = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['category'] ) && !empty( $_GET['category'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['category'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$categoryName = $viral->name;
			$categoryCategories = $viral->categories;
			$categoryDisplay = $viral->display;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=category" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=category&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}
?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-category"></span> Category
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_category" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="category_name" id="category_name" class="vcs_field_input_30" value="<?php echo $categoryName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Categories</span></td>
							<td class="vcs_td_after_label">
								<select name="category_categories[]" id="category_categories" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $categoryCategories ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Categories</option> -->
									<?php
										$args = array(
											'orderby' => 'name',
											'hide_empty' => 1,
											'order' => 'ASC'
											);
										$getcategories = get_categories( $args );
										if ( $getcategories || count( $getcategories ) ) {
											foreach ( $getcategories as $cat ) {
												$selected = "";
												if ( in_array( '{' . $cat->cat_ID . '}', explode( ',', $categoryCategories ) ) ) {
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
							<td class="vcs_td_label"><span class="vcs_form_label">Display</span></td>
							<td class="vcs_td_after_label">
								<input type="number" name="category_display" id="category_display" class="vcs_field_input_number" value="<?php echo $categoryDisplay; ?>" min="1"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_category', 'viralcontentslider_save_category_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_category" id="viralcontentslider_save_category" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>