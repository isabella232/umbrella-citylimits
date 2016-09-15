<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rewrite
 *
 * @author Grzegorz
 */
class Wpjb_Utility_Rewrite {

    protected $_rewrites = null;
    
    protected $_config = null;
    
    public function __construct() 
    {
        $rewrites = wpjb_shortcode_rewrites();
        
        $this->_rewrites = $rewrites["rewrites"];
        $this->_config = $rewrites["config"];
    }
    
    public function getRewrites()
    {
        return $this->_rewrites;
    }
    
    public function getGlue($url = null) {
        
        if($url === null) {
            $url = get_the_permalink();
        }
        
        if(get_option('permalink_structure')) {
            $glue = "/";
        } elseif(stripos($url, "?")===false) {
            $glue = "?";
        } else {
            $glue = "&";
        }
        
        return $glue;
    }
    
    public function findLink($link)
    {
        foreach($this->getRewrites() as $k => $v) {
            foreach($v["links"] as $lk => $lv) {
                if($lk == $link) {
                    return array("param"=>$v["param"], "link"=>$lv);
                }
            }
        }
        
        return null;
    }
    
    public function findRoute($path)
    {
        foreach($this->getRewrites() as $k => $v) {
            foreach($v["links"] as $lk => $lv) {
                if($lv == $path) {
                    return $lk;
                }
            }
        }
        
        return null;
    }
    
    public function linkTo($key, $object = null, $param = null, $page_id = null)
    {
        $glue = $this->getGlue();
        $link = $this->findLink($key);
        $qstring = array();
        
        if(!empty($link["link"])) {
            $path = $link["link"]."/";
        } else {
            $path = "";
        }
        
        if($object && stripos($path, "/id")) {
            $path = str_replace("/id", "/".$object->id, $path);
        }
        if($object && stripos($path, "/slug") && $object->get("slug")!=null) {
            $path = str_replace("/slug", "/".$object->slug, $path);
        }
        
        foreach($param as $k => $v) {
            if(stripos($path, "/".$k)) {
                $path = str_replace("/".$k, "/".$v, $path);
            } else {
                $qstring[$k] = $v;
            }
        }

        $front_page_id = get_option( 'page_on_front' );
        
        if(get_option('permalink_structure')) {
            if($front_page_id != $page_id) {
                $url = rtrim(get_the_permalink($page_id), "/").$glue.$path;
            } else {
                $url = rtrim(_get_page_link($page_id), "/").$glue.$path;
            }
            if(!empty($qstring)) {
                $url .= "?".http_build_query($qstring);
            }
        } else {

            if(!isset($link["param"])) {
                $params = array();
                $append = "";
            } else {
                $a1 = (array)$link["param"];
                $a2 = explode("/", trim($path, "/"));
                $a3 = explode("/", trim($link["link"], "/"));
                
                $c1 = count($a1);
                $c2 = count($a2);
                $c3 = count($a3);
                
                if($c1 < $c2 && $c2 == $c3) {
                    switch($a3[$c3-1]) {
                        case "id": $a1[] = "wpjbid"; break;
                        case "slug": $a1[] = "wpjbslug"; break;
                    }
                }
                
                #var_dump($link); var_dump($a2);
                
                $params = array_combine($a1, $a2);
                $append = $glue. http_build_query($params);
            }
            
            $url = rtrim(get_the_permalink($page_id), "/").$append;
        }
        
        return $url;
    }
    
    public function resolve($default = null, $narrow = null) 
    {
        foreach($this->getRewrites() as $k => $v) {
            
            if(!is_null($narrow) && $narrow!=$k) {
                continue;
            }
            
            $matched = false;
            $matches = array();
            $numeric = array();
            
            $vars = array_merge((array)$v["param"], array_values($this->_config["vars"]));

            foreach($vars as $p) {
                if(get_query_var($p)) {
                    $matched = true;
                    $matches[$p] = get_query_var($p);
                    $numeric[] = get_query_var($p);
                }
            }
            
            if(!$matched) {
                continue;
            }
            
            $path = join("/", $matches);
            
            foreach($v["links"] as $lk => $lv) {
                
                $isId = stripos($lv, "/id");
                $isSl = stripos($lv, "/slug");
                
                $regex = $lv;
                $regex = str_replace("/id", "/[0-9]{1,}", $regex);
                $regex = str_replace("/slug", "/[a-z0-9\-_]{1,}", $regex);
                $param = array();
                
                if(empty($regex)) {
                    $matched = empty($path);
                } elseif($isId || $isSl) {
                    $matched = preg_match("#".$regex."#", $path);
                } else {
                    $matched = $path == $regex;
                }
                
                if($matched) {
                    
                    $parts = explode("/", $lv);
                    $count = count($parts);
                    
                    for($i=1; $i<$count; $i++) {
                        $param[$parts[$i]] = $numeric[$i];
                    }
                    
                    
                    return array(
                        "route" => $lk,
                        "param" => $param
                    );
                }
            }
        }
        
        return array(
            "route" => $default,
            "param" => array()
        );
    }
    
    public function convertRoute($route, $resolved) 
    {
        $result = array(
            "param" => $resolved["param"],
            "route" => $resolved["route"],
            "module" => $route["module"],
            "action" => $route["action"],
            "path" => null,
            "object" => null
        );

        if(isset($route["model"])) {
            $object = new stdClass();
            $object->objClass = $route["model"];
            $result["object"] = $object;
        } 
        
        return $result;
    }
    
    public function titles($title, $id = null) 
    {
        if( !is_numeric($id) ) {
            $id = get_the_ID();
        }
        
        $ok = false;
        $instance = Wpjb_Project::getInstance();
        $arr = array("urls_link_job", "urls_link_job_add", "urls_link_emp_panel", "urls_link_cand_panel");
        
        foreach($arr as $key) {
            if($instance->conf($key) == $id) {
                $ok = true;
                break;
            }
        }

        if(!$ok || !$instance->doTitle()) {
            return $title;
        }
        
        $resolved = $this->resolve();
        $route = $this->getOldRoute($resolved["route"]);
        $result = $this->convertRoute($route, $resolved);
        $object = null;

        if($resolved["route"] === null) {
            return $title;
        }

        if(isset($result["object"]->objClass) && isset($result["param"]["id"])) {
            $cl = $result["object"]->objClass;
            $object = new $cl($result["param"]["id"]);
        } elseif(isset($result["object"]->objClass) && isset($result["param"]["slug"])) {
            $cl = $result["object"]->objClass;
            $query  = new Daq_Db_Query();
            $query->from($cl." t");
            $query->where("slug = ?", $result["param"]["slug"]);
            $query->limit(1);
            $list = $query->execute();
            
            if(isset($list[0])) {
                $object = $list[0];
            }
            
        } else {
            $object = new stdClass();
            $object->applicant_name = null;
            $object->job_title = null;
            $object->title = null;
            $object->price = null;
            $object->type = null;
        }
        
        if(!$object) {
            return $title;
        }
        
        $r = $resolved["route"];
        
        $titles = array(
            "step_add" => __("Create Ad", "wpjobboard"), 
            "step_preview" => __("Preview", "wpjobboard"), 
            "step_save" => __("Publish", "wpjobboard"), 
            "step_reset" => null, 
            "step_complete" => __("Complete", "wpjobboard"),
            
            "job_edit" => __("Edit Job", "wpjobboard"),
            "job_delete" => __("Delete Job", "wpjobboard"),
            "employer_new" => null,        
            "employer_login" => null,   
            "employer_logout" => null,
            "employer_edit" => __("Company Profile", "wpjobboard"),
            "employer_panel" => __("Active Listings", "wpjobboard"),
            "employer_panel_expired" => __("Expired Listings", "wpjobboard"),
            "employer_password" => __("Change Password", "wpjobboard"),
            "employer_delete" => __("Delete Account", "wpjobboard"),
            "employer_verify" => __("Request Manual Verification", "wpjobboard"),
            "job_application" => sprintf(__("Application By '%s'", "wpjobboard"), $object->applicant_name),
            "job_applications" => sprintf(__("'%s' Applications", "wpjobboard"), $object->job_title),      
            "membership_details" => sprintf(__("Package Details: '%s'", "wpjobboard"), $object->title),
            "membership_purchase" => $object->price ? __("Activate Membership", "wpjobboard") : __("Purchase Membership", "wpjobboard"),
            "membership" => __("Membership", "wpjobboard"),
            "employer_home"  => __("Employer Dashboard", "wpjobboard"),
           
            "myresume_detail_add" => get_query_var("wpjbslug")=="experience" ? __("Add Work Experience", "wpjobboard") : __("Add Education", "wpjobboard"),
            "myresume_detail_edit" => $object->type==1 ? __("Edit Work Experience", "wpjobboard") : __("Edit Education", "wpjobboard"),
            "myresume_edit" => __("My Resume Details", "wpjobboard"),
            "myresume_password" => __("Change Password", "wpjobboard"),
            "myresume_delete" => __("Delete Account", "wpjobboard"),
            "myapplications" => __("My Applications", "wpjobboard"),
            "mybookmarks" => __("My Bookmarks", "wpjobboard"),
            "myresume" => __("My Resume Details", "wpjobboard"),
            "myresume_home" => __("My Dashboard", "wpjobboard"),
            
            "type" => sprintf(__("Job Type: %s", "wpjobboard"), $object->title),
            "category" => sprintf(__("Category: %s", "wpjobboard"), $object->title),
        );
        
        if(isset($titles[$r]) && !empty($titles[$r])) {
            return apply_filters("wpjb_rewrite_titles", $titles[$r], $r, $object);
        } else {
            return $title;
        }
        
    }
    
    public function getOldRoute($route, $app = null)
    {
        if($app == null) {
            $app = array("frontend", "resumes");
        } else {
            $app = array($app);
        }
        
        $instance = Wpjb_Project::getInstance();
        
        foreach($app as $a) {
            $routed = $instance->router($a)->getRoute($route);
            
            if(!is_null($routed)) {
                return $routed;
            }
        }
    }
}

?>
