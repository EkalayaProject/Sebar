<?php
require( VIRALCONTENTSLIDER_PLUGIN_DIR . 'libraries/youtube/youtube.madcoda.class.php' );
use Madcoda\ViralTrafficBoostMadCodaYoutube;

class ViralTrafficBoostBrowseYoutube{
	private $youtube;
	public function __construct( $googleKey = '' ) {
		if ( empty( $googleKey ) ) {
			throw new Exception( 'Google API Key is empty.' );
		}

		$this->youtube = new ViralTrafficBoostMadCodaYoutube( array( 'key' => $googleKey ) );
	}

	private function getDuration( $videoId ) {
		$duration = '';
		$video = $this->youtube->getVideoInfo( $videoId );
		if ( !empty( $video ) ) {
			if ( isset( $video->contentDetails->duration ) ) {
				$duration = $this->covtime( $video->contentDetails->duration );
			}
		}
		return $duration;
	}

	public function getChannelIdByChannelName( $channelName ) {
		try {
			$channel = $this->youtube->getChannelByName( $channelName );
			if ( !empty( $channel ) ) {
				if ( isset( $channel->id ) ) {
					return $channel->id;
				}
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function get_playlist_videos_madcoda( $type, $key ) {
		$theVideos = array();
		
		if ( $type == 'playlist' ) {
			try {
				$youtubeVideos = $this->youtube->getPlaylistItemsByPlaylistId( $key );
				if ( empty( $youtubeVideos ) ) {
					throw new Exception( 'Empty video found with Playlist ID ' . $key );
				}

				foreach ( $youtubeVideos as $video ) {
					$videoId = $video->snippet->resourceId->videoId;

					$videoObject = new stdClass();
					$videoObject->published = $video->snippet->publishedAt;
					$videoObject->title = $video->snippet->title;
					$videoObject->description = $video->snippet->description;
					$videoObject->video_id = $videoId;
					$videoObject->thumbnail = $video->snippet->thumbnails->high->url;
					$videoObject->duration = $this->getDuration( $videoId );

					$theVideos[] = $videoObject;
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		if ( $type == 'channel' ) {
			try {
				$youtubeVideos = $this->youtube->searchChannelVideos( '', $key, 20 );
				if ( empty( $youtubeVideos ) ) {
					throw new Exception( 'Empty video found with Channel ID ' . $key );
				}

				foreach ( $youtubeVideos as $video ) {
					$videoId = $video->id->videoId;

					$videoObject = new stdClass();
					$videoObject->published = $video->snippet->publishedAt;
					$videoObject->title = $video->snippet->title;
					$videoObject->description = $video->snippet->description;
					$videoObject->video_id = $videoId;
					$videoObject->thumbnail = $video->snippet->thumbnails->high->url;
					$videoObject->duration = $this->getDuration( $videoId );

					$theVideos[] = $videoObject;
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		if ( $type == 'keyword' ) {
			try {
				$youtubeVideos = $this->youtube->searchVideos( $key );
				/*$params = array(
				  'q' => 'Android',
				  'type' => 'video',
				  'part' => 'id, snippet',
				  'maxResults' => 50
				);
				$youtubeVideos = $this->youtube->searchAdvanced( $params, true );
				if (isset($youtubeVideos['info']['nextPageToken'])) {
			    $youtubeVideos['pageToken'] = $youtubeVideos['info']['nextPageToken'];
				}
				$youtubeVideos = $this->youtube->searchAdvanced($params, true);*/

				if ( empty( $youtubeVideos ) ) {
					throw new Exception( 'Empty video found with keyword ' . $key );
				}

				foreach ( $youtubeVideos as $video ) {
					$videoId = $video->id->videoId;

					$videoObject = new stdClass();
					$videoObject->published = $video->snippet->publishedAt;
					$videoObject->title = $video->snippet->title;
					$videoObject->description = $video->snippet->description;
					$videoObject->video_id = $videoId;
					$videoObject->thumbnail = $video->snippet->thumbnails->high->url;
					$videoObject->duration = $this->getDuration( $videoId );

					$theVideos[] = $videoObject;
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}
		return $theVideos;
	}

	private function covtime( $youtubeDuration ) {
		preg_match_all( '/(\d+)/', $youtubeDuration, $parts );

		$hours = 0;
		if ( isset( $parts[0][0] ) ) {
			$hours = $parts[0][0];	
		}
		
		$minutes = 0;
		if ( isset( $parts[0][1] ) ) {
			$minutes = $parts[0][1];
		}
		
		$seconds = 0;
		if ( isset( $parts[0][2] ) ) {
			$seconds = $parts[0][2];
		}

		$duration = '';
		if ( $hours > 0 ) {
			$duration .= ' ' . $hours . ' hours';
		}
		if ( $minutes > 0 ) {
			$duration .= ' ' . $minutes . ' minutes';
		}
		if ( $seconds > 0 ) {
			$duration .= ' ' . $seconds . ' seconds';
		}

		return trim( $duration );
	}
}
?>