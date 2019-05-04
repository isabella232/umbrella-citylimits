jQuery(document).ready(function($) {
	var homebannerCounter = 0;
	$('.newsletter-signup .not-expanded').on('mouseover click', function(e) {
		homebannerCounter++;
		$c(homebannerCounter);
		$(this).hide();
		var $expanded = $(this).parent().find('.expanded')
		$expanded.show();
		$expanded.mouseout(function() {
			if (homebannerCounter < 3) {
				$(this).hide();
				$(this).parent().find('.not-expanded').show();
			}
		});
	});
});

function $c(t) {
	console.log(t);
}

