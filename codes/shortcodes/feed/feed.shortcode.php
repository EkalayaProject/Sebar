<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeFeed {
	private $viral;
	private $options;
	private $extensionOptions;
	private $randSlider;
	private $notAvailableImage;

	public function __construct( &$viral, &$options, &$extensionOptions, $randSlider, $notAvailableImage ) {
		$this->viral = $viral;
		$this->options = $options;
		$this->extensionOptions = $extensionOptions;
		$this->randSlider = $randSlider;
		$this->notAvailableImage = $notAvailableImage;
	}

	public function generate() {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';

		$feedShortcodeContent = '';
		$type = 'feed';

		$orderSort = strtoupper( $this->options->display_sort );
		$orderBy = '';
		if ( $this->options->display_type == 'bytitle' ) {
			$orderBy = ' ORDER BY title ' . $orderSort;
		} elseif ( $this->options->display_type == 'bydatepublished' ) {
			$orderBy = ' ORDER BY date_published ' . $orderSort;
		}

		$feeds = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableFeeds WHERE id_viral = %d AND feed = %s AND deleted_at IS NULL $orderBy LIMIT %d", $this->viral->id, $this->viral->feed, $this->viral->display ) );
		if ( !empty( $feeds ) ) {
			$feedShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			if ( $this->options->display_type == 'random' ) {
				shuffle( $feeds );
			}
			foreach ( $feeds as $feed ) {
				$feedLink = $feed->link;
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$feedLink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( $feedLink ) . '&type=feed&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $feed->id ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$feedImageHTML = '<img class="vcs_image" src="' . $this->notAvailableImage . '"/>';
				if ( !empty( $feed->image ) ) {
					$feedImageThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $feed->image ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );
					$feedImageHTML = '<img class="vcs_image" src="' . $feedImageThumbnail . '"/>';
				}

				$feedTitle = $feed->title;
				$feedTrimmedTitle = wp_trim_words( $feed->title, $this->options->title_limit_words );
				$feedDescription = strip_tags( str_replace( array( '"', "'" ), '', $feed->description ) );
				$feedTrimmedDescription = wp_trim_words( $feedDescription, $this->options->description_limit_words );
				$feedDatePublished = date( 'j M y, h:ia', strtotime( $feed->date_published ) );
				$feedDatePublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $feedDatePublished . '">' . $feedDatePublished . '</em>';

				$feedShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		  		<a href="{$feedLink}" {$openInNewTab} title="{$feedTitle}">{$feedImageHTML}</a>
		  	</div>
		  	<div class="vcs_content_inline">
		  		<a href="{$feedLink}" {$openInNewTab} title="{$feedTitle}"><h3 class="vcs_headline">{$feedTrimmedTitle}</h3></a>
			    <div class="vcs_description" title="{$feedDescription}">
			      {$feedTrimmedDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$feedLink}" {$openInNewTab} title="{$feedTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$feedDatePublishedHTML}</span>
			    </div>
		  	</div>
		  </div>
		</li>
HTML;
			}

			$feedShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $feedShortcodeContent;
	}
}
