<?php
/*
Plugin Name: Largo Extended
Plugin URI: https://bitbucket.org/projectlargo/largo-extended
Description: Extensions and enhancements to the core Largo theme
Author: INN with an assist from Cornershop Creative
Version: 0.1
Author URI: http://investigativenewsnetwork.org/
*/

// Setup some contants we'll need in various places
define('LARGO_EXT_DIR', dirname(__FILE__));
define('LARGO_EXT', __FILE__);

/*
 * For the Largo network sites we add an additional Google Analytics tag to track INN members
 * XXXX: Not sure if we can redeclare this here or not...
 */
if (!function_exists('largo_google_analytics')) :
	function largo_google_analytics() {
		if ( !is_user_logged_in() ) : // don't track logged in users ?>
			<script>
				var _gaq = _gaq || [];
			<?php if ( of_get_option( 'ga_id', true ) ) : // make sure the ga_id setting is defined ?>
				_gaq.push(['_setAccount', '<?php echo of_get_option( "ga_id" ) ?>']);
				_gaq.push(['_trackPageview']);
			<?php endif; ?>
				_gaq.push(
					["inn._setAccount", "UA-17578670-2"],
					["inn._setCustomVar", 1, "MemberName", "<?php bloginfo('name') ?>"],
					["inn._trackPageview"]
				);
				_gaq.push(
					["largo._setAccount", "UA-17578670-4"],
					["largo._setCustomVar", 1, "SiteName", "<?php bloginfo('name') ?>"],
					["largo._trackPageview"]
				);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
			</script>
		<?php endif;
	}
	add_action( 'wp_footer', 'largo_google_analytics' );
endif;


function largo_extended_init() {
	// Loop thru optional includes and load them if the plugin is active
	$includes = array('/inc/registration.php');

	// Load our plugin customizations (only if the plugins are installed and active)
	$optional_includes = array(
		'wpjobboard/index.php' => '/inc/job-board.php', //WP Job Board plugin
		'gravityforms/gravityforms.php' => '/inc/gravityforms/events-calendar.php',
	);

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	foreach ( $optional_includes as $plugin => $include_file ) {
		if (is_plugin_active($plugin))
			array_push($includes, $include_file);
	}

	foreach ( $includes as $include ) {
		require_once( LARGO_EXT_DIR . $include );
	}
}
add_action('init', 'largo_extended_init');
