<?php

/**
 * Description of Login
 *
 * @author greg
 * @package
 */

class Wpjb_Form_Resumes_Register extends Daq_Form_Abstract
{
    public function init()
    {
        if(!is_admin()) {
            $e = $this->create("_wpjb_action", "hidden");
            $e->setValue("reg_candidate");
            $this->addElement($e, "_internal");
        }
        
        $this->addGroup("default", __("Register", "wpjobboard"));

        $e = $this->create("firstname");
        $e->setLabel(__("First name", "wpjobboard"));
        $e->setRequired(true);
        $this->addElement($e, "default");
        
        $e = $this->create("lastname");
        $e->setLabel(__("Last name", "wpjobboard"));
        $e->setRequired(true);
        $this->addElement($e, "default");
        
        $e = $this->create("user_login");
        $e->setLabel(__("Username", "wpjobboard"));
        $e->setRequired(true);
        $e->addFilter(new Daq_Filter_Trim());
        $e->addFilter(new Daq_Filter_WP_SanitizeUser());
        $e->addValidator(new Daq_Validate_WP_Username());
        $this->addElement($e, "default");
        
        $e = $this->create("user_password", "password");
        $e->setLabel(__("Password", "wpjobboard"));
        $e->addFilter(new Daq_Filter_Trim());
        $e->addValidator(new Daq_Validate_StringLength(4, 32));
        $e->addValidator(new Daq_Validate_PasswordEqual("user_password2"));
        $e->setRequired(true);
        $this->addElement($e, "default");

        $e = $this->create("user_password2", "password");
        $e->setLabel(__("Password (repeat)", "wpjobboard"));
        $e->setRequired(true);
        $this->addElement($e, "default");

        $e = $this->create("user_email");
        $e->setLabel(__("E-mail", "wpjobboard"));
        $e->addFilter(new Daq_Filter_Trim());
        $e->addValidator(new Daq_Validate_WP_Email());
        $e->setRequired(true);
        $this->addElement($e, "default");

        apply_filters("wpjr_form_init_register", $this);
    }

    public function save()
    {      
        $id = wp_insert_user(array(
            "user_login" => $this->getElement("user_login")->getValue(), 
            "user_email" => $this->getFieldValue("user_email"), 
            "user_pass" => $this->getElement("user_password")->getValue(),
            "first_name" => $this->getFieldValue("firstname"),
            "last_name" => $this->getFieldValue("lastname"),
            "role" => "subscriber"
        ));

        $fullname = $this->value("firstname")." ".$this->value("lastname");
        
        if(wpjb_conf("cv_approval") == 1) {
            $active = 0; // manual approval
        } else {
            $active = 1;
        }
        
        $resume = new Wpjb_Model_Resume();
        $resume->candidate_slug = Wpjb_Utility_Slug::generate(Wpjb_Utility_Slug::MODEL_RESUME, $fullname);
        $resume->phone = "";
        $resume->user_id = $id;
        $resume->headline = "";
        $resume->description = "";
        $resume->created_at = date("Y-m-d");
        $resume->modified_at = date("Y-m-d");
        $resume->candidate_country = wpjb_locale();
        $resume->candidate_zip_code = "";
        $resume->candidate_state = "";
        $resume->candidate_location = "";
        $resume->is_public = wpjb_conf("cv_is_public", 1);
        $resume->is_active = $active;
        $resume->save();
        
        apply_filters("wpjr_form_save_register", $this);
        
        return $resume->id;
    }
    
    public function getId()
    {
        return null;
    }

}

?>