<?php
/**
 * Page Template: Mapping The Future of NYC Neighborhoods
 * Template Name: Mapping The Future Project - Series Home
 * Description: Custom landing page for the Mapping The Future project with the /neighborhoods/ slug
 */

global $shown_ids, $post;

/*
 * Establish some common query parameters
 */
$features = get_the_terms( $post->ID, 'series' );
// we're going to assume that the series landing page is in no more than one series, because that's how you're *supposed* to do it.
$series = $features[0];
$project_tax_query = array(
		'taxonomy' => 'series',
		'terms' => $series->term_id,
		'field' => 'ID',
	);

// begin the page rendering

// This is the rezone-specific header, /header-rezone.php
get_header( 'rezone' );

?>



<?php
	include( locate_template( 'partials/neighborhoods-overview.php' ) );
	include( locate_template( 'partials/neighborhoods-news.php' ) );
	include( locate_template( 'partials/neighborhoods-videos.php' ) );
	include( locate_template( 'partials/neighborhoods-commentary.php' ) );
	include( locate_template( 'partials/neighborhoods-map.php' ) );
	include( locate_template( 'partials/neighborhoods-101.php' ) );
	include( locate_template( 'partials/neighborhoods-documents.php' ) );
	include( locate_template( 'partials/neighborhoods-ctas.php' ) );
	get_footer();
