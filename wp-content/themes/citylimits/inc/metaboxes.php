<?php
/**
 * Class to facilitate creating metaboxes
 */
class CityLimits_Create_Meta_Boxes {
	private $screens = array(
		'zonein_events',
	);
	private $fields = array(
		array(
			'id' => 'date_time',
			'label' => 'Date & Time',
			'type' => 'datetime',
		),
	);

	/**
	 * Class construct method. Adds actions to their respective WordPress hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_datepicker' ) );
		add_action( 'admin_footer', array( $this, 'initialize_datepicker' ) );
	}

	/**
	 * Hooks into WordPress' add_meta_boxes function.
	 * Goes through screens (post types) and adds the meta box.
	 */
	public function add_meta_boxes() {
		foreach ( $this->screens as $screen ) {
			add_meta_box(
				'event-information',
				__( 'Event Information', 'citylimits' ),
				array( $this, 'add_meta_box_callback' ),
				$screen,
				'normal',
				'default'
			);
		}
	}

	/**
	 * Generates the HTML for the meta box
	 * 
	 * @param object $post WordPress post object
	 */
	public function add_meta_box_callback( $post ) {
		wp_nonce_field( 'event_information_data', 'event_information_nonce' );
		$this->generate_fields( $post );
	}

	/**
	 * Generates the field's HTML for the meta box.
	 */
	public function generate_fields( $post ) {
		$output = '';
		foreach ( $this->fields as $field ) {
			$label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$db_value = get_post_meta( $post->ID, 'event_information_' . $field['id'], true );
			switch ( $field['type'] ) {
				case 'datetime':
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$field['type'] !== 'color' ? 'class="regular-text"' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						date( 'F j, Y G:i', $db_value )
					);
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$field['type'] !== 'color' ? 'class="regular-text"' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						$db_value
					);
			}
			$output .= $this->row_format( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}

	/**
	 * Generates the HTML for table rows.
	 */
	public function row_format( $label, $input ) {
		return sprintf(
			'<tr><th scope="row">%s</th><td>%s</td></tr>',
			$label,
			$input
		);
	}

	/**
	 * hooks into wordpress' save_post function
	 */
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['event_information_nonce'] ) )
			return $post_id;

		$nonce = $_POST['event_information_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'event_information_data' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		foreach ( $this->fields as $field ) {
			if ( isset( $_POST[ $field['id'] ] ) ) {
				switch ( $field['type'] ) {
					case 'datetime':
						$_POST[ $field['id'] ] = strtotime( $_POST[ $field['id'] ] );
						break;
				}
				update_post_meta( $post_id, 'event_information_' . $field['id'], $_POST[ $field['id'] ] );
			} else if ( $field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, 'event_information_' . $field['id'], '0' );
			}
		}
	}

	public function enqueue_datepicker() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script(
			'jquery-timepicker',
			get_stylesheet_directory_uri().'/js/jquery-ui-timepicker-addon.js',
			array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
			filemtime( get_stylesheet_directory().'/js/jquery-ui-timepicker-addon.js' )
		);
	}

	public function initialize_datepicker() {
		$current_screen = get_current_screen();
		if ( in_array( $current_screen->id, $this->screens ) ) { 
			wp_register_style(
				'jquery-ui-smoothness',
				get_stylesheet_directory_uri().'/css/jquery-ui-smoothness.css',
				array(),
				filemtime( get_stylesheet_directory().'/css/jquery-ui-smoothness.css' ),
			);
			wp_register_style(
				'jquery-ui-datepicker',
				get_stylesheet_directory_uri().'/css/datepicker.css',
				array(),
				filemtime( get_stylesheet_directory().'/css/datepicker.css' ),
			);
			wp_register_style(
				'jquery-ui-timepicker-addon',
				get_stylesheet_directory_uri().'/css/jquery-ui-timepicker-addon.css',
				array(),
				filemtime( get_stylesheet_directory().'/css/jquery-ui-timepickr-addon.css' ),
			);

			wp_enqueue_style( 'jquery-ui-smoothness' );
			wp_enqueue_style( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui-timepicker-addon' );
			?>
				<script>
					jQuery(document).ready(function($) {
						$("input[type=datetime]").datetimepicker();
					});
				</script>
			<?php
		}
	}
}
new CityLimits_Create_Meta_Boxes;
