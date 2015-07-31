<?php
/**
 * Feed
 */
if ( isset( $_POST['viralcontentslider_save_feed'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_feed_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_feed_nonce'], 'viralcontentslider_save_feed' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$name = sanitize_text_field( stripslashes_deep( $_POST['feed_name'] ) );
	$feedUrl = sanitize_text_field( $_POST['feed_url'] );
	$display = sanitize_text_field( $_POST['feed_display'] );

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, feed = %s, display = %d, created_at = NOW()", 'feed', $name, $feedUrl, $display ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, feed = %s, display = %d, updated_at = NOW() WHERE id = %d", $name, $feedUrl, $display, $idViral ) );
	}

	/*require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'libraries/feed/feed.class.php' );
	$class = new ViralContentSliderFeed();
	$feed = $class->fetchFeed( $idViral, $feedUrl, $display );*/

	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=feed&feed=' . $idViral );
}