<?php
/**
 * Manage video
 */
if ( isset( $_POST['viralcontentslider_save_update_video'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_update_video_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_update_video_nonce'], 'viralcontentslider_save_update_video' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$idObj = sanitize_text_field( $_POST['viralcontentslider_hidden_obj'] );
	$title = sanitize_text_field( stripslashes_deep( $_POST['video_update_title'] ) );
	$description = sanitize_text_field( stripslashes_deep( $_POST['video_update_description'] ) );
	$thumbnail = sanitize_text_field( $_POST['video_update_thumbnail'] );

	if ( !empty( $idViral ) && !empty( $idObj ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableVideos SET title = %s, description = %s, thumbnail = %s WHERE id = %d AND id_viral = %d", $title, $description, $thumbnail, $idObj, $idViral ) );
	}
	
	wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
}