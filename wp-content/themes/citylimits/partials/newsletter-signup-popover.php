<?php
$newsletter_page = get_page_by_path('newsletter-subscriptions');
wp_enqueue_script( 'cl-newsletter' );
?>
<div class="newsletter-signup footer clearfix mobile visible-xs">
	<a href="<?= site_url() ?>/newsletter-subscriptions/"></a>
	<div id="close_bar"><div class="close_box"><span class="visuallyhidden">Close this popup</span></div></div>
	<div class="mobile_footer_content">
		<hgroup>
			<h1><?php esc_html_e( 'City Limits Newsletters', 'citylimits' ); ?></h1>
		</hgroup>
		<div class="post_content"><?= $newsletter_page->post_content ?></div>
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
		<div class="btn btn-primary">Sign Up</div>
	</div><!--.mobile_footer_content-->
</div><!--.newsletter-signup-->
