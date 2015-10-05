<?php
/*
Plugin Name: Sebar
Plugin URI: http://mtasuandi.com
Description: Display awesome related post for better user engagement.
Version: 1.0.1
Author: M Teguh A Suandi
Author URI: http://mtasuandi.com
License: GPLv2 or later.
*/

/**
 * Prevent the plugin file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

/**
 * Defined values
 */
define( 'VIRALCONTENTSLIDER_VERSION', '1.0.1' );
define( 'VIRALCONTENTSLIDER_PLUGIN_SLUG', 'viraltrafficboost' );
define( 'VIRALCONTENTSLIDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VIRALCONTENTSLIDER_PLUGIN_ASSETS_URL', plugins_url( 'assets', __FILE__ ) );

define( 'VCSEXTENSION_VERSION', '1.0.1' );
define( 'VCSEXTENSION_PLUGIN_SLUG', 'vtbextension' );
define( 'VCSEXTENSION_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'VCSEXTENSION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) . 'vtbextension/' );

/**
 * Plugin class
 * Class name may called in some functions in extension or primary plugin, please beware!
 */
class ViralContentSlider {
	/**
	 * Class Constructor
	 * Put all the WordPress Hooks here
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'viralcontentslider_run_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'viralcontentslider_run_deactivation' ) );
		add_action( 'init', array( $this, 'viralcontentslider_init' ) );
		add_action( 'admin_menu', array( $this, 'viralcontentslider_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'viralcontentslider_admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_viral' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_options' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_settings' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_trash' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_trash_customlink' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_trash_content_feed' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_trash_content_video' ) );
		add_shortcode( 'sebar', array( $this, 'viralcontentslider_shortcode' ) );
		add_action( 'admin_init', array( $this, 'viralcontentslider_handle_feed' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'viralcontentslider_wp_enqueue_script' ) );
		add_action( 'template_redirect', array( $this, 'viralcontentslider_thumbnail_generator' ) );
		add_action( 'wp_ajax_viralcontentslider_ajax', array( $this, 'viralcontentslider_ajax' ) );
		add_filter( 'cron_schedules', array( $this, 'viralcontentslider_cron_interval' ) );
		add_action( 'init', array( $this, 'viralcontentslider_setup_schedule' ) );
		add_action( 'viralcontentslider_fetch_feed_event', array( $this, 'viralcontentslider_fetch_feed_event_run') );
		add_action( 'viralcontentslider_delete_old_data_event', array( $this, 'viralcontentslider_delete_old_data_event_run') );
		add_filter( 'tiny_mce_version', array( $this, 'viralcontentslider_tiny_mce_version' ) );
		add_action( 'init', array( $this, 'viralcontentslider_tiny_mce_button' ) );
		add_action( 'wp_ajax_viralcontentslider_tinymce', array( $this, 'viralcontentslider_tinymce_ajax' ) );

		add_action( 'widgets_init', array( $this, 'vcsextension_widget' ) );
		add_action( 'admin_init', array( $this, 'vcsextension_handle_extension_options' ) );
		add_action( 'template_redirect', array( $this, 'vcsextension_landing_page' ) );
		add_filter( 'the_content', array( $this, 'vcsextension_filter_content') );
	}

	/**
	 * Initialize Widget
	 */
	public function vcsextension_widget() {
		if ( !class_exists( 'VcsExtension_Widget' ) ) {
			include_once( VCSEXTENSION_PLUGIN_DIR . 'codes/widget/widget.php' );
		}
		register_widget( 'VcsExtension_Widget' );
	}

	/**
	 * Handle to save additional options
	 */
	public function vcsextension_handle_extension_options() {
		global $wpdb;
		$tableExtensionOptions = $wpdb->prefix . 'viralcs_extension_options';
		require( VCSEXTENSION_PLUGIN_DIR . 'codes/options/extension.options.handle.php' );
	}

	/**
	 * Landing page
	 */
	public function vcsextension_landing_page() {
		$slug = VIRALCONTENTSLIDER_PLUGIN_SLUG;
		if ( isset( $_GET['vcs_landing'] ) && $_GET['vcs_landing'] == $slug ) {
			if ( isset( $_GET['type'] ) && isset( $_GET['te'] ) ) {
				$linkBack = base64_decode( str_replace( ' ', '+', $_GET['back'] ) );
				$facebookAppId = get_option( 'viralcontentslider_settings_social_media_facebook_app_id', '' );
				$twitterUsername = get_option( 'viralcontentslider_settings_social_media_twitter_username', '' );

				$type = sanitize_text_field( $_GET['type'] );
				$idViral = (int)base64_decode( $_GET['te'] );
				$idObject = (int)base64_decode( $_GET['obj'] );
				$permalink = base64_decode( str_replace( ' ', '+', $_GET['permalink'] ) );
				$youtubeEmbedPermalink = $permalink;

				$extOption = $this->get_extension_options( $idViral );
				if ( !empty( $extOption->facebook_app_id ) ) {
					$facebookAppId = $extOption->facebook_app_id;
				}

				if ( !empty( $extOption->twitter_username ) ) {
					$twitterUsername = $extOption->twitter_username;
				}

				switch ( $type ) {
					case 'video':
						$video = $this->get_video_ext( $idObject, $idViral );
						if ( empty( $video ) ) {
							die( 'Invalid parameters!' );
						}
						$permalink = 'https://www.youtube.com/watch?v=' . end( explode( '/', $permalink ) );
						$title = $video->title;
						break;
					case 'feed':
						$feed = $this->get_feed_ext( $idObject, $idViral );
						if ( empty( $feed ) ) {
							die( 'Invalid parameters!' );
						}
						$title = $feed->title;
						break;
					case 'link':
						$link = $this->get_link_ext( $idObject, $idViral );
						$title = $link->title;
						break;
					case 'article':
					case 'category':
					case 'tag':
					case 'author':
					case 'custom':
						$title = get_the_title( $idObject );
						break;
				}
				include_once( VCSEXTENSION_PLUGIN_DIR . 'codes/landing/landing.page.php' );
				exit();
			}
		}
	}

	/**
	 * Filter content
	 * Inject the slider based on the settings
	 */
	public function vcsextension_filter_content( $content ) {
		$totalParagraphInContent = $this->vcsextension_count_paragraph( $content );

		global $wpdb;
		$tableViral = $wpdb->prefix . 'viralcs_virals';
		$tableExtensionOptions = $wpdb->prefix . 'viralcs_extension_options';

		$slider = '';
		$extOptions = array();
		$categories = get_the_category();
		if ( !empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$idCategory = '{' . $category->cat_ID . '}';
				$extensionOptions = $wpdb->get_results( "SELECT * FROM $tableExtensionOptions WHERE autoinsert_in LIKE '%$idCategory%'" );
				if ( !empty( $extensionOptions ) ) {
					foreach ( $extensionOptions as $extensionOption ) {
						$extOptions[$extensionOption->id] = array(
							'id_viral' => $extensionOption->id_viral,
							'autoinsert_position' => $extensionOption->autoinsert_position,
							'autoinsert_paragraph' => $extensionOption->autoinsert_paragraph
						);
					}
				}
			}
		}

		if ( !empty( $extOptions ) ) {
			if ( !class_exists( 'VcsShortcode' ) ) {
				include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/shortcode.php' );
			}

			foreach ( $extOptions as $extOption ) {
				$extOption = (object)$extOption;
				$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $extOption->id_viral ) );
				if ( !empty( $viral ) ) {
					$shortcode = new VcsShortcode( $viral, 'sdc' );
					$slider = $shortcode->generate();

					$paragraphId = (int)$extOption->autoinsert_paragraph;
					if ( $extOption->autoinsert_position != 'paragraph' ) {
						$paragraphId = $extOption->autoinsert_position;
					}

					if ( is_int( $paragraphId ) ) {
						if ( $paragraphId > $totalParagraphInContent ) {
							$paragraphId = 'after';
						}
					}

					$content = $this->vcsextension_insert_after_paragraph( $slider, $paragraphId, $content );
				}
			}
		}
		return $content;
	}

	private function vcsextension_count_paragraph( $content ) {
		$closingParagraph = '</p>';
		$paragraphs = explode( $closingParagraph, $content );
		$countParagraph = count( $paragraphs );
		return $countParagraph - 1;
	}

	/**
	 * Inject slider in specifi paragraph
	 */
	private function vcsextension_insert_after_paragraph( $insertion, $paragraphId, $content ) {
		$closingParagraph = '</p>';
		$paragraphs = explode( $closingParagraph, $content );
		$countParagraph = count( $paragraphs );

		if ( $paragraphId == 'after' ) {
			return $content . $insertion;
		} elseif ( $paragraphId == 'before' ) {
			return $insertion . $content;
		} elseif ( $paragraphId > $countParagraph ) {
			$paragraphs[$countParagraph - 1] .= $closingParagraph;
			$paragraphs[$countParagraph - 1] .= $insertion;
		} else {
			foreach ($paragraphs as $index => $paragraph) {
				if ( trim( $paragraph ) ) {
					$paragraphs[$index] .= $closingParagraph;
				}
				if ( $paragraphId == $index + 1 ) {
					$paragraphs[$index] .= $insertion;
				}
			}
		}
		return implode( '', $paragraphs );
	}

	/**
	 * Get Extension options
	 * Used in codes/options/extension.options.handle.php
	 */
	private function get_extension_options( $idViral ) {
		global $wpdb;
		$tableExtensionOptions = $wpdb->prefix . 'viralcs_extension_options';
		$get = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableExtensionOptions WHERE id_viral = %d", $idViral ) );
		return $get;
	}

	/**
	 * Get video object
	 */
	private function get_video_ext( $id, $idViral ) {
		global $wpdb;
		$tableVideos = $wpdb->prefix . 'viralcs_videos';
		$video = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableVideos WHERE id = %d AND id_viral = %d AND deleted_at IS NULL", $id, $idViral ) );
		return $video;
	}

	/**
	 * Get feed object
	 */
	private function get_feed_ext( $id, $idViral ) {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$feed = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableFeeds WHERE id = %d AND id_viral = %d AND deleted_at IS NULL", $id, $idViral ) );
		return $feed;
	}

	/**
	 * Get link object
	 */
	private function get_link_ext( $id, $idViral ) {
		global $wpdb;
		$tableLinks = $wpdb->prefix . 'viralcs_links';
		$link = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableLinks WHERE id = %d AND id_viral = %d AND deleted_at IS NULL", $id, $idViral ) );
		return $link;
	}

	/**
	 * Multisite
	 */
	public function viralcontentslider_run_activation( $networkwide ) {
		global $wpdb, $switched;
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $networkwide ) {
				$old_blog	= $wpdb->blogid;
				$blogids	= $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
				foreach ( $blogids as $blogid ) {
					switch_to_blog( $blogid );
					$this->viralcontentslider_activation();
				}
				switch_to_blog( $old_blog );
				return;
			}
		}
		$this->viralcontentslider_activation();
	}

	/**
	 * Clear scheduled event if the plugin is deactivated
	 */
	public function viralcontentslider_run_deactivation() {
		wp_clear_scheduled_hook( 'viralcontentslider_fetch_feed_event' );
		wp_clear_scheduled_hook( 'viralcontentslider_delete_old_data_event' );
	}

	/**
	 * Required process during the plugin activation should be put here
	 * @Database creation
	 * Related function is register_deactivation_hook which is hook during the plugin deactivation
	 */
	public function viralcontentslider_activation() {
		if ( !function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$tableLinks = $wpdb->prefix . 'viralcs_links';
		$tableOptions = $wpdb->prefix . 'viralcs_options';
		$tableVideos = $wpdb->prefix . 'viralcs_videos';
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';
		$tableExtensionOptions = $wpdb->prefix . 'viralcs_extension_options';

		$sqlViral = <<<SQL
CREATE TABLE {$tableVirals} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
type VARCHAR(10) NOT NULL,
name VARCHAR(50) NOT NULL,
categories VARCHAR(255) NOT NULL,
tags VARCHAR(255) NOT NULL,
authors VARCHAR(255) NOT NULL,
posts VARCHAR(255) NOT NULL,
pages VARCHAR(255) NOT NULL,
feed VARCHAR(255) NOT NULL,
videos VARCHAR(255) NOT NULL,
links TEXT NOT NULL,
display INT(11) NOT NULL,
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
deleted_at DATETIME NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlViral );

		$sqlFeed = <<<SQL
CREATE TABLE {$tableFeeds} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
id_viral INT(11) NOT NULL,
feed VARCHAR(255) NOT NULL,
link VARCHAR(255) NOT NULL,
title VARCHAR(255) NOT NULL,
description TEXT NOT NULL,
date_published DATETIME NOT NULL,
image VARCHAR(255) NOT NULL,
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
deleted_at DATETIME NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlFeed );

		$sqlLink = <<<SQL
CREATE TABLE {$tableLinks} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
id_viral INT(11) NOT NULL,
link VARCHAR(255) NOT NULL,
title VARCHAR(255) NOT NULL,
description TEXT NOT NULL,
image VARCHAR(255) NOT NULL,
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
deleted_at DATETIME NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlLink );

		$sqlOption = <<<SQL
CREATE TABLE {$tableOptions} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
id_viral INT(11) NOT NULL,
type VARCHAR(10) NOT NULL,
display_type VARCHAR(28) NOT NULL DEFAULT 'random',
display_sort VARCHAR(5) NOT NULL DEFAULT 'asc',
box_shadow INT(11) NOT NULL DEFAULT 5,
text_align VARCHAR(10) NOT NULL DEFAULT 'justify',
title_limit_words INT(11) NOT NULL DEFAULT 6,
title_font_size INT(11) NOT NULL DEFAULT 16,
title_font_color VARCHAR(28) NOT NULL DEFAULT '#000000',
description_limit_words INT(11) NOT NULL DEFAULT 13,
description_font_size INT(11) NOT NULL DEFAULT 14,
description_font_color VARCHAR(28) NOT NULL DEFAULT '#2E2E2E',
nav_show VARCHAR(5) NOT NULL DEFAULT 'yes',
readmore_font_size INT(11) NOT NULL DEFAULT 12,
readmore_font_color VARCHAR(28) NOT NULL DEFAULT '#AAAAAA',
readmore_text VARCHAR(50) NOT NULL DEFAULT 'Read more',
autoplay VARCHAR(5) NOT NULL DEFAULT 'no',
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlOption );

		$sqlVideo = <<<SQL
CREATE TABLE {$tableVideos} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
id_viral INT(11) NOT NULL,
video_id VARCHAR(28) NOT NULL,
published DATETIME NOT NULL,
title VARCHAR(255) NOT NULL,
description TEXT NOT NULL,
duration VARCHAR(255) NOT NULL,
str_duration INT(11) NOT NULL,
thumbnail VARCHAR(255) NOT NULL,
link VARCHAR(255) NOT NULL,
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
deleted_at DATETIME NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlVideo );

		$sqlAnalytic = <<<SQL
CREATE TABLE {$tableAnalytics} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
type VARCHAR(50) NOT NULL,
title VARCHAR(255) NOT NULL,
url VARCHAR(255) NOT NULL,
fb_share INT(11) NOT NULL,
fb_like INT(11) NOT NULL,
fb_comment INT(11) NOT NULL,
fb_total INT(11) NOT NULL,
fb_click INT(11) NOT NULL,
tweet INT(11) NOT NULL,
linkedin INT(11) NOT NULL,
pinterest INT(11) NOT NULL,
googleplus INT(11) NOT NULL,
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
deleted_at DATETIME NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlAnalytic );

		$sqlExtensionOptions = <<<SQL
CREATE TABLE {$tableExtensionOptions} (
id INT(11) unsigned NOT NULL AUTO_INCREMENT,
id_viral INT(11) NOT NULL,
type VARCHAR(10) NOT NULL,
open_in_landing_page VARCHAR(5) NOT NULL DEFAULT 'yes',
open_in_new_tab VARCHAR(5) NOT NULL DEFAULT 'no',
font_family VARCHAR(255) NOT NULL DEFAULT 'template',
display_in_home VARCHAR(5) NOT NULL DEFAULT 'no',
arrow_style VARCHAR(10) NOT NULL DEFAULT 'default',
autoinsert_in VARCHAR(255) NOT NULL,
autoinsert_position VARCHAR(10) NOT NULL DEFAULT 'before',
autoinsert_paragraph INT(11) NOT NULL DEFAULT 1,
background_color VARCHAR(28) NOT NULL DEFAULT '#FFFFFF',
shadow_color VARCHAR(28) NOT NULL DEFAULT '#CCCCCC',
thumbnail_width INT(11) NOT NULL DEFAULT 120,
thumbnail_height INT(11) NOT NULL DEFAULT 100,
thumbnail_width_type VARCHAR(5) NOT NULL DEFAULT 'px',
thumbnail_height_type VARCHAR(5) NOT NULL DEFAULT '%',
margin_top INT(11) NOT NULL DEFAULT 0,
margin_bottom INT(11) NOT NULL DEFAULT 5,
margin_left INT(11) NOT NULL DEFAULT 0,
margin_right INT(11) NOT NULL DEFAULT 0,
speed_transition INT(11) NOT NULL DEFAULT 1000,
pause_interval INT(11) NOT NULL DEFAULT 5000,
facebook_app_id VARCHAR(50) NOT NULL,
twitter_username VARCHAR(50) NOT NULL,
created_at DATETIME NOT NULL,
updated_at TIMESTAMP NOT NULL,
PRIMARY KEY id (id)
) DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci;
SQL;
		dbDelta( $sqlExtensionOptions );
	}

	/**
	 * Include the extensions plugin
	 */
	public function viralcontentslider_init() {
		require_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'vtbextension/vcsextension.php' );
	}

	/**
	 * Create Wordpress admin menu
	 * Display license menu if the plugin is not activated
	 */
	public function viralcontentslider_admin_menu() {
		add_menu_page( __( 'Sebar', VIRALCONTENTSLIDER_PLUGIN_SLUG ),
			__( 'Sebar', VIRALCONTENTSLIDER_PLUGIN_SLUG ),
			'manage_options',
			VIRALCONTENTSLIDER_PLUGIN_SLUG,
			array( $this, 'viralcontentslider_main_page' ), 'dashicons-share-alt'
		);
	}

	/**
	 * Display license page if the plugin is not activated
	 */
	public function viralcontentslider_license_page() {
		require_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/license/license.php' );
	}

	/**
	 * PHP Curl request
	 */
	private function cCurl( $url ) {
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:40.0) Gecko/20100101 Firefox/40.0',
			CURLOPT_AUTOREFERER => true,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_SSL_VERIFYPEER => false
	  );

		$ch = curl_init( $url );
	  curl_setopt_array( $ch, $options );
	  $content = curl_exec( $ch );
	  curl_close( $ch );
	  return $content;
	}

	/**
	 * Handle navigation for the main page
	 */
	public function viralcontentslider_main_page() {
		$tab = '';
		if ( isset( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( $_GET['tab'] );
		}

		$node = '';
		if ( isset( $_GET['node'] ) ) {
			$node = sanitize_text_field( $_GET['node'] );
		}

		switch ( $tab ) {
			case 'viral':
				switch ( $node ) {
					case 'video':
						if ( isset( $_GET['action'] ) && $_GET['action'] == 'manage' ) {
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/table/manage.video.table.class.php' );
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/video/manage.video.php' );
						} else if ( isset( $_GET['action'] ) && $_GET['action'] == 'update' ) {
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/video/manage.video.update.php' );
						} else {
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/video/video.php' );
						}
						break;
					case 'feed':
						if ( isset( $_GET['action'] ) && $_GET['action'] == 'manage' ) {
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/table/manage.content.feed.table.class.php' );
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/feed/manage.content.php' );
						} else if( isset( $_GET['action'] ) && $_GET['action'] == 'update' ) {
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/feed/manage.content.update.php' );
						} else {
							require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/feed/feed.php' );
						}
						break;
					case 'article':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/article/article.php' );
						break;
					case 'category':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/category/category.php' );
						break;
					case 'tag':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/tag/tag.php' );
						break;
					case 'author':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/author/author.php' );
						break;
					case 'custom':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/custom/custom.php' );
						break;
					case 'link':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/link/link.php' );
						break;
					case 'customlink':
						require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/virals/link/custom.link.php' );
						break;
				}
				break;
			case 'options':
				require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/options/options.php' );
				break;
			case 'settings':
				require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/settings/settings.php' );
				break;
			case 'analytics':
				require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/analytics/analytics.class.php' );
				require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/analytics/analytics.php' );
				break;
			default:
				require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/table/viral.table.class.php' );
				require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'views/dashboard/dashboard.php' );
				break;
		}
	}

	/**
	 * Include all css files and javascripts/jQuery library
	 * Implement to the admin page
	 */
	public function viralcontentslider_admin_enqueue_scripts() {
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-admin', plugins_url( 'assets/styles/app.admin.css', __FILE__ ), false, 'screen' );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-shortcode-admin', plugins_url( 'assets/styles/app.shortcode.admin.css', __FILE__ ), false, 'screen' );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-tabs-admin', plugins_url( 'assets/styles/app.tabs.admin.css', __FILE__ ), false, 'screen' );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-jqueryui-lightness', plugins_url( 'assets/styles/jqueryui/jquery-ui.css', __FILE__ ), false, 'screen' );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-messenger', plugins_url( 'assets/scripts/messenger/css/messenger.css', __FILE__ ), false, 'screen' );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-messenger-theme', plugins_url( 'assets/scripts/messenger/css/messenger-theme-flat.css', __FILE__ ), false, 'screen' );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-chosen', plugins_url( 'assets/scripts/chosen/chosen.min.css', __FILE__ ), false, 'screen' );

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . 'google-js-api', '//www.google.com/jsapi' );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-jscolor', plugins_url( 'assets/scripts/jscolor/jscolor.js', __FILE__ ), 'jquery' );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-admin-feed', plugins_url( 'assets/scripts/app.admin.feed.js', __FILE__ ), array(), get_bloginfo( 'version' ), true );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-admin', plugins_url( 'assets/scripts/app.admin.js', __FILE__ ), array(), get_bloginfo( 'version' ), true );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-tabs-admin', plugins_url( 'assets/scripts/app.tabs.admin.js', __FILE__ ), array(), get_bloginfo( 'version' ), true );
		wp_localize_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-admin', 'viralcontentslider_params', array(
			'viralcontentslider_ajax_nonce' => wp_create_nonce( 'viralcontentslider-ajax-nonce' ),
			'viralcontentslider_ajax_url' => admin_url( 'admin-ajax.php' ),
			'viralcontentslider_plugin_url' => plugins_url() . '/viralcontentslider',
			'viralcontentslider_admin_url' => admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG,
			'viralcontentslider_fetch_feed_nonce' => wp_create_nonce( 'viralcontentslider_fetch_feed' )
		) );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-admin-youtube', plugins_url( 'assets/scripts/app.admin.youtube.js', __FILE__ ), array(), get_bloginfo( 'version' ), true );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-messenger', plugins_url( 'assets/scripts/messenger/js/messenger.min.js', __FILE__ ), 'jquery' );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-messenger-theme', plugins_url( 'assets/scripts/messenger/js/messenger-theme-flat.js', __FILE__ ), 'jquery' );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-chosen', plugins_url( 'assets/scripts/chosen/chosen.jquery.min.js', __FILE__ ), 'jquery' );

		wp_enqueue_media();
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-admin-media', plugins_url( 'assets/scripts/app.admin.media.js', __FILE__ ), array(), get_bloginfo( 'version' ), true );
	}

	/**
	 * Handle saving the values from Viral Traffic Boost to the database
	 * @Viral Feed
	 */
	public function viralcontentslider_handle_viral() {
		global $wpdb;
		$tableViral = $wpdb->prefix . 'viralcs_virals';
		$tableLinks = $wpdb->prefix . 'viralcs_links';
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$tableVideos = $wpdb->prefix . 'viralcs_videos';

		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/video/video.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/feed/feed.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/article/article.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/category/category.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/tag/tag.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/author/author.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/custom/custom.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/link/link.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/link/custom.link.handle.php' );

		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/feed/feed.manage.content.handle.php' );
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/virals/video/video.manage.content.handle.php' );
	}

	/**
	 * Handle saving options for created viral
	 */
	public function viralcontentslider_handle_options() {
		global $wpdb;
		$tableOptions = $wpdb->prefix . 'viralcs_options';
		$tableVirals = $wpdb->prefix . 'viralcs_virals';
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/options/options.handle.php' );
	}

	/**
	 * Handle saving settings
	 */
	public function viralcontentslider_handle_settings() {
		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/settings/settings.handle.php' );
	}

	/**
	 * Get viral
	 */
	private function get_viral( $idViral ) {
		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';
		$get = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableVirals WHERE id = %d AND deleted_at IS NULL", $idViral ) );
		return $get;
	}

	/**
	 * Get options
	 * Used in codes/options/options.handle.php
	 */
	private function get_options( $idViral ) {
		global $wpdb;
		$tableOptions = $wpdb->prefix . 'viralcs_options';

		$get = $wpdb->get_row( $wpdb->prepare( "SELECT id_viral FROM $tableOptions WHERE id_viral = %d", $idViral ) );
		return $get;
	}

	/**
	 * Get link
	 * Used in codes/virals/link/link.handle.php
	 */
	private function get_link( $link ) {
		global $wpdb;
		$tableLinks = $wpdb->prefix . 'viralcs_links';

		$get = $wpdb->get_row( $wpdb->prepare( "SELECT link FROM $tableLinks WHERE link = %s AND deleted_at IS NULL", $link ) );
		return $get;
	}

	/**
	 * Get links
	 * Used in views/virals/link/custom.link.php
	 */
	private function get_links( $idViral ) {
		global $wpdb;
		$tableLinks = $wpdb->prefix . 'viralcs_links';
		$get = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableLinks WHERE id_viral = %d AND deleted_at IS NULL", $idViral ) );
		return $get;
	}

	/**
	 * Get video
	 * Used in codes/virals/video/video.handle.php
	 */
	private function get_video_byvideoid( $videoId, $idViral ) {
		global $wpdb;
		$tableVideos = $wpdb->prefix . 'viralcs_videos';
		$get = $wpdb->get_row( $wpdb->prepare( "SELECT video_id FROM $tableVideos WHERE video_id = %s AND id_viral = %d AND deleted_at IS NULL", $videoId, $idViral ) );
		return $get;
	}

	/**
	 * Get video object
	 */
	private function get_video( $id, $idViral ) {
		global $wpdb;
		$tableVideos = $wpdb->prefix . 'viralcs_videos';
		$get = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableVideos WHERE id = %s AND id_viral = %d AND deleted_at IS NULL", $id, $idViral ) );
		return $get;
	}

	/**
	 * Get video objects
	 */
	private function get_videos( $idViral ) {
		global $wpdb;
		$tableVideos = $wpdb->prefix . 'viralcs_videos';
		$get = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableVideos WHERE id_viral = %d AND deleted_at IS NULL", $idViral ) );
		return $get;
	}

	/**
	 * Get feed object
	 */
	private function get_feed( $id, $idViral ) {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$feed = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableFeeds WHERE id = %d AND id_viral = %d AND deleted_at IS NULL", $id, $idViral ) );
		return $feed;
	}

	/**
	 * Get feeds objects
	 */
	private function get_feeds( $idViral ) {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$get = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableFeeds WHERE id_viral = %d AND deleted_at IS NULL", $idViral ) );
		return $get;
	}

	/**
	 * Trash created viral
	 * Process is called using ajax request using GET method
	 * Related file /assets/scripts/app.admin.js
	 */
	public function viralcontentslider_handle_trash() {
		global $wpdb;
		$tableViral = $wpdb->prefix . 'viralcs_virals';

		if ( isset( $_GET['viralcontentslider_trash_viral_nonce'] ) ) {
			if ( !isset( $_GET['viralcontentslider_trash_viral_nonce'] ) || !wp_verify_nonce( $_GET['viralcontentslider_trash_viral_nonce'], 'viralcontentslider_trash_viral' ) ) {
				die( 'Cheating, uh?' );
			}

			$idViral = sanitize_text_field( $_GET['viral'] );
			$node = sanitize_text_field( $_GET['node'] );

			if ( $node == 'trash' ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET deleted_at = NOW() WHERE id = %d", $idViral ) );
				echo 'VIRAL_TRASHED';
				die();
			}
		}
	}

	/**
	 * Trash custom link
	 */
	public function viralcontentslider_handle_trash_customlink() {
		global $wpdb;
		$tableLinks = $wpdb->prefix . 'viralcs_links';

		if ( isset( $_GET['viralcontentslider_trash_custom_link_nonce'] ) ) {
			if ( !isset( $_GET['viralcontentslider_trash_custom_link_nonce'] ) || !wp_verify_nonce( $_GET['viralcontentslider_trash_custom_link_nonce'], 'viralcontentslider_trash_custom_link' ) ) {
				die( 'Cheating, uh?' );
			}

			$idViral = sanitize_text_field( $_GET['viral'] );
			$idObject = sanitize_text_field( $_GET['customlink'] );
			$node = sanitize_text_field( $_GET['node'] );
			if ( $node == 'trash' ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET deleted_at = NOW() WHERE id = %d AND id_viral = %d", $idObject, $idViral ) );
				echo 'CUSTOM_LINK_TRASHED';
				die();
			}
		}
	}

	/**
	 * Trash content feed
	 */
	public function viralcontentslider_handle_trash_content_feed() {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';

		if ( isset( $_GET['viralcontentslider_trash_content_feed_nonce'] ) ) {
			if ( !isset( $_GET['viralcontentslider_trash_content_feed_nonce'] ) || !wp_verify_nonce( $_GET['viralcontentslider_trash_content_feed_nonce'], 'viralcontentslider_trash_content_feed' ) ) {
				die( 'Cheating, uh?' );
			}

			$idViral = sanitize_text_field( $_GET['feed'] );
			$idObject = sanitize_text_field( $_GET['obj'] );
			$action = sanitize_text_field( $_GET['action'] );
			if ( $action == 'trash' ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $tableFeeds SET deleted_at = NOW() WHERE id = %d AND id_viral = %d", $idObject, $idViral ) );
				echo 'FEED_CONTENT_TRASHED';
				die();
			}
		}
	}

	/**
	 * Trash content video
	 */
	public function viralcontentslider_handle_trash_content_video() {
		global $wpdb;
		$tableVideos = $wpdb->prefix . 'viralcs_videos';

		if ( isset( $_GET['viralcontentslider_trash_content_video_nonce'] ) ) {
			if ( !isset( $_GET['viralcontentslider_trash_content_video_nonce'] ) || !wp_verify_nonce( $_GET['viralcontentslider_trash_content_video_nonce'], 'viralcontentslider_trash_content_video' ) ) {
				die( 'Cheating, uh?' );
			}

			$idViral = sanitize_text_field( $_GET['video'] );
			$idObject = sanitize_text_field( $_GET['obj'] );
			$action = sanitize_text_field( $_GET['action'] );
			if ( $action == 'trash' ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $tableVideos SET deleted_at = NOW() WHERE id = %d AND id_viral = %d", $idObject, $idViral ) );
				echo 'VIDEO_CONTENT_TRASHED';
				die();
			}
		}
	}

	/**
	 * Display shortcode
	 * @param $atts get parameter from the shortcode
	 * More info please visit http://codex.wordpress.org/Shortcode_API
	 */
	public function viralcontentslider_shortcode( $atts ) {
		extract( shortcode_atts( array( 'id' => '' ), $atts ) );

		if ( !empty( $id ) ) {
			global $wpdb;
			$tableViral = $wpdb->prefix . 'viralcs_virals';

			$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d AND deleted_at IS NULL", $id ) );
			if ( !empty( $viral ) ) {
				if ( !class_exists( 'VcsShortcode' ) ) {
					include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/shortcode.php' );
				}

				$shortcode = new VcsShortcode( $viral, 'sdc' );

				/**
				 * Check for extension options
				 * If the display in home setting is yes then show the slider
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					$extensionOptions = $this->get_extension_options( $viral->id );
					if ( !empty( $extensionOptions ) ) {
						/**
						 * If home page is displayed and display in home option is yes, then display the slider
						 * More info can be found here http://codex.wordpress.org/Function_Reference/is_home
						 */
						if ( is_home() && $extensionOptions->display_in_home == 'yes' ) {
							return $shortcode->generate();
						}
					}
				}

				/**
				 * If single page is displayed then always display the slider
				 * More info can be found here http://codex.wordpress.org/Function_Reference/is_single
				 */
				if ( is_single() || is_page() ) {
					return $shortcode->generate();
				}
			}
		}
	}

	/**
	 * Helper to debug the codes
	 */
	private function debug( $objects ) {
		echo '<pre>'; print_r( $objects ); echo '</pre>';
	}

	/**
	 * Fetch feed url
	 * Related file /libraries/feed/feed.class.php
	 */
	public function viralcontentslider_handle_feed() {
		if ( isset( $_GET['viralcontentslider_fetch_feed_nonce'] ) ) {
			if ( !isset( $_GET['viralcontentslider_fetch_feed_nonce'] ) || !wp_verify_nonce( $_GET['viralcontentslider_fetch_feed_nonce'], 'viralcontentslider_fetch_feed' ) ) {
				die( 'Cheating, uh?' );
			}

			$idViral = sanitize_text_field( $_GET['viral'] );
			$url = sanitize_text_field( urldecode( $_GET['url'] ) );
			$node = sanitize_text_field( $_GET['node'] );

			if ( $node == 'fetchfeed' ) {
				if ( !empty( $url ) ) {
					require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'libraries/feed/feed.class.php' );
					$class = new ViralContentSliderFeed();
					$feed = $class->fetchFeed( $idViral, $url, 10 );
					echo json_encode( $feed );
					die();
				}
			}
		}
	}

	/**
	 * Include Js and CSS files to site page
	 */
	public function viralcontentslider_wp_enqueue_script() {
		wp_register_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-lightslider', plugins_url( 'assets/scripts/lightSlider/css/lightSlider.css', __FILE__ ) );
		wp_enqueue_style( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-lightslider' );

		wp_enqueue_script( 'jquery' );

		wp_register_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-lightslider', plugins_url( 'assets/scripts/lightSlider/js/jquery.lightSlider.js', __FILE__ ), 'jquery' );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-lightslider' );
		wp_enqueue_script( VIRALCONTENTSLIDER_PLUGIN_SLUG . '-app-page', plugins_url( 'assets/scripts/app.page.js', __FILE__ ), array(), get_bloginfo( 'version' ), true );
	}

	/**
	 * Thumbnail Generator
	 */
	public function viralcontentslider_thumbnail_generator() {
		if ( isset( $_GET['viralthumbnail'] ) ) {
			if ( !isset( $_GET['viralthumbnail_nonce'] ) || !wp_verify_nonce( $_GET['viralthumbnail_nonce'], 'viralthumbnail' ) ) {
				die( 'Cheating, uh?' );
			}

			$square = 200;
			$imgSrc = sanitize_text_field( urldecode( base64_decode( $_GET['url'] ) ) );
			$imgSrc = str_replace( 'https://', 'http://', $imgSrc );
			$thumb_width = $square;
			$thumb_height = $square;
			$imgExt = strtolower( substr( $imgSrc, -3 ) );

			if( $imgExt == 'jpg') { $myImage = imagecreatefromjpeg( $imgSrc ); }
			if( $imgExt == 'peg') { $myImage = imagecreatefromjpeg( $imgSrc ); }
			if( $imgExt == 'gif') { $myImage = imagecreatefromgif( $imgSrc ); }
			if( $imgExt == 'png') { $myImage = imagecreatefrompng( $imgSrc ); }

			list( $width_orig, $height_orig ) = getimagesize( $imgSrc );

		 	$ratio_orig = $width_orig/$height_orig;

			if ( $thumb_width/$thumb_height > $ratio_orig ) {
		  	$new_height = $thumb_width/$ratio_orig;
		  	$new_width = $thumb_width;
			} else {
		  	$new_width = $thumb_height*$ratio_orig;
		  	$new_height = $thumb_height;
			}

			$x_mid = $new_width/2;
			$y_mid = $new_height/2;

			$process = imagecreatetruecolor( round( $new_width ), round( $new_height ) );
			imagecopyresampled( $process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig );
			$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

			imagecopyresampled( $thumb, $process, 0, 0, ( $x_mid-( $thumb_width/2 ) ), ( $y_mid-( $thumb_height/2 ) ), $thumb_width, $thumb_height, $thumb_width, $thumb_height );
			imagedestroy( $process );
			imagedestroy( $myImage );

			if ( $imgExt == 'jpg' ) { imagejpeg( $thumb, null, 100 ); }
			if ( $imgExt == 'peg' ) { imagejpeg( $thumb, null, 100 ); }
			if ( $imgExt == 'gif' ) { imagegif( $thumb ); }
			if ( $imgExt == 'png' ) { imagepng( $thumb, null, 9 ); }
		  exit();
		}
	}

	/**
	 * Ajax request admin side
	 */
	public function viralcontentslider_ajax() {
		if ( !isset( $_POST['viralcontentslider_ajax_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_ajax_nonce'], 'viralcontentslider-ajax-nonce' ) ) {
			die( 'Permissions check failed.' );
		}

		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'libraries/youtube/youtube.class.php' );
		$googleApiKey = get_option( 'viralcontentslider_settings_google_api_key' );
		$youtube = new ViralTrafficBoostBrowseYoutube( $googleApiKey );
		$ajaxNode = sanitize_text_field( $_POST['ajaxNode'] );

		switch ( $ajaxNode ) {
			case 'browseYoutubeVideos':
				$keyword = sanitize_text_field( $_POST['keyword'] );
				if ( strpos( $keyword, 'playlist?list' ) !== false ) {
					$type = 'playlist';
					$parse = parse_url( $keyword );
					$key = ltrim( $parse['query'], 'list=' );
				} elseif ( strpos( $keyword, '/user/' ) !== false || strpos( $keyword, '/channel/' ) !== false ) {
					$type = 'channel';
					$objUrl = explode( '/', $keyword );
					$key = end( $objUrl );
				} else {
					$type = 'keyword';
					$key = $keyword;
				}

				$youtubeVideos = $youtube->get_playlist_videos_madcoda( $type, $key );
				if ( $type == 'channel' ) {
					if ( is_string( $youtubeVideos ) ) {
						if ( strpos( $youtubeVideos, 'invalidChannelId' ) !== false ) {
							$channelId = $youtube->getChannelIdByChannelName( $key );
							$youtubeVideos = $youtube->get_playlist_videos_madcoda( $type, $channelId );
						}
					}
				}

				$youtubeHTML = '';
				if ( !empty( $youtubeVideos ) && is_array( $youtubeVideos ) ) {
					$youtubeHTML .= '<center><ul class="viralcontentslider_youtube_thumbnail_lists">';
					foreach ( $youtubeVideos as $youtubeVideo ) {
						$idVideo = $youtubeVideo->video_id;
						$published = $youtubeVideo->published;
						$title = $youtubeVideo->title;
						$description = $youtubeVideo->description;
						$trimmedTitle = wp_trim_words( $title, 3 );
						$duration = $youtubeVideo->duration;
						$youtubeThumbnail = $youtubeVideo->thumbnail;
						$strDuration = 0;

						$youtubeHTML .= <<<YOUTUBE
<li>
	<a href="https://www.youtube.com/watch?v={$idVideo}" target="_blank" title="{$title} - {$duration}">
		<img src="{$youtubeThumbnail}" alt="{$title} - {$duration}" style="max-width:150px;">
	</a>
	<center style="padding-top:5px;">
		<input type="checkbox" class="viralcontentslider_youtube_select_video" data-trimmedtitle="{$trimmedTitle}" data-title="{$title}" data-duration="{$duration}" data-strduration="{$strDuration}" data-thumbnail="{$youtubeThumbnail}" data-videoid="{$idVideo}" data-link="https://www.youtube.com/embed/{$idVideo}" data-published="{$published}" data-description="{$description}"/>
	</center>
</li>
YOUTUBE;
					}
					$youtubeHTML .= '</ul></center>';
				} else {
					$youtubeHTML .= '<strong>' . $youtubeVideos . '</strong>';
				}
				echo $youtubeHTML;
				die();
				break;
		}
	}

	/**
	 * Add additional schedules to WP Cron
	 * Reference http://codex.wordpress.org/Category:WP-Cron_Functions
	 */
	public function viralcontentslider_cron_interval( $schedules ) {
		$cronInterval = get_option( 'viralcontentslider_settings_cron_interval', 1 ); # set default to 1
		$cronType = get_option( 'viralcontentslider_settings_cron_type', 'hours' ); # Set default to hours if user didn't specific the schedule

		switch ( $cronType ) {
			case 'minutes':
				$schedules['viralcontentslider_cron_schedule_in_minutes'] = array(
					'interval' => $cronInterval * 60,
					'display' => __( 'Once ' . $cronInterval . ' minutes' )
				);
				break;
			case 'hours':
				$schedules['viralcontentslider_cron_schedule_in_hours'] = array(
					'interval' => $cronInterval * ( 60 * 60 ),
					'display' => __( 'Once ' . $cronInterval . ' hours' )
				);
				break;
			case 'days':
				$schedules['viralcontentslider_cron_schedule_in_days'] = array(
					'interval' => $cronInterval * ( 24 * 3600 ),
					'display' => __( 'Once ' . $cronInterval . ' days' )
				);
				break;
		}

		return $schedules;
	}

	/**
	 * Setup cron schedule
	 * Reference http://codex.wordpress.org/Function_Reference/wp_schedule_event
	 */
	public function viralcontentslider_setup_schedule() {
		if ( !wp_next_scheduled( 'viralcontentslider_fetch_feed_event' ) ) {

			$cronType = get_option( 'viralcontentslider_settings_cron_type', 'hours' );
			switch ( $cronType ) {
				case 'minutes':
					$cronSchedule = 'viralcontentslider_cron_schedule_in_minutes';
					break;
				case 'hours':
					$cronSchedule = 'viralcontentslider_cron_schedule_in_hours';
					break;
				case 'days':
					$cronSchedule = 'viralcontentslider_cron_schedule_in_days';
					break;
			}

			wp_schedule_event( time(), $cronSchedule, 'viralcontentslider_fetch_feed_event' );
		}

		if ( !wp_next_scheduled( 'viralcontentslider_delete_old_data_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'viralcontentslider_delete_old_data_event' );
		}
	}

	/**
	 * When WP Cron for scheduled fired, do the stuffs here
	 */
	public function viralcontentslider_fetch_feed_event_run() {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$tableViral = $wpdb->prefix . 'viralcs_virals';
		$tableLinks = $wpdb->prefix . 'viralcs_links';

		require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'libraries/feed/feed.class.php' );

		$feeds = $wpdb->get_results( $wpdb->prepare( "SELECT id, feed, display FROM $tableViral WHERE type = %s AND deleted_at IS NULL", 'feed' ) );
		if ( !empty( $feeds ) ) {
			foreach ( $feeds as $feed ) {
				$class = new ViralContentSliderFeed();
				$class->fetchFeed( $feed->id, $feed->feed, 10 );
			}
		}

		/**
		 * Hook analytics to cron job
		 */
		$this->viralcontentslider_handle_analytics_internal();
		$this->viralcontentslider_handle_analytics_external_video();
		$this->viralcontentslider_handle_analytics_external_feed();
		$this->viralcontentslider_handle_analytics_external_link();

		/*$links = $wpdb->get_results( "SELECT * FROM $tableLinks" );
		if ( !empty( $links ) ) {
			foreach ( $links as $link ) {
				if ( empty( $link->title ) || empty( $link->description ) || empty( $link->image ) ) {
					$apiUrl = 'http://radar.runway7.net/?url=' . urldecode( $link->link );
					$getJson = $this->cCurl( $apiUrl );
					$objLink = json_decode( $getJson );
					$title = $objLink->title;
					$description = $objLink->description;
					$image = $objLink->image;
					if ( empty( $image ) ) {
						$image = $objLink->og->image;
					}

					if ( empty( $link->title ) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET title = %s WHERE id_viral = %d AND link = %s", $title, $link->id_viral, $link->link ) );
					}

					if ( empty( $link->description ) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET description = %s WHERE id_viral = %d AND link = %s", $description, $link->id_viral, $link->link ) );
					}

					if ( empty( $link->image ) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET image = %s WHERE id_viral = %d AND link = %s", $image, $link->id_viral, $link->link ) );
					}
				}
			}
		}*/
	}

	/**
	 * Automatically deleted old data
	 * Cron schedule will run every one hour
	 */
	public function viralcontentslider_delete_old_data_event_run() {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';

		$oldDataInterval = get_option( 'settings_cron_purge_old_data_interval' );
		if ( $oldDataInterval > 0 ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $tableFeeds WHERE DATE(created_at) <= DATE_SUB(CURDATE(), INTERVAL %s DAY)", $oldDataInterval ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM $tableAnalytics WHERE DATE(created_at) <= DATE_SUB(CURDATE(), INTERVAL %s DAY)", $oldDataInterval ) );
		}
	}

	/**
	 * To be honest, i still don't know about this code, but better if we have it, yeay!
	 */
	public function viralcontentslider_tiny_mce_version( $ver ) {
	  $ver += 3;
	  return $ver;
	}

	/**
	 * Add Tiny MCE button to the WP Editor
	 */
	public function viralcontentslider_tiny_mce_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
			return;

		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'viralcontentslider_add_tiny_mce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'viralcontentslider_register_tiny_mce_button' ) );
		}
	}

	/**
	 * Register the Js file
	 */
	public function viralcontentslider_add_tiny_mce_plugin( $plugins ) {
		$plugins['viralcontentslider'] = plugins_url( 'assets/scripts/tinymce/tinymce.js', __FILE__ );
		return $plugins;
	}

	/**
	 * Register Tiny MCE button
	 */
	public function viralcontentslider_register_tiny_mce_button( $buttons ) {
		array_push( $buttons, '|', 'viralcontentslider' ); return $buttons;
	}

	/**
	 * Ajax request for custom Tiny MCE button
	 */
	public function viralcontentslider_tinymce_ajax() {
		if ( !isset( $_POST['viralcontentslider_ajax_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_ajax_nonce'], 'viralcontentslider-ajax-nonce' ) ) {
			die( 'Permissions check failed.' );
		}

		global $wpdb;
		$tableViral = $wpdb->prefix . 'viralcs_virals';

		$virals = $wpdb->get_results( "SELECT * FROM $tableViral WHERE deleted_at IS NULL" );
		if ( !empty( $virals ) ) {
			echo '<div id="vcs_shortcodes">';
			echo '	<p><center>';
			echo '		<ul class="viralcontentslider_thumbnail_lists">';
			foreach ( $virals as $viral ) {
				echo '		<li class="viralcontentslider_add_shortcode" data-id="' . $viral->id . '">';
				echo '			<a>';
				echo '				<h3>' . $viral->name . '</h3>';
				echo '			</a>';
				echo '		</li>';
			}
			echo '		</ul>';
			echo '	</center></p>';
			echo '</div>';
		}
		die();
	}

	/**
	 * Analytics internal link
	 */
	private function viralcontentslider_handle_analytics_internal() {
		global $wpdb;
		$tablePosts = $wpdb->prefix . 'posts';
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';

		$allContents = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tablePosts WHERE (post_type = %s OR post_type = %s) AND post_status = %s", 'post', 'page', 'publish' ) );
		if ( !empty( $allContents ) ) {
			foreach ( $allContents as $content ) {
				$title = $content->post_title;
				$permalink = get_permalink( $content->ID );
				$sCount = $this->social_counts( $permalink );

				/**
				 * Save to Database
				 */
				$objects = new stdClass();
				$objects->type = 'internal';
				$objects->title = $title;
				$objects->permalink = $permalink;
				$objects->fbShareCount = $sCount->fbShareCount;
				$objects->fbLikeCount = $sCount->fbLikeCount;
				$objects->fbCommentCount = $sCount->fbCommentCount;
				$objects->fbTotalCount = $sCount->fbTotalCount;
				$objects->fbClickCount = $sCount->fbClickCount;
				$objects->twitterCount = $sCount->twitterCount;
				$objects->linkedinCount = $sCount->linkedinCount;
				$objects->pinterestCount = $sCount->pinterestCount;
				$objects->googlePlusCount = $sCount->googlePlusCount;
				$this->update_analytic( $objects );
			}
		}
	}

	/**
	 * Analytics external link - video
	 */
	private function viralcontentslider_handle_analytics_external_video() {
		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';

		$virals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableVirals WHERE type = %s AND deleted_at IS NULL", 'video' ) );
		if ( !empty( $virals ) ) {
			foreach ( $virals as $viral ) {
				/**
				 * Video
				 */
				$videos = $this->get_videos( $viral->id );
				if ( !empty( $videos ) ) {
					foreach ( $videos as $video ) {
						$videoUrl = 'https://www.youtube.com/watch?v=' . $video->video_id;
						$sCount = $this->social_counts( $videoUrl );

						/**
						 * Save to Database
						 */
						$objects = new stdClass();
						$objects->type = 'external';
						$objects->title = $video->title;
						$objects->permalink = $videoUrl;
						$objects->fbShareCount = $sCount->fbShareCount;
						$objects->fbLikeCount = $sCount->fbLikeCount;
						$objects->fbCommentCount = $sCount->fbCommentCount;
						$objects->fbTotalCount = $sCount->fbTotalCount;
						$objects->fbClickCount = $sCount->fbClickCount;
						$objects->twitterCount = $sCount->twitterCount;
						$objects->linkedinCount = $sCount->linkedinCount;
						$objects->pinterestCount = $sCount->pinterestCount;
						$objects->googlePlusCount = $sCount->googlePlusCount;
						$this->update_analytic( $objects );
					}
				}
			}
		}
	}

	/**
	 * Analytics external link - feed
	 */
	private function viralcontentslider_handle_analytics_external_feed() {
		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';

		$virals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableVirals WHERE type = %s AND deleted_at IS NULL", 'feed' ) );
		if ( !empty( $virals ) ) {
			foreach ( $virals as $viral ) {
				/**
				 * Feed
				 */
				$feeds = $this->get_feeds( $viral->id );
				if ( !empty( $feeds ) ) {
					foreach ( $feeds as $feed ) {
						$sCount = $this->social_counts( $feed->link );

						/**
						 * Save to Database
						 */
						$objects = new stdClass();
						$objects->type = 'external';
						$objects->title = $feed->title;
						$objects->permalink = $feed->link;
						$objects->fbShareCount = $sCount->fbShareCount;
						$objects->fbLikeCount = $sCount->fbLikeCount;
						$objects->fbCommentCount = $sCount->fbCommentCount;
						$objects->fbTotalCount = $sCount->fbTotalCount;
						$objects->fbClickCount = $sCount->fbClickCount;
						$objects->twitterCount = $sCount->twitterCount;
						$objects->linkedinCount = $sCount->linkedinCount;
						$objects->pinterestCount = $sCount->pinterestCount;
						$objects->googlePlusCount = $sCount->googlePlusCount;
						$this->update_analytic( $objects );
					}
				}
			}
		}
	}

	/**
	 * Analytics external link - link
	 */
	private function viralcontentslider_handle_analytics_external_link() {
		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';

		$virals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableVirals WHERE type = %s AND deleted_at IS NULL", 'link' ) );
		if ( !empty( $virals ) ) {
			foreach ( $virals as $viral ) {
				/**
				 * Link
				 */
				$links = $this->get_links( $viral->id );
				if ( !empty( $links ) ) {
					foreach ( $links as $link ) {
						$sCount = $this->social_counts( $link->link );

						/**
						 * Save to Database
						 */
						$objects = new stdClass();
						$objects->type = 'external';
						$objects->title = $link->title;
						$objects->permalink = $link->link;
						$objects->fbShareCount = $sCount->fbShareCount;
						$objects->fbLikeCount = $sCount->fbLikeCount;
						$objects->fbCommentCount = $sCount->fbCommentCount;
						$objects->fbTotalCount = $sCount->fbTotalCount;
						$objects->fbClickCount = $sCount->fbClickCount;
						$objects->twitterCount = $sCount->twitterCount;
						$objects->linkedinCount = $sCount->linkedinCount;
						$objects->pinterestCount = $sCount->pinterestCount;
						$objects->googlePlusCount = $sCount->googlePlusCount;
						$this->update_analytic( $objects );
					}
				}
			}
		}
	}

	/**
	 * Get Analytic By Permalink
	 */
	private function get_analytic( $permalink ) {
		global $wpdb;
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';
		$get = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $tableAnalytics WHERE url = %s", $permalink ) );
		return $get;
	}

	/**
	 * Update analytic
	 */
	private function update_analytic( &$objects ) {
		global $wpdb;
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';

		$wpdb->query( $wpdb->prepare(
			"INSERT INTO $tableAnalytics SET
			type = %s,
			title = %s,
			url = %s,
			fb_share = %d,
			fb_like = %d,
			fb_comment = %d,
			fb_total = %d,
			fb_click = %d,
			tweet = %d,
			linkedin = %d,
			pinterest = %d,
			googleplus = %d,
			created_at = NOW()",
			$objects->type,
			$objects->title,
			$objects->permalink,
			$objects->fbShareCount,
			$objects->fbLikeCount,
			$objects->fbCommentCount,
			$objects->fbTotalCount,
			$objects->fbClickCount,
			$objects->twitterCount,
			$objects->linkedinCount,
			$objects->pinterestCount,
			$objects->googlePlusCount
			)
		);
	}

	/**
	 * Social Counter API
	 */
	private function social_counts( $permalink ) {
		$twitterCount = 0;
		$fbShareCount = 0;
		$fbLikeCount = 0;
		$fbCommentCount = 0;
		$fbTotalCount = 0;
		$fbClickCount = 0;
		$linkedinCount = 0;
		$pinterestCount = 0;
		$googlePlusCount = 0;

		/**
		 * Using service provided by Aljt Media
		 * http://www.aljtmedia.com/blog/getting-your-social-share-counts-with-php/
		 */
		$aljtMediaApi = 'http://api.aljtmedia.com/social/?url=' . rawurlencode( $permalink );
		$aljtMediaRequest = $this->cCurl( $aljtMediaApi );
		$objectStats = json_decode( $aljtMediaRequest );
		if ( !empty( $objectStats ) ) {
			$twitterCount = $objectStats->twitter;
			$fbShareCount = $objectStats->facebook->share_count;
			$fbLikeCount = $objectStats->facebook->like_count;
			$fbCommentCount = $objectStats->facebook->comment_count;
			$fbTotalCount = $objectStats->facebook->total_count;
			$fbClickCount = $objectStats->facebook->click_count;
			$linkedinCount = $objectStats->linkedin;
			$pinterestCount = $objectStats->pinterest;
			$googlePlusCount = $objectStats->google;
		}

		$socialCounts = new stdClass();
		$socialCounts->twitterCount = $twitterCount;
		$socialCounts->fbShareCount = $fbShareCount;
		$socialCounts->fbLikeCount = $fbLikeCount;
		$socialCounts->fbCommentCount = $fbCommentCount;
		$socialCounts->fbTotalCount = $fbTotalCount;
		$socialCounts->fbClickCount = $fbClickCount;
		$socialCounts->linkedinCount = $linkedinCount;
		$socialCounts->pinterestCount = $pinterestCount;
		$socialCounts->googlePlusCount = $googlePlusCount;
		return $socialCounts;
	}

	/**
	 * Social Stats Stand alone API
	 */
	private function social_counts_stand_alone( $permalink ) {
		/**
		 * Twitter
		 */
		/*$twitterApi = 'https://cdn.api.twitter.com/1/urls/count.json?url=' . rawurlencode( $permalink );
		$twitterRequest = $this->cCurl( $twitterApi );
		$objectTwitter = json_decode( $twitterRequest );
		if ( isset( $objectTwitter->count ) ) {
			$twitterCount = $objectTwitter->count;
		}*/

		/**
		 * Facebook
		 */
		/*$facebookApi = 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . rawurlencode( $permalink );
		$facebookRequest = $this->cCurl( $facebookApi );
		$objectFacebook = json_decode( $facebookRequest );
		if ( !empty( $objectFacebook ) ) {
			$fbShareCount = $objectFacebook->share_count;
			$fbLikeCount = $objectFacebook->like_count;
			$fbCommentCount = $objectFacebook->comment_count;
			$fbTotalCount = $objectFacebook->total_count;
			$fbClickCount = $objectFacebook->click_count;
		}*/

		/**
		 * Linkedin
		 */
		/*$linkedinApi = 'https://www.linkedin.com/countserv/count/share?url=' . rawurlencode( $permalink ) . '&format=json';
		$linkedinRequest = $this->cCurl( $linkedinApi );
		$objectLinkedin = json_decode( $linkedinRequest );
		if ( isset( $objectLinkedin->count ) ) {
			$linkedinCount = $objectLinkedin->count;
		}*/

		/**
		 * Pinterest
		 */
		/*$pinterestApi = 'http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=' . rawurlencode( $permalink );
		$pinterestRequest = $this->cCurl( $pinterestApi );
		$jsonPinterest = preg_replace( '/^receiveCount\((.*)\)$/', "\\1", $pinterestRequest );
		$objectPinterest = json_decode( $jsonPinterest );
		if ( isset( $objectPinterest->count ) ) {
			$pinterestCount = $objectPinterest->count;
		}*/
	}
} new ViralContentSlider();
