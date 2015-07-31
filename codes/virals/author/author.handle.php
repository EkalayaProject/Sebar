<?php
/**
 * Author
 */
if ( isset( $_POST['viralcontentslider_save_author'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_author_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_author_nonce'], 'viralcontentslider_save_author' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$authorName = sanitize_text_field( stripslashes_deep( $_POST['author_name'] ) );

	$doAuthors = $_POST['author_authors'];
	if ( is_null( $doAuthors ) ) {
		$authorAuthors = '';
	} else {
		if ( in_array( 'ALL', $doAuthors ) ) {
			$authorAuthors = '{ALL}';
		} else {
			foreach( $doAuthors as $idAuthor ) {
				$arrDisplayOnAuhtors[] = '{' . $idAuthor . '}';
			}
			$authorAuthors = implode( ',', $arrDisplayOnAuhtors );
		}
	}

	$authorDisplay = sanitize_text_field( $_POST['author_display'] );

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, authors = %s, display = %d, created_at = NOW()", 'author', $authorName, $authorAuthors, $authorDisplay ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, authors = %s, display = %d, updated_at = NOW() WHERE id = %d", $authorName, $authorAuthors, $authorDisplay, $idViral ) );
	}
	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=author&author=' . $idViral );
}