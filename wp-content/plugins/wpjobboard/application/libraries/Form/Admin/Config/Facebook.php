<?php
/**
 * Description of Frontend
 *
 * @author greg
 * @package
 */

class Wpjb_Form_Admin_Config_Facebook extends Daq_Form_Abstract
{
    public $name = null;
    
    public $facebook = "";

    public function init()
    {
        $this->name = __("Facebook", "wpjobboard");
        $instance = Wpjb_Project::getInstance();

        $e = $this->create("facebook_app_id");
        $e->setValue($instance->getConfig("facebook_app_id"));
        $e->setLabel(__("App ID", "wpjobboard"));
        $this->addElement($e);
        
        $e = $this->create("facebook_app_secret");
        $e->setValue($instance->getConfig("facebook_app_secret"));
        $e->setLabel(__("App Secret", "wpjobboard"));
        $this->addElement($e);
        
        $this->executePostSave(null);

        apply_filters("wpja_form_init_config_facebook", $this);

    }
    
    public function renderUser($field)
    {
        return $this->facebook;
    }
    
    public function executeInit($controller)
    {
        $controller->view->submit_action = wpjb_admin_url("config", "edit", null, array("form"=>"facebook"));
        
        $access_token = wpjb_conf("facebook_access_token");
        $user_token = wpjb_conf("facebook_user_token");
        $request = Daq_Request::getInstance();
        $project = Wpjb_Project::getInstance();
        $msg = "";
        
        $facebook = Wpjb_Service_Facebook::facebook();
        
        if($request->get("reset")==1 && $request->get("noheader")==1) {
            $project->setConfigParam("facebook_user_token", null);
            $project->setConfigParam("facebook_access_token", null);
            $project->saveConfig();
            
            wp_redirect(wpjb_admin_url("config", "edit", null, array("form"=>"facebook")));
            exit;
        }
        
        if($request->get("auth") == 1) {
            $user_token = $facebook->getAccessToken();
            $project->setConfigParam("facebook_user_token", $user_token);
            $project->saveConfig();
        }
        
        if($request->post("facebook_access_token_revoke")) {
            $user_token = null;
            $access_token = null;
            $project->setConfigParam("facebook_user_token", $user_token);
            $project->setConfigParam("facebook_access_token", $access_token);
            $project->saveConfig();
            
            $facebook->destroySession();
            
        } elseif($request->post("facebook_access_token")) {
            $access_token = $request->post("facebook_access_token");
            $project->setConfigParam("facebook_access_token", $access_token);
            $project->saveConfig();
        }
        
        if($access_token) {
            try {
                $facebook->setAccessToken($access_token);
                $me = $facebook->api('/me');
                $msg.= sprintf(__("Authenticated as <strong>%s</strong>", "wpjobboard"), $me["name"])." ";
                //$msg.= "<em>".$this->_radio("revoke", "revoke_access", __("Revoke all Facebook access", "wpjobboard"))."</em>";
                $msg .= '<input type="submit" id="revoke" name="facebook_access_token_revoke" value="'.__("Revoke all Facebook access", "wpjobboard").'" />';

            } catch(Exception $e) {
                $msg.= "<p style='color:red;font-weight:bold;margin-bottom:10px'>".$e->getMessage()."</p>";
                $user_token = null;
            }
        } elseif($user_token) {

            try {
                
                $me = $facebook->api('/me');
                $msg.= $this->_radio($me["id"], $user_token, $me["name"]);
                
                $facebook->setAccessToken($user_token);
                $accounts = $facebook->api(
                   '/me/accounts',
                   'GET',
                   array(
                      'access_token' => $user_token
                   )
                );
                
                if(isset($accounts["data"]) && is_array($accounts["data"])) {
                    $pages = $accounts["data"];
                } else {
                    $pages = array();
                }
                
                foreach($pages as $page) {
                    $name = $page["name"]." (".$page["category"].")";
                    $msg.= $this->_radio("id-".$page["id"], $page["access_token"], $name);
                }
                $controller->view->submit_title = __("Step 2/3", "wpjobboard");
                
            } catch(Exception $e) {
                $msg.= "<p style='color:red;font-weight:bold;margin-bottom:10px'>".$e->getMessage()."</p>";
                $user_token = null;
            }
        }
        
        if(!$user_token) {
            
            $params = array(
              'scope' => 'publish_pages,manage_pages',
              'fbconnect' =>  1,
              'redirect_uri' => wpjb_admin_url("config", "edit", null, array("form"=>"facebook", "auth"=>1))
            );
            
            $pinit = array();

            if($request->post("facebook_app_id")) {
                $pinit["appId"] = $request->post("facebook_app_id");
            }
            if($request->post("facebook_app_secret")) {
                $pinit["secret"] = $request->post("facebook_app_secret");
            }
            
            $facebook = Wpjb_Service_Facebook::facebook($pinit);
            
            $url = $facebook->getLoginUrl($params);
            $msg.= '<a href="'.esc_attr($url).'" class="button-primary">'.__("Connect with Facebook to continue", "wpjobboard").'</a> ';
            
            $url = wpjb_admin_url("config", "edit", null, array("form"=>"facebook", "reset"=>1, "noheader"=>1));
            $msg.= '<a href="'.esc_attr($url).'" class="button-secondary">'.__("Reset Facebook configuration", "wpjobboard").'</a> ';
            $controller->view->submit_title = __("Step 1/3", "wpjobboard");
        }
        
        $this->facebook = $msg;
    }
    
    protected function _radio($id, $value, $name)
    {
        $checked = null;
        if(wpjb_conf("facebook_access_token") == $value) {
            $checked = "checked";
        }
        
        $input = new Daq_Helper_Html("input", array(
            "type" => "radio",
            "id" => $id,
            "name" => "facebook_access_token",
            "value" => $value,
            "checked" => $checked
        ));
        
        return '<label for="'.$id.'">'.$input.' '.esc_html($name).'</label><br/>'; 
    }
    
    public function executePostSave($controller)
    {
        
        $instance = Wpjb_Project::getInstance();
        $request = Daq_Request::getInstance();
        
        if(wpjb_conf("facebook_app_id") && wpjb_conf("facebook_app_secret")) {
            $e = $this->create("_facebook_user");
            $e->setRenderer(array($this, "renderUser"));
            $e->setLabel(__("Account", "wpjobboard"));
            $this->addElement($e);
        }
        
        if($request->post("facebook_access_token_revoke")) {
            return;
        }
        
        if(wpjb_conf("facebook_access_token")) {
            $e = $this->create("facebook_share", "checkbox");
            $e->setValue($instance->getConfig("facebook_share", 1));
            $e->addOption(1, 1, __("Share new jobs on Facebook", "wpjobboard"));
            $e->addFilter(new Daq_Filter_Int());
            $this->addElement($e);
            
            $e = $this->create("facebook_share_message");
            $e->setLabel(__("Message", "wpjobboard"));
            $e->setRequired(true);
            $e->setValue($instance->getConfig("facebook_share_message"));
            $e->setAttr("placeholder", __("E.g. New job posting!", "wpjobboard"));
            $this->addElement($e);
            
            $e = $this->create("facebook_share_name");
            $e->setLabel(__("Title", "wpjobboard"));
            $e->setValue($instance->getConfig("facebook_share_name", '{$job.job_title}'));
            $this->addElement($e);
            
            $e = $this->create("facebook_share_caption");
            $e->setLabel(__("Caption", "wpjobboard"));
            $e->setValue($instance->getConfig("facebook_share_caption"), get_option("blogname"));
            $e->setAttr("placeholder", __("Site headline here", "wpjobboard"));
            $this->addElement($e);
            
            $e = $this->create("_facebook_placeholder");
            $e->setRenderer("wpjb_admin_variable_renderer");
            $e->setHint(__("You can use above variables in Message, Title and Caption", "wpjobboard"));
            $e->setValue(array("job"));
            $this->addElement($e);
            
            add_action("wpjb_config_edit_buttons", array($this, "executeButtons"));
        }
        
        if($request->post("facebook_share_test") && $controller) {
            $list = new Daq_Db_Query();
            $list->select("*");
            $list->from("Wpjb_Model_Job t");
            $list->limit(1);
            $result = $list->execute();
            
            if(empty($result)) {
                $controller->view->_flash->addError(__("Facebook: You need to have at least one posted job to send test tweet.", "wpjobboard"));
            } else {
                $job = $result[0];
                try {
                    Wpjb_Service_Facebook::shareTest($job);
                    $controller->view->_flash->addInfo(__("Share has been sent, please check your Facebook account.", "wpjobboard"));
                } catch(Exception $e) {
                    $controller->view->_flash->addError($e->getMessage());
                }
            }
        }
    }
    
    public function executeButtons()
    {
        $button = new Daq_Helper_Html("input", array(
            "type" => "submit",
            "name" => "facebook_share_test",
            "value" => __("Send Test Share", "wpjobboard")
        ));
        
        echo $button->render();
    }
}

?>