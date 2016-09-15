<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Google
 *
 * @author Grzegorz
 */
class Wpjb_Form_Admin_Config_Google extends Daq_Form_Abstract
{
    public $name = null;

    public function init()
    {
        $this->name = __("Google APIs", "wpjobboard");
        $instance = Wpjb_Project::getInstance();

        $e = $this->create("google_api_key");
        $e->setValue($instance->getConfig("google_api_key"));
        $e->setLabel(__("Google API Key", "wpjobboard"));
        $e->addFilter(new Daq_Filter_Trim);
        $e->setRenderer(array($this, "fieldApiKey"));
        $this->addElement($e);
       
        
        apply_filters("wpja_form_init_config_google", $this);

    }
    
    public function fieldApiKey(Daq_Form_Element $field, $form) 
    {
        $button = new Daq_Helper_Html("a", array(
            "id" => "wpjb-google-api-validate",
            "class" => "button-secondary",
            "href" => "#"
        ), __("Check"));
        return $field->render() . $button->render();
    }
}

?>
