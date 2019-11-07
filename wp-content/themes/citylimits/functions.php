<?php

define( 'SHOW_STICKY_NAV', false );
define( 'SHOW_CATEGORY_RELATED_TOPICS', false );

// Setup some contants we'll need in various places
define( 'LARGO_EXT_DIR', dirname( __FILE__ ) );
define( 'LARGO_EXT', __FILE__ );


/**
 * Include theme files
 *
 * Based off of how Largo loads files: https://github.com/INN/Largo/blob/master/functions.php#L358
 *
 * 1. hook function Largo() on after_setup_theme
 * 2. function Largo() runs Largo::get_instance()
 * 3. Largo::get_instance() runs Largo::require_files()
 *
 * This function is intended to be easily copied between child themes, and for that reason is not prefixed with this child theme's normal prefix.
 *
 * @link https://github.com/INN/Largo/blob/master/functions.php#L145
 */
function largo_child_require_files() {
	$includes = array(
		'/inc/ajax-functions.php',
		'/inc/communitywire.php',
		'/inc/enqueue.php',
		'/inc/metaboxes.php',
		'/inc/post-templates.php',
		'/inc/registration.php',
		'/inc/term-meta.php',
		// plugin compat
		'/inc/acf.php',
		'/inc/doubleclick-for-wordpress.php',
		'/inc/jetpack.php',
		'/inc/tribe-events-calendar.php',
		'/inc/widgets/jp-related-posts.php',
		// widgets
		'/inc/widgets/communitywire-announcements.php',
		'/inc/widgets/communitywire-sidebar.php',
		'/inc/widgets/neighborhood-content.php',
		'/inc/widgets/zonein-events.php',
		'/inc/widgets/cl-newsletter-header.php',
		'/inc/widgets/class-citylimits-special-projects-widget.php',
		'/inc/widgets/class-citylimits-podcast-widget.php',
		'/inc/widgets/class-citylimits-series-seven-stories-widget.php',
		// homepage
		'/homepages/layout.php',
	);

	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
		$includes[] = '/inc/gravityforms/events-calendar.php';
	}

	foreach ( $includes as $include ) {
		require_once( get_stylesheet_directory() . $include );
	}

}
add_action( 'after_setup_theme', 'largo_child_require_files' );

// re-enable the default WP RSS widget
function citylimits_widgets_init() {
	register_widget( 'WP_Widget_RSS' );
	register_widget( 'neighborhood_content' );
	register_widget( 'zonein_events' );
	register_widget( 'communitywire_announcements' );
	register_widget( 'communitywire_sidebar' );
	register_widget( 'jp_cl_related_posts' );
	register_widget( 'cl_newsletter_header' );
	unregister_widget( 'TribeCountdownWidget' );
}
add_action( 'widgets_init', 'citylimits_widgets_init', 14 );

/**
 * Set the number of posts in the right-hand side of the Top Stories homepage template to 2.
 *
 * Largo's default is 6. CityLimits does not want the "More headlines" area to appear, which appears if 4 or more posts are in the area.
 *
 * @return 2
 * @param int $showstories
 */
function citylimits_featured_stories_count( $showstories ) {
	return 3;
}
add_filter( 'largo_homepage_topstories_post_count', 'citylimits_featured_stories_count' );

/* Remove the largo logo from login page */
add_action( 'init', function() {
	remove_action( 'login_head', 'largo_custom_login_logo' );
});


/* Show City Limits logo on login page */
function citylimits_custom_login_logo() {
	echo '<style type="text/css">
			.login h1 a {
				background-image: url(' . get_stylesheet_directory_uri() . '/img/citylimits.png) !important;
				background-size: 200px 200px;
				height: 200px;
				width: 200px;
			}
		</style>';
}
add_action('login_head', 'citylimits_custom_login_logo');


function citylimits_login_redirect( $redirect_to, $request, $user ) {
	if ( isset( $_GET['redirect_to'] ) || isset( $_POST['redirect_to'] ) ) {
		return $redirect_to;
	} else {
		return home_url();
	}
}
add_filter( 'login_redirect', 'citylimits_login_redirect', 10, 3 );

function create_neighborhoods_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Neighborhoods', 'Taxonomy General Name', 'citylimits' ),
		'singular_name'              => _x( 'Neighborhood', 'Taxonomy Singular Name', 'citylimits' ),
		'menu_name'                  => __( 'Neighborhoods', 'citylimits' ),
		'all_items'                  => __( 'All Neighborhoods', 'citylimits' ),
		'parent_item'                => __( 'Parent Neighborhood', 'citylimits' ),
		'parent_item_colon'          => __( 'Parent Neighborhood:', 'citylimits' ),
		'new_item_name'              => __( 'New Neighborhood', 'citylimits' ),
		'add_new_item'               => __( 'Add New Neighborhood', 'citylimits' ),
		'edit_item'                  => __( 'Edit Neighborhood', 'citylimits' ),
		'update_item'                => __( 'Update Neighborhood', 'citylimits' ),
		'view_item'                  => __( 'View Neighborhood', 'citylimits' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'neighborhoods', array( 'post' ), $args );

}
add_action( 'init', 'create_neighborhoods_taxonomy', 0 );

function register_zonein_menu() {
  register_nav_menu('zonein-menu',__( 'Mapping the Future Menu' ));
}
add_action( 'init', 'register_zonein_menu' );

function register_neighborhood_sidebars() {
	register_sidebar( array(
		'name'		=> __( 'Neighborhoods Taxonomy Sidebar', 'citylimits' ),
		'id'		=> 'rezone-neighborhoods-sidebar',
		'description'	=> __( 'Widgets in this area will be shown on all neighborhood taxonomy pages' ),
		'before_widget'	=> '<section id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</section>',
		'before_title'	=> '<h2 class="widgettitle">',
		'after_title'	=> '</h2>'
	) );

	register_sidebar( array(
		'name'		=> __( 'Mapping The Future Subpage', 'citylimits' ),
		'id'		=> 'rezone-subpage-sidebar',
		'description'	=> __( 'Widgets in this area will be shown on all neighborhood taxonomy pages' ),
		'before_widget'	=> '<li id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</li>',
		'before_title'	=> '<h2 class="widgettitle">',
		'after_title'	=> '</h2>'
	) );

	register_sidebar( array(
		'name'		=> __( 'CommunityWire Listings', 'citylimits' ),
		'id'		=> 'communitywire-listings',
		'description'	=> __( 'Widgets in this area will be shown on the CommunityWire listing page' ),
		'before_widget'	=> '<div class="span6">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h2 class="widgettitle">',
		'after_title'	=> '</h2>'
	) );

	register_sidebar( array(
		'name'		=> __( 'CommunityWire Sidebar Content', 'citylimits' ),
		'id'		=> 'communitywire-sidebar-content',
		'description'	=> __( 'Widgets in this area will be shown as part of the CommunityWire Widget' ),
		'before_widget'	=> '<aside class="widget">',
		'after_widget'	=> '</aside>',
		'before_title'	=> '<h3 class="widgettitle">',
		'after_title'	=> '</h3>'
	) );
}
add_action( 'widgets_init', 'register_neighborhood_sidebars' );

// Register Custom Post Type
function create_zonein_events_post_type() {

	$labels = array(
		'name'                  => 'Mapping the Future Events',
		'singular_name'         => 'Mapping the Future Event',
		'menu_name'             => 'Mapping the Future Events',
		'name_admin_bar'        => 'Mapping the Future Events',
		'archives'              => 'Mapping the Future Events Archives',
		'all_items'             => 'All Mapping the Future Events',
		'add_new_item'          => 'Add New Mapping the Future Event',
	);
	$rewrite = array(
		'slug'                  => 'zonein-events',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => 'Mapping the Future Event',
		'description'           => 'Events for the Mapping the Future Series',
		'labels'                => $labels,
		'supports'              => array( ),
		'taxonomies'            => array( 'neighborhoods' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'		=> 'dashicons-calendar',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'page',
	);
	register_post_type( 'zonein_events', $args );

}
add_action( 'init', 'create_zonein_events_post_type', 0 );

function citylimits_print_event_time() {
	if ( 'zonein_events' == get_post_type() ) {
		$date = get_post_meta( get_the_ID(), 'event_information_date_time', true );
		if ( $date ) {
			echo '<span class="date">' . date( 'F d, Y', $date ) . '</span> ';
			echo '<span class="time">' . date( 'g:ia', $date ) . '</span> ';
		}
	}
}
add_action( 'largo_after_post_header', 'citylimits_print_event_time' );


/**
 * Order the zonein events archive by the event date, not by the published date
 * @see citylimits_modify_zonein_events_lmp_query
 */
function citylimits_modify_zonein_events_query( $query ) {

	if ( $query->is_main_query() && is_post_type_archive( 'zonein_events' ) ) {
		$query->set( 'meta_key', 'event_information_date_time' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
	}
	return $query;
}
add_action( 'pre_get_posts', 'citylimits_modify_zonein_events_query' );

/**
 * Order the zonein events archive LMP by the event date, not by the published date
 * @see citylimits_modify_zonein_events_query
 */
function citylimits_modify_zonein_events_lmp_query( $args ) {
	if ( $args['post_type'] == 'zonein_events' ) {
		$args['meta_key'] = 'event_information_date_time';
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'ASC';
	}
	return $args;
}
add_action( 'largo_lmp_args', 'citylimits_modify_zonein_events_lmp_query' );

/**
 * Filter the main WP_Query by neighborhood on neighborhood post types
 */
function zonein_tax_archive_query( $query ) {
	if ( $query->is_archive() && isset( $query->query['post-type'] ) && isset( $_GET['neighborhood'] ) && ! empty( $_GET['neighborhood'] ) ) {
		$query->set( 'tax_query', array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'post-type',
				'field'    => 'slug',
				'terms'    => array( $query->query['post-type'] ),
			),
			array(
				'taxonomy' => 'neighborhoods',
				'field' => 'slug',
				'terms' => sanitize_key( $_GET['neighborhood'] ),
			),
		) );
		return $query;
	}
}
add_action( 'pre_get_posts', 'zonein_tax_archive_query', 1 );

/**
 * get other scripts
 */
function citylimits_communitywire_enqueue() {
 	if (is_page_template( 'page-communitywire.php' )) {
		wp_enqueue_script( 'inn-tools', get_stylesheet_directory_uri() . '/js/communitywire.js', array( 'jquery' ), '1.1', true );
	}
}
add_action( 'wp_enqueue_scripts', 'citylimits_communitywire_enqueue' );

function citylimits_newsletter_enqueue() {
	wp_enqueue_script( 'jscookies', get_stylesheet_directory_uri() . '/js/cookies.js', null, '1.1', true );

	wp_register_script( 'cl-newsletter', get_stylesheet_directory_uri() . '/js/newsletter.js', array( 'jquery', 'jscookies' ), null, true );
	wp_localize_script( 'cl-newsletter', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
	wp_enqueue_script( 'cl-newsletter' );
}
add_action( 'wp_enqueue_scripts', 'citylimits_newsletter_enqueue' );

/* need this to allow Gravity Forms to post to API */
add_filter( 'gform_webhooks_request_args', function ( $request_args, $feed ) {
    $request_url = rgars( $feed, 'meta/requestURL' );
    if ( strpos( $request_url, '{rest_api_url}' ) === 0 || strpos( $request_url, rest_url() ) === 0 ) {
        $request_args['headers']['Authorization'] = 'Basic ' . base64_encode( USERNAME_HERE . ':' . PASSWORD_HERE );
    }
 
    return $request_args;
}, 10, 2 );

/**
 * Set max srcset image width to 771px, because otherwise WP will display the full resolution version
 */
function set_max_srcset_image_width( $max_width ) {
    $max_width = 771;
    return $max_width;
}
add_filter( 'max_srcset_image_width', 'set_max_srcset_image_width' );

/**
 * newsletter subscribe forms
 */

function citylimits_newsletter_form_interstitial() {
	get_template_part( 'partials/newsletter-signup', 'maincolumn' );
}
add_action( 'largo_before_sticky_posts', 'citylimits_newsletter_form_interstitial', 11 );
add_action( 'largo_category_after_primary_featured_post', 'citylimits_newsletter_form_interstitial', 11 );
add_action( 'largo_series_before_stories', 'citylimits_newsletter_form_interstitial', 11 );
add_action( 'largo_archive_before_stories', 'citylimits_newsletter_form_interstitial', 11 );

function citylimits_newsletter_form_footer() {
	get_template_part( 'partials/newsletter-signup', 'footer' );
}
add_action( 'largo_before_footer', 'citylimits_newsletter_form_footer', 11 );

function citylimits_newsletter_form_popover() {
	get_template_part( 'partials/newsletter-signup', 'popover' );
}
add_action( 'wp_footer', 'citylimits_newsletter_form_popover', 11 );

add_shortcode('cl-newsletter', function() {
	ob_start();
	get_template_part( 'partials/newsletter-signup', 'maincolumn' );
	return ob_get_clean();
});

/**
 * Register custom menu locations for the main nav and sticky mobile navs
 * Also unregister default Largo menus that aren't used
 */
function register_citylimits_menu_locations() {

	// menus to be registered
	register_nav_menu( 'languages-menu', __( 'Languages Menu' ) );
	register_nav_menu( 'mobile-sticky-menu', __( 'Mobile Sticky' ) );
	register_nav_menu( 'special-projects-secondary-menu', __( 'Special Projects Secondary Navigation Menu' ) ) ;

	// default Largo menus to be unregistered
	unregister_nav_menu( 'global-nav' );
	unregister_nav_menu( 'main-nav' );
	unregister_nav_menu( 'dont-miss' );

}
add_action( 'init', 'register_citylimits_menu_locations' );

/**
 * Enqueue specific styles and scripts for City Limits child theme
 */
function citylimits_enqueue_styles(){
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'citylimits-navigation', get_stylesheet_directory_uri() . '/js/navigation.js', array( 'jquery', 'largo-modernizr' ), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'citylimits_enqueue_styles' );

/**
 * Dequeue specific scripts
 */
function citylimits_dequeue_scripts(){
	wp_dequeue_script( 'largo-navigation' );
}
add_action( 'wp_print_scripts', 'citylimits_dequeue_scripts', 100 );

/**
 * Function to add thumbnail images to the secondary special project nav menu
 * 
 * @param Arr $sorted_menu_items The menu items, sorted by each menu item's menu order.
 * @param Obj $args An object containing wp_nav_menu() arguments.
 * 
 * @return Arr $sorted_menu_items The menu items, sorted by each menu item's menu order.
 */
function add_thumbnails_to_special_projects_nav( $sorted_menu_items, $args ) {

    // don't continue if we're not on the special projects secondary menu
    if( $args->theme_location != 'special-projects-secondary-menu' ) {

		return $sorted_menu_items;

    }

    foreach( $sorted_menu_items as $menu_item ) {

		$thumbnail = '';

		$menu_item_id = $menu_item->object_id;
		$menu_item_object_type = $menu_item->object;

		if( taxonomy_exists( $menu_item_object_type ) ){
		
			// get the term featured media thumbnail image
			$term_meta_post = largo_get_term_meta_post( $menu_item_object_type, $menu_item_id );

			if( largo_has_featured_media( $term_meta_post ) ){

				$thumbnail = largo_get_featured_media( $term_meta_post );
				$thumbnail = $thumbnail['attachment_data']['sizes']['thumbnail']['url'];

			}

		} else if( 'post' === $menu_item_object_type ){

			if( largo_has_featured_media( $menu_item_id ) ){
				
				$thumbnail = largo_get_featured_media( $menu_item_id );
				$thumbnail = $thumbnail['attachment_data']['sizes']['thumbnail']['url'];

			}

		}
	
		$menu_item->title = ! empty( $thumbnail ) ? '<img src="'.$thumbnail.'" class="secondary-nav-item-thumbnail"><span class="secondary-nav-item-title">'.$menu_item->title.'</span>' : $menu_item->title;

    }

    // return all of the menu objects
    return $sorted_menu_items;
	
}
add_filter( 'wp_nav_menu_objects', 'add_thumbnails_to_special_projects_nav', 10, 2 );

/**
 * Helper function for getting posts in proper order for a series
 *
 * @uses largo_is_series_enabled
 * @param integer $series_id series term id
 * @param integer $number number of posts to fetch, defaults to all
 * @param string $order how to sort the returned posts, optional. series landing option will be used if empty
 */
function citylimits_get_series_posts( $series_id, $number = -1, $order = null ) {

	// If series are not enabled, then there are no posts in a series.
	if ( !largo_is_series_enabled() ) return;

	$term = get_term_by( 'id', $series_id, 'series' );
	$term_slug = $term->slug;

	$series_args = array(
		'post_type' 	 	=> 'post',
		'taxonomy' 		 	=> 'series',
		'term' 		 	 	=> $term_slug,
		'order' 		 	=> 'DESC',
		'orderby' 	 		=> 'date',
		'posts_per_page' 	=> $number
	);

	if( empty( $order ) ){
		$order = get_post_meta( $landing->post->ID, 'post_order', TRUE );
	}

	switch ( $order ) {
		case 'ASC':
			$series_args['order'] = 'ASC';
			break;
		case 'custom':
			$series_args['orderby'] = 'series_custom';
			break;
		case 'featured, DESC':
		case 'featured, ASC':
			$series_args['orderby'] = $order;
			break;
	}

	$series_posts = new WP_Query( $series_args );

	if ( $series_posts->found_posts ) return $series_posts;

	return false;

}
