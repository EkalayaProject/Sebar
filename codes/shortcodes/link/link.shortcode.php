<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeLink {
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
		$tableLinks = $wpdb->prefix . 'viralcs_links';

		$linkShortcodeContent = '';
		$type = 'link';

		$orderSort = strtoupper( $this->options->display_sort );
		$orderBy = '';
		if ( $this->options->display_type == 'bytitle' ) {
			$orderBy = ' ORDER BY title ' . $orderSort;
		}

		$links = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableLinks WHERE id_viral = %d $orderBy", $this->viral->id ) );
		if ( !empty( $links ) ) {
			$linkShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			if ( $this->options->display_type == 'random' ) {
				shuffle( $links );
			}
			foreach ( $links as $link ) {
				$linkImageHTML = '<img class="vcs_image" src="' . $this->notAvailableImage . '"/>';
				if ( !empty( $link->image ) ) {
					$linkImageThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $link->image ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );
					$linkImageHTML = '<img class="vcs_image" src="' . $linkImageThumbnail . '"/>';
				}

				$linkPermalink = $link->link;
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$linkPermalink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( $linkPermalink ) . '&type=link&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $link->id ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$linkTitle = $link->title;
				$trimmedLinkTitle = wp_trim_words( $link->title, $this->options->title_limit_words );
				$linkDescription = strip_tags( str_replace( array( '"', "'" ), '', $link->description ) );
				$trimmedLinkDescription = wp_trim_words( $link->description, $this->options->description_limit_words );

				$linkShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		   		<a href="{$linkPermalink}" {$openInNewTab} title="{$linkTitle}">{$linkImageHTML}</a>
		   	</div>
		   	<div class="vcs_content_inline">
			    <a href="{$linkPermalink}" {$openInNewTab} title="{$linkTitle}"><h3 class="vcs_headline">{$trimmedLinkTitle}</h3></a>
			    <div class="vcs_description" title="{$linkDescription}">
			      {$trimmedLinkDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$linkPermalink}" {$openInNewTab} title="{$linkTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$linkShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $linkShortcodeContent;
	}
}
