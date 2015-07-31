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

	$openInLandingPage = sanitize_text_field( $_POST['options_open_link_in_landing_page'] );
	$openInNewTab = sanitize_text_field( $_POST['options_open_link_in_new_tab'] );
	$fontFamily = sanitize_text_field( $_POST['options_font_family'] );
	$displayInHome = sanitize_text_field( $_POST['options_display_in_home'] );
	$arrowStyle = sanitize_text_field( $_POST['options_arrow_style'] );
	$doCats = $_POST['options_autoinsert_in'];
	if ( is_null( $doCats ) ) {
		$autoinsertIn = '';
	} else {
		if ( in_array( 'ALL', $doCats ) ) {
			$autoinsertIn = '{ALL}';
		} else {
			foreach( $doCats as $idCat ) {
				$arrDisplayOnCats[] = '{' . $idCat . '}';
			}
			$autoinsertIn = implode( ',', $arrDisplayOnCats );
		}
	}
	$autoinsertPosition = sanitize_text_field( $_POST['options_autoinsert_position'] );
	$autoinsertParagraph = sanitize_text_field( $_POST['options_autoinsert_paragraph'] );
	$backgroundColor = sanitize_text_field( $_POST['options_background_color'] );
	$shadowColor = sanitize_text_field( $_POST['options_shadow_color'] );
	$thumbnailWidth = sanitize_text_field( $_POST['options_thumbnail_width'] );
	$thumbnailHeight = sanitize_text_field( $_POST['options_thumbnail_height'] );
	$thumbnailWidthType = sanitize_text_field( $_POST['options_thumbnail_width_type'] );
	$thumbnailHeightType = sanitize_text_field( $_POST['options_thumbnail_height_type'] );
	$marginTop = sanitize_text_field( $_POST['options_margin_top'] );
	$marginBottom = sanitize_text_field( $_POST['options_margin_bottom'] );
	$marginLeft = sanitize_text_field( $_POST['options_margin_left'] );
	$marginRight = sanitize_text_field( $_POST['options_margin_right'] );
	$speedTransition = sanitize_text_field( $_POST['options_speed_transition'] );
	$pauseInterval = sanitize_text_field( $_POST['options_pause_interval'] );;
	$facebookAppId = sanitize_text_field( $_POST['options_facebook_app_id'] );
	$twitterUsername = sanitize_text_field( $_POST['options_twitter_username'] );

	$getExistingViral = $this->get_extension_options( $idViral );
	if ( empty( $getExistingViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableExtensionOptions SET
			id_viral = %d,
			type = %s,
			open_in_landing_page = %s,
			open_in_new_tab = %s,
			font_family = %s,
			display_in_home = %s,
			arrow_style = %s,
			autoinsert_in = %s,
			autoinsert_position = %s,
			autoinsert_paragraph = %d,
			background_color = %s,
			shadow_color = %s,
			thumbnail_width = %d,
			thumbnail_height = %d,
			thumbnail_width_type = %s,
			thumbnail_height_type = %s,
			margin_top = %d,
			margin_bottom = %d,
			margin_left = %d,
			margin_right = %d,
			speed_transition = %d,
			pause_interval = %d,
			facebook_app_id = %s,
			twitter_username = %s,
			created_at = NOW()",
			$idViral,
			$type,
			$openInLandingPage,
			$openInNewTab,
			$fontFamily,
			$displayInHome,
			$arrowStyle,
			$autoinsertIn,
			$autoinsertPosition,
			$autoinsertParagraph,
			$backgroundColor,
			$shadowColor,
			$thumbnailWidth,
			$thumbnailHeight,
			$thumbnailWidthType,
			$thumbnailHeightType,
			$marginTop,
			$marginBottom,
			$marginLeft,
			$marginRight,
			$speedTransition,
			$pauseInterval,
			$facebookAppId,
			$twitterUsername
		) );
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableExtensionOptions SET
			type = %s,
			open_in_landing_page = %s,
			open_in_new_tab = %s,
			font_family = %s,
			display_in_home = %s,
			arrow_style = %s,
			autoinsert_in = %s,
			autoinsert_position = %s,
			autoinsert_paragraph = %d,
			background_color = %s,
			shadow_color = %s,
			thumbnail_width = %d,
			thumbnail_height = %d,
			thumbnail_width_type = %s,
			thumbnail_height_type = %s,
			margin_top = %d,
			margin_bottom = %d,
			margin_left = %d,
			margin_right = %d,
			speed_transition = %d,
			pause_interval = %d,
			facebook_app_id = %s,
			twitter_username = %s,
			updated_at = NOW() WHERE id_viral = %d",
			$type,
			$openInLandingPage,
			$openInNewTab,
			$fontFamily,
			$displayInHome,
			$arrowStyle,
			$autoinsertIn,
			$autoinsertPosition,
			$autoinsertParagraph,
			$backgroundColor,
			$shadowColor,
			$thumbnailWidth,
			$thumbnailHeight,
			$thumbnailWidthType,
			$thumbnailHeightType,
			$marginTop,
			$marginBottom,
			$marginLeft,
			$marginRight,
			$speedTransition,
			$pauseInterval,
			$facebookAppId,
			$twitterUsername,
			$idViral
		) );
	}
}