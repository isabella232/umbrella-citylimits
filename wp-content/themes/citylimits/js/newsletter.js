jQuery(document).ready(function($) {
	var homebannerCounter = 0, signupH, signupOpenH, submitted = false;
	if ($('.newsletter-signup.maincolumn .not-expanded').length) {
		signupH = $('.newsletter-signup .not-expanded').outerHeight() + 'px';
		signupOpenH = $('.newsletter-signup .expanded').outerHeight() + 'px';
		$('.newsletter-signup.maincolumn').css({'height': signupOpenH, 'max-height': signupH});

		$('.newsletter-signup .not-expanded').on('mouseover click', function(e) {
			if (submitted) {
				return;
			}
			var $expanded = $(this).parent().find('.expanded');
			if (e.originalEvent.type == 'click' || homebannerCounter < 3) {
				$(this).parent().addClass('open');
				$(this).parent().css({'max-height': signupOpenH});
				$expanded.fadeIn(300);
			}
			$expanded.mouseleave(function(e) {
				//don't hide if they're in the middle of filling it out.
				if ($expanded.find('input[name=newsletter_fname]').val() 
				|| $expanded.find('input[name=newsletter_lname]').val() 
				|| $expanded.find('input[name=newsletter_email]').val() 
				|| $expanded.find("input:checkbox[name='newsletter[]']").is(":checked") ) {
					return
				}
				if (homebannerCounter <= 3 && !submitted) {
					$(this).parent().removeClass('open');
					$(this).parent().css({'max-height': signupH});
					$(this).fadeOut(300);
				}
			});
			homebannerCounter++;
		});


		var footerShown = false;
		//show footer on scroll for wide layouts
		$(window).scroll(function() {
			if (!submitted 
			&& $(window).width() >= 769 
			&& !footerShown 
			&& $(window).scrollTop() + $(window).height() >= $('#site-footer').position().top 
			&& !$('body').hasClass('newsletter-landing')) {
				footerShown = true;
				setTimeout(function() {
					$('.newsletter-signup.footer').css({'background-size': $(window).width() + 'px', 'max-height': '500px'});
					setTimeout(function() {
						//after the CSS transition, set the background-size back to contain
						$('.newsletter-signup.footer').css({'background-size': 'contain'});
					}, 1400);
				}, 1000);
			}
		});
		
		//show footer on timeout for narrow
		if ($(window).width() < 769 
		&& !Cookies.get('newsletter_modal_snooze')
		&& !$('body').hasClass('newsletter-landing')) {
			var footerH = $('.newsletter-signup.footer.mobile .mobile_footer_content').outerHeight();
			setTimeout(function() {
				$('.newsletter-signup.footer.mobile').css({'max-height': (footerH + 183) + 'px', 'background-position': '13% ' + (footerH + 20) + 'px'});
			}, 10000);
		}
				
		$('.newsletter-signup.footer .close_box').click(function(e) {
			$('.newsletter-signup.footer').css({'max-height': 0});
			Cookies.set('newsletter_modal_snooze', true, {expires: 1 * 24 * 60 * 60});//1 day
		});

		//HTML 5 validate checkbox group
		$('.newsletter-signup form input[type=submit]').click(function(e) {
			$this_form = $(this).parents('form');
			$cbx_group = $this_form.find("input:checkbox[name='newsletter[]']");
			$cbx_group.prop('required', true);
			if ($cbx_group.is(":checked")) {
				$cbx_group.prop('required', false);
			}
		})
		
	}
	
	//Newsletter Landing Page
	$('.subscribe_button').click(function() {
		if ($(this).hasClass('selected')) {
			return
		}
		$(this).addClass('selected')
		var id = $(this).attr('data-newsletter-id')
		var title = $(this).parent().find('.newsletter_title').text()
		var entry = $('<li class="newsletter_to_subscribe" data-newsletter-id="' + id + '">' + title + '<div class="remove"></div></li>')
		$('#selected_newsletters').append(entry)
		$('#newsletter_cart').show()
		$(window).resize()
		$('#newsletter_cart input[name=newsletter_fname]').focus()
		
		entry.children('.remove').click(function() {
			var id = $(this).parent().attr('data-newsletter-id')
			$('.subscribe_button[data-newsletter-id=' + id + ']').removeClass('selected')
			$(this).parent().remove()
			if (!$('#selected_newsletters .newsletter_to_subscribe').length) {
				$('#newsletter_cart').hide()
			}
		})
	})
	
	$(window).resize(function() {
		if ($(window).width() < 769) {
			$('#newsletter_cart').css({width: 'auto'});
		} else {
			$('#newsletter_cart').width($('#sidebar').width() - 80)
		}
	})
	

	/*FORM SUBMIT*/
	//this form will do dual-duty for mini-forms on all pages, as well as 'cart' on newsletter landing
	$('.newsletter-signup form, #newsletter_cart form').submit(function(e) {
		e.preventDefault();
		var $this = $(e.target);
		$this.find('input[type=submit]').attr('disabled', true)
		var email = $this.find('input[name=newsletter_email]').val()
		var fname = $this.find('input[name=newsletter_fname]').val()
		var lname = $this.find('input[name=newsletter_lname]').val()

		var newsletters = []
		if ($('body').hasClass('newsletter-landing')) {
			$('.newsletter_to_subscribe').each(function(i, x) {
				newsletters.push($(x).attr('data-newsletter-id'))
			})
		} else {
			$this.find('input[type=checkbox]').filter(':checked').each(function(i, x) {
				newsletters.push($(x).val())
			})
		}
		$.ajax({
			type : "post",
			dataType : "json",
			url : myAjax.ajaxurl,
			data : {action: "cl_mc_signup", fname: fname, lname: lname, email: email, newsletters: newsletters},
			success: function(response) {
				Cookies.set('signed_up_for_newsletter', true, { expires: Infinity });
				if ($('body').hasClass('newsletter-landing')) {
					$('#main').html('<h1>Thanks for signing up!</h1>')
				} else {
					$('.newsletter-signup form, .newsletter-signup .not-expanded').hide()
					$('.newsletter-thanks').show()
					$('.newsletter-signup.maincolumn').removeClass('open').css({'max-height': '100px'})
					submitted = true
				}
			}
		})
	});
});

function $c(t) {
	console.log(t);
}
