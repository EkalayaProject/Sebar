<?php
	$idViral = sanitize_text_field( $_GET['feed'] );
	$name = '';
	$feed = '';
	$viral = $this->get_viral( $idViral );
	if ( !empty( $viral ) ) {
		$name = $viral->name;
		$feed = $viral->feed;
	}
	$feedUrl = admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=feed&feed=' . $idViral;
?>
<style type="text/css">
	.wp-list-table .column-name {
		width: 20%;
	}
	.wp-list-table .column-feed {
		width: 25%;
	}
	.wp-list-table .column-title {
		width: 25%;
	}
	.wp-list-table .column-link_html {
		width: 15%;
		text-align: center;
	}
	.wp-list-table .column-date_published {
		width: 15%;
	}
	.row-actions {
		visibility : visible !important;
	}
</style>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<h2><span class="dashicons dashicons-index-card"></span> Manage contents <a href="<?php echo $feedUrl; ?>" class="add-new-h2">Back</a></h2>
			<p class="about-description"><?php echo $name . ' (' . $feed . ')'; ?></p>
			<div class="welcome-panel-column-container">
				<form action="" method="POST" class="vcs_awesome_form">
					<?php
						$dash = new ViralsFeedsTable();
						$dash->prepare_items();
						$dash->search_box( 'search', 'search_id' );
						$dash->display();
					?>
				</form>
			</div>
		</div>
	</div>
</div>