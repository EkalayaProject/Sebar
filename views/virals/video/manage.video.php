<?php
	$idViral = sanitize_text_field( $_GET['video'] );
	$name = '';
	$viral = $this->get_viral( $idViral );
	if ( !empty( $viral ) ) {
		$name = $viral->name;
	}
	$videoUrl = admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=video&video=' . $idViral;
?>
<style type="text/css">
	.wp-list-table .column-name {
		width: 25%;
	}
	.wp-list-table .column-title {
		width: 45%;
	}
	.wp-list-table .column-link_html {
		width: 15%;
		text-align: center;
	}
	.wp-list-table .column-published {
		width: 15%;
	}
	.row-actions {
		visibility : visible !important;
	}
</style>
<div class="wrap">
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<h2><span class="dashicons dashicons-index-card"></span> Manage videos <a href="<?php echo $videoUrl; ?>" class="add-new-h2">Back</a></h2>
			<p class="about-description"><?php echo $name; ?></p>
			<div class="welcome-panel-column-container vcs_welcome_panel_column_container">
				<form action="" method="POST" class="vcs_awesome_form">
					<?php
						$dash = new ViralsVideosTable();
						$dash->prepare_items();
						$dash->search_box( 'search', 'search_id' );
						$dash->display();
					?>
				</form>
			</div>
		</div>
	</div>
</div>