<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

if ( isset( $_POST['viralcontentslider_settings_form'] ) ) {
	if ( !isset( $_POST['viralcontentslider_settings_form_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_settings_form_nonce'], 'viralcontentslider_settings_form' ) ) {
		die( 'Cheating, uh?' );
	}

	# Cron
	$cronInterval = sanitize_text_field( $_POST['settings_cron_interval'] );
	$cronType = sanitize_text_field( $_POST['settings_cron_type'] );

	# Delete Old data
	$deleteOldDataInterval = sanitize_text_field( $_POST['settings_cron_purge_old_data_interval'] );

	# Thumbnail
	$defaultThumbnail = sanitize_text_field( $_POST['settings_default_thumbnail'] );

	# API
	$googleAPIKey = sanitize_text_field( $_POST['settings_google_api_key'] );

	if ( class_exists( 'VcsExtension' ) ) :
		# Widget
		$marginTop = sanitize_text_field( $_POST['settings_widget_margin_top'] );
		$marginBottom = sanitize_text_field( $_POST['settings_widget_margin_bottom'] );
		$marginLeft = sanitize_text_field( $_POST['settings_widget_margin_left'] );
		$marginRight = sanitize_text_field( $_POST['settings_widget_margin_right'] );
		$titleFontSize = sanitize_text_field( $_POST['settings_widget_title_font_size'] );
		$descriptionFontSize = sanitize_text_field( $_POST['settings_widget_description_font_size'] );
		$readmoreFontSize = sanitize_text_field( $_POST['settings_widget_readmore_font_size'] );
		$thumbnailWidth = sanitize_text_field( $_POST['settings_widget_thumbnail_width'] );
		$thumbnailHeight = sanitize_text_field( $_POST['settings_widget_thumbnail_height'] );
		$thumbnailWidthType = sanitize_text_field( $_POST['settings_widget_thumbnail_width_type'] );
		$thumbnailHeightType = sanitize_text_field( $_POST['settings_widget_thumbnail_height_type'] );

		# Social media
		$facebookAppId = sanitize_text_field( $_POST['settings_social_media_facebook_app_id'] );
		$twitterUsername = sanitize_text_field( $_POST['settings_social_media_twitter_username'] );
	endif;

	# Cron
	update_option( 'viralcontentslider_settings_cron_interval', $cronInterval );
	update_option( 'viralcontentslider_settings_cron_type', $cronType );

	# Delete Old data
	update_option( 'settings_cron_purge_old_data_interval', $deleteOldDataInterval );

	# Thumbnail
	update_option( 'viralcontentslider_settings_default_thumbnail', $defaultThumbnail );

	# API
	update_option( 'viralcontentslider_settings_google_api_key', $googleAPIKey );

	if ( class_exists( 'VcsExtension' ) ) :
		# Widget
		update_option( 'viralcontentslider_settings_widget_margin_top', $marginTop );
		update_option( 'viralcontentslider_settings_widget_margin_bottom', $marginBottom );
		update_option( 'viralcontentslider_settings_widget_margin_left', $marginLeft );
		update_option( 'viralcontentslider_settings_widget_margin_right', $marginRight );
		update_option( 'viralcontentslider_settings_widget_title_font_size', $titleFontSize );
		update_option( 'viralcontentslider_settings_widget_description_font_size', $descriptionFontSize );
		update_option( 'viralcontentslider_settings_widget_readmore_font_size', $readmoreFontSize );
		update_option( 'viralcontentslider_settings_widget_thumbnail_width', $thumbnailWidth );
		update_option( 'viralcontentslider_settings_widget_thumbnail_height', $thumbnailHeight );
		update_option( 'viralcontentslider_settings_widget_thumbnail_width_type', $thumbnailWidthType );
		update_option( 'viralcontentslider_settings_widget_thumbnail_height_type', $thumbnailHeightType );

		# Social media
		update_option( 'viralcontentslider_settings_social_media_facebook_app_id', $facebookAppId );
		update_option( 'viralcontentslider_settings_social_media_twitter_username', $twitterUsername );
	endif;
}
