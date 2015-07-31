<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$linkName = '';
	$linkLinks = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['link'] ) && !empty( $_GET['link'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['link'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$linkName = $viral->name;
			$linkLinks = $viral->links;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=link" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=link&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}

?>
<div class="wrap vcs_wrap">
	<h2>
		<span class="dashicons dashicons-admin-links"></span> Link
		<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Back</a> <?php echo $suggestionLink; ?>
	</h2>
	<form action="" method="POST" id="form_link" class="vcs_form">
		<table style="width:100%;">
			<tr>
				<td style="width:250px;"><span class="vcs_form_label">Name</span></td>
				<td>
					<input type="text" style="width:30%;" name="link_name" id="link_name" value="<?php echo $linkName; ?>" autofocus>
				</td>
			</tr>
			<tr>
				<td><span class="vcs_form_label">Links</span></td>
				<td>
					<textarea name="link_links" style="width:100%;" rows="10"><?php echo $linkLinks; ?></textarea>
					<br/><em>Separate link with new line (enter).</em>
				</td>
			</tr>
		</table>
		<div style="margin-top:10px;">
			<?php wp_nonce_field( 'viralcontentslider_save_link', 'viralcontentslider_save_link_nonce' ); ?>
			<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>">
			<button class="button-primary" name="viralcontentslider_save_link" id="viralcontentslider_save_link"><?php echo $buttonText; ?></button>
		</div>
	</form>
</div>