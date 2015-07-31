<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsShortcodeArticle {
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
		$tablePosts = $wpdb->prefix . 'posts';
		$articleShortcodeContent = '';
		$type = 'article';

		$postIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->posts ) );
		$pageIds = explode( ',', str_replace( array( '{', '}' ), '', $this->viral->pages ) );
		$mergeIds = array_merge( $postIds, $pageIds );
		$mergedIds = implode( ',', $mergeIds );
		$mergedIds = rtrim( $mergedIds, ',' );

		$orderSort = strtoupper( $this->options->display_sort );
		$orderBy = '';
		if ( $this->options->display_type == 'bytitle' ) {
			$orderBy = ' ORDER BY post_title ' . $orderSort;
		} elseif ( $this->options->display_type == 'bydatepublished' ) {
			$orderBy = ' ORDER BY post_date ' . $orderSort;
		} elseif ( $this->options->display_type == 'bycommented' ) {
			$orderBy = ' ORDER BY comment_count ' . $orderSort;
		}

		$articles = $wpdb->get_col( "SELECT ID FROM $tablePosts WHERE ID IN($mergedIds) $orderBy" );
		if ( $this->options->display_type == 'random' ) {
			shuffle( $articles );
		}

		if ( !empty( $articles ) ) {
			$articleShortcodeContent .= <<<SLIDER
<div class="viralcontentslider_slider_main_{$type}_{$this->viral->id}_{$this->randSlider}">
  <ul id="viralcontentslider_{$type}_{$this->viral->id}_{$this->randSlider}" class="viralcontentslider_slider_ul_{$type}_{$this->viral->id}_{$this->randSlider}">
SLIDER;

			foreach ( $articles as $idArticle ) {
				$idFeaturedImage = get_post_thumbnail_id( $idArticle );
				if ( !empty( $idFeaturedImage ) ) {
					$articleImageUrl = wp_get_attachment_url( $idFeaturedImage );
				} else {
					$articleImageUrl = $this->notAvailableImage;
				}

				$article = get_post( $idArticle );
				$articlePermalink = get_permalink( $idArticle );
				$openInNewTab = '';

				/**
				 * Check if extension plugin is activated and option is yes
				 */
				if ( class_exists( 'VcsExtension' ) ) {
					if ( $this->extensionOptions->open_in_landing_page == 'yes' ) {
						$articlePermalink = site_url() . '/?vcs_landing=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&permalink=' . base64_encode( get_permalink( $idArticle ) ) . '&type=article&te=' . base64_encode( $this->viral->id ) . '&obj=' . base64_encode( $idArticle ) . '&back=' . base64_encode( $_SERVER['REQUEST_URI'] );
					}

					if ( $this->extensionOptions->open_in_new_tab == 'yes' ) {
						$openInNewTab = 'target="_blank"';
					}
				}

				$articleTitle = get_the_title( $idArticle );
				$articleDescription = strip_shortcodes( strip_tags( str_replace( array( '"', "'" ), '', $article->post_content ) ) );
				$trimmedArticleTitle = wp_trim_words( $articleTitle, $this->options->title_limit_words );
				$trimmedArticleDescription = wp_trim_words( $articleDescription, $this->options->description_limit_words );
				$articlePublished = date( 'j M y, h:ia', strtotime( $article->post_date ) );
				$articlePublishedHTML = '<em style="font-size:10px !important;" title="Published at ' . $articlePublished . '">' . $articlePublished . '</em>';
				$articleThumbnail = wp_nonce_url( site_url() . '/?viralthumbnail=true&url=' . base64_encode( $articleImageUrl ) . '', 'viralthumbnail', 'viralthumbnail_nonce' );
				if ( empty( $idFeaturedImage ) ) {
					$articleImage = '<img class="vcs_image" src="' . $articleImageUrl . '"/>';
				} else {
					$articleImage = '<img class="vcs_image" src="' . $articleThumbnail . '"/>';
				}

				$articleShortcodeContent .= <<<HTML
		<li>
		  <div class="vcs_content">
		  	<div class="vcs_media_inline">
		    	<a href="{$articlePermalink}" {$openInNewTab} title="{$articleTitle}">{$articleImage}</a>
		    </div>
		    <div class="vcs_content_inline">
			    <a href="{$articlePermalink}" {$openInNewTab} title="{$articleTitle}"><h3 class="vcs_headline">{$trimmedArticleTitle}</h3></a>
			    <div class="vcs_description" title="{$articleDescription}">
			      {$trimmedArticleDescription}
			    </div>
			    <div class="vcs_readmore">
			    	<a href="{$articlePermalink}" {$openInNewTab} title="{$articleTitle}" class="vcs_readmore_text">{$this->options->readmore_text}</a>
			    	<span style="margin-right:10px;float:right;">{$articlePublishedHTML}</span>
			    </div>
			  </div>
		  </div>
		</li>
HTML;
			}

			$articleShortcodeContent .= <<<SLIDER
	</ul>
</div>
SLIDER;
		}

		return $articleShortcodeContent;
	}
}
