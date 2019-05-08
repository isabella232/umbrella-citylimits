jQuery(document).ready(function($) {
	var homebannerCounter = 0, signupH, signupOpenH, submitted = false;
	if ($('.newsletter-signup.maincolumn .not-expanded').length) {
		signupH = $('.newsletter-signup .not-expanded').height() + 'px';
		signupOpenH = $('.newsletter-signup .expanded').height() + 'px';
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
				if ($expanded.find('input[name=newsletter_fname]').val() || $expanded.find('input[name=newsletter_lname]').val() || $expanded.find('input[name=newsletter_email]').val()) {
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
		$(window).scroll(function() {
			if (!submitted && !footerShown && $(window).scrollTop() + $(window).height() >= $('#site-footer').position().top) {
				footerShown = true;
				setTimeout(function() {
					$('.newsletter-signup.footer').css({'max-height': '1000px'});
				}, 1000);
			}
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
		var entry = $('<h6 class="newsletter_to_subscribe" data-newsletter-id="' + id + '">' + title + '<div class="remove">X</div></h6>')
		$('#selected_newsletters').append(entry)
		if ($(window).width() < 769) {
			cartW = $(window).width()
		} else {
			cartW = $('#sidebar').width()
		}
		$c(cartW)
		$('#newsletter_cart').width(cartW).show()
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
		$('#newsletter_cart').width($('#sidebar').width())
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
