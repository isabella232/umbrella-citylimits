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
