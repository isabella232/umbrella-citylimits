<?php
/**
 * Description of Resume
 *
 * @author greg
 * @package
 */

class Wpjb_Form_Resume extends Wpjb_Form_Abstract_Resume
{

    public function init()
    {
        parent::init();
        $this->removeElement("is_approved");
        $this->removeElement("status");
        
        if($this->isNew() && current_user_can("manage_resumes")) {
            $user = new WP_User(get_current_user_id());
            $default = array("first_name", "last_name", "user_email", "user_url");
            foreach($default as $key) {
                if($this->hasElement($key)) {
                    $this->getElement($key)->setValue($user->$key);
                }
            }
        }
        
        add_filter("wpjr_form_init_resume", array($this, "apply"), 9);
        apply_filters("wpjr_form_init_resume", $this);
    }
    
    public function save($append = array())
    {
        if($this->hasElement("modified_at")) {
            $this->removeElement("modified_at");
        }

        $append = array("modified" => date("Y-m-d H:i:s"));
        
        if($this->isNew()) {
            $title = trim($this->value("first_name")." ".$this->value("last_name"));
            $append["user_id"] = get_current_user_id();
            $append["candidate_slug"] = Wpjb_Utility_Slug::generate("resume", $title, $this->getId());
        }

        parent::save($append);
        
        apply_filters("wpjr_form_save_resume", $this);
        
    }

}

?>