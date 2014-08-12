<?php

add_action('wpjb_inject_media', function($media) {
	$media['css'] = false;
	return $media;
});

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
