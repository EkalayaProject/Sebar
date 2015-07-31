<?php
if ( !class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ViralsTable extends WP_List_Table {
	public function __construct() {
		global $status, $page;
		parent::__construct( array(
			'singular' => 'viral',
			'plural' => 'virals',
			'ajax' => false
		));
  }

	private function get_related_datas() {
		global $wpdb;
		$tableViral = $wpdb->prefix . 'viralcs_virals';

		$searchString = '';
		if ( isset( $_POST['s'] ) ) {
			$searchString = sanitize_text_field( $_POST['s'] );
		}

		if ( empty( $searchString ) ) {
			$q = "SELECT * FROM $tableViral WHERE deleted_at IS NULL";
			$orderby = !empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'updated_at';
			$order = !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
			if ( !empty( $orderby ) && !empty( $order ) ) { $q.=' ORDER BY ' . $orderby . ' ' . $order; }
			$getDatas = $wpdb->get_results( $q );
		} else {
			$q = "SELECT * FROM $tableViral WHERE name LIKE '%$searchString%' OR type LIKE '%$searchString%' AND deleted_at IS NULL ORDER BY updated_at DESC";
			$getDatas = $wpdb->get_results( $q );
		}

		$tableFields = array();
		if ( !empty( $getDatas ) ) {
			foreach ( $getDatas as $gData ) {
				switch ( $gData->type ) {
					case 'customlink':
						$type = 'Custom Link';
						break;
					case 'custom':
						$type = 'Custom Article';
						break;
					default:
						$type = ucfirst( $gData->type );
						break;
				}
				
				$tableFields[] = array(
					'id' => $gData->id,
					'name' => $gData->name,
					'type' => $type,
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
			'type' => 'Type',
			'updated_at' => 'Last updated',
		);
		return $columns;
	}

	public function column_name( $item ) {
		switch ( $item['type'] ) {
			case 'Custom Link':
				$type = 'customlink';
				break;
			case 'Custom Article':
				$type = 'custom';
				break;
			default:
				$type = $item['type'];
				break;
		}
		
		$actions = array(
			'edit' => sprintf( '<span class="dashicons dashicons-edit"></span> <a href="?page=%s&tab=%s&node=%s&%s=%d">Edit</a>',
				VIRALCONTENTSLIDER_PLUGIN_SLUG,
				'viral',
				strtolower( $type ),
				strtolower( $type ),
				sanitize_text_field( $item['id'] )
			),
			'trash' => sprintf( '<span class="dashicons dashicons-trash"></span><a href="%s" class="%s" data-name="%s">Trash</a>',
				wp_nonce_url( admin_url() . 'admin.php?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=viral&node=trash&viral=' . $item['id'], 'viralcontentslider_trash_viral', 'viralcontentslider_trash_viral_nonce' ),
				'viralcontentslider_trash_viral',
				sanitize_text_field( $item['name'] )
			),
			'options' => sprintf( '<span class="dashicons dashicons-art"></span><a href="%s">Style & options</a>',
				'?page=' . VIRALCONTENTSLIDER_PLUGIN_SLUG . '&tab=options&node=' . strtolower( $type ) . '&viral=' . sanitize_text_field( $item['id'] )
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
      if ( !empty( $_POST['viral'] ) ) {
				foreach ( $_POST['viral'] as $idViral ) {
					global $wpdb;
					$tableViral = $wpdb->prefix . 'viralcs_virals';
					$wpdb->query( $wpdb->prepare( "UPDATE $tableViral SET deleted_at = NOW() WHERE id = %d", $idViral ) );
				}
			}
    }
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', false ),
			'type' => array( 'type', false )
		);
		return $sortable_columns;
	}
	
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'type':
			case 'updated_at':
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