<?php
/**
 * Functionality related to Tribe's The Events Calendar plugin.
 */

/**
 * Remove the Tribe Customizer css <script>
 * https://gist.github.com/elimn/50cc4ac8b56cc2809bbc48e7c7e3b461
 */
function tribe_remove_customizer_css(){
	if ( class_exists( 'Tribe__Customizer' ) ) {
		remove_action( 'wp_print_footer_scripts', array( Tribe__Customizer::instance(), 'print_css_template' ), 15 );
	}
}
add_action( 'wp_footer', 'tribe_remove_customizer_css' );


/**
 * filter search results: remove old events
 */
function cl_pre_get_posts($query) {
	if ( !is_admin() && $query->is_main_query() && $query->is_search ) {
		$meta_query = $query->get('meta_query');
		$additional_query = array(
			'relation' => 'OR',
			array(
				'key' => '_EventStartDate',
				'value' => date("Y-m-d H:i:s"),
				'compare' => '>=',
				'type' => 'DATETIME'
			) ,
			array(
				'key' => '_EventStartDate',
				'compare' => 'NOT EXISTS'
			)
		);
		if ( is_array( $meta_query ) ) {
			$meta_query[] = $additional_query;
		} else {
			$meta_query = $additional_query;
		}

		$query->set('meta_query', $meta_query);
	}
	return $query;
}
add_action( 'pre_get_posts', 'cl_pre_get_posts' );

/**
 * Custom query for event list widget
 *
 * @link https://theeventscalendar.com/support/forums/topic/how-to-limit-widget-events-by-date/
 */
function tribe_custom_list_widget_events ( ){

	// uncomment the line below and fill in your custom args
	$args = array(
		// 'eventDisplay'=>'upcoming',
		// 'posts_per_page'=>-1,
		'tax_query'=> array(
			array(
				'taxonomy' => 'tribe_events_cat',
				'field' => 'slug',
				'terms' => 'communitywire-events'
			)
		)
	);

	$posts = tribe_get_events( $args );

	return $posts;
}
add_filter( 'tribe_get_list_widget_events', 'tribe_custom_list_widget_events' );

function add_cpt_capability_organizer( $args, $post_type ) {
	// Make sure we're only modifying our desired post type.
	if ( 'tribe_organizer' != $post_type ) 
		return $args;
	$args['capability_type'] = 'post';
	$args['public'] = 1;
	return $args;
}
add_filter( 'register_post_type_args', 'add_cpt_capability_organizer', 10, 2 );

function add_cpt_capability_venue( $args, $post_type ) {
	// Make sure we're only modifying our desired post type.
	if ( 'tribe_venue' != $post_type ) 
		return $args;
	$args['capability_type'] = 'post';
	$args['public'] = 1;
	return $args;
}
add_filter( 'register_post_type_args', 'add_cpt_capability_venue', 999, 2 );
