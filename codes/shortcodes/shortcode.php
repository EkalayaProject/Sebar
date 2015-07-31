<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcode {
	private $viral;
	private $node;

	public function __construct( &$viral, $node ) {
		$this->viral = $viral;
		$this->node = $node;
	}

	public function generate() {
		global $wpdb;
		$tableOptions = $wpdb->prefix . 'viralcs_options';

		$notAvailableImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/images/imageNA.jpg';
		$defaultThumbnail = get_option( 'viralcontentslider_settings_default_thumbnail' );
		if ( !empty( $defaultThumbnail ) ) {
			$notAvailableImage = $defaultThumbnail;
		}

		$randSlider = $this->node . rand();

		/**
		 * Get styling and options for each type
		 */
		$options = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableOptions WHERE id_viral = %d", $this->viral->id ) );
		if ( empty( $options ) ) {
			$options = new stdClass();
			$options->display_type = 'random';
			$options->display_sort = 'asc';
			$options->box_shadow = 5;
			$options->text_align = 'justify';
			$options->title_limit_words = 6;
			$options->title_font_size = 16;
			$options->title_font_color = '#000000';
			$options->description_limit_words = 13;
			$options->description_font_size = 14;
			$options->description_font_color = '#2E2E2E';
			$options->nav_show = 'yes';
			$options->readmore_font_size = 12;
			$options->readmore_font_color = '#AAAAAA';
			$options->readmore_text = 'Read more';
			$options->autoplay = 'no';
		}

		/**
		 * If extension plugin is activated, send additional options to be processed
		 */
		if ( class_exists( 'VcsExtension' ) ) {
			$tableExtensionOptions = $wpdb->prefix . 'viralcs_extension_options';
			$extensionOptions = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableExtensionOptions WHERE id_viral = %d", $this->viral->id ) );
			if ( empty( $extensionOptions ) ) {
				$extensionOptions = new stdClass();
				$extensionOptions->open_in_landing_page = 'yes';
				$extensionOptions->open_in_new_tab = 'no';
				$extensionOptions->font_family = 'template';
				$extensionOptions->display_in_home = 'no';
				$extensionOptions->arrow_style = 'default';
				$extensionOptions->autoinsert_in = '';
				$extensionOptions->autoinsert_position = 'before';
				$extensionOptions->autoinsert_paragraph = 1;
				$extensionOptions->background_color = '#FFFFFF';
				$extensionOptions->shadow_color = '#CCCCCC';
				$extensionOptions->thumbnail_width = 120;
				$extensionOptions->thumbnail_height = 100;
				$extensionOptions->thumbnail_width_type = 'px';
				$extensionOptions->thumbnail_height_type = '%';
				$extensionOptions->margin_top = 0;
				$extensionOptions->margin_bottom = 5;
				$extensionOptions->margin_left = 0;
				$extensionOptions->margin_right = 0;
				$extensionOptions->speed_transition = 1000;
				$extensionOptions->pause_interval = 5000;
			}
		}

		switch ( $this->viral->type ) {
			case 'article':
				$contents = $this->get_shortcode_content_article( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'category':
				$contents = $this->get_shortcode_content_category( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'custom':
				$contents = $this->get_shortcode_content_custom( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'tag':
				$contents = $this->get_shortcode_content_tag( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'author':
				$contents = $this->get_shortcode_content_author( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'feed':
				$contents = $this->get_shortcode_content_feed( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'link':
			case 'customlink':
				$contents = $this->get_shortcode_content_link( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
			case 'video':
				$contents = $this->get_shortcode_content_video( $this->viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
				break;
		}
		return $contents;
	}

	/**
	 * Generate slider if type is Article
	 */
	private function get_shortcode_content_article( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeArticle' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/article/article.shortcode.php' );
		}
		$article = new VcsShortcodeArticle( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'article', $viral, $options, $extensionOptions, $randSlider );
		return $article->generate() . $styleScripts;
	}

	/**
	 * Generate slider if type is Category
	 */
	private function get_shortcode_content_category( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeCategory' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/category/category.shortcode.php' );
		}
		$category = new VcsShortcodeCategory( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'category', $viral, $options, $extensionOptions, $randSlider );
		return $category->generate() . $styleScripts;
	}

	/**
	 * Generate slider if type is Custom
	 */
	private function get_shortcode_content_custom( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeCustom' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/custom/custom.shortcode.php' );
		}
		$custom = new VcsShortcodeCustom( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'custom', $viral, $options, $extensionOptions, $randSlider );
		return $custom->generate() . $styleScripts;
	}

	/**
	 * Generate slider if type is Tag
	 */
	private function get_shortcode_content_tag( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeTag' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/tag/tag.shortcode.php' );
		}
		$tag = new VcsShortcodeTag( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'tag', $viral, $options, $extensionOptions, $randSlider );
		return $tag->generate() . $styleScripts;
	}

	/**
	 * Generate slider if type is Author
	 */
	private function get_shortcode_content_author( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeAuthor' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/author/author.shortcode.php' );
		}
		$author = new VcsShortcodeAuthor( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'author', $viral, $options, $extensionOptions, $randSlider );
		return $author->generate() . $styleScripts;
	}

	/**
	 * Generate slider if type is Author
	 */
	private function get_shortcode_content_feed( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeFeed' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/feed/feed.shortcode.php' );
		}
		$feed = new VcsShortcodeFeed( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'feed', $viral, $options, $extensionOptions, $randSlider );
		return $feed->generate() . $styleScripts;
	}

	/**
	 * Get slider if type is Link
	 */
	private function get_shortcode_content_link( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeLink' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/link/link.shortcode.php' );
		}
		$link = new VcsShortcodeLink( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'link', $viral, $options, $extensionOptions, $randSlider );
		return $link->generate() . $styleScripts;
	}

	/**
	 * Get slider if type is Video
	 */
	private function get_shortcode_content_video( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		if ( !class_exists( 'VcsShortcodeVideo' ) ) {
			include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/video/video.shortcode.php' );
		}
		$video = new VcsShortcodeVideo( $viral, $options, $extensionOptions, $randSlider, $notAvailableImage );
		$styleScripts = $this->get_style_scripts( 'video', $viral, $options, $extensionOptions, $randSlider );
		return $video->generate() . $styleScripts;
	}

	/**
	 * Generate style and js code
	 */
	private function get_style_scripts( $type, &$viral, &$options, &$extensionOptions, $randSlider ) {
		/**
		 * If extension plugin is activated, send additional options to be processed
		 */
		$cssBackgroundColor = 'background-color: #FFFFFF;';
		$shadowColor = '#CCCCCC';
		$cssFontFamily = '';
		$cThumbnailWidth = 120;
		$cThumbnailHeight = 100;
		$cThumbnailWidthType = 'px';
		$cThumbnailHeightType = '%';
		$cThumbnailWidthDimension = $cThumbnailWidth . $cThumbnailWidthType;
		$cThumbnailHeightDimension = $cThumbnailHeight . $cThumbnailHeightType;
		$cssMargin = '';
		$cSpeedTransition = 1000;
		$cPauseInterval = 5000;

		$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a00.png';
		$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 50px;
	  display: block;
	  top: 50%;
	  height: 50px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.1;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;

		if ( class_exists( 'VcsExtension' ) ) {
			$cssBackgroundColor = 'background-color: ' . $extensionOptions->background_color . ';';
			$shadowColor = $extensionOptions->shadow_color;

			$cThumbnailWidth = $extensionOptions->thumbnail_width;
			$cThumbnailHeight = $extensionOptions->thumbnail_height;
			$cThumbnailWidthType = $extensionOptions->thumbnail_width_type;
			$cThumbnailHeightType = $extensionOptions->thumbnail_height_type;
			$cThumbnailWidthDimension = $cThumbnailWidth . $cThumbnailWidthType;
			$cThumbnailHeightDimension = $cThumbnailHeight . $cThumbnailHeightType;

			$cMarginTop = $extensionOptions->margin_top;
			$cMarginBottom = $extensionOptions->margin_bottom;
			$cMarginLeft = $extensionOptions->margin_left;
			$cMarginRight = $extensionOptions->margin_right;

			$cSpeedTransition = $extensionOptions->speed_transition;
			$cPauseInterval = $extensionOptions->pause_interval;

			$cssMargin = <<<MARGIN
margin-top: {$cMarginTop}px;
margin-bottom: {$cMarginBottom}px;
margin-left: {$cMarginLeft}px;
margin-right: {$cMarginRight}px;
MARGIN;

			if ( $extensionOptions->font_family != 'template' ) {
				switch ( $extensionOptions->font_family ) {
					case 'opensans':
						$fontUrl = '//fonts.googleapis.com/css?family=Open+Sans';
						$cssFontFamily = "font-family: 'Open Sans', sans-serif;";
						break;
					case 'lato':
						$fontUrl = '//fonts.googleapis.com/css?family=Lato';
						$cssFontFamily = "font-family: 'Lato', sans-serif;";
						break;
					case 'oswald':
						$fontUrl = '//fonts.googleapis.com/css?family=Oswald';
						$cssFontFamily = "font-family: 'Oswald', sans-serif;";
						break;
					case 'lora':
						$fontUrl = '//fonts.googleapis.com/css?family=Lora';
						$cssFontFamily = "font-family: 'Lora', serif;";
						break;
					case 'opensanscondensed':
						$fontUrl = '//fonts.googleapis.com/css?family=Open+Sans+Condensed:300';
						$cssFontFamily = "font-family: 'Open Sans Condensed', sans-serif;";
						break;
					case 'raleway':
						$fontUrl = '//fonts.googleapis.com/css?family=Raleway';
						$cssFontFamily = "font-family: 'Raleway', sans-serif;";
						break;
					case 'ubuntu':
						$fontUrl = '//fonts.googleapis.com/css?family=Ubuntu';
						$cssFontFamily = "font-family: 'Ubuntu', sans-serif;";
						break;
					case 'yanonekaffeesatz':
						$fontUrl = '//fonts.googleapis.com/css?family=Yanone+Kaffeesatz';
						$cssFontFamily = "font-family: 'Yanone Kaffeesatz', sans-serif;";
						break;
					case 'dosis':
						$fontUrl = '//fonts.googleapis.com/css?family=Dosis';
						$cssFontFamily = "font-family: 'Dosis', sans-serif;";
						break;
					case 'poiretone':
						$fontUrl = '//fonts.googleapis.com/css?family=Poiret+One';
						$cssFontFamily = "font-family: 'Poiret One', cursive;";
						break;
					case 'play':
						$fontUrl = '//fonts.googleapis.com/css?family=Play';
						$cssFontFamily = "font-family: 'Play', sans-serif;";
						break;
				}

				wp_register_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-' . $extensionOptions->font_family, $fontUrl );
				wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-' . $extensionOptions->font_family );
			}

			switch ( $extensionOptions->arrow_style ) {
				case 'default':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a00.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 50px;
	  display: block;
	  top: 50%;
	  height: 50px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.1;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
				case 'a01':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a01.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 55px;
	  display: block;
	  top: 50%;
	  height: 55px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.5;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
				case 'a02':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a02.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 40px;
	  display: block;
	  top: 50%;
	  height: 45px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.5;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
				case 'a03':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a03.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 40px;
	  display: block;
	  top: 50%;
	  height: 45px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.5;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
				case 'a04':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a04.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 40px;
	  display: block;
	  top: 50%;
	  height: 45px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.5;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
				case 'a05':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a05.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 55px;
	  display: block;
	  top: 50%;
	  height: 55px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.5;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
				case 'a06':
					$arrowImage = VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL . '/scripts/lightSlider/img/a06.png';
					$arrow = <<<ARROW
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a {
	  width: 55px;
	  display: block;
	  top: 50%;
	  height: 55px;
	  background-image: url('{$arrowImage}');
	  cursor: pointer;
	  position: absolute;
	  z-index: 9999;
	  margin-top: -25px;
	  opacity: 0.5;
	  -webkit-transition: opacity 0.35s linear 0s;
	  transition: opacity 0.35s linear 0s;
	  border-bottom: 0px;
	}
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider} .lSAction > a:hover {
    opacity: 1;
	}
</style>
ARROW;
					break;
			}
		}

		if ( $this->node == 'sdc' ) {
			$style = <<<STYLE
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider}{
		box-shadow: 0px 0px {$options->box_shadow}px {$shadowColor};
		{$cssMargin}
		{$cssFontFamily}
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} li{
		{$cssBackgroundColor}
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_media_inline {
		float: left;
		margin-right: 10px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} h3{
		margin: 0;
		color: {$options->title_font_color};
		font-size: {$options->title_font_size}px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_image{
		width: {$cThumbnailWidthDimension};
		height: {$cThumbnailHeightDimension};
		min-height: 100px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_headline{
		clear: none;
		margin: 0;
		padding-top: 10px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_description{
		font-size: {$options->description_font_size}px;
		text-align: {$options->text_align};
		margin: 5px 10px 0 0;
		color: {$options->description_font_color};
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_readmore{
		margin:0;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_readmore_text{
		text-decoration: none;
		border-bottom: 0px;
		font-size: {$options->readmore_font_size}px;
		color: {$options->readmore_font_color};
	}
</style>
STYLE;
		}

		if ( $this->node == 'wdg' ) {
			$marginTop = get_option( 'viralcontentslider_settings_widget_margin_top', 0 );
			$marginBottom = get_option( 'viralcontentslider_settings_widget_margin_bottom', 0 );
			$marginLeft = get_option( 'viralcontentslider_settings_widget_margin_left', 10 );
			$marginRight = get_option( 'viralcontentslider_settings_widget_margin_right', 10 );
			$titleFontSize = get_option( 'viralcontentslider_settings_widget_title_font_size', 14 );
			$descriptionFontSize = get_option( 'viralcontentslider_settings_widget_description_font_size', 12 );
			$readmoreFontSize = get_option( 'viralcontentslider_settings_widget_readmore_font_size', 11 );
			$thumbnailWidth = get_option( 'viralcontentslider_settings_widget_thumbnail_width', 85 );
			$thumbnailHeight = get_option( 'viralcontentslider_settings_widget_thumbnail_height', 100 );
			$thumbnailWidthType = get_option( 'viralcontentslider_settings_widget_thumbnail_width_type', 'px' );
			$thumbnailHeightType = get_option( 'viralcontentslider_settings_widget_thumbnail_height_type', '%' );
			$thumbnailWidthDimension = $thumbnailWidth . $thumbnailWidthType;
			$thumbnailHeightDimension = $thumbnailHeight . $thumbnailHeightType;

			$style = <<<STYLE
<style type="text/css">
	.viralcontentslider_slider_main_{$type}_{$viral->id}_{$randSlider}{
		box-shadow: 0px 0px {$options->box_shadow}px {$shadowColor};
		margin-top: {$marginTop}px;
		margin-bottom: {$marginBottom}px;
		margin-left: {$marginLeft}px;
		margin-right: {$marginRight}px;
		{$cssFontFamily}
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} li{
		{$cssBackgroundColor}
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_media_inline {
		float: left;
		margin-right: 10px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} h3{
		margin: 0;
		color: {$options->title_font_color};
		font-size: {$titleFontSize}px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_image{
		width: {$thumbnailWidthDimension};
		height: {$thumbnailHeightDimension};
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_headline{
		clear: none;
		margin: 0;
		padding-top: 5px;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_description{
		font-size: {$descriptionFontSize}px;
		text-align: {$options->text_align};
		margin: 5px 10px 0 0;
		color: {$options->description_font_color};
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_readmore{
		margin:0;
	}
	.viralcontentslider_slider_ul_{$type}_{$viral->id}_{$randSlider} .vcs_readmore_text{
		text-decoration: none;
		border-bottom: 0px;
		font-size: {$readmoreFontSize}px;
		color: {$options->readmore_font_color};
	}
</style>
STYLE;
		}

		if ( $options->nav_show == 'yes' ) {
			$navShowOption = 'controls: true,';
		} else {
			$navShowOption = 'controls: false,';
		}

		if ( $options->autoplay == 'yes' ) {
			$autoPlay = 'auto: true,';
		} else {
			$autoPlay = 'auto: false,';
		}

		$script = <<<JS
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#viralcontentslider_{$type}_{$viral->id}_{$randSlider}").lightSlider({
	    item:1,
	    loop:true,
	    keyPress:true,
	    adaptiveHeight:true,
	    pager:false,
	    gallery:false,
	    autoWidth:false,
	    controls:true,
	    slideMargin:10,
	    mode:'slide',
	    speed:{$cSpeedTransition},
	    pause:{$cPauseInterval},
	    {$navShowOption}
	    {$autoPlay}
	  });
	});
</script>
JS;
		return $arrow . $style . $script;
	}
}
