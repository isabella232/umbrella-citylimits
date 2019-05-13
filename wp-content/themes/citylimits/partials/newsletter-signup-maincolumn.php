<?php
$newsletter_page = get_page_by_path('newsletter-subscriptions');
?>
<div class="newsletter-signup maincolumn clearfix">
	<div class="not-expanded">
		<div class="not-expanded-bkg"></div>
	</div>

	<form class="expanded">
		<div class="row">
			<div class="column signup_text_holder">
				<div class="signup_text"></div>
				<p><?= $newsletter_page->post_content ?></p>
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
		</div><!--.row-->
		<div class="row">
			<div class="column newsletter_form">
				<input type="text" name="newsletter_fname" placeholder="First Name" required>
				<input type="text" name="newsletter_lname" placeholder="Last Name" required>
				<input type="email" name="newsletter_email" placeholder="Email" required>
				<input type="submit" class="subscribe_button" value="Sign Up">
			</div>
		</div><!--.row-->
	</form>
	<div class="newsletter-thanks">
		Thanks for signing up.
	</div>
</div><!--.newsletter-signup-->