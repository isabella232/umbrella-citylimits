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
			'typekit',
			'https://use.typekit.net/xkz6lbv.css'
		);

		wp_enqueue_style(
			'largo-child-styles',
			get_stylesheet_directory_uri() . '/css/child-style.css',
			array( 'largo-stylesheet', 'typekit' ),
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
	}

/**
 * ADD Google Tag Manager Verification code TO HEADER
 */
add_action('wp_head', 'add_gtm');
function add_gtm() {
	print <<<EOH
<!-- Google Tag Manager Verification for citylimits.org -->
<meta name=“google-site-verification” content=“xtkYdoGbfrA1xVBUGe11w1Z6HyrnplNW9OSjNh6HZx8" />
EOH;
}


/**
 * ADD HOTJAR TO HEADER
 */
add_action('wp_head', 'add_hotjar');
function add_hotjar() {
	print <<<EOH
<!-- Hotjar Tracking Code for citylimits.org -->
<script>
	(function(h,o,t,j,a,r){
		h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
		h._hjSettings={hjid:1295994,hjsv:6};
		a=o.getElementsByTagName('head')[0];
		r=o.createElement('script');r.async=1;
		r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
		a.appendChild(r);
		})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
EOH;
}

/**
 * remove WordPress' emoji support for print
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Custom Google Analytics things.
 */
function citylimits_google_analytics() {
	if ( ! is_user_logged_in() ) { // don't track logged in users ?>
		<script>
			( function ( i, s, o, g, r, a, m ) {i['GoogleAnalyticsObject']=r;i[r]=i[r]|| function() {( i[r].q=i[r].q||[] ).push( arguments )},i[r].l=1*new Date();a=s.createElement( o ), m=s.getElementsByTagName( o )[0];a.async=1;a.src=g;m.parentNode.insertBefore( a, m )} )
			( window,document,'script','https://www.google-analytics.com/analytics.js','ga' );

			ga( 'create', 'UA-529003-1', 'auto' );

			<?php
			global $post, $wp_query;

			if ( is_singular() ) {
				// Single objects

				if ( has_term( 'zonein', 'series' ) or has_term( 'futuremap', 'series' ) ) {
					echo "ga( 'set', 'contentGroup1', 'MappingTheFuture' );\n";
				} elseif ( 'page-neighborhoods.php' === get_page_template_slug() ) {
					echo "ga( 'set', 'contentGroup1', 'MappingTheFuture' );\n";
				}

				/*
				 * Content Group 2 "election 2017" in response to https://secure.helpscout.net/conversation/421881188/1229/?folderId=1259187
				 */
				if (
					has_term( '2017-election', 'series' )
					|| has_term( 'campaign-2017-newswire', 'post_tag' )
					|| has_term( 'democracys-timetable-campaign-2017-schedules', 'post_tag' )
					|| has_term( 'district-data', 'post_tag' )
					|| has_term( 'max-murphy-podcasts', 'category' )
					// below here are specific sections
					|| 20726 === $post->ID // https://citylimits.org/citizens-toolkit/
					|| 1750692 === $post->ID // https://citylimits.org/campaign-2017-candidate-debate-calendar/
					|| 1663505 === $post->ID // https://citylimits.org/politistat-2017/
					|| 1667230 === $post->ID // https://citylimits.org/our-2017-political-polls-vote-here-see-results/
					|| 1664887 === $post->ID // https://citylimits.org/lookback-dispatches-from-new-york-city-campaign-history/
					|| 1685102 === $post->ID // https://citylimits.org/a-users-guide-to-new-york-citys-elected-positions/
					|| 1663553 === $post->ID // https://citylimits.org/mayoral-race-2017/
					|| 1663554 === $post->ID // https://citylimits.org/council-races-2017/
				) {
					echo "ga( 'set', 'contentGroup2', 'election2017' );\n";
				} elseif ( 'page-neighborhoods.php' === get_page_template_slug() ) {
					echo "ga( 'set', 'contentGroup2', 'election2017' );\n";
				}
			} elseif ( is_tax() || is_archive() ) {
				// Term archives

				$term = $wp_query->get_queried_object();
				if (
					$term->slug === 'zonein'
					|| $term->slug === 'futuremap'
					|| $term->slug === 'zonein-espanol'
					|| $term->taxonomy === 'neighborhoods'
				) {
					echo "ga( 'set', 'contentGroup1', 'MappingTheFuture' );\n";
				}
				if (
					'2017-election' === $term->slug
					|| 'campaign-2017-newswire' === $term->slug
					|| 'democracys-timetable-campaign-2017-schedules' === $term->slug
					|| 'district-data' === $term->slug
					|| 'max-murphy-podcasts' === $term->slug
				) {
					echo "ga( 'set', 'contentGroup2', 'election2017' );\n";
				}
			}
			?>

			ga( 'send', 'pageview' );
		</script>
		<?php
	}
}
add_action( 'wp_head', 'citylimits_google_analytics' );

/**
 * Taboola code
 */
function citylimits_taboola_header() {
	?>
	<script type="text/javascript">
		window._taboola = window._taboola || [];
		_taboola.push(
		{article:'auto'}
		);
		!function (e, f, u)
		{ e.async = 1; e.src = u; f.parentNode.insertBefore(e, f); }
		(document.createElement('script'),
		document.getElementsByTagName('script')[0],
		'//cdn.taboola.com/libtrc/citylimit/loader.js');
	</script>
	<?php
}
add_action( 'wp_head', 'citylimits_taboola_header' );


function citylimits_taboola_footer() {
	?>
	<script type="text/javascript">
		window._taboola = window._taboola || [];
		_taboola.push(
		{flush: true}
		);
	</script>
	<?php
}
add_action( 'wp_footer', 'citylimits_taboola_footer' );
