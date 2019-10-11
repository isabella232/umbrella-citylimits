<?php
include_once get_template_directory() . '/homepages/homepage-class.php';

class CityLimits extends Homepage {
	var $name = 'City Limits';
	var $type = 'citylimits';
	var $description = 'The homepage for City Limits.';
	var $rightRail = false;

	public function __construct( $options = array() ) {
		$defaults = array(
			'template' => get_stylesheet_directory() . '/homepages/template.php',
			'assets' => array(
				array(
					'homepage',
					get_stylesheet_directory_uri() . '/homepages/assets/css/homepage.css',
					array(),
					filemtime( get_stylesheet_directory() . '/homepages/assets/css/homepage.css' ),
				),
			),
			'prominenceTerms' => array(
				array(
					'name' => __('Homepage Featured', 'largo'),
					'description' => __('If you are using the Newspaper or Carousel optional homepage layout, add this label to posts to display them in the featured area on the homepage.', 'largo'),
					'slug' => 'homepage-featured'
				),
				array(
					'name' => __('Homepage Top Story', 'largo'),
					'description' => __('If you are using a "Big story" homepage layout, add this label to a post to make it the top story on the homepage', 'largo'),
					'slug' => 'top-story'
				),
			),
			'sidebars' => array(
				'Homepage Bottom (The bottom area of the homepage)',
			),
		);
		$options = array_merge( $defaults, $options );
		$this->load( $options );
	}
}

/**
 * Register this layout with Largo
 */
function workday_homepage_layout() {
	register_homepage_layout( 'CityLimits' );
}
add_action( 'init', 'workday_homepage_layout' );
