<?php
/**
 * Description of Resume
 *
 * @author greg
 * @package 
 */

class Wpjb_Form_Abstract_Resume extends Daq_Form_ObjectAbstract
{
    protected $_custom = "wpjb_form_resume";
    
    protected $_key = "resume";
    
    protected $_model = "Wpjb_Model_Resume";

    public function _exclude()
    {
        if($this->_object->getId()) {
            return array("id" => $this->_object->getId());
        } else {
            return array();
        }
    }
    
    public function init()
    {
        $this->_upload = array(
            "path" => wpjb_upload_dir("{object}", "{field}", "{id}", "basedir"),
            "object" => "resume",
            "field" => null,
            "id" => wpjb_upload_id($this->getId())
        );
        
        $this->addGroup("_internal", "");
        $this->addGroup("default", __("Account Information", "wpjobboard"));
        $this->addGroup("location", __("Address", "wpjobboard"));
        $this->addGroup("resume", __("Resume", "wpjobboard"));
        $this->addGroup("experience", __("Experience", "wpjobboard"));
        $this->addGroup("education", __("Education", "wpjobboard"));

        $this->_group["experience"]->setAlwaysVisible(true);
        $this->_group["education"]->setAlwaysVisible(true);

        $user = new WP_User($this->getObject()->user_id);
        
        $e = $this->create("first_name");
        $e->setLabel(__("First Name", "wpjobboard"));
        $e->setRequired(true);
        $e->setValue($user->first_name);
        $this->addElement($e, "default");
        
        $e = $this->create("last_name");
        $e->setLabel(__("Last Name", "wpjobboard"));
        $e->setRequired(true);
        $e->setValue($user->last_name);
        $this->addElement($e, "default");

        $def = wpjb_locale();
        $e = $this->create("candidate_country", "select");
        $e->setLabel(__("Country", "wpjobboard"));
        $e->setValue(($this->_object->candidate_country) ? $this->_object->candidate_country : $def);
        $e->addOptions(wpjb_form_get_countries());
        $e->addClass("wpjb-location-country");
        $this->addElement($e, "location");
        
        $e = $this->create("candidate_state");
        $e->setLabel(__("State", "wpjobboard"));
        $e->setValue($this->_object->candidate_state);
        $e->addClass("wpjb-location-state");
        $this->addElement($e, "location");

        $e = $this->create("candidate_zip_code");
        $e->setLabel(__("Zip-Code", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(null, 20));
        $e->setValue($this->_object->candidate_zip_code);
        $this->addElement($e, "location");

        $e = $this->create("candidate_location");
        $e->setValue($this->_object->candidate_location);
        $e->setRequired(true);
        $e->setLabel(__("City", "wpjobboard"));
        $e->setHint(__('For example: "Chicago", "London", "Anywhere" or "Telecommute".', "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(null, 120));
        $e->addClass("wpjb-location-city");
        $this->addElement($e, "location");
        
        $e = $this->create("user_email");
        $e->setRequired(true);
        $e->setLabel(__("Email Address", "wpjobboard"));
        $e->setHint(__('This field will be shown only to registered employers.', "wpjobboard"));
        $e->addValidator(new Daq_Validate_Email(array("exclude"=>$user->ID)));
        $e->setValue($user->user_email);
        $this->addElement($e, "default");

        $e = $this->create("phone");
        $e->setLabel(__("Phone Number", "wpjobboard"));
        $e->setHint(__('This field will be shown only to registered employers.', "wpjobboard"));
        $e->setValue($this->_object->phone);
        $this->addElement($e, "default");

        $e = $this->create("user_url");
        $e->setLabel(__("Website", "wpjobboard"));
        $e->setHint(__('This field will be shown only to registered employers.', "wpjobboard"));
        $e->addFilter(new Daq_Filter_WP_Url());
        $e->addValidator(new Daq_Validate_Url());
        $e->setValue($user->user_url);
        $this->addElement($e, "default");
        
        $e = $this->create("is_public", "checkbox");
        $e->setLabel(__("Privacy", "wpjobboard"));
        $e->addOption(1, 1, __("Show my resume in search results.", "wpjobboard"));
        $e->setValue($this->_object->is_public);
        $e->addFilter(new Daq_Filter_Int());
        $this->addElement($e, "default");
        
        $e = $this->create("is_active", "checkbox");
        $e->setValue($this->_object->is_active);
        $e->setLabel(__("Status", "wpjobboard"));
        $e->addOption(1, 1, __("Resume is approved.", "wpjobboard"));
        $this->addElement($e, "_internal");
        
        $e = $this->create("modified_at", "text_date");
        $e->setDateFormat(wpjb_date_format());
        $e->setValue($this->ifNew(date("Y-m-d"), $this->_object->modified_at));
        $this->addElement($e, "_internal");
        
        $e = $this->create("created_at", "text_date");
        $e->setDateFormat(wpjb_date_format());
        $e->setValue($this->ifNew(date("Y-m-d"), $this->_object->created_at));
        $this->addElement($e, "_internal");

        $e = $this->create("image", "file");
        $e->setLabel(__("Your Photo", "wpjobboard"));;
        $e->addValidator(new Daq_Validate_File_Default());
        $e->addValidator(new Daq_Validate_File_Ext("jpg,jpeg,gif,png"));
        $e->addValidator(new Daq_Validate_File_Size(300000));
        $e->setUploadPath($this->_upload);
        $e->setRenderer("wpjb_form_field_upload");
        $this->addElement($e, "default");

        $e = $this->create("category", "select");
        $e->setLabel(__("Category", "wpjobboard"));
        $e->setValue($this->_object->getTagIds("category"));
        $e->addOptions(wpjb_form_get_categories());
        $this->addElement($e, "resume");
        $this->addTag($e);

        $e = $this->create("headline");
        $e->setLabel(__("Professional Headline", "wpjobboard"));
        $e->setHint(__("Describe yourself in few words, for example: Experienced Web Developer", "wpjobboard"));
        $e->addValidator(new Daq_Validate_StringLength(1, 120));
        $e->setValue($this->_object->headline);
        $this->addElement($e, "resume");
        
        $e = $this->create("description", "textarea");
        $e->setLabel(__("Profile Summary", "wpjobboard"));
        $e->setHint(__("Use this field to list your skills, specialities, experience or goals", "wpjobboard"));
        $e->setValue($this->_object->description);
        $e->setEditor(Daq_Form_Element_Textarea::EDITOR_TINY);
        $this->addElement($e, "resume");


    }

    public function save($append = array())
    {
        $fullname = array("first_name", "last_name");
        foreach($fullname as $name) {
            if($this->hasElement($name) && $this->getObject()->user_id) {
                update_user_meta($this->getObject()->user_id, $name, $this->value($name));
            }
        }
        
        parent::save($append);
        
        $user = $this->getObject()->getUser(true);
        $names = array_merge($user->getFieldNames(), $fullname);
        $userdata = array("ID"=>$user->ID);
        $user = new WP_User($this->getObject(true)->ID);
        $update = false;
        
        foreach($names as $key) {
            if($this->hasElement($key)  && !in_array($key, array("user_login", "user_pass"))) {
                $userdata[$key] = $this->value($key);
                $update = true;
            }
        }
        
        if($update) {
            wp_update_user($userdata);
        }
        
        $temp = wpjb_upload_dir("resume", "", null, "basedir");
        $finl = dirname($temp)."/".$this->getId();
        wpjb_rename_dir($temp, $finl);
        
        

    }

    public function dump()
    {
        $dump = parent::dump();
        $count = count($dump);
        
        for($i=0; $i<$count; $i++) {
            if(in_array($dump[$i]->name, array("experience", "education"))) {
                $dump[$i]->editable = false;
            }
        }
        
        return $dump;
    }
    
}

?>