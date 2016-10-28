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

			$project_tax_query = array(
				'taxonomy'  => 'neighborhoods',
				'field'     => 'slug',
				'terms'     => $title
			);

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
						<h1 class="page-title">
							<?php echo $title; ?>		
						</h1>
					<?php endif; ?>

					<?php largo_post_social_links(); ?>

					<div class="zone-w-status"><div class="circle <?php echo $status; ?>"></div><?php echo ucfirst( $status ); ?></div>

					<?php if ( isset( $description ) ) : ?>
						<div class="archive-description"><?php echo $description; ?></div>
					<?php endif; ?>

					<section class="map">
						<?php echo wp_get_attachment_image( $featured['attachment'], 'full' ); ?>
					</section>

				</header>

				

				<?php
				$args = array (
					'tax_query' => array(
						array(
							'taxonomy' => 'post-type',
							'field'    => 'slug',
							'terms'    => array( 'photos' ),
							'operator' => 'IN',
						),
						$project_tax_query,
						'relation' => 'AND'
					),
					'posts_per_page' => '3',
					'post__not_in' 	 => $shown_ids

				);
				$photos = new WP_Query( $args );
				?>
				<?php if ( $photos->have_posts() ) : ?>
					<section class="photos">
						<h2>Photos</h2>
						<?php while ( $photos->have_posts() ) : $photos->the_post(); $shown_ids[] = get_the_id(); ?>
							<!-- <div class="span4">
								<a href=" <?php echo the_post_thumbnail_url( 'full' ); ?> " target="_blank"><?php the_post_thumbnail( 'medium' ); ?></a>
							</div> -->

							<?php largo_featured_gallery_hero(); ?>
						<?php endwhile; ?>
						<div class="row-fluid">
						</div>
					</section>
				<?php endif; ?>

				<?php
				$args = array (
					'tax_query' => array(
						array(
							'taxonomy' => 'post-type',
							'field'    => 'slug',
							'terms'    => array( 'news' ),
							'operator' => 'IN',
						),
						$project_tax_query,
						'relation' => 'AND'
					),
					'posts_per_page' => '4',
					'post__not_in' 	 => $shown_ids

				);
				$news = new WP_Query( $args );
				?>

				<?php if ( $news->have_posts() ) : ?>
					<section class="news">
						<h2>News</h2>
						<?php while ( $news->have_posts() ) : $news->the_post(); $shown_ids[] = get_the_id(); ?>
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
						<?php endwhile; ?>
						<div class="morelink"><a href="<?php echo get_term_link( 'news', 'post-type' ); ?>" class="btn more">More News</a></div>
					</section>
				<?php endif; ?>

			</div><!-- end content -->
			<div class="span4">
				<?php dynamic_sidebar( 'rezone-neighborhoods-sidebar' ); ?>
			</div>

			<div class="bottom-ctas row-fluid">
				<div class="span3">
					<a href="/get-involved/" class="btn"><span>Get Involved</span></a>
				</div>
				<div class="span3">
					<a href="/share-your-views/" class="btn"><span>Share Your Views</span></a>
				</div>
				<div class="span3">
					<a href="/post-type/events/" class="btn"><span>Events Calendar</span></a>
				</div>
				<div class="span3">
					<a href="https://visitor.r20.constantcontact.com/manage/optin?v=001zxpjLyMMmAo1Y-WQNhg7iyT04D-FOREjm0-ANydGbm8w104RXMOiQFjO6VGBAzXRgotexijmxL7Om3KrcmFJQa9bYLRea0IxMyj1AdQ62z6kf2UgI6bkBnJESDGhczS53WMNhwsTFmaLjpQEEmfrnc8nLycrIsrSHNt87avSEmJbuO7EKGWEvtpptS4qzlrVwaLsxeI8UlSHyoSPcB9--xgihfk8jZON" class="btn"><span>Get the Newsletter</span></a>
				</div>
			</div>

		</div>
		<?php } else {
			get_template_part( 'partials/content', 'not-found' );
		}
	?>
</div>

<script>
	jQuery(document).ready(function($){
		var slides = $('section.photos .featured-gallery img');

		//get first three slides in photos post
		for (var i=0; i<3; i++) {
			var $slide = $(slides[i]);
			
			if ($slide.length > 0) {
				var img = $slide.data('lazy');
				$('section.photos .row-fluid').append('<div class="span4"><img src="' + img + '" />');
			}
		}

		var $displayed = $('section.photos .span4 img');

		for (var i = 0, len = $displayed.length; i < len; i++){

		    (function(index){
				$($displayed[i]).click(function(){
					var img_num = index;
					var $slider = $('.navis-slideshow');

					expandSlider($slider);

					$($('.slick-dots button')[img_num]).click();
				});
			})(i);

		}

		// below functions from: https://github.com/INN/Largo/blob/24146c45839121bc3f8520f1c73cc88ffadd74c8/lib/navis-slideshows/js/navis-slideshows.js
		// closeFull() was modified

		function addCloseX($slider){
	        $slider.prepend('<span class="navis-before">X</span>');
	        $('.navis-before').click(function(){
	            closeFull($slider);
	        });
	    }

	    function expandSlider($slider){
	        var full = 'navis-full';

	        if (!$slider.hasClass(full)){
	            $slider.addClass(full);
	            $slider.slick('setPosition');

	            addCloseX($slider);
	        }
	    };

	    function closeFull($slider){
	        $('.navis-before').remove();
	        $('.navis-full').removeClass('navis-full');
	    }

	    $(document).keyup(function(e) {
	        var $slider = $('.navis-full');

	        if (e.keyCode == 27) { // escape
	            if ($slider.length > 0) {
	                closeFull($slider);
	            }
	        }
   		});

	});


</script>

<?php get_template_part( 'partials/rezone-footer' ); ?>

<?php get_footer();
