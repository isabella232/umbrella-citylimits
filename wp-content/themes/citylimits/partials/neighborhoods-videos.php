<section class="videos">
	<h2>Videos</h2>
	<div class="row-fluid">
		<?php
		$args = array (
			'tax_query' => array(
				array(
					'taxonomy'      => 'category',
					'field'         => 'slug',
					'terms'         => array( 'video' )
				),
				$project_tax_query,
				'relation' => 'AND'
			),
			'posts_per_page' => '3',
			'post__not_in'   => $shown_ids
		);
		$videos = new WP_Query( $args );
		?>
		<?php if ( $videos->have_posts() ) : ?>
			<?php $count = 0; ?>
			<?php while ( $videos->have_posts() ) : $videos->the_post(); $shown_ids[] = get_the_id(); ?>
				<div class="span4">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
				</div>
				<?php $count++; ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
	<div class="morelink"><a href="<?php echo get_term_link( 'videos', 'post-type' ); ?>" class="btn more">More Videos</a></div>
</section>

