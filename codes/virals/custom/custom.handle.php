<?php
/**
 * Custom
 */
if ( isset( $_POST['viralcontentslider_save_custom'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_custom_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_custom_nonce'], 'viralcontentslider_save_custom' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$customName = sanitize_text_field( stripslashes_deep( $_POST['custom_name'] ) );
	$doCats = $_POST['custom_categories'];
	if ( is_null( $doCats ) ) {
		$customCategories = '';
	} else {
		if ( in_array( 'ALL', $doCats ) ) {
			$customCategories = '{ALL}';
		} else {
			foreach( $doCats as $idCat ) {
				$arrDisplayOnCats[] = '{' . $idCat . '}';
			}
			$customCategories = implode( ',', $arrDisplayOnCats );
		}
	}

	$doTags = $_POST['custom_tags'];
	if ( is_null( $doTags ) ) {
		$customTags = '';
	} else {
		if ( in_array( 'ALL', $doTags ) ) {
			$customTags = '{ALL}';
		} else {
			foreach( $doTags as $idTag ) {
				$arrDisplayOnTags[] = '{' . $idTag . '}';
			}
			$customTags = implode( ',', $arrDisplayOnTags );
		}
	}

	$doAuthors = $_POST['custom_authors'];
	if ( is_null( $doAuthors ) ) {
		$customAuthors = '';
	} else {
		if ( in_array( 'ALL', $doAuthors ) ) {
			$customAuthors = '{ALL}';
		} else {
			foreach( $doAuthors as $idAuthor ) {
				$arrDisplayOnAuhtors[] = '{' . $idAuthor . '}';
			}
			$customAuthors = implode( ',', $arrDisplayOnAuhtors );
		}
	}

	$customDisplay = sanitize_text_field( $_POST['custom_display'] );

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, categories = %s, tags = %s, authors = %s, display = %d, created_at = NOW()", 'custom', $customName, $customCategories, $customTags, $customAuthors, $customDisplay ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, categories = %s, tags = %s, authors = %s, display = %d, updated_at = NOW() WHERE id = %d", $customName, $customCategories, $customTags, $customAuthors, $customDisplay, $idViral ) );
	}
	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=custom&custom=' . $idViral );
}