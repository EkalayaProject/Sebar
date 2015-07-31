<?php
class VtbAnalytics {
	public static function get_top( $type, $order, $limit ) {
		global $wpdb;
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';
		$get = $wpdb->get_results( $wpdb->prepare( "SELECT
			id,
			type,
			title,
			url,
			MAX(fb_share) AS fb_share,
			MAX(fb_like) AS fb_like,
			MAX(fb_comment) AS fb_comment,
			MAX(fb_click) AS fb_click,
			MAX(tweet) AS tweet,
			MAX(linkedin) AS linkedin,
			MAX(pinterest) AS pinterest,
			MAX(googleplus) AS googleplus,
			(fb_total + fb_click + tweet + linkedin + pinterest + googleplus) AS total
			FROM $tableAnalytics
			WHERE type = %s
			GROUP BY url ORDER BY $order DESC, created_at ASC LIMIT %d", $type, $limit ) );
		return $get;
	}

	public static function search( $keyword ) {
		global $wpdb;
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';
		$keyword = sanitize_text_field( $keyword );
		$get = $wpdb->get_results( "SELECT id, title, url FROM $tableAnalytics WHERE title LIKE '%$keyword%' OR url LIKE '%$keyword%' GROUP BY url ORDER BY created_at DESC" );
		return $get;
	}

	public static function searchByUrl( $url ) {
		global $wpdb;
		$tableAnalytics = $wpdb->prefix . 'viralcs_analytics';
		$get = $wpdb->get_results( $wpdb->prepare( "SELECT
			id,
			title,
			url,
			fb_share,
			fb_like,
			fb_comment,
			fb_click,
			tweet,
			linkedin,
			pinterest,
			googleplus,
			(fb_total + fb_click + tweet + linkedin + pinterest + googleplus) AS total,
			updated_at
			FROM $tableAnalytics WHERE url = %s ORDER BY created_at DESC", $url ) );
		return $get;
	}
}