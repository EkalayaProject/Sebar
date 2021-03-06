<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeCategory {
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
		$categoryShortcodeContent = '';
		$type = 'category';

		$categoryIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->categories ) );
		$implodedCategoryIds = implode( ',', $categoryIds );
		$implodedCategoryIds = rtrim( $implodedCategoryIds, ',' );
		$orderSort = strtoupper( $this->options->display_sort );

		$arrayCatParams = array( 'category__in' => $categoryIds, 'posts_per_page' => $this->viral->display );

		if ( $this->options->display_type == 'bytitle' ) {
			$arrayCatParams['orderby'] = 'title';
			$arrayCatParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydatepublished' ) {
			$arrayCatParams['orderby'] = 'date';
			$arrayCatParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'random' ) {
			$arrayCatParams['orderby'] = 'rand';
		}

		if ( $this->options->display_type == 'bycommented' ) {
			$arrayCatParams['orderby'] = 'comment_count';
			$arrayCatParams['order'] = $orderSort;
		}

		if ( $this->options->display_type == 'bydefault' ) {
			$arrayCatParams['orderby'] = 'none';
		}

		$posts = get_posts( $arrayCatParams );

		if ( !empty( $posts ) ) {
			$categoryShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			foreach ( $posts as $post ) {
				$idFeaturedImage = get_post_thumbnail_id( $post->ID );
				if ( !empty( $idFeaturedImage ) ) {
					$categoryImageUrl = wp_get_attachment_url( $idFeaturedImage );
				} else {
					$categoryImageUrl = $this->notAvailableImage;
				}

				$categoryPermalink = get_permalink( $post->ID );
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$categoryPermalink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( get_permalink( $post->ID ) ) . '&type=category&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $post->ID ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$categoryTitle = $post->post_title;
				$categoryDescription = strip_shortcodes( strip_tags( str_replace( array( '"', "'" ), '', $post->post_content ) ) );
				$trimmedCategoryTitle = wp_trim_words( $categoryTitle, $this->options->title_limit_words );
				$trimmedCategoryDescription = wp_trim_words( $categoryDescription, $this->options->description_limit_words );
				$categoryPublished = date( 'j M y, h:ia', strtotime( $post->post_date ) );
				$categoryPublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $categoryPublished . '">' . $categoryPublished . '</em>';
				$categoryThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $categoryImageUrl ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );

				if ( empty( $idFeaturedImage ) ) {
					$categoryImage = '<img class="vcs_image" src="' . $categoryImageUrl . '"/>';
				} else {
					$categoryImage = '<img class="vcs_image" src="' . $categoryThumbnail . '"/>';
				}

				$categoryShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		    	<a href="{$categoryPermalink}" {$openInNewTab} title="{$categoryTitle}">{$categoryImage}</a>
		    </div>
		    <div class="vcs_content_inline">
			    <a href="{$categoryPermalink}" {$openInNewTab} title="{$categoryTitle}"><h3 class="vcs_headline">{$trimmedCategoryTitle}</h3></a>
			    <div class="vcs_description" title="{$categoryDescription}">
			      {$trimmedCategoryDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$categoryPermalink}" {$openInNewTab} title="{$categoryTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$categoryPublishedHTML}</span>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$categoryShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $categoryShortcodeContent;
	}
}
