<section class="rezone-overview">
	<?php largo_post_social_links(); ?>
	<div class="row-fluid">
		<div class="span12">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<div class="series-header-container">
	<div class="series-banner">
		<?php the_post_thumbnail( 'large' ); ?>
	</div>
	<section id="series-header" class="">
		<span class="special-project"><?php esc_html_e( 'Special Project', 'citylimits' ); ?></span>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php
			if ( $opt['show_series_byline'] ) {
				echo '<h5 class="byline">' . largo_byline( false, false, get_the_ID() ) . '</h5>';
			}
		?>
			<div class="description">
				<?php echo apply_filters( 'the_content', $post->post_excerpt ); ?>
			</div>
	</section>
</div>

