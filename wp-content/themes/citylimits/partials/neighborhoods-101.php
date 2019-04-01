<section class="rezone-101">
	<?php
	$args = array(
		'order'          => 'DESC',
		'post_type'      => 'page',
		'post__in'       => array(
			// these are the pages on Staging
			989818,
			989821,
			989822
		),
		'ignore_sticky_posts' => true
	);
	$get_children_array = get_posts( $args );  //returns Array ( [$image_ID].
	?>

	<?php if ( count( $get_children_array ) > 0 ) : ?>
		<div class="row-fluid">
			<?php foreach ( $get_children_array as $child ) : ?>
				<?php setup_postdata( get_post( $child ) ); ?>
				<div class="span4">
					<h3><?php echo '<a href="' . get_permalink( $child->ID ) . '" title="' . get_the_title( $child->ID ) . '">' .  get_the_title( $child->ID ) . '</a>'; ?></h3>
					<p><?php echo get_the_excerpt( $child->ID ); ?></p>
					<?php echo '<a href="' . get_permalink( $child->ID ) . '" title="' . get_the_title( $child->ID ) . '" class="read-more">Read more ></a>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>
</section>


