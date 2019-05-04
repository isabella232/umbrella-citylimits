<div class="newsletter-signup header">
	 <? if ($_COOKIE['signedUpNewsletter'] == true) { ?>
	<a href="<?= site_url() ?>/get-the-city-limits-weekly-brief/" class="header-cl-ad"><img src="<?= get_stylesheet_directory_uri() ?>/img/newsletter_signup_header.gif"></a>
	<? } else { ?>
	<a href="<?= site_url() ?>/donate?campaign=7011U000000M5rC" class="header-cl-ad"><img src="<?= get_stylesheet_directory_uri() ?>/img/donate_header.gif"></a>
	<? } ?>
</div><!--.newsletter-signup-->