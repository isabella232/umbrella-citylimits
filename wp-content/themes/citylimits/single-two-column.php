<?php
/**
 * Single Post Template: Two Column (Classic Layout)
 * Template Name: Two Column (Classic Layout)
 * Description: Shows the post and sidebar if specified.
 *
 * This template merges in portions of largo's partials/content-single-classic.php
 * and partials/content-page.php because the desired layout puts things in places on the page
 * that are not accomplishable without doing so:
 * - putting the header above the span4/span8 split
 */

global $shown_ids;

add_filter('body_class', function($classes) {
	if ( is_page() ) {
		$classes[] = 'hnews';
		$classes[] = 'item';
	}

	$classes[] = 'classic row-fluid span12';
	return $classes;
});

get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/Article">
	<?php if ( is_page() ) { do_action('largo_before_page_header'); } ?>
	<header>

		<hgroup>
			<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>
			<?php
				if ( $subtitle = get_post_meta( $post->ID, 'subtitle', true ) ) {
					echo '<h2 class="subtitle">' . $subtitle . '</h2>';
				}
			?>
		</hgroup>

		<?php
			if ( ! is_page() ) {
				?>
					<h5 class="byline">
						<span class="label"><?php
							/* @todo make this count authors */
							esc_html_e( 'Author', 'citylimits' );
						?></span>
						<?php largo_byline( true, true, get_the_ID() ); ?>
					</h5>
				<?php

				echo sprintf(
					'<span class="date"><span class="label">%1$s</span><time class="entry-date updated dtstamp pubdate" datetime="%2$s">%3$s</time></span>',
					esc_html( 'Date', 'citylimits' ),
					esc_attr( get_the_date( 'c', get_the_ID() ) ),
					largo_time( false, get_the_ID() )
				);

				if ( !of_get_option( 'single_social_icons' ) == false ) {
					largo_post_social_links();
				}
			}
		?>


		<?php largo_post_metadata( $post->ID ); ?>

	</header><!-- / entry header -->

	<?php if ( is_single() ) { do_action('largo_after_post_header'); } ?>
	<?php if ( is_page() ) { do_action('largo_after_page_header'); } ?>

	<div id="content" class="span8" role="main">
		<?php
			while ( have_posts() ) : the_post();

				$shown_ids[] = get_the_ID();

				$partial = ( is_page() ) ? 'page' : 'single-classic';


				if ( $partial == 'single-classic' ) {
					do_action( 'largo_after_post_header' );

					largo_hero( null,'' );

					do_action( 'largo_after_hero' );

					?>
						<div class="entry-content clearfix" itemprop="articleBody">
							<?php largo_entry_content( $post ); ?>
						</div><!-- .entry-content -->

						<?php do_action( 'largo_after_post_content' ); ?>

						<footer class="post-meta bottom-meta">
						</footer><!-- /.post-meta -->
					<?php

					do_action( 'largo_after_post_footer' );

					do_action( 'largo_before_post_bottom_widget_area' );

					do_action( 'largo_post_bottom_widget_area' );

					do_action( 'largo_after_post_bottom_widget_area' );

					do_action( 'largo_before_comments' );

					comments_template( '', true );

					do_action( 'largo_after_comments' );
				} else {
					?>
						<section class="entry-content">
							<?php
								do_action('largo_before_page_content');
								the_content();
								do_action('largo_after_page_content');
							?>
						</section><!-- .entry-content -->
					<?php
				}

			endwhile;
		?>
	</div>

	<?php do_action('largo_after_content'); ?>

	<?php get_sidebar(); ?>
</article><!-- #post-<?php the_ID(); ?> -->

<?php get_footer();
