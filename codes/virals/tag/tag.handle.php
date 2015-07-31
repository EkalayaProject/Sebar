<?php
/**
 * Tag
 */
if ( isset( $_POST['viralcontentslider_save_tag'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_tag_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_tag_nonce'], 'viralcontentslider_save_tag' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$tagName = sanitize_text_field( stripslashes_deep( $_POST['tag_name'] ) );
	
	$doTags = $_POST['tag_tags'];
	if ( is_null( $doTags ) ) {
		$tagTags = '';
	} else {
		if ( in_array( 'ALL', $doTags ) ) {
			$tagTags = '{ALL}';
		} else {
			foreach( $doTags as $idTag ) {
				$arrDisplayOnTags[] = '{' . $idTag . '}';
			}
			$tagTags = implode( ',', $arrDisplayOnTags );
		}
	}

	$tagDisplay = sanitize_text_field( $_POST['tag_display'] );

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, tags = %s, display = %d, created_at = NOW()", 'tag', $tagName, $tagTags, $tagDisplay ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, tags = %s, display = %d, updated_at = NOW() WHERE id = %d", $tagName, $tagTags, $tagDisplay, $idViral ) );
	}
	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=tag&tag=' . $idViral );
}