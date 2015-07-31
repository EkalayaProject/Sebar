<?php
/**
 * Video
 */
if ( isset( $_POST['viralcontentslider_save_video'] ) ) {
	if ( !isset( $_POST['viralcontentslider_save_video_nonce'] ) || !wp_verify_nonce( $_POST['viralcontentslider_save_video_nonce'], 'viralcontentslider_save_video' ) ) {
		die( 'Cheating, uh?' );
	}

	$idViral = sanitize_text_field( $_POST['viralcontentslider_hidden_id'] );
	$name = sanitize_text_field( $_POST['video_name'] );
	$videos = $_POST['viralcontentslider_yvideos'];

	if ( empty( $idViral ) ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO $tableViral SET type = %s, name = %s, created_at = NOW()", 'video', $name ) );
		$idViral = $wpdb->insert_id;
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET name = %s, updated_at = NOW() WHERE id = %d", $name, $idViral ) );
	}

	$videoIds = array();
	if ( !empty( $videos ) ) {
		foreach ( $videos as $video ) {
			$obj = explode( '_VCS_', $video );
			$videoId = sanitize_text_field( $obj[0] );
			$title = sanitize_text_field( stripslashes_deep( $obj[1] ) );
			$duration = sanitize_text_field( $obj[2] );
			$strDuration = sanitize_text_field( $obj[3] );
			$link = $obj[4];
			$thumbnail = $obj[5];
			$pub = sanitize_text_field( $obj[6] );
			$published = date( 'Y-m-d H:i:s', strtotime( $pub ) );
			$description = sanitize_text_field( stripslashes_deep( $obj[7] ) );
			$getVideo = $this->get_video_byvideoid( $videoId, $idViral );
			if ( empty( $getVideo ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO $tableVideos SET id_viral = %d, video_id = %s, published = %s, title = %s, description = %s, duration = %s, str_duration = %d, thumbnail = %s, link = %s, created_at = NOW()", $idViral, $videoId, $published, $title, $description, $duration, $strDuration, $thumbnail, $link ) );
			}

			/**
			 * If video exist but flag as deleted then reset the flag
			 */
			if ( !empty( $getVideo ) ) {
				if ( $getVideo->video_id == $videoId ) {
					$wpdb->query( $wpdb->prepare( "UPDATE $tableVideos SET deleted_at = NULL WHERE video_id = %s AND id_viral = %d", $videoId, $idViral ) );
				}
			}

			$videoIds[] = "'" . $videoId . "'";
		}
		$implodedVideoIds = implode( ',', $videoIds );
		$wpdb->query( $wpdb->prepare( "UPDATE $tableVideos SET deleted_at = NOW() WHERE id_viral = %d AND video_id NOT IN($implodedVideoIds)", $idViral ) );
	} else {
		/**
		 * If videos removed from element, that means the video is deleted by the user.
		 */
		$wpdb->query( $wpdb->prepare( "UPDATE $tableVideos SET deleted_at = NOW() WHERE id_viral = %d", $idViral ) );
	}

	wp_safe_redirect( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=video&video=' . $idViral );
}