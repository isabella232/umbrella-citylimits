<?php
/**
 * Regarding enqueueing and dequeueing styles and scripts
 */

/**
 * remove this theme's style.css, since we're using child-style.css instead
 * see https://github.com/INN/Largo-Sample-Child-Theme/issues/14
 */
remove_action( 'wp_enqueue_scripts', 'largo_enqueue_child_theme_css' );

/**
 * Copy Largo's largo_enqueue_js() so that we can make a case-specific modification to whether or not the largo sticky nav is set to display or not
 * Yes, this is the easiest way to do this. Other ways of doing it would involve cloning Largo/js/navigation.js
 * It's easiest to hack the variable that we use for this.
 */
	/**
	 * Enqueue our core javascript and css files
	 *
	 * @since 1.0
	 * @global LARGO_DEBUG
	 */
	function largo_enqueue_js() {
		/**
		 * Here is the main difference
		 * @todo add conditions as necessary
		 */
		$sticky_of = (bool) of_get_option( 'sticky_nav_display_article', 1 );
		$is_rezoning_project = (
			has_term( 'rezone', 'series' )
		) ? true : false ;
		$sticky_nav_display_article = ( $sticky_of || $is_rezoning_project );

		/*
		 * Use minified assets if LARGO_DEBUG is false.
		 */
		$suffix = (LARGO_DEBUG)? '' : '.min';
		$version = largo_version();

		// Our primary stylesheet. Often overridden by custom-less-variables version.
		wp_enqueue_style(
			'largo-stylesheet',
			get_template_directory_uri() . '/css/style' . $suffix . '.css',
			null,
			$version
		);

		wp_enqueue_style(
			'largo-stylesheet',
			get_template_directory_uri() . '/css/style' . $suffix . '.css',
			null,
			$version
		);

		wp_enqueue_style(
			'largo-child-styles',
			get_stylesheet_directory_uri() . '/css/child-style.css',
			array('largo-stylesheet'),
			filemtime( get_stylesheet_directory() . '/css/child-style.css' )
		);

		// Core JS includes some utilities, initializes carousels, search form behavior,
		// popovers, responsive header image, etc.
		wp_enqueue_script(
			'largoCore',
			get_template_directory_uri() . '/js/largoCore' . $suffix . '.js',
			array( 'jquery' ),
			$version,
			true
		);

		// Navigation-related JS
		wp_enqueue_script(
			'largo-navigation',
			get_template_directory_uri() . '/js/navigation' . $suffix . '.js',
			array( 'largoCore' ),
			$version,
			true
		);

		// Largo configuration object for use in frontend JS
		wp_localize_script(
			'largoCore', 'Largo', array(
			'is_home' => is_home(),
			'is_single' => is_single() || is_singular(),
			'sticky_nav_options' => array(
				'sticky_nav_display_article' => $sticky_nav_display_article,
				'main_nav_hide_article' => (bool) of_get_option( 'main_nav_hide_article', 0 ),
				'nav_overflow_label' => of_get_option( 'nav_overflow_label', 'More' )
			)
		));

		/*
		 * The following files are already minified:
		 *
		 * - modernizr.custom.js
		 * - largoPlugins.js
		 * - jquery.idTabs.js
		 */
		wp_enqueue_script(
			'largo-modernizr',
			get_template_directory_uri() . '/js/modernizr.custom.js',
			null,
			$version
		);
		wp_enqueue_script(
			'largoPlugins',
			get_template_directory_uri() . '/js/largoPlugins.js',
			array( 'jquery' ),
			$version,
			true
		);

		// Only load jquery tabs for the related content box if it's active
		if ( is_single() ) {
			wp_enqueue_script(
				'idTabs',
				get_template_directory_uri() . '/js/jquery.idTabs.js',
				array( 'jquery' ),
				$version,
				true
			);
		}
	}
