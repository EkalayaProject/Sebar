<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeTag {
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
		$tagShortcodeContent = '';
		$type = 'tag';

		$tagIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->tags ) );
		$orderSort = strtoupper( $this->options->display_sort );

		$arrayTagParams = array( 'tag__in' => $tagIds, 'posts_per_page' => $this->viral->display );

		if ( $this->options->display_type == 'bytitle' ) {
			$arrayTagParams['orderby'] = 'title';
			$arrayTagParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydatepublished' ) {
			$arrayTagParams['orderby'] = 'date';
			$arrayTagParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'random' ) {
			$arrayTagParams['orderby'] = 'rand';
		}

		if ( $this->options->display_type == 'bycommented' ) {
			$arrayTagParams['orderby'] = 'comment_count';
			$arrayTagParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydefault' ) {
			$arrayTagParams['orderby'] = 'none';
		}

		$posts = get_posts( $arrayTagParams );
		if ( !empty( $posts ) ) {
			$tagShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			foreach ( $posts as $post ) {
				$idFeaturedImage = get_post_thumbnail_id( $post->ID );
				if ( !empty( $idFeaturedImage ) ) {
					$tagImageUrl = wp_get_attachment_url( $idFeaturedImage );
				} else {
					$tagImageUrl = $this->notAvailableImage;
				}

				$tagPermalink = get_permalink( $post->ID );
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$tagPermalink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( get_permalink( $post->ID ) ) . '&type=tag&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $post->ID ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$tagTitle = $post->post_title;
				$tagDescription = strip_shortcodes( strip_tags( str_replace( array( '"', "'" ), '', $post->post_content ) ) );
				$trimmedTagTitle = wp_trim_words( $tagTitle, $this->options->title_limit_words );
				$trimmedTagDescription = wp_trim_words( $tagDescription, $this->options->description_limit_words );
				$tagPublished = date( 'j M y, h:ia', strtotime( $post->post_date ) );
				$tagPublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $tagPublished . '">' . $tagPublished . '</em>';
				$tagThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $tagImageUrl ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );

				if ( empty( $idFeaturedImage ) ) {
					$tagImage = '<img class="vcs_image" src="' . $tagImageUrl . '"/>';
				} else {
					$tagImage = '<img class="vcs_image" src="' . $tagThumbnail . '"/>';
				}

				$tagShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		    	<a href="{$tagPermalink}" {$openInNewTab} title="{$tagTitle}">{$tagImage}</a>
		    </div>
		    <div class="vcs_content_inline">
			    <a href="{$tagPermalink}" {$openInNewTab} title="{$tagTitle}"><h3 class="vcs_headline">{$trimmedTagTitle}</h3></a>
			    <div class="vcs_description" title="{$tagDescription}">
			      {$trimmedTagDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$tagPermalink}" {$openInNewTab} title="{$tagTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$tagPublishedHTML}</span>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$tagShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $tagShortcodeContent;
	}
}
