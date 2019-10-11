<?php
/**
 * Used on the custom homepage layout
 *
 * Expects a variable $featured being a WP_Post
 */
?>
<article <?php post_class( '', $featured->ID ); ?> >
	<a href="<?php echo esc_attr( get_permalink( $featured ) ); ?>" class="align-right"><?php echo get_the_post_thumbnail( $featured, 'thumbnail' ); ?></a>
	<?php largo_maybe_top_term( array( 'post' => $featured->ID ) ); ?>
	<h2><a href="<?php the_permalink( $featured ); ?>"><?php echo get_the_title( $featured ); ?></a></h2>
	<?php largo_excerpt( $featured, 4 ); ?>
	<h5 class="byline"><?php largo_byline( true, false, $topstory ); ?></h5>
</article>

