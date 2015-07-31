<?php
/**
 * Article
 */
if ( isset( $_POST['viralcontentslider_save_article'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_article_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_article_nonce'], 'viralcontentslider_save_article' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$articleName = sanitize_text_field( stripslashes_deep( $_POST['article_name'] ) );
	
	$doPosts = $_POST['article_posts'];
	if ( is_null( $doPosts ) ) {
		$articlePosts = '';
	} else {
		foreach( $doPosts as $idPost ) {
			$arrDisplayOnPosts[] = '{' . $idPost . '}';
		}
		$articlePosts = implode( ',', $arrDisplayOnPosts );
	}

	$doPages = $_POST['article_pages'];
	if ( is_null( $doPages ) ) {
		$articlePages = '';
	} else {
		foreach( $doPages as $idPage ) {
			$arrDisplayOnPages[] = '{' . $idPage . '}';
		}
		$articlePages = implode( ',', $arrDisplayOnPages );
	}

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, posts = %s, pages = %s, created_at = NOW()", 'article', $articleName, $articlePosts, $articlePages ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, posts = %s, pages = %s, updated_at = NOW() WHERE id = %d", $articleName, $articlePosts, $articlePages, $idViral ) );
	}
	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=article&article=' . $idViral );
}