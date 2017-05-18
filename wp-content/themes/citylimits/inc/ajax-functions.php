<?php
/**
 * A file of functions modifying Largo's inc/ajax-functions.php
 * Primarily used for LMP modifications
 */

/**
 * set the correct partial for LMP posts at /zonein-events/
 * @since Largo 0.5.5.3
 * @filter largo_lmp_template_partial
 */
function citylimits_zonein_events_lmp_template_partial( $partial, $query ) {
	if ( is_array($query->query) ) {
		$args = $query->query;
		if ( $args['post_type'] === 'zonein_events' ) {
			$partial = 'zoneinevents';
		}

	}
	return $partial;
}
add_filter( 'largo_lmp_template_partial', 'citylimits_zonein_events_lmp_template_partial', 10, 2 );

/**
 * Filter the LMP WP_Query by neighborhood on neighborhood post types
 */
function citylimits_neighborhood_archive_lmp_query( $config ) {
	if ( $config['query']['post-type'] === 'news' && isset( $_GET['neighborhood'] ) && ! empty( $_GET['neighborhood'] ) ) {
		$config['query']['tax_query'] = array(
			array(
				'taxonomy' => 'neighborhoods',
				'field' => 'slug',
				'terms' => sanitize_key( $_GET['neighborhood'] ),
			),
		);
	}
	return $config;
}
add_action( 'largo_load_more_posts_json', 'citylimits_neighborhood_archive_lmp_query', 1 );
