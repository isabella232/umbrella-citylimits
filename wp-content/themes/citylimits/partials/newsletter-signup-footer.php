<div class="newsletter-signup footer clearfix">
	<form>
		<div class="row">
			<div class="column signup_text_holder">
				<div class="signup_text"></div>
			</div>
			<div class="column newsletter_list">
<?php foreach ( get_field('newsletter_group', 'option') as $group ) { 
		foreach ( $group['newsletters'] as $newsletter) {
			if (!$newsletter['active']) {
				continue;
			}
?>
				<section>
					<h3><?= $newsletter['title'] ?></h3>
					<?= $newsletter['description'] ?>
					<input type="checkbox" id="newsletter_<?= $newsletter['id'] ?>" value="<?= $newsletter['id'] ?>" name="newsletter[]" required><label for="newsletter_<?= $newsletter['id'] ?>">Sign up for <?= $newsletter['title'] ?></label>
				</section>
<?php }
} ?>
			</div>
			<div class="column newsletter_form">
				<div class="newsletter_fields">
					<input type="text" name="newsletter_fname" placeholder="First Name" required>
					<input type="text" name="newsletter_lname" placeholder="Last Name" required>
					<input type="email" name="newsletter_email" placeholder="Email" required>
				</div>
				<input type="submit" class="subscribe_button" value="Sign Up">
			</div>
		</div>
	</form>
	<div class="newsletter-thanks">
		<div class="newsletter-thanks-content">
			<h3>Thank you for signing up for our newsletters.</h3>
			<p>Check your email for our confirmation.</p>
		</div>
	</div>
</div><!--.newsletter-signup-->