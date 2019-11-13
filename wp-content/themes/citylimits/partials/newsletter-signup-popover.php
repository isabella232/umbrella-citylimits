<?php
$newsletter_page = get_page_by_path('newsletter-subscriptions');
?>
<div class="newsletter-signup footer clearfix mobile visible-xs">
	<a href="<?= site_url() ?>/newsletter-subscriptions/"></a>
	<div id="close_bar"><div class="close_box"><span class="visuallyhidden">Close this popup</span></div></div>
	<div class="mobile_footer_content">
		<div class="signup_text_holder">
			<div class="signup_text"></div>
		</div>
		<p><?= $newsletter_page->post_content ?></p>
		<ul>
			<?php
				if ( function_exists( 'get_field' ) ) {
					foreach ( get_field('newsletter_group', 'option') as $group ) {
						foreach ( $group['newsletters'] as $newsletter) {
							if (!$newsletter['active']) {
								continue;
							}
							?>
								<li><?= $newsletter['title'] ?></li>
							<?php
						}
					}
				}
			?>
		</ul>
		<div class="btn">Sign Up</div>
	</div><!--.mobile_footer_content-->
</div><!--.newsletter-signup-->
