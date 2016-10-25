<?php
/**
 * Page Template: The Future of NYC Neighborhoods
 * Template Name: Rezone Project - Subpage
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


<section class="rezone-overview">
	<div class="row-fluid">
		<div class="span8">
			<?php while ( have_posts() ) : the_post(); ?>
				<h1><?php the_title(); ?></h1>
				<?php
					do_action('largo_after_post_header');

					largo_hero(null,'span12');

					do_action('largo_after_hero');
				?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</div>
		<div class="span4">
			<?php dynamic_sidebar( 'rezone-subpage-sidebar' ); ?>
		</div>
	</div>
</section>


<?php get_template_part( 'partials/rezone-footer' ); ?>

<?php get_footer();
