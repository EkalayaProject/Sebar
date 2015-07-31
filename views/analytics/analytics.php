<?php
	$orderInternal = 'total';
	$limitInternal = 10;
	if ( isset( $_POST['vcs_analytics_submit_filter_internal'] ) ) {
		$orderInternal = sanitize_text_field( $_POST['most_internal'] );
		$limitInternal = sanitize_text_field( $_POST['limit_internal'] );
	}

	$orderExternal = 'total';
	$limitExternal = 10;
	if ( isset( $_POST['vcs_analytics_submit_filter_external'] ) ) {
		$orderExternal = sanitize_text_field( $_POST['most_external'] );
		$limitExternal = sanitize_text_field( $_POST['limit_external'] );
	}

	$current1 = 'id="current"';
	$current2 = '';
	$style1 = 'style="display:block;"';
	$style2 = 'style="display:none;"';
	if ( isset( $_GET['node'] ) ) {
		if ( $_GET['node'] == 'external' ) {
			$current1 = '';
			$current2 = 'id="current"';
			$style1 = 'style="display:none;"';
			$style2 = 'style="display:block;"';
		}
	}

	$keyword = '';
	if ( isset( $_POST['vcs_keyword_trend'] ) ) {
		$keyword = sanitize_text_field( $_POST['vcs_keyword_trend'] );
	}
?>
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
				<form action="" method="POST" class="vcs_awesome_form">
					<ul id="vcs_nav_tabs">
				    <li <?php echo $current1; ?>><a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=analytics&node=internal" name="tab1">Internal link</a></li>
				    <li <?php echo $current2; ?>><a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=analytics&node=external" name="tab2">External link</a></li>
					</ul>
					<div id="vcs_nav_content">
			    	<div id="tab1" <?php echo $style1; ?>>
			    		<h2><span class="dashicons dashicons-admin-links"></span> Top <?php echo $limitInternal; ?></h2>

							<?php
								$topInternal = VtbAnalytics::get_top( 'internal', $orderInternal, $limitInternal );
								$k = 1;
								if ( !empty( $topInternal ) ) {
									echo '<table style="width:100%;">';
									echo '	<tr>';
									echo '		<td></td>';
									echo '		<td style="font-weight:bold;color:green;">Title</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Share</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Like</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Comment</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Click</td>';
									echo '		<td style="font-weight:bold;color:green;">Twitter</td>';
									echo '		<td style="font-weight:bold;color:green;">Linkedin</td>';
									echo '		<td style="font-weight:bold;color:green;">Pinterest</td>';
									echo '		<td style="font-weight:bold;color:green;">Google+</td>';
									echo '		<td style="font-weight:bold;color:green;text-align:right;">Total</td>';
									echo '	</tr>';
									foreach ( $topInternal as $data ) {
										echo '<tr>';
										echo '	<td style="text-align:right;color:green;">' . $k . '</td>';
										echo '	<td style="border-bottom:1px solid green;" title="' . $data->title . '">' . '<a href="' . $data->url . '" target="_blank">' . $data->title . '</a>' . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_share . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_like . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_comment . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_click . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->tweet . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->linkedin . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->pinterest . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->googleplus . '</td>';
										echo '	<td style="border-bottom:1px solid green;text-align:right;" title="Total : ' . $data->total . '">' . $data->total . '</td>';
										echo '</tr>';
										$k++;
									}
									echo '	<tr>';
									echo '		<td colspan="11">';
									echo '		<input type="number" name="limit_internal" class="vcs_field_input_number_50" min="1" value="10"/>';
									echo '		<input type="radio" name="most_internal" value="total" checked="checked"/>Total';
									echo '		<input type="radio" name="most_internal" value="fb_share"/>Facebook share';
									echo '		<input type="radio" name="most_internal" value="fb_like"/>Facebook like';
									echo '		<input type="radio" name="most_internal" value="fb_comment"/>Facebook comment';
									echo '		<input type="radio" name="most_internal" value="fb_click"/>Facebook click';
									echo '		<input type="radio" name="most_internal" value="tweet"/>Twitter';
									echo '		<input type="radio" name="most_internal" value="linkedin"/>Linkedin';
									echo '		<input type="radio" name="most_internal" value="pinterest"/>Pinterest';
									echo '		<input type="radio" name="most_internal" value="googleplus"/>Google+';
									echo '		<input type="submit" class="button-primary" name="vcs_analytics_submit_filter_internal" value="Filter"/>';
									echo '		</td>';
									echo '	</tr>';
									echo '</table>';
								} else {
									echo '<em>Ops, nothing to show yet. ;-)</em>';
								}
							?>
			    	</div>
			    	<div id="tab2" <?php echo $style2; ?>>
			    		<h2><span class="dashicons dashicons-admin-links"></span> Top <?php echo $limitExternal; ?></h2>

							<?php
								$topExternal = VtbAnalytics::get_top( 'external', $orderExternal, $limitExternal );
								$i = 1;
								if ( !empty( $topExternal ) ) {
									echo '<table style="width:100%;">';
									echo '	<tr>';
									echo '		<td></td>';
									echo '		<td style="font-weight:bold;color:green;">Title</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Share</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Like</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Comment</td>';
									echo '		<td style="font-weight:bold;color:green;">FB<br>Click</td>';
									echo '		<td style="font-weight:bold;color:green;">Twitter</td>';
									echo '		<td style="font-weight:bold;color:green;">Linkedin</td>';
									echo '		<td style="font-weight:bold;color:green;">Pinterest</td>';
									echo '		<td style="font-weight:bold;color:green;">Google+</td>';
									echo '		<td style="font-weight:bold;color:green;text-align:right;">Total</td>';
									echo '	</tr>';
									foreach ( $topExternal as $data ) {
										echo '<tr>';
										echo '	<td style="text-align:right;color:green;">' . $i . '</td>';
										echo '	<td style="border-bottom:1px solid green;" title="' . $data->title . '">' . '<a href="' . $data->url . '" target="_blank">' . $data->title . '</a>' . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_share . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_like . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_comment . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->fb_click . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->tweet . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->linkedin . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->pinterest . '</td>';
										echo '	<td style="border-bottom:1px solid green;">' . $data->googleplus . '</td>';
										echo '	<td style="border-bottom:1px solid green;text-align:right;" title="Total : ' . $data->total . '">' . $data->total . '</td>';
										echo '</tr>';
										$i++;
									}
									echo '	<tr>';
									echo '		<td colspan="11">';
									echo '		<input type="number" name="limit_external" class="vcs_field_input_number_50" min="1" value="10"/>';
									echo '		<input type="radio" name="most_external" value="total" checked="checked"/>Total';
									echo '		<input type="radio" name="most_external" value="fb_share"/>Facebook share';
									echo '		<input type="radio" name="most_external" value="fb_like"/>Facebook like';
									echo '		<input type="radio" name="most_external" value="fb_comment"/>Facebook comment';
									echo '		<input type="radio" name="most_external" value="fb_click"/>Facebook click';
									echo '		<input type="radio" name="most_external" value="tweet"/>Twitter';
									echo '		<input type="radio" name="most_external" value="linkedin"/>Linkedin';
									echo '		<input type="radio" name="most_external" value="pinterest"/>Pinterest';
									echo '		<input type="radio" name="most_external" value="googleplus"/>Google+';
									echo '		<input type="submit" class="button-primary" name="vcs_analytics_submit_filter_external" value="Filter"/>';
									echo '		</td>';
									echo '	</tr>';
									echo '</table>';
								} else {
									echo '<em>Ops, nothing to show yet. ;-)</em>';
								}
							?>
			    	</div>
			    </div>
				</form>
			</div>
		</div>
	</div>

	<?php if ( class_exists( 'VcsExtension' ) ) : ?>
	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container">
				<form action="" method="POST" class="vcs_awesome_form">
					<ul id="vcs_nav_tabs">
				    <li id="current"><a href="#" name="tab1">Trend</a></li>
					</ul>
					<div id="vcs_nav_content">
			    	<div id="tab1" style="display:block;">
			    		<input type="text" name="vcs_keyword_trend" class="vcs_keyword_trend" placeholder="Search trend by typing a keyword or url..."/>
							<?php
								if ( !empty( $keyword ) ) :
								echo '<h2 style="color:green;font-style:italic;">Result for keyword: ' . $keyword . '<h2>';
								$searchs = VtbAnalytics::search( $keyword );
								if ( !empty( $searchs ) ) {
									foreach ( $searchs as $search ) {
										echo '<h3><a href="' . $search->url . '" target="_blank">' . $search->title . '</a></h3>';
										$searchByUrl = VtbAnalytics::searchByUrl( $search->url );
										if ( !empty( $searchByUrl ) ) {
											echo '<table style="width:100%;margin-bottom:20px;">';
											echo '	<tr>';
											echo '		<td></td>';
											echo '		<td style="font-weight:bold;color:green;">FB<br>Share</td>';
											echo '		<td style="font-weight:bold;color:green;">FB<br>Like</td>';
											echo '		<td style="font-weight:bold;color:green;">FB<br>Comment</td>';
											echo '		<td style="font-weight:bold;color:green;">FB<br>Click</td>';
											echo '		<td style="font-weight:bold;color:green;">Twitter</td>';
											echo '		<td style="font-weight:bold;color:green;">Linkedin</td>';
											echo '		<td style="font-weight:bold;color:green;">Pinterest</td>';
											echo '		<td style="font-weight:bold;color:green;">Google+</td>';
											echo '		<td style="font-weight:bold;color:green;">Total</td>';
											echo '		<td style="font-weight:bold;color:green;text-align:right;">Timestamp</td>';
											echo '	</tr>';
											$l = 1;
											$arraySearchByUrl = array_slice( $searchByUrl, 0, 10 );
											foreach ( $arraySearchByUrl as $sUrl ) {
												$timeStamp = date( 'j M y, h:i a', strtotime( $sUrl->updated_at ) );
												echo '<tr>';
												echo '	<td style="text-align:right;color:green;">' . $l . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->fb_share . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->fb_like . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->fb_comment . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->fb_click . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->tweet . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->linkedin . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->pinterest . '</td>';
												echo '	<td style="border-bottom:1px solid green;">' . $sUrl->googleplus . '</td>';
												echo '	<td style="border-bottom:1px solid green;" title="Total : ' . $sUrl->total . '">' . $sUrl->total . '</td>';
												echo '	<td style="border-bottom:1px solid green;text-align:right;">' . $timeStamp . '</td>';
												echo '</tr>';
												$l++;
											}
											echo '</table>';
										}
									}
								}
								endif;
							?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
