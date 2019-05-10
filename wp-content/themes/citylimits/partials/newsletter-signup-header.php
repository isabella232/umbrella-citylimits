<div class="newsletter-signup header">
	<div class="cl-tagline-holder">
		<div class="cl-tagline">
		</div>
	</div>
	 <?php if ( $_COOKIE['signedUpNewsletter'] == true || $post->post_name == 'newsletter-subscriptions' ) { ?>
	<a href="<?= site_url() ?>/donate?campaign=7011U000000M5rC" class="header-cl-ad"><img src="<?= get_stylesheet_directory_uri() ?>/img/donate_header.gif"></a>
	<? } else { ?>
	<a href="<?= site_url() ?>/newsletter-subscriptions/" class="header-cl-ad"><img src="<?= get_stylesheet_directory_uri() ?>/img/header_newsletter_signup.png"></a>
	<? } ?>
</div><!--.newsletter-signup-->