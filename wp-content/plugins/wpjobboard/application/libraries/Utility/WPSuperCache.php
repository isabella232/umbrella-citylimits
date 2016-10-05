<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WPSuperCache
 *
 * @author Grzegorz
 */
class Wpjb_Utility_WPSuperCache 
{
    public static function connect()
    {
        global $cache_rejected_uri, $wp_cache_config_file, $file_prefix;
        
        if(!isset($cache_rejected_uri) || !function_exists("wp_cache_sanitize_value")) {
            return;
        }
        
        $appended = false;
        $instance = Wpjb_Project::getInstance();
        
        $pagej = null;
        $pager = null;
        
        if(isset($instance->getApplication("frontend")->getPage()->post_name)) {
            $pagej = "/".$instance->getApplication("frontend")->getPage()->post_name."/";
        }
        
        if(isset($instance->getApplication("resumes")->getPage()->post_name)) {
            $pager = "/".$instance->getApplication("resumes")->getPage()->post_name."/";
        }
        
        if($pagej === null && $pager === null) {
            return;
        }
        
        $isj = in_array($pagej, $cache_rejected_uri);
        $isr = in_array($pager, $cache_rejected_uri);
        
        $my_rejected_uri = $cache_rejected_uri;
        
        if(!$isj) {
            $my_rejected_uri[] = $pagej;
            $appended = true;
        }
        
        if(!$isr) {
            $my_rejected_uri[] = $pager;
            $appended = true;
        }
        
        if(!$appended) {
            return;
        }
        
        $my_rejected_uri = implode("\n", $my_rejected_uri);
        
        $text = wp_cache_sanitize_value( str_replace( '\\\\', '\\', $my_rejected_uri ), $cache_rejected_uri );
	wp_cache_replace_line('^ *\$cache_rejected_uri', "\$cache_rejected_uri = $text;", $wp_cache_config_file);
        
        wp_cache_clean_cache($file_prefix);
    }
}

?>
