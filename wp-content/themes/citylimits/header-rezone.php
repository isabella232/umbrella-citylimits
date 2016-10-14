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

			get_template_part( 'partials/nav', 'sticky-rezone' );

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
		?>

		<div class="rezone-header">
			<div class="row-fluid">
				<div class="span8">
					<h1 class="entry-title"><?php _e( 'The Future of NYC Neighborhoods', 'citylimits'); ?></h1>
				</div>
				<div class="span4">
					<?php // @TODO ReZone Newsletter Code Here ?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<a href="/zone-in/"><img src="/wp-content/themes/citylimits/img/zonein-logo.jpg" alt="ZoneIn Project Logo" width="100%" /></a>
					<?php get_template_part( 'partials/nav', 'rezone' ); ?>
				</div>
			</div>
		</div>
