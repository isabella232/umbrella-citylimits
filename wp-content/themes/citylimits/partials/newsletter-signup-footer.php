<div class="newsletter-signup footer clearfix">
	<form>
		<div class="row">
			<div class="column signup_text_holder">
				<div class="signup_text"></div>
			</div>
			<div class="column newsletter_list">
<?php if ( function_exists( 'get_field' ) ) {
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
					<h3><?= $newsletter['title'] ?></h3>
					<p><?= $newsletter['short_description'] ?></p>
					<input type="checkbox" id="newsletter_<?= $newsletter['id'] ?>" value="<?= $newsletter['id'] ?>" name="newsletter[]"<?= $checked ?>  required><label for="newsletter_<?= $newsletter['id'] ?>">Sign up for <?= $newsletter['title'] ?></label>
				</section>
<?php }
} 
}
?>
			</div>
			<div class="column newsletter_form">
				<div class="newsletter_fields">
					<input type="text" name="newsletter_fname" placeholder="First Name" required>
					<input type="email" name="newsletter_email" placeholder="Email" required>
				</div>
				<input type="submit" class="subscribe_button" value="Sign Up">
			</div>
		</div>
	</form>
	<div class="newsletter-response">
		<div class="row">
			<div class="column signup_text_holder">
				<div class="signup_text"></div>
			</div>
			<div class="column newsletter-response-content">
				<div class="signup_intro"><?= get_field('thank_you_text', 'option') ?></div>
			</div>
		</div><!--.row-->
	</div><!--.newsletter-response-->
</div><!--.newsletter-signup-->