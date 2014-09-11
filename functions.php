<?php

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

/* Custom fields for user registration */
function citylimits_custom_signup_fields($values, $errors) {
	extract($values);
?>

	<div class="form-group">
		<label for="organization"><?php _e('Organization name', 'citylimits'); ?></label>
		<input type="text" value="<?php if (!empty($organization)) { echo $organization; } ?>" name="organization">

	<?php if ( $errmsg = $errors->get_error_message('organization') ) { ?>
		<p class="alert alert-error"><?php echo $errmsg; ?></p>
	<?php } ?>
	</div>

	<div class="form-group">
		<label for="mailing_id"><?php _e('I would like to receive the (please check all that apply)', 'citylimits'); ?></label>
		<input <?php if ($mailing_id && in_array('8', (array)$mailing_id)) {echo 'checked'; } ?> type="checkbox"
			value="8" name="mailing_id[]"> CityLimits.org Monthly Newsletter <br />
		<input <?php if ($mailing_id && in_array('1', (array)$mailing_id)) { echo 'checked'; } ?> type="checkbox"
			value="1" name="mailing_id[]"> CityLimits.org Weekly Newsletter<br />
		<input <?php if ($mailing_id && in_array('2', (array)$mailing_id)) { echo 'checked'; } ?> type="checkbox"
			value="2" name="mailing_id[]"> CityLimits.org: NYC Jobs Update <br />
		<input <?php if ($mailing_id && in_array('4', (array)$mailing_id)) { echo 'checked'; } ?> type="checkbox"
			value="4" name="mailing_id[]"> CityLimits.org: NYC Events Update<br />

	<?php if ( $errmsg = $errors->get_error_message('mailing_id') ) { ?>
		<p class="alert alert-error"><?php echo $errmsg; ?></p>
	<?php } ?>
	</div>


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
add_action('largo_registration_extra_fields', 'citylimits_custom_signup_fields', 10, 2);



/**
 * Verify citylimits custom signup fields applied above.
 *
 * @param $result. array. The $_POST variables from the form to validate.
 * @param $extras. array. ??
 */
function citylimits_verify_custom_signup_fields($result,$extras = null) {
	
	/* Check the reCaptcha */
	require_once('lib/recaptchalib.php');
	$privatekey = RECAPCHA_PRIVATE_KEY;
	
	$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $result["recaptcha_challenge_field"],
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

