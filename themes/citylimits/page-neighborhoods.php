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

<div class="rezone-header">
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
			<img src="/wp-content/themes/citylimits/img/rezone-logo.jpg" alt="Rezone Project Logo" width="100%" />
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
</section>

<section class="rezone-101">
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

<section class="map">
	<h3>Proposed Rezoning</h3>
	<div class="row-fluid">
		<div class="span8">
			<?php
			/**
			 *  @TODO Add Map
			 *  Google Map Wizard has some styles that will work really well here - https://mapstyle.withgoogle.com
			 *  We'll need an API key for this
			 */
			?>
			<div style="width:100%;background:#ccc;text-align:center;padding:12em 0;">Map</div>
		</div>
		<div class="span4">
			<?php // @TODO How do we want to pull in Mayor de Blasio's Plan? Used the quick/dirty post_id method here for now ?>
			<h5><a href="<?php echo get_permalink( '891922' ); ?>" title="<?php echo get_the_title( '891922' ); ?>"><?php echo get_the_title( '891922' ); ?></a></h5>
			<?php
			$deblasios_plan = get_post( '891922' ); 
			$content = apply_filters( 'the_content', wpautop( $deblasios_plan->post_content ) ); 
			echo '<p>' . wp_trim_words( $content, 75 ) . '</p>';
			echo '<p><a href="' . get_permalink( '891922' ) . '" title="Read More of ' . get_the_title( '891922' ) . '">Read More ></a></p>';

			/**
			 * @TODO Review
			 * wp_trim_words() removes whitespace added by wpautop(). 
			 * If we want to trim to word length, but still have paragraph breaks, we'll need an alternate method.
			 */
			?>
		</div>
	</div>
	<div class="plan-status">
		<h3>Rezone Plan Status</h3>
		<?php
		$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhoods', 'hide_empty' => false ) );
		$count = 0;
		?>
		<?php foreach ( $neighborhoods as $neighborhood ) : ?> 
			<?php if ( 0 == $count%4 ) : ?> 
				<div class="row-fluid">
			<?php endif; ?>
				<div class="span3">
					<h5><?php echo $neighborhood->name; ?><div class="circle green"></div></h5>
				</div>
			<?php if ( 3 == $count%4 ) : ?> 
				</div>
			<?php endif; ?>
			<?php $count++; ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="news">
	<h3>Latest News</h3>
	<div class="row-fluid">
		<div class="span8">

		</div>
		<div class="span4">

			<a href="#" class="btn more">More News</a>
		</div>
	</div>
</section>

<section class="videos">
	<h3>Videos</h3>
	<div class="row-fluid">
		<div class="span4">

		</div>
	</div>
</section>

<div class="bottom-ctas row-fluid">
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
	</div>
</div>

<section class="commentary">
	<h3>Commentary</h3>
	<div class="row-fluid">
		<div class="span4">
			<?php // @TODO Commentary posts ?>
			<a href="#" class="btn more">More Commentary</a>
		</div>
		<div class="span8">
			<?php // @TODO Make Your Voice Heard form ?>
		</div>
	</div>
</section>

<section class="documents">
	<h3>Documents</h3>
	<div class="row-fluid">
		<div class="span4">

		</div>
		<div class="span4">

		</div>
		<div class="span4">

			<a href="#" class="btn more">More Documents</a>
		</div>
	</div>
</sections>

<?php get_footer();
