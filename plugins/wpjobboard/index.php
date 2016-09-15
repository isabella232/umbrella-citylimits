<?php
/*
Plugin Name: WPJobBoard
Plugin URI: http://wpjobboard.net/
Description: Probably the most advanced yet user friendly job board plugin. The plugin allows to publish jobs, manage user resumes and applications. On activation it will create two Pages: "Jobs" and "Resumes", you might also want to add "Job Board Menu" and "Resumes Menu" widgets to the sidebar as they have all the navigation links. 
Author: Grzegorz Winiarski
Version: 4.3.4
Author URI: http://wpjobboard.net
*/

if(defined("WPJOBBOARD")) {
    return;
}

global $wpdb;

define("WPJB_MAX_DATE", "9999-12-31");

if(version_compare(PHP_VERSION, "5.2.0", "<")) {
    die("<b>Cannot activate:</b> WPJobBoard requires at least PHP 5.1.6, your PHP version is ".PHP_VERSION);
}

define("WPJOBBOARD", "wpjobboard");
$basepath = dirname(__FILE__);

$wpjobboard = null;
$wpjobboard_title = null;

if(!class_exists("Daq_Loader")) {
    require_once $basepath."/framework/Loader.php";
}

Daq_Loader::registerFramework($basepath."/framework");
Daq_Loader::registerAutoloader();

foreach((array)glob($basepath."/application/functions/*") as $wpjbfile) {
    include_once $wpjbfile;
}

Daq_Request::getInstance();
Daq_Db::getInstance()->setDb($wpdb);

$wpjbIni = Daq_Config::parseIni($basepath."/application/config/project.ini");
$wpjbPaths = Daq_Config::parseIni($basepath."/application/config/paths.ini");
Daq_Loader::registerLibrary($wpjbIni["prefix_class"], $basepath."/application/libraries");

$wpjobboard = Wpjb_Project::getInstance();
$wpjobboard->loadPaths($wpjbPaths);
$wpjobboard->setBaseDir($basepath);

foreach($wpjbIni as $wpjbk => $wpjbv) {
    $wpjobboard->setEnv($wpjbk, $wpjbv);
}

Daq_Helper::registerAll();

$routes = Daq_Config::parseIni(
    $wpjobboard->path("app_config")."/frontend-routes.ini",
    $wpjobboard->path("user_config")."/frontend-routes.ini",
    true
);

$wpjbbase = $wpjobboard->path("templates")."/";
$wpjobboard->setEnv("template_base", $wpjbbase);

$view = new Daq_View();
$view->addDir(get_stylesheet_directory()."/wpjobboard/job-board");
$view->addDir(get_template_directory()."/wpjobboard/job-board");
$view->addDir($wpjbbase."job-board");
$view->addHelper("flash", new Wpjb_Utility_Session);
$app = new Wpjb_Application_Frontend;
$app->setRouter(new Daq_Router($routes));
$app->setController("Wpjb_Module_Frontend_*");
$app->setView($view);
$app->setLog(new Daq_Log($wpjobboard->path("logs"), "error-front.txt", "debug-front.txt"));
$app->addOption("link_name", "link_jobs");
$app->addOption("query_var", "job_board");
$app->addOption("shortcode", "[wpjobboard-jobs]");

$routes = Daq_Config::parseIni(
    $wpjobboard->path("app_config")."/resumes-routes.ini",
    $wpjobboard->path("user_config")."/resumes-routes.ini",
    true
);

$view = new Daq_View();
$view->addDir(get_stylesheet_directory()."/wpjobboard/resumes");
$view->addDir(get_template_directory()."/wpjobboard/resumes");
$view->addDir($wpjbbase."resumes");
$view->addHelper("flash", new Wpjb_Utility_Session);
$res = new Wpjb_Application_Resumes();
$res->setRouter(new Daq_Router($routes));
$res->setController("Wpjb_Module_Resumes_*");
$res->setView($view);
$res->setLog(new Daq_Log($wpjobboard->path("logs"), "error-resumes.txt", "debug-resumes.txt"));
$res->addOption("link_name", "link_resumes");
$res->addOption("query_var", "job_resumes");
$res->addOption("shortcode", "[wpjobboard-resumes]");

$routes = Daq_Config::parseIni(
    $wpjobboard->path("app_config")."/admin-routes.ini",
    $wpjobboard->path("user_config")."/admin-routes.ini",
    true
);

$view = new Daq_View($basepath.$wpjobboard->pathRaw("admin_views"));
$view->addHelper("url", new Daq_Helper_AdminUrl());
$view->addHelper("flash", new Daq_Helper_Flash_User("wpjb_admin_flash"));
$admin = new Wpjb_Application_Admin;
$admin->isAdmin(true);
$admin->setRouter(new Daq_Router($routes));
$admin->setLog(new Daq_Log($wpjobboard->path("logs"), "error-admin.txt", "debug-admin.txt"));
$admin->setController("Wpjb_Module_Admin_*");
$admin->setView($view);

$view = new Daq_View();
$view->addHelper("flash", new Wpjb_Utility_Session);
$wpjbApi = new Wpjb_Application_Api;
$wpjbApi->setRouter(new Daq_Router(Daq_Config::parseIni($wpjobboard->path("app_config")."/api-routes.ini", null, true)));
$wpjbApi->setController("Wpjb_Module_Api_*");
$wpjbApi->addOption("query_var", "wpjobboard");
$wpjbApi->setView($view);

$wpjobboard->addApplication("api", $wpjbApi);
$wpjobboard->addApplication("frontend", $app);
$wpjobboard->addApplication("resumes", $res);
$wpjobboard->addApplication("admin", $admin);

$wpjobboard->addUserWidgets($basepath."/widgets/*.php");

$wpjobboard->run();

