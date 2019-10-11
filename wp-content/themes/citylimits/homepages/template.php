<?php
	global $shown_ids;

	$topstory = largo_home_single_top();
	$featured_stories = largo_home_featured_stories( 3 );
	$shown_ids[] = $topstory->ID;
?>

<div id="homepage-featured">
	<?php
		largo_render_template( 'partials/home', 'top', array( 'topstory' => $topstory ) );
	?>
	<div id="featured">
		<?php
			/**
			 * Grab three featured posts, output two, check if there's a widget area, output third or widget
			 */
			$counter = 0;
			foreach ( $featured_stories as $featured ) {
				$counter++;
				if ( $counter != 3 ) {
					largo_render_template( 'partials/home', 'featured', array( 'featured' => $featured ) );
				} else {
					if ( ! dynamic_sidebar( 'homepage-featured' ) ) {
						largo_render_template( 'partials/home', 'featured', array( 'featured' => $featured ) );
					}
				}
			}
		?>
	</div>
</div>

<div id="widget-area" class="clearfix">
	<?php
		dynamic_sidebar( 'Homepage Bottom' );
	?>
</div>
