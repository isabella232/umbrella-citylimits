<?php
/**
 * Description of Email
 *
 * @author greg
 * @package 
 */

class Wpjb_Module_Admin_Email extends Wpjb_Controller_Admin
{
    protected $_mailList = null;

    public function init()
    {
        $this->_mailList = array();
        $this->view->mailList = $this->_mailList;
        
        $this->_virtual = array(
           "addAction" => array(
                "form" => "Wpjb_Form_Admin_Email",
                "info" => __("New Email Template has been created.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard"),
                "url" => wpjb_admin_url("email", "edit", "%d")
            ),
            "deleteAction" => array(
                "info" => __("Email Template #%d deleted.", "wpjobboard"),
                "page" => "email"
            ),
            "_delete" => array(
                "model" => "Wpjb_Model_Email",
                "info" => __("Email Template deleted.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard")
            ),
            "_multiDelete" => array(
                "model" => "Wpjb_Model_Email"
            )
        );
    }

    public function indexAction()
    {
        $query = new Daq_Db_Query();
        $data = $query->select("t1.*")->from("Wpjb_Model_Email t1")->order("sent_to")->execute();
        $item = array();
        
        foreach($data as $d) {
            if(!isset($item[$d->sent_to]) || !is_array($item[$d->sent_to])) {
                $item[$d->sent_to] = array();
            }
            $item[$d->sent_to][] = $d;
        }
        
        $desc = array(
            1 => __("Emails sent to admin <small>(emails are sent From and To email address specified in Mail From field)</small>", "wpjobboard"),
            2 => __("Emails sent to employer <small>(to email address specified in Company Email field)</small>", "wpjobboard"),
            3 => __("Emails sent to candidate", "wpjobboard"),
            4 => __("Other Emails", "wpjobboard"),
            5 => __("Custom Emails", "wpjobboard")
        );
        
        $this->view->desc = $desc;
        $this->view->data = $item;
    }

    public function editAction()
    {
        $job = new Wpjb_Model_Job();
        $job = $job->toArray();
        unset($job["read"]);
        unset($job["cache"]);
        $job["tag"] = array("category", "type");
        
        $payment = new Wpjb_Model_Payment;
        $payment = $payment->toArray();
        
        $company = new Wpjb_Model_Company;
        $company = $company->toArray();
        
        $application = new Wpjb_Model_Application;
        $application = $application->toArray();
        
        $alert = new Wpjb_Model_Alert;
        $alert = $alert->toArray();

        $resume = new Wpjb_Model_Resume;
        $resume = $resume->toArray();
        $resume["tag"] = array("category");
        
        $rDetail = new Wpjb_Model_ResumeDetail();
        $resume["experience"] = $rDetail->toArray();
        $resume["education"] = $rDetail->toArray();
        
        $this->view->vars = array(
            array(
                "var" => "job",
                "title" => __("Job Variable", "wpjobboard"),
                "item" => $job
            ),
            array(
                "var" => "payment",
                "title" => __("Payment Variable", "wpjobboard"),
                "item" => $payment
            ),
            array(
                "var" => "company",
                "title" => __("Company Variable", "wpjobboard"),
                "item" => $company
            ),
            array(
                "var" => "application",
                "title" => __("Application Variable", "wpjobboard"),
                "item" => $application
            ),
            array(
                "var" => "alert",
                "title" => __("Alert Variable", "wpjobboard"),
                "item" => $alert
            ),
            array(
                "var" => "resume",
                "title" => __("Resume Variable", "wpjobboard"),
                "item" => $resume
            ),
        );
        
        $eObjects = array(
            "notify_admin_new_job" => array("job", "payment", "company"),
            "notify_admin_payment_received" => array("payment"),
            "notify_employer_new_job" => array("job", "payment", "company"),
            "notify_employer_job_expires" => array("job"),
            "notify_admin_new_application" => array("job", "application", "resume"),
            "notify_admin_general_application" => array("application", "resume"),
            "notify_applicant_applied" => array("job", "application"),
            "notify_employer_register" => array(), //3
            "notify_canditate_register" => array(), //3
            "notify_admin_grant_access" => array("company"), //1
            "notify_employer_verify" => array("company"),
            "notify_employer_new_application" => array("job", "application", "resume"),
            "notify_job_alerts" => array("alert"), //3
            "notify_employer_job_paid" => array("job"),
            "notify_employer_resume_paid" => array("resume"),
            "notify_applicant_status_change" => array("job", "application")
        );
        
        $eCustom = array(
            "notify_employer_register" => array(
                'username' => __("username selected when registering", "wpjobboard"),
                'password' => __("unencrypted password", "wpjobboard"),
                'login_url' => __("full url to login form", "wpjobboard")
            ), 
            "notify_canditate_register" => array(
                'username' => __("username selected when registering", "wpjobboard"),
                'password' => __("unencrypted password", "wpjobboard"),
                'login_url' => __("full url to login form", "wpjobboard"),
            ), 
            "notify_admin_grant_access" => array(
                'company_edit_url' => __("absolute URL to company profile page (in wp-admin)", "wpjobboard")
            ), 
            "notify_job_alerts" => array(
                'unsubscribe_url' => __("URL user can use to unsubscribe from email alerts", "wpjobboard"),
                'jobs' => __("array of matched Job objects", "wpjobboard")
            ), 
            "notify_employer_resume_paid" => array(
                'resume_unique_url' => __("unique URL to Resume details page", "wpjobboard")
            ), 
            "notify_applicant_status_change" => array(
                'status' => __("Current application status", "wpjobboard")
            ),
        );
        
        $email = new Wpjb_Model_Email($this->_request->get("id"));
        list($email_name) = explode("-", $email->name);
        
        if(isset($eObjects[$email->name])) {
            $objects = $eObjects[$email->name];
        } elseif(isset($eObjects[$email_name])) {
            $objects = $eObjects[$email_name];
        } else {
            $objects = array();
        }
        
        if(isset($eCustom[$email->name])) {
            $customs = $eCustom[$email->name];
        } elseif(isset($eCustom[$email_name])) {
            $customs = $eCustom[$email_name];
        } else {
            $customs = array();
        }
        
        $this->view->objects = apply_filters("wpjb_email_template_objects", $objects, $email->name);
        $this->view->customs = apply_filters("wpjb_email_template_customs", $customs, $email->name);
        
        $form = new Wpjb_Form_Admin_Email($this->_request->getParam("id"));
        $this->view->id = $this->_request->getParam("id");
        if($this->isPost()) {
            $isValid = $form->isValid($this->_request->getAll());
            if($isValid) {
                $this->_addInfo(__("Email Template saved.", "wpjobboard"));
                $form->save();
            } else {
                $this->_addError(__("There are errors in the form.", "wpjobboard"));
            }
        }

        $this->view->form = $form;
    }
}

?>