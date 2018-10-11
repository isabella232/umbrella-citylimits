<?php
/**
 * Page Template: CommunityWire
 * Template Name: CommunityWire
 * Description: Custom landing page
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
// get_header( 'rezone' );

?>


<section class="rezone-overview">
	<div class="row-fluid">
		<?php dynamic_sidebar( 'communitywire-listings' ); ?>
	</div>
</section>


<!-- <?php get_template_part( 'partials/rezone-footer' ); ?> -->

<?php get_footer();
