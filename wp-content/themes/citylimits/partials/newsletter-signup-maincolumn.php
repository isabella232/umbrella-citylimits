<?php
$newsletter_page = get_page_by_path('newsletter-subscriptions');
?>
<aside class="newsletter-signup maincolumn clearfix">
	<div class="not-expanded">
		<hgroup>
			<h1><?php esc_html_e( 'City Limits Newsletters', 'citylimits' ); ?></h1>
			<h2><?php esc_html_e( 'Sign up for our newsletters to get our reporting delivered to you.', 'citylimits' ); ?></h2>
		</hgroup>
		<span class="btn btn-primary">
			<?php esc_html_e( 'Sign up' ); ?>
		</span>
	</div>

	<form class="expanded">
		<hgroup>
			<h1><?php esc_html_e( 'City Limits Newsletters', 'citylimits' ); ?></h1>
			<h2><?php esc_html_e( 'Sign up for our newsletters to get our reporting delivered to you.', 'citylimits' ); ?></h2>
		</hgroup>
		<div class="row">
			<div class="column newsletter_list">
				<div class="post_content"><?php echo wp_kses_post(  $newsletter_page->post_content ) ?></div>
				<?php
					$checkedFlag = false;
					if ( function_exists( 'get_field' ) ) {
						foreach ( get_field('newsletter_group', 'option') as $group ) {
							foreach ( $group['newsletters'] as $newsletter) {
								if (!$newsletter['active']) {
									continue;
								}
								$checked = '';
								if (!$checkedFlag) {
									$checked = ' checked="checked"';
									$checkedFlag = true;
								}
								?>
									<section>
										<input type="checkbox" id="newsletter_<?= $newsletter['id'] ?>" value="<?= $newsletter['id'] ?>" name="newsletter[]"<?= $checked ?> required>
										<label class="" for="newsletter_<?= $newsletter['id'] ?>">
											<h3><?php esc_html_e( $newsletter['title'] ); ?></h3>
											<p><?php echo wp_kses_post(  $newsletter['short_description'] ); ?></p>
										</label>
									</section>
								<?php
							}
						}
					}
				?>
			</div>
			<div class="column newsletter_form">
				<label for="newsletter_fname"><?php esc_html_e( 'First Name', 'citylimits' ); ?></label>
				<input type="text" name="newsletter_fname" placeholder="<?php esc_attr_e( 'First Name', 'citylimits' ); ?>" required>
				<label for="newsletter_fname"><?php esc_html_e( 'Email address', 'citylimits' ); ?></label>
				<input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Email address', 'citylimits' ); ?>" required>
				<input type="submit" class="btn btn-primary" value="Sign Up">
			</div>
		</div><!--.row-->
	</form>
	<?php if ( function_exists( 'get_field' ) ) { ?>
		<div class="newsletter-response">
			<div class="row">
				<div class="column signup_text_holder">
					<div class="signup_text"></div>
					<div class="signup_intro"><?php echo wp_kses_post( get_field('intro_text', 'option') ); ?></div>
				</div>
				<div class="column newsletter-response-content">
					<?php echo wp_kses_post( get_field('thank_you_text', 'option') ) ; ?>
				</div>
			</div><!--.row-->
		</div><!--.newsletter-response-->
	<?php } ?>
</aside>
