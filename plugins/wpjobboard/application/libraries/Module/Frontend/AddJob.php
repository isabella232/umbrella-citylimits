<?php
/**
 * Description of AddJob
 *
 * @author greg
 * @package 
 */

class Wpjb_Module_Frontend_AddJob extends Wpjb_Controller_Frontend
{

    public function init()
    {
        $instance = Wpjb_Project::getInstance();
        $this->view->steps = array(
            1 => __("Create Ad", "wpjobboard"),
            2 => __("Preview", "wpjobboard"),
            3 => __("Publish", "wpjobboard")
        );

        $urls = new stdClass();
        if(!$instance->shortcodeIs() || $instance->shortcodeIs("wpjb_employer_panel", false)) {
            $urls->add = wpjb_link_to("step_add");
        } else {
            $urls->add = get_the_permalink();
        }
        $urls->preview = wpjb_link_to("step_preview");
        $urls->reset = wpjb_link_to("step_reset");
        $urls->save = wpjb_link_to("step_save");
        $this->view->urls = $urls;
    }

    
    public function sessionSet($key, $value)
    {
        $id = "wpjb_session_".str_replace("-", "_", wpjb_transient_id());
        $transient = get_transient($id);
        
        if($transient === false) {
            $transient = array();
        }
        
        $transient[$key] = $value;
        
        set_transient($id, $transient, 3600);
    }
    
    public function sessionGet($key, $default = null) 
    {
        $id = "wpjb_session_".str_replace("-", "_", wpjb_transient_id());
        $transient = get_transient($id);
        
        if($transient === false) {
            $transient = array();
        }
        
        if(!isset($transient[$key])) {
            return $default;
        } else {
            return $transient[$key];
        }
    }

    private function _canPost()
    {
        $info = wp_get_current_user();
        $isAdmin = true;
        if(!isset($info->wp_capabilities) || !$info->wp_capabilities['administrator']) {
            $isAdmin = false;
        }

        if(!$isAdmin && Wpjb_Project::getInstance()->conf("posting_allow")==3) {
            $this->view->_flash->addError(__("Only Admin can post jobs", "wpjobboard"));
            $this->view->canPost = false;
            $this->view->can_post = false;
            return false;
        }

        $employer = Wpjb_Model_Company::current();
        if($employer === null && wpjb_conf("posting_allow")==2) {
            $this->view->_flash->addError(__("Only registered members can post jobs", "wpjobboard"));
            $this->view->canPost = false;
            $this->view->can_post = false;
            return "../default/form";
            return false;
        }
        
        if($employer !== null && $employer->is_active == Wpjb_Model_Company::ACCOUNT_INACTIVE) {
            $this->view->_flash->addError(__("You cannot post jobs. Your account is inactive.", "wpjobboard"));
            $this->view->canPost = false;
            $this->view->can_post = false;
            return false;
        }

        $this->view->canPost = true;
        $this->view->can_post = true;
        return true;
    }
    
    private function _republish()
    {
        $id = $this->_request->get("republish");
        $job = new Wpjb_Model_Job($id);
        
        $info = wp_get_current_user();
        $isAdmin = true;
        if(!isset($info->wp_capabilities) || !$info->wp_capabilities['administrator']) {
            $isAdmin = false;
        }
        
        $company = Wpjb_Model_Company::current();
        if(!$isAdmin && $company->id != $job->employer_id) {
            return;
        }
   
        $arr = $job->toArray();
        unset($arr["meta"]);
        unset($arr["tag"]);
        
        $data = $job->toArray();
        
        if($job->getLogoDir()) {
            $dir = wpjb_upload_dir("job", "company-logo", null,  "basedir");
            $file = $dir."/".basename($job->getLogoDir());
            if(!is_dir($dir)) {
                wp_mkdir_p($dir);
            }
            if(!is_file($file)) {
                copy($job->getLogoDir(), $file);
            }
            
        }
        
        foreach($job->meta as $k => $value) {
            if($value->conf("type") == "ui-input-file") {
                foreach((array)$job->file->{$value->name} as $file) {
                    //echo $file."<br/>";
                }
            }
        }
        
        foreach($data["meta"] as $k => $v) {
            if(count($v["values"]) > 1) {
                $arr[$k] = $v["values"];
            } else {
                $arr[$k] = $v["value"];
            }
        }
        foreach($data["tag"] as $k => $v) {
            $arr[$k] = array();
            foreach($v as $vi) {
                $arr[$k][] = $vi["id"];
            }
        }
        
        return $arr;
    }
    
    private function _company()
    {
        $c = Wpjb_Model_Company::current();
        
        $upload = wpjb_upload_dir("company", "company-logo", $c->id);
        $file = wpjb_glob($upload["basedir"]."/*");
        
        if(isset($file[0])) {
            $file = $upload["basedir"]."/".basename($file[0]);
            $upload = wpjb_upload_dir("job", "company-logo");
            $dir = $upload["basedir"];
            $new_file = $dir."/".basename($file);
            
            if(wp_mkdir_p($dir)) {
                $wpupload = wp_upload_dir();
                $stat = @stat($wpupload["basedir"]);
                $perms = $stat['mode'] & 0007777;
                chmod($dir, $perms);
                
                copy($file, $new_file);

                // Set correct file permissions
                $stat = @stat( dirname( $new_file ) );
                $perms = $stat['mode'] & 0007777;
                $perms = $perms & 0000666;
                @ chmod( $new_file, $perms );
                clearstatcache();
            }


        }
        
        return array(
            "company_name" => $c->company_name,
            "company_email" => $c->getUser(true)->user_email,
            "company_url" => $c->company_website,
            "job_country" => $c->company_country,
            "job_state" => $c->company_state,
            "job_zip_code" => $c->company_zip_code,
            "job_city" => $c->company_location,
        );
    }
    
    public function redirect($path) {
        if(Wpjb_Project::getInstance()->shortcodeIs()) {
            switch($path) {
                case "step_add": return $this->addAction();
                case "step_preview": return $this->previewAction();
                case "step_save": return $this->saveAction();
                case "step_complete": return $this->completeAction();
            }
        } else {
            parent::redirect(wpjb_link_to($path));
        }
    }

    public function resetAction()
    {
        wpjb_recursive_delete(wpjb_upload_dir("job", "", null, "basedir"));
        
        $this->sessionSet("job", null);
        $this->sessionSet("job_id", null);
        
        $this->view->_flash->addInfo(__("Form has been reset.", "wpjobboard"));
        return $this->redirect("step_add");
    }
    
    public function addAction()
    {
        wp_enqueue_script("wpjb-suggest");
        
        $this->view->current_step = 1;
        $this->setTitle($this->view->steps[1]);
        
        $this->view->show_pricing = true;
        $canPost = $this->_canPost();
        if(is_string($canPost)) {
            
            $form = new Wpjb_Form_Login();
            $form->getElement("redirect_to")->setValue(wpjb_link_to("step_add"));
            
            $this->view->form = $form;
            $this->view->submit = __("Login", "wpjobboard");
            $this->view->buttons = array(
                array(
                    "tag"=>"a", 
                    "href"=>wpjb_link_to("employer_new"), 
                    "html"=>__("Not a member? Register", "wpjobboard")
                ),
            );
            return $canPost;
        } elseif($canPost !== true) {
            return $canPost;
        }

        $query = new Daq_Db_Query;
        $l = $query->select("*")->from("Wpjb_Model_Pricing t")->execute();
        $listing = array();
        foreach($l as $li) {
            $listing[$li->getId()] = $li;
        }
        $this->view->listing = $listing;
        
        $this->sessionSet("job_id", null);

        $form = new Wpjb_Form_AddJob();
        
        if(!$form->hasElement("listing") && !$form->hasElement("coupon")) {
            $this->view->show_pricing = false;
        }
        
        if($this->_request->get("republish")) {
            $arr = $this->_republish();
        } elseif(Wpjb_Model_Company::current()) {
            $arr = $this->_company();
        } else {
            $arr = array();
        }
       
        $jobArr = $this->sessionGet("job", null);
        
        if($this->_request->get("listing") && $form->hasElement("listing")) {
            $form->getElement("listing")->setValue($this->_request->get("listing"));
        }
        
        if(is_array($jobArr)) {
            $form->isValid($jobArr);
        } else {
            $form->setDefaults($arr);
        }

        $this->view->form = $form;
        
        return "add";
    }

    public function previewAction()
    {
        if($this->_canPost() !== true) {
            return $this->redirect("step_add");
        }

        $this->view->current_step = 2;
        $this->setTitle($this->view->steps[2]);

        $form = new Wpjb_Form_AddJob();

        if($this->isPost()) {
            $jobArr = $this->_request->getAll();
            $this->sessionSet("job", $jobArr);
        } else {
            $jobArr = $this->sessionGet("job", array());
        }

        if(!$form->isValid($jobArr)) {
            $this->view->_flash->addError(__("There are errors in your form. Please correct them before proceeding.", "wpjobboard"));
            return $this->redirect("step_add");
        } elseif($this->isPost()) {
            $form->upload(wpjb_upload_dir("{object}", "{field}", "{id}", "basedir"));
        }

        $this->view->job = $form->buildModel();
         
        return "preview";
    }

    public function saveAction()
    {
        if($this->_canPost() !== true) {
            return $this->redirect("step_add");
        }

        $this->view->current_step = 3;
        $this->setTitle($this->view->steps[3]);
        
        $form = new Wpjb_Form_AddJob();
        $id = $this->sessionGet("job_id");

        if($form->hasElement("recaptcha_response_field")) {
            $form->removeElement("recaptcha_response_field");
        }
        
        if($id < 1) {
            if($form->isValid($this->sessionGet("job", array()))) {

                $form->save();
                $job = $form->getObject();

                $this->sessionSet("job", null);
                $this->sessionSet("job_id", $job->getId());
            } else {
                return $this->redirect("step_add");
            }
        } else {
            $job = new Wpjb_Model_Job($id);
        }

        if($job->employer_id>0) {
            $company = new Wpjb_Model_Company($job->employer_id);
        } else {
            $company = null;
        }
        
        $payment = $job->getPayment(true);
        
        if($payment->payment_sum > 0) {
            if($payment->payment_sum!=$job->payment_paid) {
                $action = "payment_form";
            } else {
                $action = "payment_already_sent";
            }
        } else {
            $action = "job_online";
            if($job->is_active && $job->is_approved) {
                $online = true;
            } else {
                $online = false;
            }
            $this->view->online = $online;
        }

        $job = new Wpjb_Model_Job($job->id);
        
        if($id<1) {
            $mail = Wpjb_Utility_Message::load("notify_admin_new_job");
            $mail->setTo(get_option("admin_email"));
            $mail->assign("job", $job);
            $mail->assign("payment", $payment);
            $mail->assign("company", $company);
            $mail->send();

            $mail = Wpjb_Utility_Message::load("notify_employer_new_job");
            $mail->setTo($job->company_email);
            $mail->assign("job", $job);
            $mail->assign("payment", $payment);
            $mail->assign("company", $company);
            $mail->send();
        }

        if($action == "payment_form") {
            $this->view->payment = $payment;
            try {
                $this->view->payment_form = Wpjb_Project::getInstance()->payment->factory($payment)->render();
            } catch(Exception $e) {
                $this->view->payment_form = "";
                $this->view->_flash->addError(__("Payment method not found.", "wpjobboard"));
            }
            
        }

        $this->view->action = $action;
        $this->view->job = $job;
        
        return "save";
    }

    public function completeAction()
    {
        if($this->_canPost() !== true) {
            return $this->redirect("step_add");
        }
        $this->view->current_step = 3;
        $this->view->action = "payment_complete";
        
        return "save";
    }

}

?>