<?php
/**
 * Description of PayPal
 *
 * @author greg
 * @package
 */

class Wpjb_Form_Admin_Config_Resumes extends Daq_Form_Abstract
{

    public $name = null;
    
    protected function _currArr()
    {
        $list = array();
        foreach(Wpjb_List_Currency::getList() as $k => $arr) {
            $v = $arr['name'];
            if($arr['symbol'] != null) {
                $v = $arr['symbol'].' '.$v;
            }
            $list[] = array($k, $k, $v);
        }
        return $list;
    }
    
    public function init()
    {
        $this->name = __("Resumes Settings", "wpjobboard");
        $instance = Wpjb_Project::getInstance();

        $e = $this->create("cv_privacy", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($instance->getConfig("cv_privacy"));
        $e->setLabel(__("Resumes Privacy", "wpjobboard"));
        $e->addOption("0", "0", __("Hide contact details only.", "wpjobboard"));
        $e->addOption(1, 1, __("Hide resume list and details", "wpjobboard"));
        $this->addElement($e);
        
        $e = $this->create("cv_access", "select");
        $e->setValue($instance->getConfig("cv_access"));
        $e->setLabel(__("Grant Resumes Access", "wpjobboard"));
        $e->setHint(__("Note that automatically activating employer accounts might cause, potential security issue for employers since anyone will be able to browse their personal data", "wpjobboard"));
        $e->addFilter(new Daq_Filter_Int());
        $e->addOption(1, 1, __("To all", "wpjobboard"));
        $e->addOption(2, 2, __("To registered members", "wpjobboard"));
        $e->addOption(3, 3, __("To employers", "wpjobboard"));
        $e->addOption(4, 4, __("To employers (verified)", "wpjobboard"));
        $e->addOption(5, 5, __("To premium members", "wpjobboard"));
        $this->addElement($e);

        $e = $this->create("cv_approval", "select");
        $e->setValue($instance->getConfig("cv_approval"));
        $e->setLabel(__("Resumes Approval", "wpjobboard"));
        $e->setHint("");
        $e->addValidator(new Daq_Validate_InArray(array(0,1)));
        $e->addOption("0", "0", __("Instant", "wpjobboard"));
        $e->addOption(1, 1, __("By Administrator", "wpjobboard"));
        $this->addElement($e);
        
        $e = $this->create("cv_is_public", "select");
        $e->setValue($instance->getConfig("cv_is_public", 1));
        $e->setLabel(__("Resumes Default Visibility", "wpjobboard"));
        $e->setHint("");
        $e->addValidator(new Daq_Validate_InArray(array(0,1)));
        $e->addOption("0", "0", __("Private (not displayed on resumes list)", "wpjobboard"));
        $e->addOption("1", "1", __("Public (visible on resumes list)", "wpjobboard"));
        $this->addElement($e);
        
        $e = $this->create("cv_show_applicant_resume", "checkbox");
        $e->setValue($instance->getConfig("cv_show_applicant_resume"));
        $e->setLabel(__("On Application", "wpjobboard"));
        $e->setHint("");
        $e->addOption(1, 1, __("Allow Employer to view whole user Resume", "wpjobboard"));
        $this->addElement($e);
        
        $e = $this->create("cv_search_bar", Daq_Form_Element::TYPE_SELECT);
        $e->setValue(Wpjb_Project::getInstance()->conf("cv_search_bar", "disabled"));
        $e->addOption("disabled", "disabled", __("Disabled", "wpjobboard"));
        $e->addOption("enabled", "enabled", __("Enabled", "wpjobboard"));
        //$e->addOption("enabled-live", "enabled-live", __("Enabled (with live search)", "wpjobboard"));
        $e->setLabel(__("Search form on jobs list", "wpjobboard"));
        $this->addElement($e);

        apply_filters("wpja_form_init_config_resume", $this);

    }
}

?>