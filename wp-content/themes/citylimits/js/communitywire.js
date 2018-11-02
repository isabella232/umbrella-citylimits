jQuery(document).ready(function($) {

	$('.type-tribe_events').parent().append('<h5>Filter events by:</h5><ul class="communitywire-filters"></ul>');

	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/class-training/">Class/Training</a></li>');
	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/exhibit/">Exhibit</a></li>');
	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/meeting-hearing/">Meeting/Hearing</a></li>');
	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/performance/">Performance</a></li>');
	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/speech-rally-protest/">Speech/Rally/Protest</a></li>');
	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/tour/">Tour</a></li>');
	$('.communitywire-filters').append('<li><a href="/events/category/communitywire-events/tour/">Other</a></li>');

});