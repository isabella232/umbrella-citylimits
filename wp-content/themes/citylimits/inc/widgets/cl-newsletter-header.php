<?php

class cl_newsletter_header extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$widget_ops = array(
			'classname' => 'cl-newsletter-header',
			'description' => __( 'Display the generic sitewide newsletter signup form.', 'citylimits' )
		);
		parent::__construct(
			'cl-newsletter-header-widget', // Base ID
			__( 'City Limits Newsletter Signup', 'citylimits' ), // Name
			$widget_ops // Args
		);

	}

	function widget( $args, $instance ) {
		get_template_part( 'partials/newsletter-signup', 'maincolumn' );
	}
}
