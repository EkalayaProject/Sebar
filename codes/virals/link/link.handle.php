<?php
/**
 * Link
 */
if ( isset( $_POST['viralcontentslider_save_link'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_link_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_link_nonce'], 'viralcontentslider_save_link' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$linkName = sanitize_text_field( stripslashes_deep( $_POST['link_name'] ) );
	$linkLinks = $_POST['link_links'];

	$objLinks = explode( "\n", $linkLinks );
	
	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, links = %s, created_at = NOW()", 'link', $linkName, $linkLinks ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, links = %s, updated_at = NOW() WHERE id = %d", $linkName, $linkLinks, $idViral ) );
	}

	$links = array();
	if ( !empty( $objLinks ) ) {
		foreach( $objLinks as $link ) {
			$apiUrl = 'http://radar.runway7.net/?url=' . urldecode( $link );
			$getJson = $this->cCurl( $apiUrl );
			$objLink = json_decode( $getJson );
			$title = $objLink->title;
			$description = $objLink->description;
			$image = $objLink->image;
			if ( empty( $image ) ) {
				$image = $objLink->og->image;
			}

			$getLink = $this->get_link( $link );
			if ( empty( $getLink ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO $tableLinks SET id_viral = %d, link = %s, title = %s, description = %s, image = %s, created_at = NOW()", $idViral, $link, $title, $description, $image ) );
			} else {
				$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET title = %s, description = %s, image = %s, updated_at = NOW() WHERE link = %s AND id_viral = %d", $title, $description, $image, $link, $idViral ) );
			}

			$links[] = "'" . $link . "'";
		}
		$implodedLinks = implode( ',', $links );
		$wpdb->query( $wpdb->prepare( "DELETE FROM $tableLinks WHERE id_viral = %d AND link NOT IN($implodedLinks)", $idViral ) );
	}

	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=link&link=' . $idViral );
}