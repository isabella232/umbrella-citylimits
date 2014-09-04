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
function citylimits_custom_signup_fields($errors) {
?>
	<div class="form-group">
		<label for="membership_type"><?php _e('I am registering an', 'citylimits'); ?></label>
		<input value="1" checked="checked" name="membership_type" id="mt_1" type="radio" /> Individual<br />
		<input value="2" name="membership_type" id="mt_2" type="radio" /> Business or For-Profit Organization<br />
		<input value="3" name="membership_type" id="mt_3" type="radio" /> Non-Profit/Community Organization<br />
		<input value="4" name="membership_type" id="mt_4" type="radio" /> Employment Agency<br />
	<?php if ( $errmsg = $errors->get_error_message('membership_type') ) { ?>
		<p class="alert alert-error"><?php echo $errmsg; ?></p>
	<?php } ?>
	</div>

	<div class="form-group">
		<label for="borough"><?php _e('Location', 'citylimits'); ?></label>
		<select size="1" name="borough">
			<option selected value="0">(All Locations)</option>
			<option value="1">Manhattan</option>
			<option value="2">Brooklyn</option>
			<option value="3">Queens</option>
			<option value="4">Bronx</option>
			<option value="5">Staten Island</option>
			<option value="-1">Outside of NYC</option>
		</select>
	</div>

	<div class="form-group">
		<label for="mailing_id"><?php _e('I would like to receive the (please check all that apply)', 'citylimits'); ?></label>
		<input type="checkbox" value="8" name="mailing_id"> City Limits Monthly Newsletter <br />
		<input type="checkbox" value="1" name="mailing_id"> City Limits Weekly Newsletter<br />
		<input type="checkbox" value="2" name="mailing_id"> City Limits: NYC Jobs Update <br />
		<input type="checkbox" value="4" name="mailing_id"> City Limits: NYC Events Update<br />
	</div>

<?php
}
add_action('signup_extra_fields', 'citylimits_custom_signup_fields', 10, 1);
