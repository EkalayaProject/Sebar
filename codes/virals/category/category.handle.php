<?php
/**
 * Category
 */
if ( isset( $_POST['viralcontentslider_save_category'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_category_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_category_nonce'], 'viralcontentslider_save_category' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$categoryName = sanitize_text_field( stripslashes_deep( $_POST['category_name'] ) );

	$doCats = $_POST['category_categories'];
	
	if ( is_null( $doCats ) ) {
		$categoryCategories = '';
	} else {
		if ( in_array( 'ALL', $doCats ) ) {
			$categoryCategories = '{ALL}';
		} else {
			foreach( $doCats as $idCat ) {
				$arrDisplayOnCats[] = '{' . $idCat . '}';
			}
			$categoryCategories = implode( ',', $arrDisplayOnCats );
		}
	}

	$categoryDisplay = sanitize_text_field( $_POST['category_display'] );

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, categories = %s, display = %d, created_at = NOW()", 'category', $categoryName, $categoryCategories, $categoryDisplay ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, categories = %s, display = %d, updated_at = NOW() WHERE id = %d", $categoryName, $categoryCategories, $categoryDisplay, $idViral ) );
	}
	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=category&category=' . $idViral );
}