<?php

/**
 * Remove the Constant Contact `wpmu_signup_user_notification` filter when creating a new user
 * via the admin dashboard as it prevents confirmation emails from going out.
 *
 * See line 77 of constant-contact-api.php
 */
function remove_cc_registration_filter() {
	global $pagenow;

	if ($pagenow == 'user-new.php')
		remove_filter('wpmu_signup_user_notification', 'constant_contact_register_post_multisite', 10);
}
add_action('plugins_loaded', 'remove_cc_registration_filter', 2);
