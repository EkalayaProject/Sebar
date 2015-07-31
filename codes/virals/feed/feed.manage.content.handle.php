<?php
/**
 * Manage content feed
 */
if ( isset( $_POST['viralcontentslider_save_update_feed'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_update_feed_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_update_feed_nonce'], 'viralcontentslider_save_update_feed' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$idObj = sanitize_text_field( $_POST['viralcontentslider_hidden_obj'] );
	$title = sanitize_text_field( stripslashes_deep( $_POST['feed_update_title'] ) );
	$link = sanitize_text_field( $_POST['feed_update_link'] );
	$description = sanitize_text_field( stripslashes_deep( $_POST['feed_update_description'] ) );
	$image = sanitize_text_field( $_POST['feed_update_image'] );

	if ( !empty( $idViral ) && !empty( $idObj ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableFeeds SET link = %s, title = %s, description = %s, image = %s WHERE id = %d AND id_viral = %d", $link, $title, $description, $image, $idObj, $idViral ) );
	}
	
	wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
}