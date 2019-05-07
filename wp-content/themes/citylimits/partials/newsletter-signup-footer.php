<div class="newsletter-signup footer clearfix">
	<form>
		<div class="row">
			<div class="column">
				<h3>The City Limits Newsletters</h3>
<?php foreach ( get_field('newsletter_group', 'option') as $group ) { 
		foreach ( $group['newsletters'] as $newsletter) {
?>
				<input type="checkbox" id="footer_newsletter_<?= $newsletter['id'] ?>" value="<?= $newsletter['id'] ?>" name="newsletter[]" required><label for="footer_newsletter_<?= $newsletter['id'] ?>"><?= $newsletter['title'] ?></label>
<?php }
} ?>
				<h4>Get our reporting in your inbox weekly</h4>
			</div>
			<div class="column">
				<input type="text" name="newsletter_fname" placeholder="First Name" required>
				<input type="text" name="newsletter_lname" placeholder="Last Name" required>
				<input type="email" name="newsletter_email" placeholder="Your Email" required>
				<input type="submit" value="submit">
			</div>
		</div>
	</form>
	<div class="newsletter-thanks">
		Thanks for signing up.
	</div>
</div><!--.newsletter-signup-->