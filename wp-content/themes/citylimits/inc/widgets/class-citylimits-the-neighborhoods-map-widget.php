<?php

/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'citylimits_the_neighborhoods_map_widget' );
});

/*
 * List all of the terms in a custom taxonomy
 */
class citylimits_the_neighborhoods_map_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' 	=> 'citylimits-the-neighborhoods-map',
			'description' 	=> __('Display the The Neighborhoods map for Mapping the Future', 'citylimits')
		);
		parent::__construct( 'citylimits-the-neighborhoods-map-widget', __('City Limits Neighborhoods Map', 'citylimits'), $widget_ops);
	}

	function widget( $args, $instance ) {
		global $post;
		// Preserve global $post
		$preserve = $post;

		$widget_title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( ! empty( $widget_title ) ) echo $args['before_title'] . $widget_title . $args['after_title'];
		
		include( locate_template( 'partials/neighborhoods-map.php' ) );

		echo $args['after_widget'];

		// Restore global $post
		wp_reset_postdata();
		$post = $preserve;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		// to control: which series, # of posts
		// @todo enhance with more control over thumbnail, icon, etc
		$instance = wp_parse_args( (array) $instance, array(
            'title' => 'The Neighborhoods',
            )
		);
		$title = esc_attr( $instance['title'] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'largo' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

	<?php
	}

}