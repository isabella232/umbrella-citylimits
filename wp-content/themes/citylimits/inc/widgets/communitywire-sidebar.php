<?php

/**
 * Neighborhood Content 
 */
class communitywire_sidebar extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$widget_ops = array(
			'classname' => 'communitywire-sidebar',
			'description' => __( 'Dynamic widget to display CommunityWire announcements + events in a sidebar.', 'citylimits' )
		);
		parent::__construct(
			'communitywire-sidebar-widget', // Base ID
			__( 'CommunityWire Sidebar', 'citylimits' ), // Name
			$widget_ops // Args
		);

	}

	/**
	 * Outputs the content of the recent posts widget.
	 *
	 * @param array $args widget arguments.
	 * @param array $instance saved values from databse.
	 * @global $post
	 * @global $shown_ids An array of post IDs already on the page, to avoid duplicating posts
	 * @global $wp_query Used to get posts on the page not in $shown_ids, to avoid duplicating posts
	 */
	function widget( $args, $instance ) {

		global $post,
			$wp_query, // grab this to copy posts in the main column
			$shown_ids; // an array of post IDs already on a page so we can avoid duplicating posts;
		
		// Preserve global $post
		$preserve = $post;
		
		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		dynamic_sidebar( 'communitywire-sidebar-content' );

		// print all of the items
		echo $output;

		echo $after_widget;

		$post = $preserve;
	}


	function form( $instance ) {
		?>

		<p>This widget outputs the widgets under the "CommunityWire Sidebar Content" sidebar area. Please make changes there.</p>

	<?php
	}
}
