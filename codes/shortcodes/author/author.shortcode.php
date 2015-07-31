<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeAuthor {
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
		$authorShortcodeContent = '';
		$type = 'author';

		$authorIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->authors ) );
		$orderSort = strtoupper( $this->options->display_sort );

		$arrayAuthorParams = array( 'author__in' => $authorIds, 'posts_per_page' => $this->viral->display );

		if ( $this->options->display_type == 'bytitle' ) {
			$arrayAuthorParams['orderby'] = 'title';
			$arrayAuthorParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydatepublished' ) {
			$arrayAuthorParams['orderby'] = 'date';
			$arrayAuthorParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'random' ) {
			$arrayAuthorParams['orderby'] = 'rand';
		}

		if ( $this->options->display_type == 'bycommented' ) {
			$arrayAuthorParams['orderby'] = 'comment_count';
			$arrayAuthorParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydefault' ) {
			$arrayAuthorParams['orderby'] = 'none';
		}

		$posts = get_posts( $arrayAuthorParams );
		if ( !empty( $posts ) ) {
			$authorShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			foreach ( $posts as $post ) {
				$idFeaturedImage = get_post_thumbnail_id( $post->ID );
				if ( !empty( $idFeaturedImage ) ) {
					$authorImageUrl = wp_get_attachment_url( $idFeaturedImage );
				} else {
					$authorImageUrl = $this->notAvailableImage;
				}

				$authorPermalink = get_permalink( $post->ID );
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$authorPermalink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( get_permalink( $post->ID ) ) . '&type=author&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $post->ID ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$authorTitle = $post->post_title;
				$authorDescription = strip_shortcodes( strip_tags( str_replace( array( '"', "'" ), '', $post->post_content ) ) );
				$trimmedAuthorTitle = wp_trim_words( $authorTitle, $this->options->title_limit_words );
				$trimmedAuthorDescription = wp_trim_words( $authorDescription, $this->options->description_limit_words );
				$authorPublished = date( 'j M y, h:ia', strtotime( $post->post_date ) );
				$authorPublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $authorPublished . '">' . $authorPublished . '</em>';
				$authorThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $authorImageUrl ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );

				if ( empty( $idFeaturedImage ) ) {
					$authorImage = '<img class="vcs_image" src="' . $authorImageUrl . '"/>';
				} else {
					$authorImage = '<img class="vcs_image" src="' . $authorThumbnail . '"/>';
				}

				$authorShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		    	<a href="{$authorPermalink}" {$openInNewTab} title="{$authorTitle}">{$authorImage}</a>
		    </div>
		    <div class="vcs_content_inline">
			    <a href="{$authorPermalink}" {$openInNewTab} title="{$authorTitle}"><h3 class="vcs_headline">{$trimmedAuthorTitle}</h3></a>
			    <div class="vcs_description" title="{$authorDescription}">
			      {$trimmedAuthorDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$authorPermalink}" {$openInNewTab} title="{$authorTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$authorPublishedHTML}</span>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$authorShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $authorShortcodeContent;
	}
}
