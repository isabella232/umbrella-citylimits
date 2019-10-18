<?php
/**
 * Used on the custom homepage layout
 *
 * Expects a variable $featured being a WP_Post
 */

global $shown_ids;
$shown_ids[] = $featured->ID;

?>
<article <?php post_class( '', $featured->ID ); ?> >
	<?php largo_maybe_top_term( array( 'post' => $featured->ID ) ); ?>
	<a href="<?php echo esc_attr( get_permalink( $featured ) ); ?>" class="img align-right"><?php echo get_the_post_thumbnail( $featured, 'thumbnail' ); ?></a>
	<h2><a href="<?php the_permalink( $featured ); ?>"><?php echo get_the_title( $featured ); ?></a></h2>
	<div class="excerpt">
		<?php largo_excerpt( $featured, 4 ); ?>
	</div>
	<h5 class="byline"><?php largo_byline( true, true, $featured ); ?></h5>
</article>

