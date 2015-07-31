<style type="text/css">
	.wp-list-table .column-name {
		width: 70%;
	}
	.wp-list-table .column-type {
		width: 15%;
	}
	.wp-list-table .column-updated_at {
		width: 15%;
		text-align: center;
	}
	.row-actions {
		visibility : visible !important;
	}
</style>
<div class="wrap">
	<?php require_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/menus/tab.menus.php' ); ?>
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container">
				<form class="vcs_awesome_form">
					<select name="type" id="viralcontentslider_type" class="vcs_field_input_select">
						<option value="article">Article</option>
						<option value="category">Category</option>
						<option value="tag">Tag</option>
						<option value="author">Author</option>
						<option value="custom">Custom article</option>
						<option value="video">Video</option>
						<option value="feed">Feed</option>
						<option value="customlink">Custom link</option>
					</select>
					<button type="button" class="button-primary" id="viralcontentslider_new"><strong><i>Create New</i></strong></button>
				</form>
			</div>
		</div>
	</div>

	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container">
				<form action="" method="POST" class="vcs_awesome_form">
					<?php
						$dash = new ViralsTable();
						$dash->prepare_items();
						$dash->search_box( 'search', 'search_id' );
						$dash->display();
					?>
				</form>
			</div>
		</div>
	</div>

</div>