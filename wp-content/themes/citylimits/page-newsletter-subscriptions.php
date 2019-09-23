<?php
global $shown_ids;

add_filter('body_class', function($classes) {
	$classes[] = 'classic';
	$classes[] = 'newsletter-landing';
	return $classes;
});

get_header();
?>
<div id="landing_header"></div>

<div id="content" class="span8" role="main">
	<?php
		while ( have_posts() ) {
			the_post();

			$shown_ids[] = get_the_ID();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

				<?php do_action('largo_before_page_header'); ?>

				<header class="entry-header">
					<?php edit_post_link(__('Edit This Page', 'largo'), '<h5 class="byline"><span class="edit-link">', '</span></h5>'); ?>
				</header><!-- .entry-header -->

				<?php do_action('largo_after_page_header'); ?>

				<section class="entry-content">

					<?php do_action('largo_before_page_content'); ?>
					<?php if ( function_exists( 'get_field' ) ) { ?>
						<div class="newsletter_intro">
							<?php echo get_field('intro_text', 'option'); ?>
						</div><!--.newsletter_intro-->
					<?php } ?>

					<?php
						$groups = get_field('newsletter_group', 'option');
						foreach ($groups as $group) {
							$newsletters = array_filter($group['newsletters'], function($n) {
								return $n['active'] == true;
							});

							?>

							<section class="newsletter_group">
								<?php
									if ( $group['group_title'] && count($newsletters) > 1 ) {
										?>
											<h2><?=$group['group_title']?></h2>
										<?php
									}

									foreach ( $newsletters as $newsletter ) {
										if (!$newsletter['active']) {
											continue;
										}
										$thumb = $newsletter['thumbnail'] ? wp_get_attachment_image( $newsletter['thumbnail']['id'], 'thumbnail' ) : '';
										$subtitle = $newsletter['subtitle'] ? "<h5 class='subtitle'>" . $newsletter['subtitle'] . "</h5>\n" : '';
										$byline = $newsletter['byline'] ? "<h5 class='byline'>" . $newsletter['byline'] . "</h5>\n" : '';
										$sample = $newsletter['sample'] ? "<a href='" . $newsletter['sample'] . "' target='_blank' class='sample'>View a sample &raquo;</a>" : '';
										
										?>
											<section class="newsletter row-fluid">
												<figure class="span3">
													<?= $thumb ?>
												</figure>
												<div class="newsletter_info span9">
													<h3 class="entry-title newsletter_title"><?= $newsletter['title'] ?></h3>
													<?= $subtitle ?>
													<?= $byline ?>
													<?= $newsletter['description'] ?>
													<?= $sample ?>
												</div><!--.newsletter_info-->
												<div class="divider"></div>
												<div class="subscribe_button" data-newsletter-id="<?= $newsletter['id'] ?>"><div class="unselected">Subscribe</div><div class="selected">Selected</div></div>
											</section><!--.newsletter-->
										<?php
									}
								?>
							</section><!--.newsletter_group-->
							<?php
						} //end foreach $groups
						do_action('largo_after_page_content'); 
					?>
				</section><!-- .entry-content -->
			</article><!-- #post-<?php the_ID(); ?> -->
			<?
		} // endwhile
	?>
</div>

<?php do_action('largo_after_content'); ?>

<aside id="sidebar" class="span4">
	<div id="newsletter_cart">
		<h3>Your Selections</h3>
		<ul id="selected_newsletters">
		</ul>
		<form>
				<input type="text" name="newsletter_fname" placeholder="First Name" required>
				<!--<input type="text" name="newsletter_lname" placeholder="Last Name" required>-->
				<input type="email" name="newsletter_email" placeholder="Email" required>
				<input type="submit" class="subscribe_button" value="Sign Up">
		</form>
	</div>
</aside><!--#sidebar-->

<?php get_footer();
