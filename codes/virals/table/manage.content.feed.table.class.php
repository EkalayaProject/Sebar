<?php
if ( !class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ViralsFeedsTable extends WP_List_Table {
	public function __construct() {
		global $status, $page;
		parent::__construct( array(
			'singular' => 'feedcontent',
			'plural' => 'feedcontents',
			'ajax' => false
		));
  }

	private function get_related_datas() {
		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';
		$tableFeeds = $wpdb->prefix . 'viralcs_feeds';

		$idViral = sanitize_text_field( $_GET['feed'] );
		$searchString = '';
		if ( isset( $_POST['s'] ) ) {
			$searchString = sanitize_text_field( $_POST['s'] );
		}

		if ( empty( $searchString ) ) {
			$q = "SELECT tf.id, tf.id_viral, tv.name, tv.feed, tf.link, tf.title, tf.description, tf.date_published, tf.image, tf.created_at, tf.updated_at
				FROM $tableFeeds AS tf LEFT JOIN $tableVirals AS tv
				ON tf.id_viral = tv.id
				WHERE tf.id_viral = $idViral AND tf.deleted_at IS NULL";
			$orderby = !empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date_published';
			$order = !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
			if ( !empty( $orderby ) && !empty( $order ) ) { $q.=' ORDER BY ' . $orderby . ' ' . $order; }
			$getDatas = $wpdb->get_results( $q );
		} else {
			$q = "SELECT tf.id, tf.id_viral, tv.name, tv.feed, tf.link, tf.title, tf.description, tf.date_published, tf.image, tf.created_at, tf.updated_at
				FROM $tableFeeds AS tf LEFT JOIN $tableVirals AS tv
				ON tf.id_viral = tv.id
				WHERE tv.name LIKE '%$searchString%'
				OR tv.feed LIKE '%$searchString%'
				OR tf.link LIKE '%$searchString%'
				OR tf.title LIKE '%$searchString%'
				OR tf.description LIKE '%$searchString%'
				AND tf.id_viral = $idViral AND tf.deleted_at IS NULL ORDER BY date_published DESC";
			$getDatas = $wpdb->get_results( $q );
		}

		$tableFields = array();
		if ( !empty( $getDatas ) ) {
			foreach ( $getDatas as $gData ) {
				$tableFields[] = array(
					'id' => $gData->id,
					'id_viral' => $gData->id_viral,
					'name' => $gData->name,
					'feed' => $gData->feed,
					'link' => $gData->link,
					'link_html' => '<a href="' . $gData->link . '" target="_blank" class="button action" title="' . $gData->title . '">Click to see</a>',
					'title' => $gData->title,
					'description' => $gData->description,
					'date_published' => date( 'j M y, h:ia', strtotime( $gData->date_published ) ),
					'image' => $gData->image,
					'created_at' => $this->relative_time( strtotime( $gData->created_at ) ),
					'updated_at' => $this->relative_time( strtotime( $gData->updated_at ) )
				);
			}
		}
		return $tableFields;
	}
	
	private function relative_time( $ptime ) {
		$etime = time() - $ptime;

		if ( $etime < 1 ) {
			return 'Just now';
		}

		$a = array( 12 * 30 * 24 * 60 * 60 => 'year',
		30 * 24 * 60 * 60 => 'month',
		24 * 60 * 60 => 'day',
		60 * 60 => 'hour',
		60 => 'minute',
		1 => 'second'
		);

		foreach ( $a as $secs => $str ) {
			$d = $etime / $secs;
			if ( $d >= 1 ) {
			$r = round( $d );
			return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
			}
		}
	}
	
	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => 'Name',
			'feed' => 'Feed',
			'title' => 'Title',
			'link_html' => 'Url',
			'date_published' => 'Date published',
		);
		return $columns;
	}

	public function column_name( $item ) {
		$actions = array(
			'update' => sprintf( '<span class="dashicons dashicons-edit"></span> <a href="?page=%s&tab=%s&node=%s&%s=%d&obj=%d&action=update">Update</a>',
				VIRALCONTENTSLIDER_PLUGIN_SLUG,
				'viral',
				'feed',
				'feed',
				sanitize_text_field( $item['id_viral'] ),
				sanitize_text_field( $item['id'] )
			),
			'trash' => sprintf( '<span class="dashicons dashicons-trash"></span><a href="%s" class="%s" data-title="%s" data-link="%s">Trash</a>',
				wp_nonce_url( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=feed&feed=' . $item['id_viral'] . '&obj=' . $item['id'] . '&action=trash', 'viralcontentslider_trash_content_feed', 'viralcontentslider_trash_content_feed_nonce' ),
				'viralcontentslider_trash_content_feed',
				sanitize_text_field( $item['title'] ),
				sanitize_text_field( $item['link'] )
			)
		);

		return sprintf( '%1$s %2$s', $item['name'], $this->row_actions( $actions ) );
	}

	public function get_bulk_actions() {
		$actions = array(
			'trash' => 'Trash'
		);
		return $actions;
	}

	public function column_cb( $item ) {
    return sprintf(
     	'<input type="checkbox" name="%1$s[]" value="%2$s" />',
      $this->_args['singular'],
      $item['id']
    );
  }

  public function process_bulk_action() {
    if ( 'trash' === $this->current_action() ) {
      if ( !empty( $_POST['feedcontent'] ) ) {
				foreach ( $_POST['feedcontent'] as $idFeed ) {
					global $wpdb;
					$tableFeeds = $wpdb->prefix . 'viralcs_feeds';
					$wpdb->query( $wpdb->prepare( "UPDATE $tableFeeds SET deleted_at = NOW() WHERE id = %d", $idFeed ) );
				}
			}
    }
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', false ),
			'date_published' => array( 'date_published', false )
		);
		return $sortable_columns;
	}
	
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'feed':
			case 'title':
			case 'link_html':
			case 'date_published':
				return $item[$column_name];
			default:
				return print_r( $item, true );
		}
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$per_page = 5;
		$current_page = $this->get_pagenum();
		$total_items = count( $this->get_related_datas() );
		$tempData = array_slice( $this->get_related_datas(), ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->set_pagination_args( array(
			'total_items' => $total_items, 
			'per_page' => $per_page
		) );
		$this->items = $tempData;
	}
}