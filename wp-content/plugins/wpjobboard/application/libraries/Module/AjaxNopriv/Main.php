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
class Wpjb_Module_AjaxNopriv_Main 
{
    
    public function deleteAction()
    {
        global $blog_id;

        if($blog_id > 1) {
            $bid = "-".$blog_id;
        } else {
            $bid = "";
        }
        
        $request = Daq_Request::getInstance();
        $id = $request->getParam("id");
        $dir = wp_upload_dir();
        $dir = $dir["basedir"]."/wpjobboard{$bid}";
        $response = new stdClass;
        $response->result = false;
        $response->msg = "";
        
        if(!is_file($dir."/".$id)) {
            $response->msg = __("File does not exist.", "wpjobboard");
            echo json_encode($response);
            return;
        }
        
        $path = explode("/", $id);
        $priv = explode("_", $path[1]);
        
        if($priv[1]=="u" && $priv[2]==get_current_user_id()) {
            
        } elseif($priv[1]=="s" && $priv[2]==wpjb_transient_id()) {
            
        } elseif(isset($path[1]) && is_numeric($path[1]) && self::userOwnsFile($path[0], $path[1])) {
            
        } elseif(current_user_can("edit_pages")) {
            
        } else {
            $response->msg = __("You do not have permissions to delete file $id.", "wpjobboard");
            die(json_encode($response));
        }
        
        $file = $dir."/".$id;
        do {
            if(is_dir($file)) {
                rmdir($file);
            } else {
                unlink($file);
                $tpath = dirname($file);
                $tname = basename($file);
                foreach(wpjb_glob("$tpath/_[_]*[0-9x]*_$tname") as $tfile) {
                    unlink($tfile);
                }
            }
            $file = dirname($file);
            $files = glob($file."/*");
        } while(empty($files));
        
        $response->result = 1;
        die(json_encode($response));
        
    }
    
    public function uploadAction()
    {
        $request = Daq_Request::getInstance();
        $response = new stdClass();
        $response->result = 0;
        $response->msg = "";
        
        $id = null;
        
        $form = $request->post("form");
        $field = $request->post("field");
        
        if(!class_exists($form)) {
            $response->msg = __("Unknown form parameter.", "wpjobboard");
            die(json_encode($response));
        }
        
        if(is_numeric($request->post("id"))) {
            $id = $request->post("id");
        }
        
        $form = new $form($id);
        
        if(!$form->hasElement($field) || $form->getElement($field)->getType() != "file") {
            $response->msg = __("Unallowed object.", "wpjobboard");
            die(json_encode($response));
        }
        
        $field = $form->getElement($field);
        $field->setValue($_FILES["file"]);
        
        /* @var $field Daq_Form_Element_File */
        
        if(!$field->validate()) {
            $response->msg = join(". ", $field->getErrors());
            die(json_encode($response));
        }
        
        $path = $field->getUploadPath();
        $upload = wpjb_upload_dir($path["object"], $path["field"], $form->getId());
        $dir = $upload["basedir"];
        $url = $upload["baseurl"];
        
        if(!wp_mkdir_p($dir)) {
            $response->msg = sprintf(__("Upload directory %s could not be created.", "wpjobboard"), $dir);
            die(json_encode($response));
        }
        
        $wpupload = wp_upload_dir();
        $stat = @stat($wpupload["basedir"]);
        $perms = $stat['mode'] & 0007777;
        chmod($dir, $perms);
        
        $field->setDestination($dir);
        $filename = $field->upload();
        
        $filename = basename($filename[0]);
        
        $response->result =  1;
        $response->filename = $filename;
        $response->url = $url."/".$filename;
        $response->path = $upload["dir"]."/{$filename}";
        
        do_action("wpjb_file_uploaded", $response);
        
        die(json_encode($response));
    }
    
    public function couponAction()
    {
        $r = Daq_Request::getInstance();
        $response = new stdClass();
        $response->result = 0;
        $response->msg = "";
        
        try {
            $listing = new Wpjb_Model_Pricing($r->getParam("id"));
            $listing->applyCoupon($r->getParam("code"));
        } catch(Wpjb_Model_PricingException $e) {
            $response->msg = $e->getMessage();
            echo json_encode($response);
            die;
        }
        
        $response->msg = sprintf(__("Coupon '%s' was applied.", "wpjobboard"), $listing->getCoupon()->title);
        $response->result = 1;
        $response->price = wpjb_price($listing->getPrice(), $listing->currency);
        $response->discount = wpjb_price($listing->getDiscount(), $listing->currency);
        $response->total = wpjb_price($listing->getTotal(), $listing->currency);
        
        echo json_encode($response);
        die;
    }
    
    public function subscribeAction()
    {
        $r = Daq_Request::getInstance();
        $response = new stdClass();
        $response->result = 0;
        $response->msg = "";
        
        $criteria = $r->getParam("criteria");
        $criteria["filter"] = "active";
        
        unset($criteria["count"]);
        unset($criteria["page"]);
        
        if(isset($criteria["sort_order"])) {
            unset($criteria["sort_order"]);
        }
        
        if(!is_email($r->getParam("email"))) {
            $response->result = 0;
            $response->msg = __("You provided invalid email address.", "wpjobboard");
            
            echo json_encode($response);
            die;
        }
        
        try {
            
            if(isset($criteria["query"]) && !empty($criteria["query"])) {
                $criteria["keyword"] = $criteria["query"];
                unset($criteria["query"]);
            }
            
            $alert = new Wpjb_Model_Alert;
            $alert->user_id = get_current_user_id();
            $alert->keyword = $criteria["keyword"];
            $alert->email = $r->getParam("email");
            $alert->created_at = date("Y-m-d H:i:s");
            $alert->last_run = "0000-00-00 00:00:00";
            $alert->frequency = $r->getParam("frequency");
            $alert->params = serialize($criteria);
            $alert->save();
            
            $response->result = 1;
            $response->msg = __("Alert was saved successfully.", "wpjobboard");
        } catch(Exception $e) {
            $response->result = 0;
            $response->msg = __("There was an error while saving alert.", "wpjobboard");
        }
        
        echo json_encode($response);
        die;
    }
    
    public static function userOwnsFile($key, $id)
    {
        $arr = array(
            "job" => "Wpjb_Model_Job",
            "resume" => "Wpjb_Model_Resume",
            "company" => "Wpjb_Model_Company",
            "application" => "Wpjb_Model_Application"
        );
        
        if(!isset($arr[$key])) {
            return false;
        }
        
        $class = $arr[$key];
        $object = new $class($id);
        
        if($key == "job" && $object->employer_id == Wpjb_Model_Company::current()->id) {
            return true;
        } elseif($object->user_id == wp_get_current_user()->ID) {
            return true;
        }
        
        return false;
    }

}

?>
