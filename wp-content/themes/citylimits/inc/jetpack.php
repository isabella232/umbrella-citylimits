<?php
/**
 * Jetpack compatibility functions
 *
 */

/**
 * remove Jetpack Related Posts from default location so we can move it elsewhere
 */
function jetpackme_remove_rp() {
    if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
        $jprp = Jetpack_RelatedPosts::init();
        $callback = array( $jprp, 'filter_add_target_to_dom' );
        remove_filter( 'the_content', $callback, 40 );
    }
}
add_filter( 'wp', 'jetpackme_remove_rp', 20 );

/**
 * limit Jetpack Related Posts results to last 3 years
 */
function jetpackme_related_posts_past_3years_only( $date_range ) {
	$date_range = array(
		'from' => strtotime( '-3 years' ),
		'to' => time(),
	);
	return $date_range;
}
add_filter( 'jetpack_relatedposts_filter_date_range', 'jetpackme_related_posts_past_3years_only' );

/**
 * Exclude some categories from Jetpack Related Posts
 */
function jetpackme_filter_exclude_category( $filters ) {
	$filters[] = array( 'not' =>
		array( 'term' => array( 'category.slug' => 'community-wire' ) )
	);
	$filters[] = array( 'not' =>
		array( 'term' => array( 'term' => 'uncategorized' ) )
	);
	return $filters;
}
add_filter( 'jetpack_relatedposts_filter_filters', 'jetpackme_filter_exclude_category' );

/**
 * Dequeue jetpack css so we can override it
 */
add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );
