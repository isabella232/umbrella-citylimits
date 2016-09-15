<?php
/**
 * Description of Index
 *
 * @author greg
 * @package 
 */

class Wpjb_Module_Frontend_Index extends Wpjb_Controller_Frontend
{
    private $_perPage = 20;

    public function init()
    {   
        $this->_perPage = wpjb_conf("front_jobs_per_page", 20);
        $this->view->placeholder = false;
        $this->view->query = null;
        $this->view->pagination = true;
        $this->view->format = null;
        $this->view->atts = array();
    }
    
    public function indexAction()
    {   
        $text = Wpjb_Project::getInstance()->conf("seo_job_board_name", __("Job Board", "wpjobboard"));
        $this->setTitle($text);
        $this->setCanonicalUrl(Wpjb_Project::getInstance()->getUrl());
        
        $param = array(
            "filter" => "active",
            "page" => $this->_request->get("page", 1),
            "query" => $this->_request->get("query"),
            "location" => $this->_request->get("location"),
            "type" => $this->_request->get("type"),
            "category" => $this->_request->get("category"),
            "count" => $this->_perPage
        );
        
        $this->view->search_bar = wpjb_conf("search_bar", "disabled");
        $this->view->search_init = array();
        $this->view->param = $param;
        $this->view->url = wpjb_link_to("home");
        $this->view->page_id = Wpjb_Project::getInstance()->conf("link_jobs");
    }

    public function companyAction()
    {
        $company = $this->getObject();
        /* @var $company Wpjb_Model_Employer */

        $text = wpjb_conf("seo_job_employer", __("{company_name}", "wpjobboard"));
        $param = array(
            'company_name' => $this->getObject()->company_name
        );
        $this->setTitle($text, $param);

        if(Wpjb_Model_Company::current() && Wpjb_Model_Company::current()->id==$company->id) {
            // do nothing
        } elseif($company->is_active == Wpjb_Model_Company::ACCOUNT_INACTIVE) {
            $this->view->_flash->addError(__("Company profile is inactive.", "wpjobboard"));
        } elseif(!$company->is_public) {
            $this->view->_flash->addInfo(__("Company profile is hidden.", "wpjobboard"));
        } elseif(!$company->isVisible()) {
            $this->view->_flash->addError(__("Company profile will be visible once employer will post at least one job.", "wpjobboard"));
        }

        $this->view->company = $company;
        $this->view->param = array(
            "filter" => "active",
            "employer_id" => $company->id
        );
    }

    public function categoryAction()
    {
        $object = $this->getObject();
        if($object->type != Wpjb_Model_Tag::TYPE_CATEGORY) {
            $this->view->_flash->addError(__("Category does not exist.", "wpjobboard"));
            return false;
        }
        
        $text = wpjb_conf("seo_category", __("Category: {category}", "wpjobboard"));
        $param = array(
            'category' => $this->getObject()->title
        );

        $this->setCanonicalUrl(wpjb_link_to("category", $this->getObject()));

        $this->view->current_category = $this->getObject();
        $this->setTitle($text, $param);

        $this->view->param = array(
            "filter" => "active",
            "page" => $this->_request->get("page", 1),
            "count" => $this->_perPage,
            "category" => $this->getObject()->id
        );
        
        $this->view->search_bar = wpjb_conf("search_bar", "disabled");
        $this->view->search_init = array("category" => $this->getObject()->id);
        $this->view->url = $object->url();
        
        return "index";
    }

    public function typeAction()
    {
        $object = $this->getObject();
        if($object->type != Wpjb_Model_Tag::TYPE_TYPE) {
            $this->view->_flash->addError(__("Job type does not exist.", "wpjobboard"));
            return false;
        }
        
        $text = wpjb_conf("seo_job_type", __("Job Type: {type}", "wpjobboard"));
        $param = array(
            'type' => $this->getObject()->title
        );
        $this->setCanonicalUrl(wpjb_link_to("type", $this->getObject()));

        $this->view->current_type = $this->getObject();
        $this->setTitle($text, $param);

        $this->view->param = array(
            "filter" => "active",
            "page" => $this->_request->get("page", 1),
            "count" => $this->_perPage,
            "type" => $this->getObject()->id
        );
        
        $this->view->search_bar = wpjb_conf("search_bar", "disabled");
        $this->view->search_init = array("type" => $this->getObject()->id);
        $this->view->url = $object->url();
        
        return "index";
    }
    
    public function searchAction()
    {
        $request = $this->getRequest();
        $job = new Wpjb_Model_Job();
        $meta = array();
        
        foreach($job->meta as $k => $m) {
            if($request->get($k)) {
                $meta[$k] = $request->get($k);
            }
        }
        
        $date_from = $request->get("date_from");
        $date_to = $request->get("date_to");
        
        if($request->get("posted")>0) {
            $posted = intval($request->get("posted"))-1;
            $date_to = date("Y-m-d");
            $date_from = date("Y-m-d", wpjb_time("$date_to -$posted DAY"));
        }
        
        $paged = get_query_var("paged", 1);
        
        if($paged < 1) {
            $paged = 1;
        }

        $param = array(
            "query" => $request->get("query"),
            "category" => $request->get("category"),
            "type" => $request->get("type"),
            "page" => $request->get("page", $paged),
            "count" => $request->get("count", $this->_perPage),
            "country" => $request->get("country"),
            "state" => $request->get("state"),
            "city" => $request->get("city"),
            "posted" => $request->get("posted"),
            "location" => $request->get("location"),
            "radius" => $request->get("radius"),
            "is_featured" => $request->get("is_featured"),
            "employer_id" => $request->get("employer_id"),
            "meta" => $meta,
            "sort" => $request->get("sort"),
            "order" => $request->get("order"),
            "date_from" => $date_from,
            "date_to" => $date_to
        );
        
        $this->view->param = $param;
        $this->view->url = wpjb_link_to("search");
        
        $query = array();
        foreach($request->get() as $k => $v) {
            if(!empty($v) && !in_array($k, array("page", "job_board", "page_id"))) {
                $query[$k] = $v;
            }
        }
        
        $init = array();
        foreach($param as $k => $v) {
            if(!empty($v) && !in_array($k, array("page", "job_board", "page_id"))) {
                $init[$k] = $v;
            }
        }
        
        $this->view->query = $query;
        
        $this->view->search_bar = wpjb_conf("search_bar", "disabled");
        $this->view->search_init = $init;

        $form = new Wpjb_Form_AdvancedSearch();
        $form->isValid($request->get());
        $this->view->form = $form;
        
        $this->setTitle(wpjb_conf("seo_search_results", __("Search Results", "wpjobboard")));
        
        if(empty($query)) {
            $this->view->show_results = false;
            return array("search", "index");
        }
        
        $this->view->show_results = true;
        $rQuery = $this->readableQuery($request->get(), $form, new Wpjb_Form_AddJob());
        $readable = array();
        foreach($rQuery as $rk => $data) {
            
            $values = array();
            
            foreach($data["value"] as $vk => $vv) {
                $aparam = array(
                    "href"=>"#", 
                    "class"=>"wpjb-glyphs wpjb-icon-cancel wpjb-refine-cancel",
                    "data-wpjb-field-remove" => $rk,
                    "data-wpjb-field-value" => $vk
                );

                $htmlA = new Daq_Helper_Html("a", $aparam, "");
                $htmlA->forceLongClosing();
                
                $values[] = $vv."".$htmlA->render();
            }
            
            $htmlB = new Daq_Helper_Html("b", array(), $data["param"]);
            $htmlS = new Daq_Helper_Html("span", array(
                "class" => "wpjb-tag",
            ), $htmlB->render() ." ". join(" ", $values));
            
            $readable[] = $htmlS->render();
        }
        if(empty($readable)) {
            $readable[] = '<span class="wpjb-tag"><em>'.__("No search params provided, showing all active jobs.", "wpjobboard").'</em></span>';
        }
        $this->view->readable = join(" ", $readable);
        
        return array("search", "index");
    }

    public function advsearchAction()
    {
        $this->setTitle(wpjb_conf("seo_adv_search", __("Advanced Search", "wpjobboard")));
        $form = new Wpjb_Form_AdvancedSearch();
        
        $this->view->show_results = false;
        $this->view->form = $form;
        return "search";
    }

    public function singleAction()
    {
        $this->view->members_only = false;
        $this->view->form_error = null;
        
        $this->setTitle(" ");
        $job = $this->getObject();

        $url = wpjb_link_to("job", $job);
        $this->setCanonicalUrl($url);
       
        $inrange = $job->time->job_created_at < time() && $job->time->job_expires_at+86400 > time();
        
        $show_related = (bool)wpjb_conf("front_show_related_jobs");
        $show_expired = (bool)wpjb_conf("front_show_expired");
        $can_apply = true;
        
        if(!$inrange) {
            $can_apply = false;
        }
        if($show_expired) {
            $inrange = true;
        }
        
        $this->view->show_related = $show_related;
        
        $this->view->show = new stdClass();
        $this->view->show->apply = 0;
        
        if($job->meta->job_source->value()) {
            $this->view->application_url = $job->company_url;
        } else {
            $this->view->application_url = null;
        }
        
        if($this->_request->get("form") == "apply") {
            $this->view->show->apply = 1;
        }

        if(($job->is_active && $job->is_approved && $inrange) || $this->_isUserAdmin()) {

            $this->view->job = $job;

            $text = wpjb_conf("seo_single", __("{job_title}", "wpjobboard"));
            $param = array('job_title' => $job->job_title, 'id' => $job->id);
            $this->setTitle($text, $param);

            $old = wpjb_conf("front_mark_as_old");

            if($old>0 && time()-strtotime($job->job_created_at)>$old*3600*24) {
                $diff = floor((time()-strtotime($job->job_created_at))/(3600*24));
                $msg = _n(
                    "Attention! This job posting is one day old and might be already filled.",
                    "Attention! This job posting is %d days old and might be already filled.",
                    $diff,
                    "wpjobboard"
                );
                $this->view->_flash->addInfo(sprintf($msg, $diff));
            }

            if($job->is_filled) {
                $msg = __("This job posting was marked by employer as filled and is probably no longer available", "wpjobboard");
                $this->view->_flash->addInfo($msg);
            }

            if($job->employer_id > 0) {
                $this->view->company = new Wpjb_Model_Company($job->employer_id);
            }
            
            $related = array(
                "query" => $job->job_title,
                "page" => 1,
                "count" => 5,
                "id__not_in" => $job->id
            );

            $this->view->related = $related;
            

        } else {
            // job inactive or not exists
            $goback = "javascript:history.back(-1);";
            
            if(isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER['HTTP_REFERER'], site_url())===0) {
                $goback = $_SERVER['HTTP_REFERER'];
            }
            
            $msg = __("Selected job is inactive or does not exist. <a href=\"%s\">Go back</a>.", "wpjobboard");
            $this->view->_flash->addError(sprintf($msg, $goback));
            $this->view->job = null;
            return false;
        }
        
        $can_apply = apply_filters("wpjb_user_can_apply", $can_apply, $this->getObject(), $this);
        
        if(!$this->isMember() && wpjb_conf("front_apply_members_only", false) && $can_apply) {
            $this->view->members_only = true;
            $m = __("Only registered members can apply for jobs.", "wpjobboard");
            $this->view->form_error = $m;
            return;
        }

        $form = new Wpjb_Form_Apply();
        $form->getElement("_job_id")->setValue($this->getObject()->id);

        $action = $this->getRequest()->post("_wpjb_action");
        
        $this->view->can_apply = $can_apply;
        $this->view->form_sent = false;
        
        if($this->isPost() && $action=="apply" && $can_apply) {
            // if we are here then application is invalid, otherwise user would be redirected to success page
            $form->isValid(Daq_Request::getInstance()->post());
            $this->view->form_sent = true;
            $this->view->form_error = __("There are errors in your form.", "wpjobboard");
            $this->view->show->apply = 1;
        } elseif(Wpjb_Model_Resume::current()) {
            $resume = Wpjb_Model_Resume::current();
            if(!is_null($resume) && $form->hasElement("email")) {
                $form->getElement("email")->setValue($resume->user->user_email);
            }
            if(!is_null($resume) && $form->hasElement("applicant_name")) {
                $form->getElement("applicant_name")->setValue($resume->user->first_name." ".$resume->user->last_name);
            }
        }
        
        $this->view->form = $form;
        
        //$this->applyAction();
    }
    
    public function paymentAction()
    {
        $payment = $this->getObject();
        $button = Wpjb_Project::getInstance()->payment->factory($payment);
        
        $this->setTitle(__("Payment", "wpjobboard"));
        
        if($payment->payment_sum == $payment->payment_paid) {
            $this->view->_flash->addInfo(__("This payment was already processed correctly.", "wpjobboard"));
            return false;
        }
        
        if($payment->object_type == 1) {
            $this->view->job = new Wpjb_Model_Job($payment->object_id);
        }
        
        $this->view->payment = $payment;
        $this->view->button = $button;
        $this->view->currency = Wpjb_List_Currency::getCurrencySymbol($payment->payment_currency);
    }
    
    public function alertAction()
    {
        $this->setTitle(__("Job Alerts", "wpjobboard"));

        $request = Daq_Request::getInstance();
        $form = new Wpjb_Form_Frontend_Alert();

        
        if($this->isPost()) {

            if($form->isValid($request->getAll())) {
            
                $alert = new Wpjb_Model_Alert;
                $alert->user_id = get_current_user_id();
                $alert->keyword = $request->post("keyword");
                $alert->email = $request->post("email");
                $alert->created_at = date("Y-m-d H:i:s");
                $alert->last_run = "0000-00-00 00:00:00";
                $alert->frequency = 1;
                $alert->params = serialize(array("filter"=>"active", "keyword"=>$alert->keyword));
                $alert->save();

                $this->view->_flash->addInfo(__("Alert was added to the database.", "wpjobboard"));
                
                return false;
            } else {
                $this->view->_flash->addError(__("Alert could not be added. There was an error in the form.", "wpjobboard"));
            }
        }
        
        $this->view->action = "";
        $this->view->submit = __("Subscribe", "wpjobboard");
        $this->view->form = $form;
        
        return "../default/form";
    }
    
    public function deleteAlertAction()
    {
        $request = Daq_Request::getInstance();
        $this->setTitle(__("Job Alerts", "wpjobboard"));
        $hash = $request->get("hash");
        
        if(empty($hash)) {
            $this->view->_flash->addError(__("Provided hash code is empty.", "wpjobboard"));
            return false;
        }
        
        $query = new Daq_Db_Query();
        $query->from("Wpjb_Model_Alert t");
        $query->where("MD5(CONCAT(t.id, '|', t.email)) = ?", $hash);
        $query->limit(1);
        
        $result = $query->execute();
        
        if(empty($result)) {
            $this->view->_flash->addError(__("Provided hash code is invalid.", "wpjobboard"));
            return false;
        }
        
        $result[0]->delete();
        
        $this->view->_flash->addInfo(__("Alert deleted.", "wpjobboard"));
        
        return false;
    }
}

?>
