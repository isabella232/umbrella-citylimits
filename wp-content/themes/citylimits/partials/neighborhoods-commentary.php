<section class="commentary">
	<h2>Opinions</h2>
	<div class="row-fluid">
		<div class="span4">
			<?php
			$args = array (
				'tax_query' => array(
					array(
						'taxonomy'  => 'post-type',
						'field'     => 'slug',
						'terms'     => array( 'commentary' )
					),
					$project_tax_query,
					'relation' => 'AND'
				),
				'posts_per_page' => '3',
				'post__not_in' 	 => $shown_ids

			);
			$commentary = new WP_Query( $args );
			?>
			<?php if ( $commentary->have_posts() ) : ?>
				<?php $count = 0; ?>
				<?php while ( $commentary->have_posts() ) : $commentary->the_post(); $shown_ids[] = get_the_id(); ?>
					<div class="story">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<div class="span8 form">
			<h3>Make Your Voice Heard</h3>
			<?php
				if ( function_exists( 'gravity_form' ) ) {
					gravity_form( 23, false, true, false, true );
				}
			?>
		</div>
	<div class="morelink left"><a href="<?php echo get_term_link( 'commentary', 'post-type' ); ?>" class="btn more">More Opinions</a><a href="https://twitter.com/search?q=%23zoneinnyc&src=typd" class="btn zonein-twitter span8">Follow the #ZoneInNYC conversation on Twitter</a></div>
	</div>
</section>

