<?php
/**
 * Functions for hooking in various sidebars, stylesheets, etc for the wpjobboard plugin
 */

/**
 * Enqueue custom sidebar styles
 */
function largo_jobboard_enqueue() {
	wp_enqueue_style('job-board-styles', get_stylesheet_directory_uri() . '/css/job-board.css', false, '20170609', 'screen');
}
add_action('wp_enqueue_scripts', 'largo_jobboard_enqueue' );

/**
 * Register the jobboard-widgets sidebar
 */
function largo_jobboard_register_sidebar() {
	register_sidebar( array(
		'name' 			=> __( 'Job Board', 'largo' ),
		'description' 	=> __( 'A widget area on job board pages', 'largo' ),
		'id' 			=> 'jobboard-widgets',
		'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widgettitle">',
		'after_title' 	=> '</h3>',
	) );
}
add_action('widgets_init', 'largo_jobboard_register_sidebar');

/**
 * Output jobboard-widgets sidebar if we're on a WPJobBoard page AND jobboard-widgets is active.
 */
function largo_jobboard_output_sidebar() {
	if ( largo_is_job_page() && is_active_sidebar( 'jobboard-widgets' )) {
		dynamic_sidebar( 'jobboard-widgets' );
	}
}
add_action('largo_after_sidebar_widgets', 'largo_jobboard_output_sidebar');

/**
 * Tests if the current page is a part of the WPJobBoard plugin
 */
function largo_is_job_page() {
	$jobboardOptions = get_option('wpjb_config', NULL);

	if (is_array($jobboardOptions))
		$wpjb_page_ids = array($jobboardOptions['link_jobs'], $jobboardOptions['link_resumes']);
	else
		return false;

	if (is_singular()) {
		global $post;
		if (in_array($post->ID, $wpjb_page_ids))
			return true;
	}

	return false;
}

/**
 * Loads custom WPJobBoard templates
 */
function largo_load_wpjoboard_templates($frontend, $result) {
	$view = $frontend->controller->view;
	$view->addDir(LARGO_EXT_DIR . '/templates/job-board', true);
}
add_action('wpjb_front_pre_render', 'largo_load_wpjoboard_templates', 0, 2);

/**
 * Loads custom WPJobBoard widget templates
 */
function largo_load_wpjoboard_widget_templates($view) {
	$view->addDir(LARGO_EXT_DIR . '/templates/widgets', true);
	return $view;
}
add_filter('daq_widget_view', 'largo_load_wpjoboard_widget_templates', 10, 1);
