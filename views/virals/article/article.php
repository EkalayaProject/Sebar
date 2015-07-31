<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$articleName = '';
	$articlePosts = '';
	$articlePages = '';
	$articleDisplay = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['article'] ) && !empty( $_GET['article'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['article'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$articleName = $viral->name;
			$articlePosts = $viral->posts;
			$articlePages = $viral->pages;
			$articleDisplay = $viral->display;

			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=article" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=article&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>';
		}
	}
?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-editor-paste-text"></span> Article
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_article" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="article_name" id="article_name" class="vcs_field_input_30" value="<?php echo $articleName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Posts</span></td>
							<td class="vcs_td_after_label">
								<select name="article_posts[]" id="article_posts" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $articlePosts ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Posts</option> -->
									<?php
										$args = array(
											'sort_order' => 'ASC',
											'sort_column' => 'post_title',
											'hierarchical' => 1,
											'post_type' => 'post',
											'post_status' => 'publish',
											'posts_per_page' => -1
										);
										$posts = get_posts( $args );
										if ( $posts || count( $posts ) ) {
											foreach ( $posts as $post ) {
												$selected = "";
												if ( in_array( '{' . $post->ID . '}', explode( ',', $articlePosts ) ) ) {
													$selected = "selected='selected'";
												}
												echo "<option value='" . $post->ID . "' " . $selected . ">" . $post->post_title . "</option>";
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Pages</span></td>
							<td class="vcs_td_after_label">
								<select name="article_pages[]" id="article_pages" class="vcs_chosen_multiple vcs_field_input_100" multiple>
									<?php
										/*$selected = "";
										if ( in_array( '{ALL}', explode( ',', $articlePages ) ) ) {
											$selected = "selected='selected'";
										}*/
									?>
									<!-- <option value="ALL" <?php echo $selected; ?>>All Pages</option> -->
									<?php
										$args = array(
											'sort_order' => 'ASC',
											'sort_column' => 'post_title',
											'hierarchical' => 1,
											'post_type' => 'page',
											'post_status' => 'publish'
										);
										$pages = get_pages( $args );
										if ( $pages || count( $pages ) ) {
											foreach ( $pages as $page ) {
												$selected = "";
												if ( in_array( '{' . $page->ID . '}', explode( ',', $articlePages ) ) ) {
													$selected = "selected='selected'";
												}
												echo "<option value='" . $page->ID . "' " . $selected . ">" . $page->post_title . "</option>";
											}
										}
									?>
								</select>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_article', 'viralcontentslider_save_article_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_article" id="viralcontentslider_save_article" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>