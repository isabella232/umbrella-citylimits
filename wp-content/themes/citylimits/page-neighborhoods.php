<?php
/**
 * Page Template: The Future of NYC Neighborhoods
 * Template Name: Rezone Project
 * Description: Custom landing page for the ReZone project with the /neighborhoods/ slug
 */

global $shown_ids, $post;

// Add the .neighborhoods-lp class
add_filter('body_class', function($classes) {
	$classes[] = 'neighborhoods-lp';
	return $classes;
});

/*
 * Establish some common query parameters
 */
$features = get_the_terms( $post->ID, 'series' );
// we're going to assume that the series landing page is in no more than one series, because that's how you're *supposed* to do it.
$series = $features[0];
$project_tax_query = array(
		'taxonomy' => 'series',
		'terms' => $series->term_id,
		'field' => 'ID',
	);

// begin the page rendering

// This is the rezone-specific header, /header-rezone.php
get_header( 'rezone' );

?>

<div class="rezone-header">
	<div class="row-fluid">
		<div class="span8">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</div>
		<div class="span4">
			<?php // @TODO ReZone Newsletter Code Here ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<a href="/zone-in/"><img src="/wp-content/themes/citylimits/img/zonein-logo.jpg" alt="ZoneIn Project Logo" width="100%" /></a>
			<?php get_template_part( 'partials/nav', 'rezone' ); ?>
		</div>
	</div>
</div>

<section class="rezone-overview">
	<div class="row-fluid">
		<div class="span12">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>

<section class="map">
	<h2>Proposed Rezoning</h2>
	<div class="row-fluid">
		<div class="span8">
			<?php
			/**
			 *  @TODO Add Map
			 *  Google Map Wizard has some styles that will work really well here - https://mapstyle.withgoogle.com
			 *  We'll need an API key for this
			 */
			?>
			<!-- <div id="map" style="width:100%;background:#ccc;text-align:center;padding:12em 0;">Map</div> -->
			<p class="instruction">Select a pin to learn more about proposed rezoning.</p>
			<iframe id="map" width="100%" height="420" scrolling="no" frameborder="no" scollwheel="false" src="https://www.google.com/fusiontables/embedviz?q=select+col0+from+1nVqV-VWkMF3sQfs3XUCsYfqcB6bAUJz2bXQl_-GV&amp;viz=MAP&amp;h=false&amp;lat=40.75&amp;lng=-73.8455445810547&amp;t=1&amp;z=10&amp;l=col0&amp;y=2&amp;tmplt=2&amp;hml=ONE_COL_LAT_LNG"></iframe>
		</div>
		<div class="span4 plan-status">
			<h2>Rezone Plan Status</h2>
			<?php
			$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhoods', 'hide_empty' => false ) );
			$count = 0;
			?>
			<div class="row-fluid">	
				<?php foreach ( $neighborhoods as $neighborhood ) : ?>			
					<div class="zone-w-status"><h5><div class="circle green"></div><?php echo $neighborhood->name; ?></h5></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="rezone-101">
	<?php
	$args = array(
	    'posts_per_page' => 3,
	    'order'          => 'DESC',
	    'post_parent'    => $post->ID,
	    'post_type'      => 'page',
		'tax_query'      => array( $project_tax_query )
	    );
	$get_children_array = get_children( $args,ARRAY_A );  //returns Array ( [$image_ID].
	?>

	<?php if ( count( $get_children_array ) > 0 ) : ?>
		<div class="row-fluid">
			<?php foreach ( $get_children_array as $child ) : ?>
				<?php setup_postdata( get_post( $child['ID'] ) ); ?>
				<div class="span4">
					<h3><?php echo '<a href="' . get_permalink( $child['ID'] ) . '" title="' . get_the_title( $child['ID'] ) . '">' .  get_the_title( $child['ID'] ) . '</a>'; ?></h3>
					<p><?php echo get_the_excerpt( $child['ID'] ); ?></p>
					<?php echo '<a href="' . get_permalink( $child['ID'] ) . '" title="' . get_the_title( $child['ID'] ) . '" class="read-more">Read more ></a>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php rewind_posts(); ?>
</section>

<section class="news">
	<h2>Latest News</h2>
	<div class="row-fluid">
		<?php
		$args = array (
			'posts_per_page' => '3',
			'post__not_in' 	 => $shown_ids,
			'tax_query'      => array( $project_tax_query )
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
	<div class="zonein-more"><a href="<?php // @TODO ?>" class="btn more">More News</a></div>
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
				<div class="span4">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
				</div>
				<?php $count++; ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
	<div class="zonein-more"><a href="<?php // @TODO ?>" class="btn more">More Videos</a></div>
</section>

<div class="bottom-ctas row-fluid">
	<div class="span3">
		<a class="btn">Get Involved</a>
	</div>
	<div class="span3">
		<a class="btn">Share Your Views</a>
	</div>
	<div class="span3">
		<a class="btn">Events Calendar</a>
	</div>
	<div class="span3">
		<a class="btn">Get the Newsletter</a>
	</div>
</div>

<section class="commentary">
	<h2>Commentary</h2>
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
			<?php // @TODO Make Your Voice Heard form ?>
		</div>
	<div class="zonein-more left"><a href="<?php // @TODO ?>" class="btn more">More Commentary</a></div>
	</div>
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
	<div class="zonein-more"><a href="<?php // @TODO ?>" class="btn more">More Documents</a></div>
</section>

<?php get_footer();
