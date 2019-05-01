<?php

/**
 * Neighborhood Content 
 */
class jp_cl_related_posts extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$widget_ops = array(
			'classname' => 'jp-cl-related-posts',
			'description' => __( 'Quick-and-dirty widget to show Jetpack\'s related posts. To configure, go to Appearance: Customize', 'citylimits' )
		);
		parent::__construct(
			'jp-cl-related-posts-widget', // Base ID
			__( 'Jetpack Related Posts', 'citylimits' ), // Name
			$widget_ops // Args
		);

	}

	/**
	 * Just a wrapper for the Jetpack shortcode.
	 *
	 */
	function widget( $args, $instance ) {
		if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
			if ( current_user_can('administrator') ) {
				echo do_shortcode( '[jetpack-related-posts]' );
			}
		}
	}
}
