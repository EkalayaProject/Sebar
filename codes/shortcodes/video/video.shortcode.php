<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeVideo {
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
		$tableVideos = $wpdb->prefix . 'viralcs_videos';

		$videoShortcodeContent = '';
		$type = 'video';

		$orderSort = strtoupper( $this->options->display_sort );
		$orderBy = '';
		if ( $this->options->display_type == 'bytitle' ) {
			$orderBy = ' ORDER BY title ' . $orderSort;
		} elseif ( $this->options->display_type == 'byduration' ) {
			$orderBy = ' ORDER BY str_duration ' . $orderSort;
		}

		$videos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tableVideos WHERE id_viral = %d $orderBy", $this->viral->id ) );
		if ( !empty( $videos ) ) {
			$videoShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			if ( $this->options->display_type == 'random' ) {
				shuffle( $videos );
			}
			foreach ( $videos as $video ) {
				$videoImageHTML = '<img class="vcs_image" src="' . $this->notAvailableImage . '"/>';
				if ( !empty( $video->thumbnail ) ) {
					$imageThumbnail = $video->thumbnail;
					$videoImageHTML = '<img class="vcs_image" src="' . $imageThumbnail . '"/>';
				}

				$oB = explode( '/', $video->link );
				$vId = end( $oB );
				$videoLink = $video->link;//'https://www.youtube.com/watch?v=' . $vId;
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$videoLink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( $videoLink ) . '&type=video&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $video->id ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$videoTitle = $video->title;
				$trimmedVideoTitle = wp_trim_words( $video->title, $this->options->title_limit_words );
				$videoDescription = strip_tags( str_replace( array( '"', "'" ), '', $video->description ) );
				$trimmedVideoDescription = wp_trim_words( $video->description, $this->options->description_limit_words );
				$videoDuration = $video->duration;
				$videoPublished = date( 'j M y, h:ia', strtotime( $video->published ) );
				$videoPublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $videoPublished . '">' . $videoPublished . '</em>';

				$videoShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		    <div class="vcs_media_inline">
		    	<a href="{$videoLink}" {$openInNewTab} title="{$videoTitle} - {$videoDuration}">{$videoImageHTML}</a>
		    </div>
		    <div class="vcs_content_inline">
			    <a href="{$videoLink}" {$openInNewTab} title="{$videoTitle} - {$videoDuration}"><h3 class="vcs_headline">{$trimmedVideoTitle}</h3></a>
			    <div class="vcs_description" title="{$videoDescription}">
			      {$trimmedVideoDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$videoLink}" {$openInNewTab} title="{$videoTitle} - {$videoDuration}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$videoPublishedHTML}</span>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$videoShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $videoShortcodeContent;
	}
}
