<?php
	global $wpdb;
	$tableViral = $wpdb->prefix . 'viralcs_virals';
	$tableOptions = $wpdb->prefix . 'viralcs_options';

	$idViral = sanitize_text_field( $_GET['viral'] );
	$name = '';
	$type = '';
	$displayType = 'random';
	$displaySort = 'asc';
	$boxShadow = 5;
	$textAlign = 'justify';
	$titleLimitWords = 6;
	$titleFontSize = 16;
	$titleFontColor = '#000000';
	$descriptionLimitWords = 13;
	$descriptionFontSize = 14;
	$descriptionFontColor = '#2E2E2E';
	$showNavigation = 'yes';
	$readmoreFontSize = 12;
	$readmoreFontColor = '#AAAAAA';
	$readmoreText = 'Read more';
	$autoPlay = 'no';

	$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d", $idViral ) );
	if ( !empty( $viral ) ) {
		$name = $viral->name;
		$type = sanitize_text_field( $_GET['node'] );
	}

	$options = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableOptions WHERE id_viral = %d", $idViral ) );
	if ( !empty( $options ) ) {
		$displayType = $options->display_type;
		$displaySort = $options->display_sort;
		$boxShadow = $options->box_shadow;
		$textAlign = $options->text_align;
		$titleLimitWords = $options->title_limit_words;
		$titleFontSize = $options->title_font_size;
		$titleFontColor = $options->title_font_color;
		$descriptionLimitWords = $options->description_limit_words;
		$descriptionFontSize = $options->description_font_size;
		$descriptionFontColor = $options->description_font_color;
		$showNavigation = $options->nav_show;
		$readmoreFontSize = $options->readmore_font_size;
		$readmoreFontColor = $options->readmore_font_color;
		$readmoreText = $options->readmore_text;
		$autoPlay = $options->autoplay;
	}

	# Required extension plugin
	$openInLandingPage = 'yes';
	$openInNewTab = 'no';
	$fontFamily = 'template';
	$displayInHome = 'no';
	$arrowStyle = 'default';
	$autoinsertIn = '';
	$autoinsertPosition = 'before';
	$autoinsertParagraph = 1;
	$backgroundColor = '#FFFFFF';
	$shadowColor = '#CCCCCC';
	$thumbnailWidth = 120;
	$thumbnailHeight = 100;
	$thumbnailWidthType = 'px';
	$thumbnailHeightType = '%';
	$marginTop = 0;
	$marginBottom = 5;
	$marginLeft = 0;
	$marginRight = 0;
	$speedTransition = 1000;
	$pauseInterval = 5000;
	$facebookAppId = '';
	$twitterUsername = '';

	if ( class_exists( 'VcsExtension' ) ) {
		$tableExtensionOptions = $wpdb->prefix . 'viralcs_extension_options';
		$extensionOptions = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableExtensionOptions WHERE id_viral = %d", $idViral ) );
		if ( !empty( $extensionOptions ) ) {
			$openInLandingPage = $extensionOptions->open_in_landing_page;
			$openInNewTab = $extensionOptions->open_in_new_tab;
			$fontFamily = $extensionOptions->font_family;
			$displayInHome = $extensionOptions->display_in_home;
			$arrowStyle = $extensionOptions->arrow_style;
			$autoinsertIn = $extensionOptions->autoinsert_in;
			$autoinsertPosition = $extensionOptions->autoinsert_position;
			$autoinsertParagraph = $extensionOptions->autoinsert_paragraph;
			$backgroundColor = $extensionOptions->background_color;
			$shadowColor = $extensionOptions->shadow_color;
			$thumbnailWidth = $extensionOptions->thumbnail_width;
			$thumbnailHeight = $extensionOptions->thumbnail_height;
			$thumbnailWidthType = $extensionOptions->thumbnail_width_type;
			$thumbnailHeightType = $extensionOptions->thumbnail_height_type;
			$marginTop = $extensionOptions->margin_top;
			$marginBottom = $extensionOptions->margin_bottom;
			$marginLeft = $extensionOptions->margin_left;
			$marginRight = $extensionOptions->margin_right;
			$speedTransition = $extensionOptions->speed_transition;
			$pauseInterval = $extensionOptions->pause_interval;
			$facebookAppId = $extensionOptions->facebook_app_id;
			$twitterUsername = $extensionOptions->twitter_username;
		}
	}
?>
<style type="text/css">
	.viralcontentslider_thumbnail_options_lists{
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.viralcontentslider_thumbnail_options_lists li{
		display: inline-block;
		vertical-align: top;
		margin-right: 10px;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		box-shadow: 0 0 3px #CCCCCC;
	}
	.viralcontentslider_thumbnail_options_lists li:hover{
		box-shadow: 0 0 3px #A3D79C;
	}
	.viralcontentslider_thumbnail_options_lists img.vcs_arrow{
		display: block;
		width: 80px;
		height: 40px;
	}
</style>
<div class="wrap">

	<div class="welcome-panel vcs_welcome_panel">
		<div class="welcome-panel-content">
			<h2><span class="dashicons dashicons-art"></span> Customize styling & options - <?php echo $name; ?><a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=dashboard" class="add-new-h2">Back</a></h2>
			<div class="welcome-panel-column-container">
				<ul id="vcs_tabs">
			    <li><a href="#" name="tab1">Display</a></li>
			    <li><a href="#" name="tab2">Content</a></li>
			    <li><a href="#" name="tab3">Navigation</a></li>
			    <li><a href="#" name="tab4">Readmore</a></li>
			    <?php if ( class_exists( 'VcsExtension' ) ) : ?>
			    <li><a href="#" name="tab5">Advance Settings</a></li>
			  	<?php endif; ?>
				</ul>

				<form action="" method="POST" id="form_options" class="vcs_awesome_form">
					<div id="vcs_content">
			    	<div id="tab1">
			    		<table style="width:100%;">
			    			<tr>
									<td class="vcs_td_label">Order by</td>
									<td class="vcs_td_after_label">
										<select name="options_display_type" class="vcs_chosen_single vcs_field_select_25">
										<option value="random" <?php selected( $displayType, 'random' ); ?>>Random</option>
											<option value="bytitle" <?php selected( $displayType, 'bytitle' ); ?>>By title</option>
											<?php if ( $type != 'link' && $type != 'video' ) { ?>
											<option value="bydatepublished" <?php selected( $displayType, 'bydatepublished' ); ?>>By date published</option>
											<?php } ?>
											<?php if ( $type == 'category' || $type == 'tag' || $type == 'author' ) { ?>
											<option value="bydefault" <?php selected( $displayType, 'bydefault' ); ?>>By default</option>
											<?php } ?>
											<?php if ( $type == 'category' || $type == 'article' || $type == 'tag' || $type == 'author' ) { ?>
											<option value="bycommented" <?php selected( $displayType, 'bycommented' ); ?>>Most commented</option>
											<?php } ?>
											<?php if ( $type == 'video' ) { ?>
											<option value="byduration" <?php selected( $displayType, 'byduration' ); ?>>By duration</option>
											<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Sort</td>
									<td class="vcs_td_after_label">
										<select name="options_display_sort" class="vcs_chosen_single vcs_field_select_15">
											<option value="asc" <?php selected( $displaySort, 'asc' ); ?>>Ascending</option>
											<option value="desc" <?php selected( $displaySort, 'desc' ); ?>>Descending</option>
										</select>
										<br/><em>Ignore it if you choose Random as display type.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Box shadow</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_box_shadow" class="vcs_field_input_number" value="<?php echo $boxShadow; ?>" min="0"/> <em>in pixel</em>
										<br/><em>Default value is 5.</em>
									</td>
								</tr>
			    		</table>
			    	</div>
			    	<div id="tab2">
			    		<table style="width:100%;">
			    			<tr>
									<td class="vcs_td_label">Text align</td>
									<td class="vcs_td_after_label">
										<select name="options_text_align" class="vcs_chosen_single vcs_field_select_15">
											<option value="justify" <?php selected( $textAlign, 'justify' ); ?>>Justify</option>
											<option value="left" <?php selected( $textAlign, 'left' ); ?>>Left</option>
										</select>
										<br/><em>Default value is justify.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Title limit words</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_title_limit_words" class="vcs_field_input_number" value="<?php echo $titleLimitWords; ?>" min="1"/>
										<br/><em>Default value is 6.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Title font size</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_title_font_size" class="vcs_field_input_number" value="<?php echo $titleFontSize; ?>" min="1"/> <em>in pixel</em>
										<br/><em>Default value is 16.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Title font color</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_title_font_color" class="color {hash:true} vcs_field_input_color" value="<?php echo $titleFontColor; ?>"/>
										<br/><em>Default value is #000000.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Description limit words</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_description_limit_words" class="vcs_field_input_number" value="<?php echo $descriptionLimitWords; ?>" min="1"/>
										<br/><em>Default value is 13.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Description font size</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_description_font_size" class="vcs_field_input_number" value="<?php echo $descriptionFontSize; ?>" min="1"/> <em>in pixel</em>
										<br/><em>Default value is 14.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Description font color</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_description_font_color" class="color {hash:true} vcs_field_input_color" value="<?php echo $descriptionFontColor; ?>"/>
										<br/><em>Default value is #2E2E2E.</em>
									</td>
								</tr>
			    		</table>
			    	</div>
			    	<div id="tab3">
			    		<table style="width:100%;">
			    			<tr>
									<td class="vcs_td_label">Show navigation</td>
									<td class="vcs_td_after_label">
										<select name="options_show_navigation" class="vcs_chosen_single vcs_field_select_15">
											<option value="yes" <?php selected( $showNavigation, 'yes' ); ?>>Yes</option>
											<option value="no" <?php selected( $showNavigation, 'no' ); ?>>No</option>
										</select>
										<br/><em>If No is selected, no navigation will appear.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Autoplay</td>
									<td class="vcs_td_after_label">
										<select name="options_autoplay" class="vcs_chosen_single vcs_field_select_15">
											<option value="no" <?php selected( $autoPlay, 'no' ); ?>>No</option>
											<option value="yes" <?php selected( $autoPlay, 'yes' ); ?>>Yes</option>
										</select>
										<br/><em>Default value is No.</em>
									</td>
								</tr>
			    		</table>
			    	</div>
			    	<div id="tab4">
			    		<table style="width:100%;">
			    			<tr>
									<td class="vcs_td_label">Readmore font size</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_readmore_font_size" class="vcs_field_input_number" value="<?php echo $readmoreFontSize; ?>"/> <em>in pixel</em>
										<br/><em>Default value is 12.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Readmore font color</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_readmore_font_color" class="color {hash:true} vcs_field_input_color" value="<?php echo $readmoreFontColor; ?>"/>
										<br/><em>Default value is #AAAAAA.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Readmore text</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_readmore_text" class="vcs_field_input_25" value="<?php echo $readmoreText; ?>"/>
										<br/><em>Default value is Read more.</em>
									</td>
								</tr>
			    		</table>
			    	</div>
			    	<?php if ( class_exists( 'VcsExtension' ) ) : ?>
			    	<div id="tab5">
			    		<table style="width:100%;">
			    			<tr>
									<td class="vcs_td_label">Open link in landing page</td>
									<td class="vcs_td_after_label">
										<select name="options_open_link_in_landing_page" class="vcs_chosen_single vcs_field_select_15">
											<option value="yes" <?php selected( $openInLandingPage, 'yes' ); ?>>Yes</option>
											<option value="no" <?php selected( $openInLandingPage, 'no' ); ?>>No</option>
										</select>
										<br/><em>Default value is Yes. If Yes is selected, the link will be opened inside an iframe in landing page.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Open link in new tab</td>
									<td class="vcs_td_after_label">
										<select name="options_open_link_in_new_tab" class="vcs_chosen_single vcs_field_select_15">
											<option value="yes" <?php selected( $openInNewTab, 'yes' ); ?>>Yes</option>
											<option value="no" <?php selected( $openInNewTab, 'no' ); ?>>No</option>
										</select>
										<br/><em>Default value is No. If Yes is selected, the link will be opened in new tab.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Font family</td>
									<td class="vcs_td_after_label">
										<ul class="viralcontentslider_thumbnail_options_lists">
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/default.png'; ?>" title="Default template"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="template" <?php checked( $fontFamily, 'template' ); ?> title="Default template"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/opensans.png'; ?>" title="Open Sans"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="opensans" <?php checked( $fontFamily, 'opensans' ); ?> title="Open Sans"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/lato.png'; ?>" title="Lato"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="lato" <?php checked( $fontFamily, 'lato' ); ?> title="Lato"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/oswald.png'; ?>" title="Oswald"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="oswald" <?php checked( $fontFamily, 'oswald' ); ?> title="Oswald"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/lora.png'; ?>" title="Lora"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="lora" <?php checked( $fontFamily, 'lora' ); ?> title="Lora"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/opensanscondensed.png'; ?>" title="Open Sans Condensed"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="opensanscondensed" <?php checked( $fontFamily, 'opensanscondensed' ); ?> title="Open Sans Condensed"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/raleway.png'; ?>" title="Raleway"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="raleway" <?php checked( $fontFamily, 'raleway' ); ?> title="Raleway"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/ubuntu.png'; ?>" title="Ubuntu"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="ubuntu" <?php checked( $fontFamily, 'ubuntu' ); ?> title="Ubuntu"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/yanonekaffeesatz.png'; ?>" title="Yanone Kaffeesatz"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="yanonekaffeesatz" <?php checked( $fontFamily, 'yanonekaffeesatz' ); ?> title="Yanone Kaffeesatz"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/dosis.png'; ?>" title="Dosis"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="dosis" <?php checked( $fontFamily, 'dosis' ); ?> title="Dosis"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/poiretone.png'; ?>" title="Poiret One"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="poiretone" <?php checked( $fontFamily, 'poiretone' ); ?> title="Poiret One"/>
												</center>
											</li>
											<li>
												<img class="vcs_font" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/fonts/play.png'; ?>" title="Play"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_font_family" value="play" <?php checked( $fontFamily, 'play' ); ?> title="Play"/>
												</center>
											</li>
										</ul>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Display in home</td>
									<td class="vcs_td_after_label">
										<select name="options_display_in_home" class="vcs_chosen_single vcs_field_select_15">
											<option value="no" <?php selected( $displayInHome, 'no' ); ?>>No</option>
											<option value="yes" <?php selected( $displayInHome, 'yes' ); ?>>Yes</option>
										</select>
										<br/><em>Default value is No. If Yes is selected, the slider will appear in home page.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Arrow style</td>
									<td class="vcs_td_after_label">
										<ul class="viralcontentslider_thumbnail_options_lists">
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a00.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="default" <?php checked( $arrowStyle, 'default' ); ?>/>
												</center>
											</li>
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a01.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="a01" <?php checked( $arrowStyle, 'a01' ); ?>/>
												</center>
											</li>
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a02.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="a02" <?php checked( $arrowStyle, 'a02' ); ?>/>
												</center>
											</li>
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a03.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="a03" <?php checked( $arrowStyle, 'a03' ); ?>/>
												</center>
											</li>
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a04.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="a04" <?php checked( $arrowStyle, 'a04' ); ?>/>
												</center>
											</li>
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a05.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="a05" <?php checked( $arrowStyle, 'a05' ); ?>/>
												</center>
											</li>
											<li>
												<img class="vcs_arrow" src="<?php echo VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/arrows/a06.png'; ?>"/>
												<center style="padding-top:5px;">
													<input type="radio" name="options_arrow_style" value="a06" <?php checked( $arrowStyle, 'a06' ); ?>/>
												</center>
											</li>
										</ul>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Autoinsert categories</td>
									<td class="vcs_td_after_label">
										<select name="options_autoinsert_in[]" class="vcs_chosen_multiple vcs_field_input_100" multiple>
											<?php
												$argsCategories = array(
													'orderby' => 'name',
													'hide_empty' => 1,
													'order' => 'ASC'
													);
												$categories = get_categories( $argsCategories );
												if ( !empty( $categories ) ) {
													foreach ( $categories as $category ) {
														$selected = "";
														if ( in_array( '{' . $category->cat_ID . '}', explode( ',', $autoinsertIn ) ) ) {
															$selected = "selected='selected'";
														}
														echo "<option value='" . $category->cat_ID . "' " . $selected . ">" . $category->name . "</option>";
													}
												}
											?>
										</select>
										<br/><em>Show slider without having to manually insert shortcode to each posts.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Autoinsert position</td>
									<td class="vcs_td_after_label">
										<select name="options_autoinsert_position" class="vcs_chosen_single vcs_field_select_25">
											<option value="before" <?php selected( $autoinsertPosition, 'before' ); ?>>Before content</option>
											<option value="after" <?php selected( $autoinsertPosition, 'after' ); ?>>After content</option>
											<option value="paragraph" <?php selected( $autoinsertPosition, 'paragraph' ); ?>>After paragraph</option>
										</select>
										<br/><em>Slider placement.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Autoinsert paragraph</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_autoinsert_paragraph" class="vcs_field_input_number" value="<?php echo $autoinsertParagraph; ?>" min="0"/>
										<br/><em>If autoinsert position is inside paragraph, please fill after which paragraph that the slider should be displayed.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Background color</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_background_color" class="color {hash:true} vcs_field_input_color" value="<?php echo $backgroundColor; ?>"/>
										<br/><em>Default value is #FFFFFF.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Shadow color</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_shadow_color" class="color {hash:true} vcs_field_input_color" value="<?php echo $shadowColor; ?>"/>
										<br/><em>Default value is #CCCCCC. It will only work if box shadow is more than 0.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Thumbnail dimension</td>
									<td class="vcs_td_after_label">
										Width : <input type="number" name="options_thumbnail_width" class="vcs_field_input_number" value="<?php echo $thumbnailWidth; ?>" min="0"/>
										<select name="options_thumbnail_width_type" class="vcs_field_input_select">
											<option value="px" <?php selected( $thumbnailWidthType, 'px' ); ?>>px (pixel)</option>
											<option value="%" <?php selected( $thumbnailWidthType, '%' ); ?>>% (percent)</option>
										</select>
										height : <input type="number" name="options_thumbnail_height" class="vcs_field_input_number" value="<?php echo $thumbnailHeight; ?>" min="0"/>
										<select name="options_thumbnail_height_type" class="vcs_field_input_select">
											<option value="%" <?php selected( $thumbnailHeightType, '%' ); ?>>% (percent)</option>
											<option value="px" <?php selected( $thumbnailHeightType, 'px' ); ?>>px (pixel)</option>
										</select>
										<br>
										<em>Default width is 120px and height is 100%.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Margin</td>
									<td class="vcs_td_after_label">
										Top : <input type="number" name="options_margin_top" class="vcs_field_input_number" value="<?php echo $marginTop; ?>" min="0"/>
										Bottom : <input type="number" name="options_margin_bottom" class="vcs_field_input_number" value="<?php echo $marginBottom; ?>" min="0"/>
										Left : <input type="number" name="options_margin_left" class="vcs_field_input_number" value="<?php echo $marginLeft; ?>" min="0"/>
										Right : <input type="number" name="options_margin_right" class="vcs_field_input_number" value="<?php echo $marginRight; ?>" min="0"/> <em>in pixel</em>
										<br>
										<em>Default value top = 0px, bottom = 5px, left = 0px and right = 0px.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Speed transition</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_speed_transition" class="vcs_field_input_number" value="<?php echo $speedTransition; ?>" min="0"/>
										<br/><em>Speed (in ms) for slide transition. Default value is 1000ms. 1000ms = 1 second.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Pause interval</td>
									<td class="vcs_td_after_label">
										<input type="number" name="options_pause_interval" class="vcs_field_input_number" value="<?php echo $pauseInterval; ?>" min="0"/>
										<br/><em>Interval (in seconds) to wait before the next slide is displayed. Default value is 5000ms. 1000ms = 1 second.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Facebook App ID</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_facebook_app_id" class="vcs_field_input_25" value="<?php echo $facebookAppId; ?>"/>
										<br/><em>If you don't provide this, the <a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=settings" target="_blank">global configuration</a> will be used instead.</em>
									</td>
								</tr>
								<tr>
									<td class="vcs_td_label">Twitter username</td>
									<td class="vcs_td_after_label">
										<input type="text" name="options_twitter_username" class="vcs_field_input_25" value="<?php echo $twitterUsername; ?>"/>
										<br/><em>If you don't provide this, the <a href="?page=<?php echo VIRALCONTENTSLIDER_PLUGIN_SLUG; ?>&tab=settings" target="_blank">global configuration</a> will be used instead.</em>
									</td>
								</tr>
			    		</table>
			    	</div>
			    <?php endif; ?>
			    </div>
			    <?php wp_nonce_field( 'viralcontentslider_options', 'viralcontentslider_options_nonce' ); ?>
					<input type="hidden" name="viralcontentslider_hidden_idviral" value="<?php echo $idViral; ?>"/>
					<input type="hidden" name="viralcontentslider_hidden_type" value="<?php echo $type; ?>"/>
					<button name="viralcontentslider_options" id="viralcontentslider_options" class="button-primary">Update Style & Options</button>
				</form>
			</div>
		</div>
	</div>

</div>
