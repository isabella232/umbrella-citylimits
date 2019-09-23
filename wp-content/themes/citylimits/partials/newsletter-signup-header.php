<div class="newsletter-signup header">
	<div class="cl-tagline-holder">
		<div class="cl-tagline">
		</div>
	</div>
	<?php if ( $_COOKIE['signed_up_for_newsletter'] == true || $post->post_name == 'newsletter-subscriptions' ) { ?>
		<a href="<?php echo site_url(); ?>/donate?campaign=7011U000000M5rC" class="header-cl-ad"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/header_donate.svg"></a>
	<?php } else { ?>
		<a href="<?php echo site_url(); ?>/newsletter-subscriptions/" class="header-cl-ad header_newsletter_signup"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/header_newsletter_signup.svg"></a>
	<?php } ?>
</div><!--.newsletter-signup-->
