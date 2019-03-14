<section class="news">
	<h2>Latest News</h2>
	<div class="row-fluid">
		<?php
		$args = array (
			'tax_query' => array(
				array(
					'taxonomy' 	=> 'category',
					'field' 	=> 'slug',
					'terms' 	=> array( 'news' )
				),
				$project_tax_query,
				'relation' => 'AND'
			),
			'posts_per_page' => '4',
			'post__not_in' 	 => $shown_ids,
		);
		$recent_posts = new WP_Query( $args );
		if ( $recent_posts->have_posts() ) :
			$count = 0;
			while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); $shown_ids[] = get_the_id();
			?>
				<?php if ( 0 == $count ) : ?>
					<div class="news-feature span8">
						<div class="span6">
							<?php the_post_thumbnail( 'full' ); ?>
						</div>
						<div class="span6">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
							<?php the_excerpt(); ?>
							<a href="<?php the_permalink(); ?>" class="read-more">Read more ></a>
						</div>
					</div>
				<?php elseif ( 1 == $count ) : ?>
					<div class="span4">
						<div <?php post_class( 'story' ); ?> >
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
						</div>
				<?php elseif ( 3 == $count ) : ?>
						<div <?php post_class( 'story' ); ?> >
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
						</div>
					</div>
				<?php else : ?>
						<div <?php post_class( 'story' ); ?> >
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
						</div>
				<?php endif; ?>
			<?php $count++; ?>
			<?php endwhile; ?>
		<?php endif; // end more featured posts ?>
	</div>
	<div class="morelink"><a href="<?php echo get_term_link( 'news', 'post-type' ); ?>" class="btn more">More News</a></div>
</section>

