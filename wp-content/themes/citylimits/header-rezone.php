<?php



// Add the .neighborhoods-lp class
add_filter('body_class', function($classes) {
	$classes[] = 'neighborhoods-lp';
	return $classes;
});
?><!DOCTYPE html>
<!--[if lt IE 7]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if IE 9]>    <html <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<?php
	/**
	 * The template for displaying the header
	 *
	 * Contains the HEAD content and opening of the id=page and id=main DIV elements.
	 *
	 * @package Largo
	 * @since 0.1
	 */
	?>
	<title>
		<?php
			global $page, $paged;
			wp_title( '|', true, 'right' );
			bloginfo( 'name' ); // Add the blog name.

			// Add the blog description for the home/front page.
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) )
				echo " | $site_description";

			// Add a page number if necessary:
			if ( $paged >= 2 || $page >= 2 )
				echo ' | ' . 'Page ' . max( $paged, $page );
		?>
	</title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_head();
?>

<script>
	//Zone-In Menu (necessary for touch devices)
	jQuery(document).ready(function($){
		

		var $items = $('#menu-zone-in > .menu-item:not(.menu-item-has-children)');
		$('#menu-zone-in').append('<li id="more-zonein" class="menu-item menu-item-has-children"><a>More</a><ul class="sub-menu"></ul></li>');
		var $clones = $items.clone();
		$('#more-zonein .sub-menu').append($clones);
		$items.addClass('menu-hide');


		var menu = '#menu-zone-in > .menu-item.menu-item-has-children';
		var active = 'zones-active';

		if ($('html').hasClass('no-touch')){
			$(menu).hover(function(){
				$(this).addClass(active);
			}, function(){
				$(this).removeClass(active);
			})
		}
		$(menu).click(function(){
			var $this = $(this);
			if ($this.hasClass(active)){
				$(menu).removeClass(active);
			} else {
				$(menu).removeClass(active);
				$this.addClass(active);
			}
		});

		$('body').click(function(e) {
		    if ($(e.target).closest(menu).length === 0) {
				$(menu).removeClass(active);
			}
		});
	});
</script>
</head>

<body <?php body_class(); ?>>

	<div id="top"></div>

	<?php

	/**
	 * Fires at the top of the page, just after the id=top DIV element.
	 *
	 * @since 0.4
	 */
	do_action( 'largo_top' );

	?>

	<div id="page" class="hfeed clearfix">

		<?php

			/**
			 * Fires before the Largo header content.
			 *
			 * @since 0.4
			 */
			do_action( 'largo_before_header' );

			get_template_part( 'partials/largo-header' );
			
			/**
			 * Fires after the Largo header content.
			 *
			 * @since 0.4
			 */
			do_action( 'largo_after_header' );

			get_template_part( 'partials/nav', 'sticky' );

			/**
			 * Fires after the Largo navigation content.
			 *
			 * @since 0.4
			*/
			do_action( 'largo_after_nav' );

		?>

		<div id="main" class="row-fluid clearfix">

		<?php

		/**
		 * Fires at the top of the Largo id=main DIV element.
		 *
		 * @since 0.4
		 */
		do_action( 'largo_main_top' );

		$title = single_term_title( '', false );
		$description = term_description();

		$queried_object = get_queried_object();
		$post_id = largo_get_term_meta_post( $queried_object->taxonomy, $queried_object->term_id );
		$post = get_post( $post_id );
		?>

		<div class="rezone-header">
			<div class="row-fluid">
				<div class="span12">
					<?php get_template_part( 'partials/nav', 'main' ); ?>
				</div>
			</div>
		</div>
		<div class="series-header-container">
			<div class="series-banner">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/neighborhoods-featured.png" class="attachment-large size-large wp-post-image">
			</div>
			<section id="series-header" class="">
				<span class="special-project"><?php esc_html_e( 'Special Project', 'citylimits' ); ?></span>
				<h1 class="entry-title">
					<?php 
						if( $title ) {
							echo $title;
						} else {
							the_title();
						}
					?>
				</h1>
				<?php
					if ( $opt['show_series_byline'] ) {
						echo '<h5 class="byline">' . largo_byline( false, false, $post_id ) . '</h5>';
					} else {
						echo '<h5 class="byline">' . $description . '</h5>';
					}
				?>
					<div class="description">
						<?php echo apply_filters( 'the_content', $post->post_excerpt ); ?>
					</div>
			</section>
		</div>
