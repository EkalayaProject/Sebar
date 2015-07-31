<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$idViral = '';
	$feedName = '';
	$feedUrl = '';
	$feedDisplay = '';

	$buttonText = '<strong><i>Create</i></strong>';
	$suggestionLink = '';

	if ( isset( $_GET['feed'] ) && !empty( $_GET['feed'] ) ) {
		$idViral = (int)sanitize_text_field( $_GET['feed'] );
		$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		if ( !empty( $viral ) ) {
			$feedName = $viral->name;
			$feedUrl = $viral->feed;
			$feedDisplay = $viral->display;
			$fetchFeedUrl = wp_nonce_url( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=fetchfeed&viral=' . $idViral . '&url=' . urlencode( $feedUrl ), 'viralcontentslider_fetch_feed', 'viralcontentslider_fetch_feed_nonce' );
			$manageFeedUrl = admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=feed&feed=' . $idViral . '&action=manage';
			$buttonText = '<strong><i>Update</i></strong>';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=feed" class="add-new-h2">Create New</a>&nbsp;';
			$suggestionLink .= '<a href="?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=feed&viral=' . $idViral . '" class="add-new-h2">Style & Options</a>&nbsp;';
			$suggestionLink .= '<a href="' . $fetchFeedUrl . '" data-feed="' . $feedUrl . '" class="add-new-h2" id="vcs_fetch_feed">Fetch content from feed</a>&nbsp;';
			$suggestionLink .= '<a href="' . $manageFeedUrl . '" class="add-new-h2">Manage contents</a>';
		}
	}
?>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<h2>
					<span class="dashicons dashicons-rss"></span> Feed
					<a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Dashboard</a> <?php echo $suggestionLink; ?>
				</h2>
				<form action="" method="POST" id="form_feed" class="vcs_awesome_form">
					<table style="width:100%;">
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Name</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="feed_name" id="feed_name" class="vcs_field_input_30" value="<?php echo $feedName; ?>" autofocus/>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Feed url</span></td>
							<td class="vcs_td_after_label">
								<input type="text" name="feed_url" id="feed_url" class="vcs_field_input_50" value="<?php echo $feedUrl; ?>"/> <button type="button" class="button button-action" id="feed_finder">RSS Finder</button>
							</td>
						</tr>
						<tr>
							<td class="vcs_td_label"><span class="vcs_form_label">Display</span></td>
							<td class="vcs_td_after_label">
								<input type="number" name="feed_display" id="feed_display" class="vcs_field_input_number" value="<?php echo $feedDisplay; ?>" min="1"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;">
						<?php wp_nonce_field( 'viralcontentslider_save_feed', 'viralcontentslider_save_feed_nonce' ); ?>
						<input type="hidden" name="viralcontentslider_hidden_id" value="<?php echo $idViral; ?>"/>
						<button name="viralcontentslider_save_feed" id="viralcontentslider_save_feed" class="button-primary"><?php echo $buttonText; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php add_thickbox(); ?>
<div id="viralcontentslider_feed_modal" style="display:none;">
	<p>
		<form action="" method="POST" id="form_find_feed">
			<table style="border:1px groove #eeeeee;width:100%;padding:5px;margin-bottom:5px;">
    		<tr>
					<td style="text-align:center;background-color:#eeeeee;">
						<input type="text" id="feed_keyword" style="width:100%;" placeholder="Keyword"/>
					</td>
				</tr>
			</table>
			<em id="loader_feed" style="display:none;"></em>
		</form>
		<div id="display_feeds"></div>
	</p>	
</div>