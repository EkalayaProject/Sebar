<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeCustom {
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
		$customShortcodeContent = '';
		$type = 'custom';

		$categoryIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->categories ) );
		$tagIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->tags ) );
		$authorIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->authors ) );
		$orderSort = strtoupper( $this->options->display_sort );

		$arrayCustomParams = array(
			'posts_per_page' => $this->viral->display
		);

		$taxCatParams = array();
		if ( !empty( $categoryIds[0] ) ) {
			$taxCatParams[] = array(
				'taxonomy' => 'category',
				'field' => 'term_id',
				'terms' => $categoryIds,
				'include_children' => true
			);
		}

		$taxTagParams = array();
		if ( !empty( $tagIds[0] ) ) {
			$taxTagParams[] = array(
				'taxonomy' => 'post_tag',
				'field' => 'term_id',
				'terms' => $tagIds
			);
		}

		$mergeTaxParam = array_merge( $taxCatParams, $taxTagParams );

		$taxQuery = array(
				'relation' => 'OR',
				$mergeTaxParam
		);

		if ( !empty( $authorIds[0] ) ) {
			$arrayCustomParams['author__in'] = $authorIds;
		}

		if ( $this->options->display_type == 'bytitle' ) {
			$arrayCustomParams['orderby'] = 'title';
			$arrayCustomParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydatepublished' ) {
			$arrayCustomParams['orderby'] = 'date';
			$arrayCustomParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'random' ) {
			$arrayCustomParams['orderby'] = 'rand';
		}

		if ( $this->options->display_type == 'bycommented' ) {
			$arrayCustomParams['orderby'] = 'comment_count';
			$arrayCustomParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydefault' ) {
			$arrayCustomParams['orderby'] = 'none';
		}

		if ( !empty( $mergeTaxParam ) ) {
			$arrayCustomParams['tax_query'] = $taxQuery;
		}
		//echo '<pre>'; print_r( $arrayCustomParams ); echo '</pre>';

		$posts = get_posts( $arrayCustomParams );

		if ( !empty( $posts ) ) {
			$customShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			foreach ( $posts as $post ) {
				$idFeaturedImage = get_post_thumbnail_id( $post->ID );
				if ( !empty( $idFeaturedImage ) ) {
					$customImageUrl = wp_get_attachment_url( $idFeaturedImage );
				} else {
					$customImageUrl = $this->notAvailableImage;
				}

				$customPermalink = get_permalink( $post->ID );
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$customPermalink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( get_permalink( $post->ID ) ) . '&type=custom&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $post->ID ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$customTitle = $post->post_title;
				$customDescription = strip_shortcodes( strip_tags( str_replace( array( '"', "'" ), '', $post->post_content ) ) );
				$trimmedCustomTitle = wp_trim_words( $customTitle, $this->options->title_limit_words );
				$trimmedCustomDescription = wp_trim_words( $customDescription, $this->options->description_limit_words );
				$customPublished = date( 'j M y, h:ia', strtotime( $post->post_date ) );
				$customPublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $customPublished . '">' . $customPublished . '</em>';
				$customThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $customImageUrl ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );

				if ( empty( $idFeaturedImage ) ) {
					$customImage = '<img class="vcs_image" src="' . $customImageUrl . '"/>';
				} else {
					$customImage = '<img class="vcs_image" src="' . $customThumbnail . '"/>';
				}

				$customShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		    	<a href="{$customPermalink}" {$openInNewTab} title="{$customTitle}">{$customImage}</a>
		    </div>
		    <div class="vcs_content_inline">
			    <a href="{$customPermalink}" {$openInNewTab} title="{$customTitle}"><h3 class="vcs_headline">{$trimmedCustomTitle}</h3></a>
			    <div class="vcs_description" title="{$customDescription}">
			      {$trimmedCustomDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$customPermalink}" {$openInNewTab} title="{$customTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$customPublishedHTML}</span>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$customShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $customShortcodeContent;
	}
}
