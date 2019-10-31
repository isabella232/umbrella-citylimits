<!DOCTYPE html>
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
	<?php
		// Fallback <title> tag on WP pre 4.1
		// @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
		// @since 0.6
		if ( ! function_exists( '_wp_render_title_tag' ) ) {
			printf(
				'<title>%1$s</title>',
				wp_title( '|', false, 'right' )
			);
		}
	?>
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php
		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_head();
	?>
</head>

<body <?php body_class(); ?>>

	<?php 

		/**
		 * Fires right after the opening body tag.
		 * 
		 * @since 0.6.4
		 */
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}

	?>

	<div id="top"></div>

	<?php

	/**
	 * Fires at the top of the page, just after the id=top DIV element.
	 *
	 * @since 0.4
	 */
	do_action( 'largo_top' );

	?>

	<?php
		if ( SHOW_GLOBAL_NAV === TRUE ) {

			/**
			 * Fires before the Largo global navigation content.
			 *
			 * @since 0.4
			 */
			do_action( 'largo_before_global_nav' );

			get_template_part( 'partials/nav', 'global' );

			/**
			 * Fires after the Largo global navigation content.
			 *
			 * @since 0.4
			 */
			do_action( 'largo_after_global_nav' );

		}
	?>

	<div id="page" class="hfeed clearfix">

		<?php 
			
			if ( of_get_option( 'leaderboard_enabled' ) == TRUE ) {
				get_template_part( 'partials/header-ad-zone' );
			}

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
	
			get_template_part( 'partials/nav', 'main' );

			if ( SHOW_SECONDARY_NAV === TRUE ) {
				get_template_part( 'partials/nav', 'secondary' );
            }
            
            get_template_part( 'partials/nav', 'sticky' ); 

            get_template_part('partials/homepage-alert'); 
            
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
