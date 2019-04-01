<section class="documents">
	<h2>Documents</h2>
	<div class="row-fluid">
		<?php
		$args = array (
			'tax_query' => array(
				array(
					'taxonomy'  => 'post-type',
					'field'     => 'slug',
					'terms'     => array( 'documents' )
				)
			),
			'posts_per_page' => '9',
			'post__not_in' 	 => $shown_ids

		);
		$documents = new WP_Query( $args );
		?>
		<?php if ( $documents->have_posts() ) : ?>
			<?php $count = 0; ?>
			<?php while ( $documents->have_posts() ) : $documents->the_post(); $shown_ids[] = get_the_id(); ?>
				<?php if ( 0 == $count%3 ) : ?>
					<div class="span4">
				<?php endif; ?>
					<div class="doc">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					</div>
				<?php if ( 2 == $count%3 ) : ?>
					</div>
				<?php endif; ?>
				<?php $count++; ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
	<div class="morelink"><a href="<?php echo get_term_link( 'documents', 'post-type' ); ?>" class="btn more">More Documents</a></div>
</section>

