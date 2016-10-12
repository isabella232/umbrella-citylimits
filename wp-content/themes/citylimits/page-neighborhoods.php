<?php
/**
 * Page Template: The Future of NYC Neighborhoods
 * Template Name: Rezone Project
 * Description: Custom landing page for the ReZone project with the /neighborhoods/ slug
 */

global $shown_ids;

add_filter('body_class', function($classes) {
	$classes[] = 'neighborhoods-lp';
	return $classes;
});

get_header();
?>

<div class="rezone-header">
	<div class="row-fluid">
		<div class="span6">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</div>
		<div class="span6">
			<?php // @TODO ReZone Newsletter Code Here ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<img src="/wp-content/themes/citylimits/img/zonein-logo.jpg" alt="ZoneIn Project Logo" width="100%" />
			<?php // @TODO Rezone Menu ?>
			<?php wp_nav_menu( array( 'theme_location' => 'zonein-menu' ) ); ?>
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
		<div class="span12">
			<?php
			/**
			 *  @TODO Add Map
			 *  Google Map Wizard has some styles that will work really well here - https://mapstyle.withgoogle.com
			 *  We'll need an API key for this
			 */
			?>
			<div style="width:100%;background:#ccc;text-align:center;padding:12em 0;">Map</div>
		</div>
	</div>
	<div class="plan-status">
		<h2>Rezone Plan Status</h2>
		<?php
		$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhoods', 'hide_empty' => false ) );
		$count = 0;
		?>
		<?php foreach ( $neighborhoods as $neighborhood ) : ?>
			<?php if ( 0 == $count%4 ) : ?>
				<div class="row-fluid">
			<?php endif; ?>
				<div class="span3">
					<h5><?php echo $neighborhood->name; ?><div class="circle green"></div></h5>
				</div>
			<?php if ( 3 == $count%4 ) : ?>
				</div>
			<?php endif; ?>
			<?php $count++; ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="rezone-101">
	<?php
	$args = array(
	    'posts_per_page' => 3,
	    'order'          => 'DESC',
	    'post_parent'    => $post->ID,
	    'post_type'      => 'page'
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
					<?php echo '<a href="' . get_permalink( $child['ID'] ) . '" title="' . get_the_title( $child['ID'] ) . '">Read More ></a>'; ?>
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
			'post__not_in' 	 => $shown_ids
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
							<p><?php the_excerpt(); ?></p>
							<a href="<?php the_permalink(); ?>">Read More ></a>
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
						<a href="<?php // @TODO ?>" class="btn more">More News</a>
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
				)
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
					<?php the_post_thumbnail( 'full' ); ?>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
					<?php if ( 2 == $count ) : ?>
						<a href="<?php // @TODO ?>" class="btn more">More News</a>
					<?php endif; ?>
				</div>
				<?php $count++; ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>

<div class="bottom-ctas row-fluid">
	<div class="span3">
		<h5 class="btn">Get Involved</h5>
	</div>
	<div class="span3">
		<h5 class="btn">Share Your Views</h5>
	</div>
	<div class="span3">
		<h5 class="btn">Events Calendar</h5>
	</div>
	<div class="span3">
		<h5 class="btn">Get the Newsletter</h5>
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
						'taxonomy' 	=> 'category',
						'field' 	=> 'slug',
						'terms' 	=> array( 'video' ) // @TODO - change to appropriate tag
					)
				),
				'posts_per_page' => '3',
				'post__not_in' 	 => $shown_ids

			);
			$commentary = new WP_Query( $args );
			?>
			<?php if ( $commentary->have_posts() ) : ?>
				<?php $count = 0; ?>
				<?php while ( $commentary->have_posts() ) : $commentary->the_post(); $shown_ids[] = get_the_id(); ?>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<h5 class="byline"><?php largo_byline( true, true ); ?></h5>
				<?php endwhile; ?>
			<?php endif; ?>
			<a href="<?php // @TODO ?>" class="btn more">More Commentary</a>
		</div>
		<div class="span8 form">
			<?php // @TODO Make Your Voice Heard form ?>
		</div>
	</div>
</section>

<section class="documents">
	<h2>Documents</h2>
	<div class="row-fluid">
		<?php
		$args = array (
			'tax_query' => array(
				array(
					'taxonomy' 	=> 'category',
					'field' 	=> 'slug',
					'terms' 	=> array( 'video' ) // @TODO - change to appropriate tag
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
					<div class="row-fluid">
				<?php endif; ?>
					<div class="span4">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php if ( 8 == $count ) : ?>
						<a href="<?php // @TODO ?>" class="btn more">More Documents</a>
					<?php endif; ?>
					</div>
				<?php if ( 2 == $count%3 ) : ?>
					</div>
				<?php endif; ?>
				<?php $count++; ?>
			<?php endwhile; ?>
		<?php endif; ?>
		</div>
	</div>
</sections>

<?php get_footer();
