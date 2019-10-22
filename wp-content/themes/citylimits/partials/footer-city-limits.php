<div id="boilerplate">
	<div class="row-fluid clearfix">
		<div class="span6">
			<div class="footer-bottom clearfix">

				<!-- If you enjoy this theme and use it on a production site we would appreciate it if you would leave the credit in place. Thanks :) -->
				<p class="footer-credit"><?php largo_copyright_message(); ?></p>
				<?php do_action('largo_after_footer_copyright'); ?>
				<?php largo_nav_menu(
					array(
						'theme_location' => 'footer-bottom',
						'container' => false,
						'depth' => 1
					) );
				?>
			</div>
		</div>

		<div class="span6 right">
            <ul id="footer-social" class="social-icons">
				<?php largo_social_links(); ?>
			</ul>
		</div>
	</div>
</div>
