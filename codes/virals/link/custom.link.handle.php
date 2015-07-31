<?php
/**
 * Link
 */
if ( isset( $_POST['viralcontentslider_save_custom_link'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_custom_link_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_custom_link_nonce'], 'viralcontentslider_save_custom_link' ) ) {
		die( 'Cheating, uh?' );
	}

	$node = sanitize_text_field( $_POST['viralcontentslider_save_custom_link'] );
	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$customLinkName = sanitize_text_field( stripslashes_deep( $_POST['custom_link_name'] ) );
	
	if ( $node == 'Continue' ) {
		if ( empty( $idViral ) ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, created_at = NOW()", 'customlink', $customLinkName ) );
			$idViral = $wpdb->insert_id;
		} else {
			$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, updated_at = NOW() WHERE id = %d", $customLinkName, $idViral ) );
		}
	} else {
		if ( !empty( $idViral ) ) {
			/**
			 * If user update the existing link
			 */
			$titles = $_POST['custom_link_titles'];
			if ( !empty( $titles ) ) {
				foreach ( $titles as $idObject => $title ) {
					$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET title = %s WHERE id = %d AND id_viral = %d", $title, $idObject, $idViral ) );
				}
			}

			$descriptions = $_POST['custom_link_descriptions'];
			if ( !empty( $descriptions ) ) {
				foreach ( $descriptions as $idObject => $description ) {
					$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET description = %s WHERE id = %d AND id_viral = %d", $description, $idObject, $idViral ) );
				}
			}

			$images = $_POST['custom_link_images'];
			if ( !empty( $images ) ) {
				foreach ( $images as $idObject => $image ) {
					$wpdb->query( $wpdb->prepare( "UPDATE $tableLinks SET image = %s WHERE id = %d AND id_viral = %d", $image, $idObject, $idViral ) );
				}
			}

			/**
			 * If user insert new link
			 */
			$link = sanitize_text_field( $_POST['custom_link_link'] );
			$title = sanitize_text_field( stripslashes_deep( $_POST['custom_link_title'] ) );
			$description = sanitize_text_field( stripslashes_deep( $_POST['custom_link_description'] ) );
			$image = sanitize_text_field( $_POST['custom_link_image'] );

			if ( !empty( $link ) && !empty( $title ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO $tableLinks SET id_viral = %d, link = %s, title = %s, description = %s, image = %s, created_at = NOW()", $idViral, $link, $title, $description, $image ) );
			}
		}
	}

	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=customlink&customlink=' . $idViral );
}