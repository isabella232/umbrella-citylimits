<?php

class cl_newsletter_header extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$widget_ops = array(
			'classname' => 'cl-newsletter-header',
			'description' => __( 'Quick-and-dirty widget to place newsletter/donate links in header', 'citylimits' )
		);
		parent::__construct(
			'cl-newsletter-header-widget', // Base ID
			__( 'CL Newsletter Link for Header', 'citylimits' ), // Name
			$widget_ops // Args
		);

	}

	function widget( $args, $instance ) {
		get_template_part( 'partials/newsletter-signup', 'header' );
	}
}
