<?php
/**
 * Description of Seo
 *
 * @author greg
 * @package 
 */

class Wpjb_Form_Admin_Config_Urls extends Daq_Form_Abstract
{
    public $name = null;

    public function init()
    {
        $this->name = __("Default Pages and URLs", "wpjobboard");
        $instance = Wpjb_Project::getInstance();

        $this->addGroup("main", "Main");
        
        $e = $this->create("urls_mode", "radio");
        $e->setRequired(true);
        $e->setValue($instance->getConfig("urls_mode", array(1)));
        $e->setLabel(__("Job Board Mode", "wpjobboard"));
        $e->addOption(1, 1, __("Embedded", "wpjobboard"));
        $e->addOption(2, 2, __("Shortcoded (recomended)", "wpjobboard"));
        $this->addElement($e, "main");
        
        $e = $this->create("urls_cpt", "checkbox");
        $e->setValue($instance->getConfig("urls_cpt"));
        $e->setLabel(__("Custom Post Types", "wpjobboard"));
        $e->addOption(1, 1, __("Use Custom Post Types", "wpjobboard"));
        $this->addElement($e, "main");
        
        $this->addGroup("embedded", __("Embedded", "wpjobboard"));
        
        $e = $this->create("link_jobs");
        $e->setValue($instance->getConfig("link_jobs"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Job Board Page", "wpjobboard"));
        $this->addElement($e, "embedded");
        
        $e = $this->create("link_resumes");
        $e->setValue($instance->getConfig("link_resumes"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Resumes Page", "wpjobboard"));
        $this->addElement($e, "embedded");
        
        $this->addGroup("shortcoded", __("Shortcode Pages", "wpjobboard"));
        
        $e = $this->create("urls_link_job");
        $e->setValue($instance->getConfig("urls_link_job"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Jobs Page", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_job_search");
        $e->setValue($instance->getConfig("urls_link_job_search"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Jobs Search", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_job_add");
        $e->setValue($instance->getConfig("urls_link_job_add"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Jobs Post", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_emp_reg");
        $e->setValue($instance->getConfig("urls_link_emp_reg"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Employer Registration", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_emp_panel");
        $e->setValue($instance->getConfig("urls_link_emp_panel"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Employer Panel", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_resume");
        $e->setValue($instance->getConfig("urls_link_resume"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Resumes Page", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_resume_search");
        $e->setValue($instance->getConfig("urls_link_resume_search"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Resumes Search", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_cand_reg");
        $e->setValue($instance->getConfig("urls_link_cand_reg"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Candidate Registration", "wpjobboard"));
        $this->addElement($e, "shortcoded");
        
        $e = $this->create("urls_link_cand_panel");
        $e->setValue($instance->getConfig("urls_link_cand_panel"));
        $e->setRenderer("wpjb_dropdown_pages");
        $e->setLabel(__("Candidate Panel", "wpjobboard"));
        $this->addElement($e, "shortcoded");

        apply_filters("wpja_form_init_config_urls", $this);
        
        
    }
    
    public function executePostSave()
    {
        global $wp_rewrite;
        
        $wp_rewrite->flush_rules();
    }
}



?>