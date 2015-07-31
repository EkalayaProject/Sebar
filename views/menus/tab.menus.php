<?php
	$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<h2 class="nav-tab-wrapper" style="padding-left:0px !important;">
	<a class="nav-tab <?php if($tab == 'dashboard') { echo 'nav-tab-active'; }?>" href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard"><span class="dashicons dashicons-dashboard"></span> Dashboard</a>
	<a class="nav-tab <?php if($tab == 'settings') { echo 'nav-tab-active'; }?>" href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=settings"><span class="dashicons dashicons-admin-settings"></span> Settings</a>
	<a class="nav-tab <?php if($tab == 'analytics') { echo 'nav-tab-active'; }?>" href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=analytics"><span class="dashicons dashicons-chart-bar"></span> Analytics</a>
</h2>