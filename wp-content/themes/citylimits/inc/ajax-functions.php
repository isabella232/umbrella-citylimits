<?php
use DrewM\MailChimp\MailChimp;
/**
 * A file of functions modifying Largo's inc/ajax-functions.php
 * Primarily used for LMP modifications
 */

/**
 * set the correct partial for LMP posts at /zonein-events/
 * @since Largo 0.5.5.3
 * @filter largo_lmp_template_partial
 */
function citylimits_zonein_events_lmp_template_partial( $partial, $query ) {
	if ( is_array($query->query) ) {
		$args = $query->query;
		if ( $args['post_type'] === 'zonein_events' ) {
			$partial = 'zoneinevents';
		}

	}
	return $partial;
}
add_filter( 'largo_lmp_template_partial', 'citylimits_zonein_events_lmp_template_partial', 10, 2 );

/**
 * Filter the LMP WP_Query by neighborhood on neighborhood post types
 */
function citylimits_neighborhood_archive_lmp_query( $config ) {
	if ( $config['query']['post-type'] === 'news' && isset( $_GET['neighborhood'] ) && ! empty( $_GET['neighborhood'] ) ) {
		$config['query']['tax_query'] = array(
			array(
				'taxonomy' => 'neighborhoods',
				'field' => 'slug',
				'terms' => sanitize_key( $_GET['neighborhood'] ),
			),
		);
	}
	return $config;
}
add_action( 'largo_load_more_posts_json', 'citylimits_neighborhood_archive_lmp_query', 1 );


if ( !function_exists( 'cl_mc_signup' ) ) {
	/**
	 * Signs up for MailChimp newsletter(s)
	 */
	function cl_mc_signup() {
		require_once(get_stylesheet_directory() . '/lib/MailChimp.php');
		$mailchimp_api_key = get_field('mailchimp_api_key', 'option');
		$list_id = get_field('list_id', 'option');
		$MC = new MailChimp($mailchimp_api_key);

		foreach ($_REQUEST['newsletters'] as $newsletter) {
			$interests[$newsletter] = true;
		}
	
		/*
		$result = $MC->post("lists/$list_id/members", [
			'email_address' => $_REQUEST['email'],
			'status' => 'subscribed',
			'merge_fields' => ['FNAME' => $_REQUEST['fname'], 'LNAME' => $_REQUEST['lname']],
			'interests' => $interests
		]);
		*/
		$email_hash = md5(strtolower($_REQUEST['email']));
		$result = $MC->put("lists/$list_id/members/$email_hash", [
			'email_address' => $_REQUEST['email'],
			'status_if_new' => 'subscribed',
/*			'merge_fields' => ['FNAME' => $_REQUEST['fname'], 'LNAME' => $_REQUEST['lname']],*/ 
			'merge_fields' => ['FNAME' => $_REQUEST['fname']],
			'interests' => $interests
		]);
		
		if ($result['status'] == 'subscribed') {
			$response['message'] = get_field('thank_you_text', 'option');
			$response['status'] = 'success';
		} else {
			$response['message'] = get_field('error_text', 'option');
			$response['message'] .= "<p>$result[detail]</p>";
			$response['status'] = 'error';
		}
		wp_send_json( array( $response ) );
		die();
	}
	add_action("wp_ajax_cl_mc_signup", "cl_mc_signup");
	add_action("wp_ajax_nopriv_cl_mc_signup", "cl_mc_signup");
}
