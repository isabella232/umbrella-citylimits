jQuery(document).ready(function($) {
	var homebannerCounter = 0, signupH, signupOpenH;
	if ($('.newsletter-signup.maincolumn .not-expanded').length) {
		signupH = $('.newsletter-signup .not-expanded').height() + 'px';
		signupOpenH = $('.newsletter-signup .expanded').height() + 'px';
		$('.newsletter-signup.maincolumn').css({'height': signupOpenH, 'max-height': signupH});

		$('.newsletter-signup .not-expanded').on('mouseover click', function(e) {
			var $expanded = $(this).parent().find('.expanded');
			if (e.originalEvent.type == 'click' || homebannerCounter < 3) {
				$(this).parent().addClass('open');
				$(this).parent().css({'max-height': signupOpenH});
				$expanded.fadeIn(300);
			}
			$expanded.mouseleave(function(e) {
				if (homebannerCounter <= 3) {
					$(this).parent().removeClass('open');
					$(this).parent().css({'max-height': signupH});
					$(this).fadeOut(300);
				}
			});
			homebannerCounter++;
		});
	}
	
	var footerShown = false;
	$(window).scroll(function() {
		if (!footerShown && $(window).scrollTop() + $(window).height() >= $('#site-footer').position().top) {
			footerShown = true;
			setTimeout(function() {
				$('.newsletter-signup.footer').css({'max-height': '1000px'});
			}, 1000);
		}
	});
});

function $c(t) {
	console.log(t);
}
