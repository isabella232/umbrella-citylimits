<?php
/**
 * Advanced Custom Fields config
 */

/**
 * create Options Page for Mailchimp newsletter settings, which are handled by ACF
 */
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Mailchimp Newsletter Settings',
		'menu_title'	=> 'CL Mailchimp',
		'menu_slug' 	=> 'cl-mailchimp-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}
