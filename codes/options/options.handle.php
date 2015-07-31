<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

if ( isset( $_POST['viralcontentslider_options'] ) ) {
	if ( !isset( $_POST['viralcontentslider_options_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_options_nonce'], 'viralcontentslider_options' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_idviral'] );
	$type = sanitize_text_field( $_POST['viralcontentslider_hidden_type'] );

	$displayType = sanitize_text_field( $_POST['options_display_type'] );
	$displaySort = sanitize_text_field( $_POST['options_display_sort'] );
	$boxShadow = sanitize_text_field( $_POST['options_box_shadow'] );
	$textAlign = sanitize_text_field( $_POST['options_text_align'] );
	$limitTitleWords = sanitize_text_field( $_POST['options_title_limit_words'] );
	if ( $limitTitleWords == 0 ) {
		$limitTitleWords = 6;
	}
	$titleFontSize = sanitize_text_field( $_POST['options_title_font_size'] );
	if ( $titleFontSize == 0 ) {
		$titleFontSize = 16;
	}
	$titleFontColor = sanitize_text_field( $_POST['options_title_font_color'] );
	if ( empty( $titleFontColor ) ) {
		$titleFontColor = '#000000';
	}
	$limitDescriptionWords = sanitize_text_field( $_POST['options_description_limit_words'] );
	if ( $limitDescriptionWords == 0 ) {
		$limitDescriptionWords = 15;
	}
	$descriptionFontSize = sanitize_text_field( $_POST['options_description_font_size'] );
	if ( $descriptionFontSize == 0 ) {
		$descriptionFontSize = 14;
	}
	$descriptionFontColor = sanitize_text_field( $_POST['options_description_font_color'] );
	if ( empty( $descriptionFontColor ) ) {
		$descriptionFontColor = '#2e2e2e';
	}
	$showNavigation = sanitize_text_field( $_POST['options_show_navigation'] );
	$readmoreFontSize = sanitize_text_field( $_POST['options_readmore_font_size'] );
	if ( $readmoreFontSize == 0 ) {
		$readmoreFontSize = 12;
	}
	$readmoreFontColor = sanitize_text_field( $_POST['options_readmore_font_color'] );
	if ( empty( $readmoreFontColor ) ) {
		$readmoreFontColor = '#aaa';
	}
	$readmoreText = sanitize_text_field( $_POST['options_readmore_text'] );
	if ( empty( $readmoreText ) ) {
		$readmoreText = 'Read more';
	}
	$autoPlay = sanitize_text_field( $_POST['options_autoplay'] );

	$getExistingViral = $this->get_options( $idViral );
	if ( empty( $getExistingViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableOptions SET
			id_viral = %d,
			type = %s,
			display_type = %s,
			display_sort = %s,
			box_shadow = %d,
			text_align = %s,
			title_limit_words = %d,
			title_font_size = %d,
			title_font_color = %s,
			description_limit_words = %d,
			description_font_size = %d,
			description_font_color = %s,
			nav_show = %s,
			readmore_font_size = %d,
			readmore_font_color = %s,
			readmore_text = %s,
			autoplay = %s,
			created_at = NOW()",
			$idViral,
			$type,
			$displayType,
			$displaySort,
			$boxShadow,
			$textAlign,
			$limitTitleWords,
			$titleFontSize,
			$titleFontColor,
			$limitDescriptionWords,
			$descriptionFontSize,
			$descriptionFontColor,
			$showNavigation,
			$readmoreFontSize,
			$readmoreFontColor,
			$readmoreText,
			$autoPlay
		) );
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableOptions SET
			type = %s,
			display_type = %s,
			display_sort = %s,
			box_shadow = %d,
			text_align = %s,
			title_limit_words = %d,
			title_font_size = %d,
			title_font_color = %s,
			description_limit_words = %d,
			description_font_size = %d,
			description_font_color = %s,
			nav_show = %s,
			readmore_font_size = %d,
			readmore_font_color = %s,
			readmore_text = %s,
			autoplay = %s,
			updated_at = NOW() WHERE id_viral = %d",
			$type,
			$displayType,
			$displaySort,
			$boxShadow,
			$textAlign,
			$limitTitleWords,
			$titleFontSize,
			$titleFontColor,
			$limitDescriptionWords,
			$descriptionFontSize,
			$descriptionFontColor,
			$showNavigation,
			$readmoreFontSize,
			$readmoreFontColor,
			$readmoreText,
			$autoPlay,
			$idViral
		) );
	}
	$wpdb->query( $wpdb->prepare( "UPDATE $tableVirals SET updated_at = NOW() WHERE id = %d", $idViral ) );
	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=' . $type . '&viral=' . $idViral );
}
