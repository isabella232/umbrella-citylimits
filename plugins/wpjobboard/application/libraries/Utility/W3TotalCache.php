<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of W3TotalCache
 *
 * @author Grzegorz
 */
class Wpjb_Utility_W3TotalCache 
{
    public static function connect()
    {
        if(!defined("W3TC_CONFIG_DIR")) {
            return;
        }
        
        $keys = include W3TC_CONFIG_DIR . "/master.php";
        
        $cache_rejected_uri = $keys["pgcache.reject.uri"];
        $my_rejected_uri = $cache_rejected_uri;
        
        $appended = false;
        $instance = Wpjb_Project::getInstance();
        
        $pagej = "/".$instance->getApplication("frontend")->getPage()->post_name."*";
        $pager = "/".$instance->getApplication("resumes")->getPage()->post_name."*";
        
        if(!in_array($pagej, $cache_rejected_uri)) {
            $my_rejected_uri[] = $pagej;
            $appended = true;
        }
        
        if(!in_array($pager, $cache_rejected_uri)) {
            $my_rejected_uri[] = $pager;
            $appended = true;
        }
        
        if(!$appended) {
            return;
        }
        
        $writer = new W3_ConfigWriter("master", false);
        $writer->set("pgcache.reject.uri", $my_rejected_uri);
        $writer->save();
        
        include_once W3TC_LIB_W3_DIR."/PgCacheFlush.php";
        
        $w3_pgcache = new W3_PgCacheFlush;
        $w3_pgcache->flush();
    }
}

?>
