<div class="newsletter-signup maincolumn clearfix">
	<div class="not-expanded">
		<div class="not-expanded-bkg"></div>
	</div>

	<form class="expanded">
		<div class="row">
			<div class="column">
				<h3>The City Limits Newsletters</h3>
				<p>Quisque vel urna vitae ipsum vestibulum semper id et leo. Sed vel ante sodales tortor pretium dapibus. Sed suscipit pulvinar congue. Nunc iaculis molestie dui sed rutrum. Vivamus hendrerit id nibh id ornare.</p>
				<h4>Get our reporting in your inbox weekly</h4>
			</div>
			<div class="column">
<?php foreach ( get_field('newsletter_group', 'option') as $group ) { 
		foreach ( $group['newsletters'] as $newsletter) {
?>
				<section>
					<h3><?= $newsletter['title'] ?></h3>
					<?= $newsletter['description'] ?>
					<input type="checkbox" id="newsletter_<?= $newsletter['id'] ?>" value="<?= $newsletter['id'] ?>" name="newsletter[]" required><label for="newsletter_<?= $newsletter['id'] ?>">Sign up for <?= $newsletter['title'] ?></label>
				</section>
<?php }
} ?>
			</div>
			<div class="column">
				<input type="text" name="newsletter_fname" placeholder="First Name" required>
				<input type="text" name="newsletter_lname" placeholder="Last Name" required>
				<input type="email" name="newsletter_email" placeholder="Email" required>
				<input type="submit" class="subscribe_button" value="Sign Up">
			</div>
		</div>
	</form>
	<div class="newsletter-thanks">
		Thanks for signing up.
	</div>
</div><!--.newsletter-signup-->