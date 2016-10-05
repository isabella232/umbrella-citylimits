<?php

ob_start();

include "../../../wp-load.php";

global $wpjobboard;

$clean = $_GET["url"];
$clean = str_replace("..", "", $clean);

list($type, $id, $path) = explode("/", $clean, 3);

$file = wpjb_upload_dir($type, "", $id, "basedir")."".$path;
$finfo = wp_check_filetype_and_ext($file, basename($file));

$isAllowed = false;

$ini = Daq_Config::parseIni(
    $wpjobboard->path("app_config")."/admin-menu.ini",
    $wpjobboard->path("user_config")."/admin-menu.ini",
    true
);

if($type == "application") {
    $application = new Wpjb_Model_Application($id);
    $job = new Wpjb_Model_Job($application->job_id);
    
    if(!is_null($job->employer_id) && $job->employer_id == Wpjb_Model_Company::current()->id) {
        $isAllowed = true;
    }
} elseif($type == "resume") {
    // do something here ... ?
}

if(current_user_can($ini["applications"]["access"]) || current_user_can("edit_files")) {
    $isAllowed = true;
}

$isAllowed = apply_filters("wpjb_restrict", $isAllowed, $clean, $file);

ob_end_clean();

if($isAllowed) {
    header('Content-type: '.$finfo["type"]);
    header('Content-Disposition: inline; filename="'.basename($file).'"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($file));
    header('Accept-Ranges: bytes');

    @readfile($file);
} else {
    wp_die(__("You are not allowed to access this file.", "wpjobboard"));
}



?>
