<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

class VcsExtension_Widget extends WP_Widget {
	/**
	 * Register widget
	 */
	public function __construct() {
		parent::__construct(
	 		VCSEXTENSION_PLUGIN_SLUG . '_widget',
			'Viral Traffic Boost',
			array( 'description' => __( 'Viral Traffic Boost', VCSEXTENSION_PLUGIN_SLUG ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$id = (int)$instance['viral'];
		if ( !empty( $id ) ) {
			global $wpdb;
			$tableViral = $wpdb->prefix . 'viralcs_virals';

			$viral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tableViral WHERE id = %d", $id ) );
			if ( !empty( $viral ) ) {
				if ( !class_exists( 'VcsShortcode' ) ) {
					include_once( VIRALCONTENTSLIDER_PLUGIN_DIR . 'codes/shortcodes/shortcode.php' );
				}
				$shortcode = new VcsShortcode( $viral, 'wdg' );
				echo $shortcode->generate();
			}
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$wViral = '';
		if ( isset( $instance['viral'] ) ) {
			$wViral = (int)$instance['viral'];
		}
		?>
		<p>
			<select id="<?php echo $this->get_field_id( 'viral' ); ?>" name="<?php echo $this->get_field_name( 'viral' ); ?>" class="widefat">
				<?php
					$virals = $this->virals();
					if ( !empty( $virals ) ) {
						foreach ( $virals as $viral ) {
							$selected = '';
							if ( $viral->id == $wViral ) {
								$selected = 'selected="selected"';
							}
							echo '<option value="' . $viral->id . '" ' . $selected . '>' . $viral->name . '</option>';
						}
					}
				?>
			</select>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['viral'] = ( !empty( $new_instance['viral'] ) ) ? strip_tags( $new_instance['viral'] ) : '';
		return $instance;
	}

	/**
	 * Get virals
	 */
	private function virals() {
		global $wpdb;
		$tableVirals = $wpdb->prefix . 'viralcs_virals';
		$virals = $wpdb->get_results( "SELECT * FROM $tableVirals WHERE deleted_at IS NULL ORDER BY name ASC" );
		return $virals;
	}
}
