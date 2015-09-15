<?php

define('SHOW_STICKY_NAV', false);
define('SHOW_CATEGORY_RELATED_TOPICS', false);


/**
 * re-enable the default WP RSS widget
 */
function citylimits_widgets_init() {
	register_widget('WP_Widget_RSS');
}
add_action( 'widgets_init', 'citylimits_widgets_init', 11 );


/**
 * Set the number of posts in the right-hand side of the Top Stories homepage template to 2.
 *
 * Largo's default is 6. CityLimits does not want the "More headlines" area to appear, which appears if 4 or more posts are in the area.
 *
 * @return 2
 * @param int $showstories
 */
function citylimits_featured_stories_count( $showstories ) {
	return 2;
}
add_filter('largo_homepage_topstories_post_count', 'citylimits_featured_stories_count');


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


/* Don't use WPJB css */
add_action('wpjb_inject_media', function($media) {
	$media['css'] = false;
	return $media;
});

/* Utilities for loading default job types and categories */
function reset_job_categories_and_types() {
	$directory = get_stylesheet_directory();
	if (file_exists($directory . '/config.php'))
		include_once $directory . '/config.php';
	else
		return false;

	$query = new Daq_Db_Query();
	$query->select('*')->from('Wpjb_Model_Category t1');
	$categories = $query->execute();
	foreach ($categories as $category)
		$category->delete();

	$query = new Daq_Db_Query();
	$query->select('*')->from('Wpjb_Model_JobType t1');
	$types = $query->execute();
	foreach ($types as $type)
		$type->delete();

	if (!empty($wpjobboard_job_types)){
		set_job_types($wpjobboard_job_types);
	}
	if (!empty($wpjobboard_categories)) {
		set_job_categories($wpjobboard_categories);
	}

	return true;
}

function set_job_categories($categories) {
	foreach ($categories as $category_attrs) {
		$cat = new Wpjb_Model_Category();
		foreach ($category_attrs as $k => $v) {
			$cat->set($k, $v);
		}
		$cat->save();
	}
}

function set_job_types($types) {
	foreach ($types as $type_attrs) {
		$jtype = new Wpjb_Model_JobType();
		foreach ($type_attrs as $k => $v) {
			$jtype->set($k, $v);
		}
		$jtype->save();
	}
}

/* Custom registration link */
add_filter('register', function($link) {
	return '<a href="' . site_url('/register') . '">Register</a>';
});


/* Custom fields for user registration */
function citylimits_custom_signup_fields_early($values) {
	extract($values);
?>

	<div class="form-group">
		<label for="organization"><?php _e('Organization name (optional)', 'citylimits'); ?></label>
		<input type="text" value="<?php if (!empty($organization)) { echo $organization; } ?>" name="organization">

	<?php if ( $errmsg = $errors->get_error_message('organization') ) { ?>
		<p class="alert alert-error"><?php echo $errmsg; ?></p>
	<?php } ?>
	</div>
<?php
}
add_action('signup_extra_fields', 'citylimits_custom_signup_fields_early', 1, 2);

function citylimits_custom_signup_fields_late($values) {
	extract($values);
?>
	<div class="form-group">
		<label for="recaptcha_response_field"><?php _e('Are you human?', 'citylimits'); ?></label>
	<?php
		/* ReCaptcha */
		require_once('lib/recaptchalib.php');
		echo recaptcha_get_html(RECAPCHA_PUBLIC_KEY);
		if ($errmsg = $errors->get_error_message('recaptcha')) { ?>
			<p class="alert alert-error"><?php echo $errmsg; ?></p>
	<? } ?>
	</div>
<?php
}
add_action('signup_extra_fields', 'citylimits_custom_signup_fields_late', 10, 2);

/**
 * Verify citylimits custom signup fields applied above.
 *
 * @param $result. array. The $_POST variables from the form to validate.
 * @param $extras. array. ??
 */
function citylimits_verify_custom_signup_fields($result, $extras=null) {
	/* Check the reCaptcha */
	require_once('lib/recaptchalib.php');
	$privatekey = RECAPCHA_PRIVATE_KEY;

	$resp = recaptcha_check_answer(
		$privatekey, $_SERVER["REMOTE_ADDR"], $result["recaptcha_challenge_field"],
		$result["recaptcha_response_field"]);

	/* Check the Captcha */
	if (!$resp->is_valid) {
		$result['errors']->add('recaptcha',__('The entered captcha was incorrect','citylimits'));
		return $result;
	} else {
		return $result;
	}
}
add_action('largo_validate_user_signup_extra_fields', 'citylimits_verify_custom_signup_fields');

function citylimits_user_profile_fields($user) {
	$organization = get_user_meta($user->ID, 'organization', true);
	$mailing_id = get_user_meta($user->ID, 'mailing_id', true);
	?>
	<h3>Other preferences</h3>

	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="organization"><?php _e('Organization name', 'citylimits'); ?></label></th>
				<td><input type="text" value="<?php if (!empty($organization)) { echo $organization; } ?>" name="organization"></td>
			</tr>
		</tbody>
	</table>

	<?php
}
add_action('show_user_profile',  'citylimits_user_profile_fields');

function citylimits_save_user_profile_fields($user_id) {
	if (!empty($_POST))
		update_user_meta($user_id, 'organization', $_POST['organization']);
}
add_action('personal_options_update', 'citylimits_save_user_profile_fields');

/* Google ad tags */
function googletag_scripts() { ?>
<script type='text/javascript'>
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
	var gads = document.createElement('script');
	gads.async = true;
	gads.type = 'text/javascript';
	var useSSL = 'https:' == document.location.protocol;
	gads.src = (useSSL ? 'https:' : 'http:') +
	'//www.googletagservices.com/tag/js/gpt.js';
	var node = document.getElementsByTagName('script')[0];
	node.parentNode.insertBefore(gads, node);
})();
</script>
<script type='text/javascript'>
	googletag.cmd.push(function() {
		googletag.defineSlot('/1291657/Brooklyn_Ad_Slot1_300x250', [300, 250], 'div-gpt-ad-1367765273715-0').addService(googletag.pubads());
		googletag.defineSlot('/1291657/Brooklyn_Ad_Slot2_300x250', [300, 250], 'div-gpt-ad-1367765273715-1').addService(googletag.pubads());
		googletag.defineSlot('/1291657/Brooklyn_Leaderboard_Ad_Slot', [728, 90], 'div-gpt-ad-1367765273715-3').addService(googletag.pubads());
		googletag.pubads().enableSingleRequest();
		googletag.enableServices();
	});
</script>
<?php
}
add_action('wp_head', 'googletag_scripts');

function largo_header() {
	$header_tag = is_home() ? 'h1' : 'h2'; // use h1 for the homepage, h2 for internal pages

	// if we're using the text only header, display the output, otherwise this is just replacement text for the banner image
	$header_class = of_get_option( 'no_header_image' ) ? 'branding' : 'visuallyhidden';
	$divider = $header_class == 'branding' ? '' : ' - ';

	// print the text-only version of the site title
	printf('<%1$s class="%2$s"><a itemprop="url" href="%3$s"><span itemprop="name">%4$s</span>%5$s<span class="tagline" itemprop="description">%6$s</span></a></%1$s>',
		$header_tag,
		$header_class,
		esc_url( home_url( '/' ) ),
		esc_attr( get_bloginfo('name') ),
		$divider,
		esc_attr( get_bloginfo('description') )
	);

	// add an image placeholder, the src is added by largo_header_js() in inc/enqueue.php
	if ( $header_class != 'branding' )
		echo '<a itemprop="url" href="' . esc_url( home_url( '/' ) ) . '"><img class="header_img" src="" alt="" /></a>';

	if ( of_get_option( 'logo_thumbnail_sq' ) )
		echo '<meta itemprop="logo" content="' . esc_url( of_get_option( 'logo_thumbnail_sq' ) ) . '"/>';
}

/* Customize job add page title */
function customize_job_add_page_title($arg) {
	if (trim($arg) == 'Create Ad')
		return 'Post a job';
	else
		return $arg;
}
add_filter("wpjb_set_title", 'customize_job_add_page_title');

function cl_widgets() {
	unregister_widget( 'TribeCountdownWidget' );
}
add_action( 'widgets_init', 'cl_widgets', 14 );

/* Remove the largo logo from login page */
add_action('init', function() {
	remove_action('login_head', 'largo_custom_login_logo');
});

/* Show City Limits logo on login page */
function citylimits_custom_login_logo() {
	echo '
		<style type="text/css">
			.login h1 a {
				background-image: url(' . get_stylesheet_directory_uri() . '/img/citylimits.png) !important;
				background-size: 200px 200px;
				height: 200px;
				width: 200px;
			}
		</style>
	';
}
add_action('login_head', 'citylimits_custom_login_logo');

function citylimits_login_redirect($redirect_to, $request, $user) {
	if (isset($_GET['redirect_to']) || isset($_POST['redirect_to']))
		return $redirect_to;
	else
		return home_url();
}
add_filter('login_redirect', 'citylimits_login_redirect', 10, 3);

function citylimits_job_query($select) {
	$select->order(
		"t1.is_featured DESC, t1.job_created_at DESC, t1.id DESC, IF(t1.company_url NOT LIKE '%indeed.com%', 1, 0) DESC, t1.id DESC");
	return $select;
}
add_filter('wpjb_jobs_query', 'citylimits_job_query', 1, 10);

function citylimits_users_can_register($option) {
	return true;
}
add_filter('pre_option_users_can_register', 'citylimits_users_can_register', 1, 10);
