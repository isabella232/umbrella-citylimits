jQuery(document).ready(function($) {
	var homebannerCounter = 0;
	$('.newsletter-signup .not-expanded').on('mouseover click', function(e) {
		var $expanded = $(this).parent().find('.expanded')
		if (e.originalEvent.type == 'click' || homebannerCounter < 3) {
			$(this).hide();
			$expanded.show();
		}
		$expanded.mouseleave(function(e) {
			if (homebannerCounter <= 3) {
				$(this).hide();
				$(this).parent().find('.not-expanded').show();
			}
		});
		homebannerCounter++;
	});
});

function $c(t) {
	console.log(t);
}

//$(e.target).closest('.readmore').length