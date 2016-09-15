<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Main
 *
 * @author greg
 */
class Wpjb_Module_AjaxNopriv_Map
{
    
    public static function dataAction()
    {
        $request = Daq_Request::getInstance();
        $olist = $request->post("objects");
        
        if(empty($olist)) {
            exit -3;
        }

        $objects = array_map("trim", explode(",", $olist));
        $data = array();
        
        if(in_array("jobs", $objects)) {
            $data += self::_jsonJobs();
        }
        
        if(in_array("resumes", $objects)) {
            $data += self::_jsonResumes();
        }
        
        if(in_array("companies", $objects)) {
            $data += self::_jsonCompanies();
        }
        
        echo json_encode($data);
        exit;
    }
    
    public static function detailsAction()
    {
        $request = Daq_Request::getInstance();
        
        switch($request->post("object")) {
            case "job": return self::_htmlJob(); break;
            case "resume": return self::_htmlResume(); break;
            case "company": return self::_htmlCompany(); break;
            default: echo "-1";  exit; break;
        }
        
        
    }
    
    protected static function _jsonJobs() 
    {
        $request = Daq_Request::getInstance();
        $post = $request->post();
        $post["ids_only"] = true;
        $post["page"] = 1;
        $post["count"] = apply_filters("wpjb_map_max_items", 1000);
        
        $list = wpjb_find_jobs($post);
        $json = array();
        
        foreach($list->job as $id) {
            $job = new Wpjb_Model_Job($id);
            $json[] = array(
                "type" => "Feature",
                "geometry" => array(
                    "type" => "Point",
                    "coordinates" => array(
                        $job->meta->geo_longitude->value(), 
                        $job->meta->geo_latitude->value()
                    ) // end coordinates
                ),
                "properties" => array(
                    "id" => $job->id,
                    "object" => "job",
                    "title" => $job->job_title
                ) // end properties
            ); // end $json[]
            unset($job);
        }
        
        return $json;
    }
    
    protected static function _jsonResumes() 
    {
        $request = Daq_Request::getInstance();
        $post = $request->post();
        $post["ids_only"] = true;
        $post["page"] = 1;
        $post["count"] = apply_filters("wpjb_map_max_items", 1000);
        
        $list = wpjb_find_resumes($post);
        $json = array();
        
        foreach($list->resume as $id) {
            $resume = new Wpjb_Model_Resume($id);
            $json[] = array(
                "type" => "Feature",
                "geometry" => array(
                    "type" => "Point",
                    "coordinates" => array(
                        $resume->meta->geo_longitude->value(), 
                        $resume->meta->geo_latitude->value()
                    ) // end coordinates
                ),
                "properties" => array(
                    "id" => $resume->id,
                    "object" => "resume",
                    "title" => $resume->headline
                ) // end properties
            ); // end $json[]
        }
        
        return $json;
    }
    
    protected static function _jsonCompanies() 
    {
        $request = Daq_Request::getInstance();
        $post = $request->post();
        $post["ids_only"] = true;
        $post["page"] = 1;
        $post["count"] = apply_filters("wpjb_map_max_items", 1000);
        
        $list = Wpjb_Model_Company::search($post);
        $json = array();
        
        foreach($list->company as $id) {
            $company = new Wpjb_Model_Company($id);
            $json[] = array(
                "type" => "Feature",
                "geometry" => array(
                    "type" => "Point",
                    "coordinates" => array(
                        $company->meta->geo_longitude->value(), 
                        $company->meta->geo_latitude->value()
                    ) // end coordinates
                ),
                "properties" => array(
                    "id" => $company->id,
                    "object" => "company",
                    "title" => $company->company_name
                ) // end properties
            ); // end $json[]
        }
        
        return $json;
    }
    
    protected static function _htmlJob()
    {
        $request = Daq_Request::getInstance();
        
        $job = new Wpjb_Model_Job($request->post("id"));
        
        if($job->exists() == false) {
            exit -2;
        }
        
        ?>
        <span class='wpjb-infobox-title'><?php esc_html_e($job->job_title) ?></span>
        <p><?php esc_html_e($job->company_name) ?></p>
        <p><a href="<?php esc_attr_e($job->url()) ?>">View Job Details <span class="wpjb-glyphs wpjb-icon-right-open"></span></a></p>
        <div class="wpjb-infobox-footer" style="background-color:<?php echo "#".$job->getTag()->type[0]->meta->color ?>">
            <span class="footer-icon wpjb-glyphs wpjb-icon-tags"></span>
            <small><?php esc_html_e($job->tag->type[0]->title) ?></small>
        </div>
        
        <?php
        exit;
    }
    
    protected static function _htmlResume()
    {
        $request = Daq_Request::getInstance();
        
        $resume = new Wpjb_Model_Resume($request->post("id"));
        
        if($resume->exists() == false) {
            exit -2;
        }
        
        ?>
        <span class='wpjb-infobox-title'><?php esc_html_e(apply_filters("wpjb_candidate_name", $resume->getSearch(true)->fullname, $resume->id)) ?></span>
        <p><?php esc_html_e($resume->headline) ?></p>
        <p><a href="<?php esc_attr_e($resume->url()) ?>">View Resume Details <span class="wpjb-glyphs wpjb-icon-right-open"></span></a></p>
        <div class="wpjb-infobox-footer">
            <span class="footer-icon wpjb-glyphs wpjb-icon-tags"></span>
            <small><?php esc_html_e($resume->tag->category[0]->title) ?></small>
        </div>
        
        <?php
        exit;
    }
    
    protected static function _htmlCompany()
    {
        $request = Daq_Request::getInstance();
        
        $company = new Wpjb_Model_Company($request->post("id"));
        
        if($company->exists() == false) {
            exit -2;
        }
        
        ?>
        <span class='wpjb-infobox-title'><?php esc_html_e($company->company_name) ?></span>
        <p><?php esc_html_e($company->locationToString()) ?></p>
        <p><a href="<?php esc_attr_e($company->url()) ?>">View Company Details <span class="wpjb-glyphs wpjb-icon-right-open"></span></a></p>
        <div class="wpjb-infobox-footer">
            <span class="footer-icon wpjb-glyphs wpjb-icon-globe"></span>
            <small><?php esc_html_e(sprintf(__("Posted Jobs %d", "wpjobboard"), $company->jobs_posted)) ?></small>
        </div>
        
        <?php
        exit;
    }
}