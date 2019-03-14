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

<?php
	get_template_part( 'partials/neighborhoods', 'overview' );
	get_template_part( 'partials/neighborhoods', 'news' );
	get_template_part( 'partials/neighborhoods', 'map' );
	get_template_part( 'partials/neighborhoods', '101' );
	get_template_part( 'partials/neighborhoods', 'commentary' );
	get_template_part( 'partials/neighborhoods', 'videos' );
	get_template_part( 'partials/neighborhoods', 'documents' );
	get_template_part( 'partials/neighborhoods', 'ctas' );
	get_footer();
