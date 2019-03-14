<?php
/**
 * Page Template: The Future of NYC Neighborhoods
 * Template Name: Rezone Project - Series Home
 * Description: Custom landing page for the ReZone project with the /neighborhoods/ slug
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
	get_template_part( 'partials/neighborhoods', 'overview' );
	get_template_part( 'partials/neighborhoods', 'news' );
	get_template_part( 'partials/neighborhoods', 'videos' );
	get_template_part( 'partials/neighborhoods', 'commentary' );
	get_template_part( 'partials/neighborhoods', 'map' );
	get_template_part( 'partials/neighborhoods', '101' );
	get_template_part( 'partials/neighborhoods', 'documents' );
	get_template_part( 'partials/neighborhoods', 'ctas' );
	get_footer();
