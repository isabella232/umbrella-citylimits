<?php
/**
 * Page Template: The Future of NYC Neighborhoods
 * Template Name: Rezone Project - Series Home
 * Description: Custom landing page for the ReZone project with the /neighborhoods/ slug
 */

global $shown_ids, $post;

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

<!-- FUSION TABLES API QUERY: https://www.googleapis.com/fusiontables/v2/query?sql=SELECT * FROM 1QfRFD6FGEhH1x4lZ9_3CJr0tyrPu778KRcq8VgqJ&key=AIzaSyCdCx_APtwGdE0m33WCFbKB73kfWaxbCHo -->

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCdCx_APtwGdE0m33WCFbKB73kfWaxbCHo&callback=initMap">
</script>
<script>
	function initMap(data){
		var $ = jQuery;

		//make list of data attributes
		var zones = $('.zone-w-status');
		var data = [];

		for (var i=0; i<zones.length; i++){
			var zone = $(zones[i]);

			var name = zone.find('a').text();
			var status = zone.data('status');
			var latlon = zone.data('latlon').replace(/ /g,'').split(',');
			var lat = parseFloat(latlon[0]);
			var lon = parseFloat(latlon[1]);
			var url = zone.find('a').attr('href');

			var color = '#c3c3c3';
			var color_name = zone.data('color');
			if (color_name == 'yellow') {
				color = '#fac409';
			} else if (color_name == 'red') {
				color = '#D41313';
			} else if (color_name == 'green') {
				color = '#10a139';
			}

			var zone_data = [name, status, color, lat, lon, url];

			data.push(zone_data);
		}

		//console.log(data);

		//NYC
		var mainLatLng = {lat: 40.75086427976074, lng: -73.89803823007811};

		var styledMapType = new google.maps.StyledMapType(

			[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"administrative.country","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.fill","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels","stylers":[{"hue":"#ffe500"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"},{"visibility":"on"}]},{"featureType":"landscape.natural","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.landcover","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry.fill","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry.stroke","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"labels.text.fill","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"labels.text.stroke","stylers":[{"visibility":"on"}]},{"featureType":"landscape.natural.terrain","elementType":"labels.icon","stylers":[{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"poi.attraction","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"poi.place_of_worship","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"poi.school","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45},{"visibility":"on"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.station.airport","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#bfd3d8"},{"visibility":"on"}]}],
			{name: 'Styled Map'});

	    var map = new google.maps.Map(document.getElementById('googft-mapCanvas'), {
	      zoom: 10,
	      center: mainLatLng,
	      scrollwheel: false
	    });

	    map.mapTypes.set('styled_map', styledMapType);
	    map.setMapTypeId('styled_map');

	    var all_markers = [];

	    for (var i=0; i<data.length; i++){
	    	var zone = data[i];
	    	var latlon = {lat: zone[3], lng: zone[4]};

	    	var marker = new google.maps.Marker({
	    		icon: {
					path: google.maps.SymbolPath.CIRCLE,
					scale: 10,
					strokeColor: 'black',
					strokeWeight: 2,
					fillColor: zone[2],
					fillOpacity: 1
				},
				position: latlon,
				map: map,
				url: zone[5],
				name: zone[0]
			});
			all_markers.push( marker );

			google.maps.event.addListener(marker, 'click', function() {
			    window.location.href = this.url;
			});

			var tooltip = $('#map-tooltip');
			var overlay = new google.maps.OverlayView();

			overlay.draw = function() {};
			overlay.setMap(map); 

			google.maps.event.addListener(marker, 'mouseover', function(e) {
				var projection = overlay.getProjection(); 
    			var pixel = projection.fromLatLngToContainerPixel(this.getPosition());

				tooltip.text(this.name);
				tooltip.css({
					'left': pixel.x + 8,
					'top': pixel.y + 8
				})
				tooltip.addClass('active');
			});

			google.maps.event.addListener(marker, 'mouseout', function(e) {
				tooltip.removeClass('active');
			});
	    }

	    //map hovers over neighborhood name trigger map hovers
	    $('.zone-w-status a').hover(function(){
	    	var name = $(this).text();
	    	for(var i=0;i<all_markers.length;i++){
			    if (all_markers[i].name === name){
			        google.maps.event.trigger(all_markers[i],'mouseover');
			        break;
			    }
			}
	    }, function(){
	    	var name = $(this).text();
	    	for(var i=0;i<all_markers.length;i++){
			    if (all_markers[i].name === name){
			        google.maps.event.trigger(all_markers[i],'mouseout');
			        break;
			    }
			}
	    });
	}
</script>
</head>


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
	<h2>The Neighborhoods</h2>
	<div class="row-fluid">
		<div class="span8">
			<p class="instruction">Click on a neighborhood to get news, documents, opinions and videos about that community.</p>
			<div id="map-container">
				<div id="googft-mapCanvas"></div>
				<div id="map-tooltip"></div>
			</div>			
		</div>
		<div class="span4 plan-status">
			<h2>Rezoning Status</h2>
			<?php
			$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhoods', 'hide_empty' => false ) );
			$count = 0;
			?>
			<div class="row-fluid">	
				<?php foreach ( $neighborhoods as $neighborhood ) : ?>			
					<?php
					$status = get_term_meta( $neighborhood->term_id, 'neighborhood-status', true ); 
					switch ( $status ) {
						case 'red':
							$status_label = 'Proposal Anticipated or on Hold';
							break;
						case 'yellow':
							$status_label = 'Proposal is in the Approval Process';
							break;
						case 'green':
							$status_label = 'Proposal Approved';
							break;	
						default:
							$status_label = '';
							break;
					}
					$status_latlon = get_term_meta( $neighborhood->term_id, 'neighborhood-latlon', true ); 
					?>

					<!-- <?php if ( isset( $title ) ) : ?>
						<h1 class="page-title"><?php echo $title; ?></h1>
					<?php endif; ?> -->

					<div class="zone-w-status" data-status="<?php echo $status_label; ?>" data-color="<?php echo $status; ?>" data-latlon="<?php echo $status_latlon; ?>"><h5><a href="<?php echo get_term_link($neighborhood); ?>" title="<?php echo $status_label; ?>"><div class="circle <?php echo $status; ?>"></div><?php echo $neighborhood->name; ?></a></h5></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="rezone-101">
	<?php
	$args = array(
		'order'          => 'DESC',
		'post_type'      => 'page',
		'post__in'       => array(
			// these are the pages on Staging
			891921,
			891920,
			891919
		),
		'ignore_sticky_posts' => true
	);
	$get_children_array = get_posts( $args );  //returns Array ( [$image_ID].
	?>

	<?php if ( count( $get_children_array ) > 0 ) : ?>
		<div class="row-fluid">
			<?php foreach ( $get_children_array as $child ) : ?>
				<?php setup_postdata( get_post( $child ) ); ?>
				<div class="span4">
					<h3><?php echo '<a href="' . get_permalink( $child->ID ) . '" title="' . get_the_title( $child->ID ) . '">' .  get_the_title( $child->ID ) . '</a>'; ?></h3>
					<p><?php echo get_the_excerpt( $child->ID ); ?></p>
					<?php echo '<a href="' . get_permalink( $child->ID ) . '" title="' . get_the_title( $child->ID ) . '" class="read-more">Read more ></a>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>
</section>

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
			<h3>Make Your Voice Heard</h3>
			<?php gravity_form( 24, false, true, false, true );?>
		</div>
	<div class="morelink left"><a href="<?php echo get_term_link( 'commentary', 'post-type' ); ?>" class="btn more">More Commentary</a><a href="https://twitter.com/search?q=%23zonein" class="btn zonein-twitter span8">Follow the #ZoneIn conversation on Twitter</a></div>
	</div>
</section>

<section class="videos">
	<h2>Videos</h2>
	<div class="row-fluid">
		<?php
		$args = array (
			'tax_query' => array(
				array(
					'taxonomy'      => 'category',
					'field'         => 'slug',
					'terms'         => array( 'video' )
				),
				$project_tax_query,
				'relation' => 'AND'
			),
			'posts_per_page' => '3',
			'post__not_in'   => $shown_ids
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
	<div class="morelink"><a href="<?php echo get_term_link( 'videos', 'post-type' ); ?>" class="btn more">More Videos</a></div>
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

<div class="bottom-ctas row-fluid">
	<div class="span3">
		<a href="/get-involved/" class="btn"><span>Get Involved</span></a>
	</div>
	<div class="span3">
		<a href="/share-your-views/" class="btn"><span>Share Your Views</span></a>
	</div>
	<div class="span3">
		<a href="/zonein-events/" class="btn"><span>Events Calendar</span></a>
	</div>
	<div class="span3">
		<a href="https://visitor.r20.constantcontact.com/manage/optin?v=001zxpjLyMMmAo1Y-WQNhg7iyT04D-FOREjm0-ANydGbm8w104RXMOiQFjO6VGBAzXRgotexijmxL7Om3KrcmFJQa9bYLRea0IxMyj1AdQ62z6kf2UgI6bkBnJESDGhczS53WMNhwsTFmaLjpQEEmfrnc8nLycrIsrSHNt87avSEmJbuO7EKGWEvtpptS4qzlrVwaLsxeI8UlSHyoSPcB9--xgihfk8jZON" class="btn"><span>Get the Newsletter</span></a>
	</div>
</div>

<?php get_footer();
