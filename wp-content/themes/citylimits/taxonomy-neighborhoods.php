<?php
/**
 * Template for various non-category archive pages (tag, term, date, etc.)
 *
 * @package Largo
 * @since 0.1
 * @filter largo_partial_by_post_type
 */
get_header( 'rezone' );
$queried_object = get_queried_object();
?>

<div class="clearfix">

	<?php
		if ( have_posts() || largo_have_featured_posts() ) {

			// queue up the first post so we know what type of archive page we're dealing with
			the_post();

			/*
			 * Display some different stuff in the header
			 * depending on what type of archive page we're looking at
			 */

			$title = single_term_title( '', false );
			$description = term_description();

			// rss links for custom taxonomies are a little tricky
			$term_id = intval( $queried_object->term_id );
			$tax = $queried_object->taxonomy;
			$rss_link = get_term_feed_link( $term_id, $tax );

			$post_id = largo_get_term_meta_post( $tax, $term_id );
			$featured = largo_get_featured_media( $post_id );
		?>


		<div class="row-fluid clearfix">
			<div class="stories span8" role="main" id="content">
				<header class="archive-background clearfix">
					<?php
						$post_id = largo_get_term_meta_post( $queried_object->taxonomy, $queried_object->term_id );
						$status = get_term_meta( $term_id, 'neighborhood-status', true );
					?>

					<?php if ( isset( $title ) ) : ?>
						<h1 class="page-title"><?php echo $title; ?></h1>
					<?php endif; ?>

					<div class="zone-w-status"><div class="circle <?php echo $status; ?>"></div><?php echo ucfirst( $status ); ?></div>

					<?php if ( isset( $description ) ) : ?>
						<div class="archive-description"><?php echo $description; ?></div>
					<?php endif; ?>

				</header>

				<section class="map">
					<h2>Proposed Action Area</h2>
					<?php echo wp_get_attachment_image( $featured['attachment'], 'full' ); ?>
				</section>

				<section class="photos">
					<h2>Photos</h2>
					<?php // @TODO add photos ?>
				</section>

				<section class="news">
					<h2>News</h2>
						<?php
						// and finally wind the posts back so we can go through the loop as usual
						rewind_posts();
						$counter = 1;
						while ( have_posts() ) : the_post();
						?>
							<div class="row-fluid">
								<div class="span3">
									<?php the_post_thumbnail( 'medium' ); ?>
								</div>
								<div class="span9">
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
									<?php the_excerpt(); ?>
									<a href="<?php the_permalink(); ?>" class="read-more">Read more ></a>
								</div>
							</div>
							<?php
							$post_type = get_post_type();
							$partial = largo_get_partial_by_post_type( 'archive', $post_type, 'archive' );
							$counter++;
						endwhile;
// @TODO limit to 4 items, then more link. Where does more link go if this is the archive page?
					?>
					<div class="zonein-more"><a href="<?php // @TODO ?>" class="btn more">More News</a></div>
				</section>

			</div><!-- end content -->
			<div class="span4">
				<div class="form">
					<h3>Make Your Voice Heard</h3>
					<p>Submit a question, commend, or idea for the ReZone project.</p>
					<?php // @TODO Make Your Voice Heard form ?>
				</div>

				<section class="commentary">
					<h2>Commentary</h2>
					<div class="row-fluid">
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
						<div class="zonein-more left"><a href="<?php // @TODO ?>" class="btn more">More Zone Commentary</a></div>
					</div>
				</section>

				<div class="sidebar-ctas row-fluid">
					<a class="btn">Get Involved</a>
				</div>

				<section class="events">
					<h2>Upcoming Events</h2>
					<?php // @TODO query ?>
				</section>

				<section class="videos">
					<h2>Videos</h2>
					<div class="row-fluid">
						<?php
						$args = array (
							'tax_query' => array(
								array(
									'taxonomy' 	=> 'category',
									'field' 	=> 'slug',
									'terms' 	=> array( 'video' )
								),
								$project_tax_query,
								'relation' => 'AND'
							),
							'posts_per_page' => '3',
							'post__not_in' 	 => $shown_ids

						);
						$videos = new WP_Query( $args );
						?>
						<?php if ( $videos->have_posts() ) : ?>
							<?php $count = 0; ?>
							<?php while ( $videos->have_posts() ) : $videos->the_post(); $shown_ids[] = get_the_id(); ?>
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
								<?php $count++; ?>
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
					<div class="zonein-more"><a href="<?php // @TODO ?>" class="btn more">More Zone Videos</a></div>
				</section>

				<section class="documents">
					<h2>Documents</h2>
					<div class="row-fluid">
						<?php
						$args = array (
							'tax_query' => array(
								array(
									'taxonomy'  => 'post-type',
									'field'     => 'slug',
									'terms'     => array( 'documents' ) // @TODO - change to appropriate tag
								)
							),
							'posts_per_page' => '9',
							'post__not_in' 	 => $shown_ids

						);
						$documents = new WP_Query( $args );
						?>
						<?php if ( $documents->have_posts() ) : ?>
							<?php while ( $documents->have_posts() ) : $documents->the_post(); $shown_ids[] = get_the_id(); ?>
								<div class="doc">
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
					<div class="zonein-more"><a href="<?php // @TODO ?>" class="btn more">More Zone Documents</a></div>
				</section>

			</div>

			<div class="bottom-ctas row-fluid">
				<div class="span3">
					<a class="btn"><span>Get Involved</span></a>
				</div>
				<div class="span3">
					<a class="btn"><span>Share Your Views</span></a>
				</div>
				<div class="span3">
					<a class="btn"><span>Events Calendar</span></a>
				</div>
				<div class="span3">
					<a class="btn"><span>Get the Newsletter</span></a>
				</div>
			</div>

		</div>
		<?php } else {
			get_template_part( 'partials/content', 'not-found' );
		}
	?>
</div>

<?php get_template_part( 'partials/rezone-footer' ); ?>

<?php get_footer();
