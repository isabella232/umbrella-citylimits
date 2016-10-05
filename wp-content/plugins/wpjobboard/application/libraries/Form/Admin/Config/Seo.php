<?php
/**
 * Description of Seo
 *
 * @author greg
 * @package 
 */

class Wpjb_Form_Admin_Config_Seo extends Daq_Form_Abstract
{
    public $name = null;

    public function init()
    {
        $this->name = __("SEO &amp; Titles", "wpjobboard");
        $instance = Wpjb_Project::getInstance();

        $e = $this->create("seo_job_board_name");
        $e->setValue($instance->getConfig("seo_job_board_name"));
        $e->setLabel(__("Job Board Name", "wpjobboard"));
        $e->setHint(__("Job board name displayed on the job board front page.", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_job_board_title");
        $e->setValue($instance->getConfig("seo_job_board_title"));
        $e->setLabel(__("Job Board Title", "wpjobboard"));
        $e->setHint(__("Text displayed in the &lt;title&gt; tag on the job board front page.", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);
        
        $e = $this->create("seo_step_1");
        $e->setValue($instance->getConfig("seo_step_1"));
        $e->setLabel(__("Add Job: Step #1", "wpjobboard"));
        $e->setHint(__("Name of the step #1. For example 'add job'", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_step_2");
        $e->setValue($instance->getConfig("seo_step_2"));
        $e->setLabel(__("Add Job: Step #2", "wpjobboard"));
        $e->setHint(__("Name of the step #2. For example 'preview'", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_step_3");
        $e->setValue($instance->getConfig("seo_step_3"));
        $e->setLabel(__("Add Job: Step #3", "wpjobboard"));
        $e->setHint(__("Name of the step #3. For example 'done!'", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_single");
        $e->setValue($instance->getConfig("seo_single"));
        $e->setLabel(__("Single Page Title", "wpjobboard"));
        $e->setHint(__("Title on the page with job details. Allowed vars: {job_title}, {id}", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_category");
        $e->setValue($instance->getConfig("seo_category"));
        $e->setLabel(__("Job Category Title", "wpjobboard"));
        $e->setHint(__("Title on the page with listings from selected category. Allowed vars: {category}.", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);
        
        $e = $this->create("seo_search_results");
        $e->setValue($instance->getConfig("seo_search_results"));
        $e->setLabel(__("Search Results Title", "wpjobboard"));
        $e->setHint(__("Title on the page with search results. Allowed vars: {keyword}.", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_job_type");
        $e->setValue($instance->getConfig("seo_job_type"));
        $e->setLabel(__("Job Types Title", "wpjobboard"));
        $e->setHint(__("Title on the page with listings with selected fob type. Allowed vars: {type}", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);
        
        $e = $this->create("seo_job_employer");
        $e->setValue($instance->getConfig("seo_job_employer"));
        $e->setLabel(__("Employer Page Title", "wpjobboard"));
        $e->setHint(__("Title on the page with employer profile. Allowed vars: {company_name}", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_resumes_name");
        $e->setValue($instance->getConfig("seo_resumes_name"));
        $e->setLabel(__("Resumes Title", "wpjobboard"));
        $e->setHint(__("Title on the resumes front page", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_resumes_view");
        $e->setValue($instance->getConfig("seo_resumes_view"));
        $e->setLabel(__("Resumes Details Title", "wpjobboard"));
        $e->setHint(__("Title on the single resume details page. Allowed vars: {full_name}", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        $e = $this->create("seo_resume_adv_search");
        $e->setValue($instance->getConfig("seo_resume_adv_search"));
        $e->setLabel(__("Resumes Advanced Search", "wpjobboard"));
        $e->setHint(__("The title on the resumes advanced search form", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);
        
        $e = $this->create("seo_search_resumes");
        $e->setValue($instance->getConfig("seo_search_resumes"));
        $e->setLabel(__("Resumes Search Results", "wpjobboard"));
        $e->setHint(__("The title on resumes search results page. Allowed vars: {keyword}", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(0, 120));
        $this->addElement($e);

        apply_filters("wpja_form_init_config_seo", $this);
    }
}

?>