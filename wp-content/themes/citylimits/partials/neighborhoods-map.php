<?php
// This map depends on the header js in page-neighborhoods.php
// FUSION TABLES API QUERY: https://www.googleapis.com/fusiontables/v2/query?sql=SELECT * FROM 1QfRFD6FGEhH1x4lZ9_3CJr0tyrPu778KRcq8VgqJ&key=AIzaSyCdCx_APtwGdE0m33WCFbKB73kfWaxbCHo

?>
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
			} else if (color_name == 'blue') {
				color = '#093ffa';
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

		for ( var i=0; i < data.length; i++ ){
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

<section class="map">
	<h2>The Neighborhoods</h2>
	<div class="row-fluid">
		<div class="span8">
			<p class="instruction">Click on a neighborhood to get news, documents, opinions and videos about that community.</p>
			<div id="map-container">
				<div id="googft-mapCanvas"></div>
				<div id="map-tooltip"></div>
				<div id="map-key">
					<h3>Key</h3>
					<div class="circle blue"><span>Proposal Anticipated</span></div>
					<div class="circle yellow"><span>Proposal in Approval Process</span></div>
					<div class="circle green"><span>Proposal Approved</span></div>
					<div class="circle red"><span>Proposal Defeated or Withdrawn</span></div>
				</div>
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
							$status_label = 'Proposal Defeated or Withdrawn';
							break;
						case 'yellow':
							$status_label = 'Proposal in Approval Process';
							break;
						case 'green':
							$status_label = 'Proposal Approved';
							break;
						case 'blue':
							$status_label = 'Proposal Anticipated';
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
