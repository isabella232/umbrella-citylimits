<div class="newsletter-signup footer clearfix">
	<form>
		<div class="row">
			<div class="column">
				<h3>The City Limits Newsletters</h3>
<?php foreach ( get_field('newsletter_group', 'option') as $group ) { 
		foreach ( $group['newsletters'] as $newsletter) {
?>
				<input type="checkbox" id="newsletter_<?= $newsletter['id'] ?>" value="<?= $newsletter['id'] ?>"><label for="newsletter_<?= $newsletter['id'] ?>"><?= $newsletter['title'] ?></label>
<?php }
} ?>
				<h4>Get our reporting in your inbox weekly</h4>
			</div>
			<div class="column">
				<input type="text" id="newsletter_name" placeholder="Your Name">
				<input type="text" id="newsletter_email" placeholder="Your Email">
				<input type="submit" value="submit">
			</div>
		</div>
	</form>
</div><!--.newsletter-signup-->