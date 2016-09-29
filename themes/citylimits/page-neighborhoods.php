<?php
/**
 * Page Template: The Future of NYC Neighborhoods
 * Template Name: Rezone Project
 * Description: Custom landing page for the ReZone project with the /neighborhoods/ slug
 */

global $shown_ids;

add_filter('body_class', function($classes) {
	$classes[] = 'neighborhoods-lp';
	return $classes;
});

get_header();
?>

<div id="rezone-header">
	<div class="row-fluid">
		<div class="span6">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</div>
		<div class="span6">
			<?php // @TODO ReZone Newsletter Code Here ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<?php // @TODO Rezone Header Img ?>
			<?php // @TODO Rezone Menu ?>
		</div>
	</div>
</div>

<section class="rezone-overview">
	<div class="row-fluid">
		<div class="span12">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</div>
	</div>

	<?php
	$args = array(
	    'posts_per_page' => 3,
	    'order'          => 'DESC',
	    'post_parent'    => $post->ID,
	    'post_type'      => 'page'
	    );

	$get_children_array = get_children( $args,ARRAY_A );  //returns Array ( [$image_ID].
	?>

	<?php if ( count( $get_children_array ) > 0 ) : ?>
		<div class="row-fluid">
			<?php foreach ( $get_children_array as $child ) : ?>
				<?php setup_postdata( get_post( $child['ID'] ) ); ?>
				<div class="span4">
					<h3><?php echo '<a href="' . get_permalink( $child['ID'] ) . '" title="' . get_the_title( $child['ID'] ) . '">' .  get_the_title( $child['ID'] ) . '</a>'; ?></h3>
					<p><?php echo get_the_excerpt( $child['ID'] ); ?></p>
					<?php echo '<a href="' . get_permalink( $child['ID'] ) . '" title="' . get_the_title( $child['ID'] ) . '">Read More ></a>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php rewind_posts(); ?>
</section>

<section>
	<div class="row-fluid">
		<div class="span8">
			<h3>Proposed Rezoning</h3>
			<?php // @TODO Map ?>
		</div>
		<div class="span4">
			<?php // @TODO Mayor de Blasio's Plan ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<h3>Rezone Plan Status</h3>

		</div>
	</div>
</section>

<section>
	<h3>Latest News</h3>
	<div class="row-fluid">
		<div class="span8">

		</div>
		<div class="span4">

		</div>
	</div>
</section>

<section>
	<h3>Videos</h3>
	<div class="row-fluid">
		<div class="span4">

		</div>
	</div>
</section>

<div class="bottom-ctas">
	<div class="span3">
		<h5 class="btn">Get Involved</h5>
	</div>
	<div class="span3">
		<h5 class="btn">Share Your Views</h5>
	</div>
	<div class="span3">
		<h5 class="btn">Events Calendar</h5>
	</div>
	<div class="span3">
		<h5 class="btn">Get the Newsletter</h5>
		<a href="#" class="btn more">More News</a>
	</div>
</div>

<section class="commentary">
	<div class="span4">
		<?php // @TODO Commentary posts ?>
		<a href="#" class="btn more">More Commentary</a>
	</div>
	<div class="span8">
		<?php // @TODO Make Your Voice Heard form ?>
	</div>
</section>

<section class="documents">
	<div class="span4">

	</div>
	<div class="span4">

	</div>
	<div class="span4">

		<a href="#" class="btn more">More Documents</a>
	</div>
</sections>

<?php get_footer();
