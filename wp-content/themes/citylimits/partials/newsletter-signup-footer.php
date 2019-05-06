<div class="newsletter-signup footer clearfix">
	<form>
		<div class="row">
			<div class="column">
				<h3>The City Limits Newsletters</h3>
<?php foreach ( get_field('newsletters', 'option') as $newsletter ) { var_log($newsletter);?>
				<input type="checkbox" id="<?= $newsletter['newsletter_id'] ?>" value="<?= $newsletter['newsletter_id'] ?>"><label for="<?= $newsletter['newsletter_id'] ?>"><?= $newsletter['newsletter_title'] ?></label>
<?php } ?>
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