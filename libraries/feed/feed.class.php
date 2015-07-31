<?php
/**
 * Include SimplePie feed provided by WordPress
 */
if ( !function_exists( 'fetch_feed' ) ) {
	include_once( ABSPATH . WPINC . '/feed.php' );
}

/**
 * Include Simple HTML DOM
 */
if ( !class_exists( 'simple_html_dom' ) ) {
	require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'libraries/simplehtmldom/simple_html_dom.php' );
}
ini_set( 'max_execution_time', 300 );

class ViralContentSliderFeed {
	public function fetchFeed( $idViral, $url, $quantity ) {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';

		$items = array();
		$rss = fetch_feed( $url );
		if ( ! is_wp_error( $rss ) ) {
			$maxItems = $rss->get_item_quantity( $quantity );
			$items = $rss->get_items( 0, $maxItems );

			if ( !empty( $items ) ) {
				$totalItem = array();
				foreach ( $items as $item ) {
					/*$getJsonImage = $this->cCurl( 'http://radar.runway7.net/?url=' . $item->get_permalink() );
					$objImage = json_decode( $getJsonImage );
					$imageUrl = '';
					if ( isset( $objImage->image ) && !empty( $objImage->image ) ) {
						$imageUrl = $objImage->image;
					}

					if ( empty( $imageUrl ) ) {
						if ( isset( $objImage->og->image ) && !empty( $objImage->og->image ) ) {
							$imageUrl = $objImage->og->image;
						}
					}*/

					$imageUrl = '';
					$htmlDOM = new simple_html_dom();
					$htmlDOM->load( str_get_html( $this->cCurl( $item->get_permalink() ) ) );
					$image = $htmlDOM->find( 'meta[property=og:image]', 0 );
					if ( empty( $image->content ) ) {
						$image = $htmlDOM->find( 'img', 0 );
						$imageUrl = $image->src;
					} else {
						$imageUrl = $image->content;
					}

					if ( !empty( $imageUrl ) ) {
						if ( strpos( $imageUrl, 'http' ) === false ) {
							$parsed = parse_url( $item->get_permalink() );
							$scheme = $parsed['scheme'];
							$host = $parsed['host'];
							$imageUrl = $scheme . '://' . $host . $imageUrl;
						}
					}

					$link = $item->get_permalink();
					$title = $item->get_title();
					$description = trim( strip_tags( $item->get_description() ) );
					$date = $item->get_date( 'Y-m-d H:i:s' );
					$check = $this->cCheck( $idViral, $link );
					if ( empty( $check ) ) {
						/**
						 * Download image to local
						 */
						if ( !empty( $imageUrl ) ) {
							$objImage = $this->downloadImage( $imageUrl );
							if ( is_array( $objImage ) ) {
								$imageUrl = $objImage['fileUrl'];
							}
						}

						$wpdb->query( $wpdb->prepare( "INSERT INTO $tableFeeds SET id_viral = %d, feed = %s, link = %s, title = %s, description = %s, date_published = %s, image = %s, created_at = NOW()", $idViral, $url, $link, $title, $description, $date, $imageUrl ) );
					}
					/*else {
						$wpdb->query( $wpdb->prepare( "UPDATE $tableFeeds SET title = %s, description = %s, date_published = %s, image = %s, updated_at = NOW() WHERE link = %s AND id_viral = %d", $title, $description, $date, $imageUrl, $link, $idViral ) );
					}*/

					$totalItem[] = $title;
				}
				return array( 'code' => 'success', 'total' => count( $totalItem ) );
			}
		} else {
			return array( 'code' => 'error', 'message' => $rss->get_error_message() );
		}
	}

	private function downloadImage( $url ) {
		$uploadDirectory = wp_upload_dir();
		$directoryPath = $uploadDirectory['path'] . DIRECTORY_SEPARATOR;
		$directoryUrl = $uploadDirectory['url'] . DIRECTORY_SEPARATOR;
		$obj = explode( '/', $url );
		$fileName = rand() . '_' . end( $obj );

		/**
		 * Some url doesn't provide url contains image extensions, so try to add extension as .png
		 */
		$imgExt = strtolower( substr( $url, -4 ) );
		if ( $imgExt != '.jpg' && $imgExt != '.png' && $imgExt != '.gif' && $imgExt != 'jpeg' ) {
			$fileName = rand() . '.png';
		}

		$rawdata = $this->cCurl( $url );
		if ( $rawdata !== false ) {
			if ( !file_put_contents( $directoryPath . $fileName, $rawdata ) ) {
				return false;
			}
		} else {
			return false;
		}
		return array(
			'uploadDirectory' => $uploadDirectory,
			'fileName' => $fileName,
			'filePath' => $directoryPath . $fileName,
			'fileUrl' => $directoryUrl . $fileName
		);
	}

	/**
	 * PHP Curl request
	 */
	private function cCurl( $url ) {
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true, 
			CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',
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

	private function cCheck( $idViral, $link ) {
		global $wpdb;
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';

		$check = $wpdb->get_row( $wpdb->prepare( "SELECT title FROM $tableFeeds WHERE id_viral = %s AND link = %s", $idViral, $link ) );
		return $check;
	}
}