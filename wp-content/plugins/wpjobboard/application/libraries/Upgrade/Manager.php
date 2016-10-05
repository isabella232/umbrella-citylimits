<?php
/**
 * Description of Manager
 *
 * @author greg
 * @package 
 */

class Wpjb_Upgrade_Manager
{
    public static $url = "http://api.wpjobboard.net/v1";
    
    const SLUG = "wpjobboard";
    
    const PATH = "wpjobboard/index.php";

    public $version = null;
    
    protected $_message = null;
    
    public static function connect($current_version)
    {
        $self = new self;
        $self->version = $current_version;

	add_filter('pre_set_site_transient_update_plugins', array($self, 'check'));
	add_filter('plugins_api', array($self, 'info'), 10, 3);
        
        
        $transient = get_site_transient("update_plugins");
        if(!isset($transient->response[self::PATH])) {
            return;
        }
        
        $transient = $transient->response[self::PATH];
        
        if($transient->downloads < 0) {
            add_filter("after_plugin_row_".self::PATH, array($self, "upgradeNotice"));
            add_action("admin_enqueue_scripts", array($self, "adminEnqueueScripts"));
        }
                
                
    }
    
    protected static function _m()
    {
        $transient = get_site_transient("update_plugins");
        $transient = $transient->response[self::PATH];
        
        if($transient->downloads == -1) {
            $url = esc_attr(wpjb_admin_url("config", "edit", null, array("form"=>"license")));
            $m =  __('Cannot update! Please enter your license number in <a href="%s">Settings (WPJB) / License</a>.', "wpjobboard");
            return sprintf($m, $url);
        } else {
            $url = esc_attr("http://wpjobboard.net/contact");
            $m = __('Cannot update! Your access to downloads expired. Please contact <a href="%s">WPJobBoard Support</a> if you would like to renew it.', "wpjobboard");
            return sprintf($m, $url);
        }
    }
    
    public function remote($action, $args = array())
    {
        $url = trim(self::$url, "/")."/".$action;
        
        $args["site_url"] = get_bloginfo("url");
        $args["site_version"] = $this->version;
        
        if(!isset($args["license"])) {
            $args["license"] = wpjb_conf("license_key");
        }
        
        $request = wp_remote_post($url, array("body"=>$args));

        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return json_decode($request["body"]);
	} else {
            return false;
        }
    }
    
    public function check($transient)
    {
        if (empty($transient->checked) && isset($transient->response[self::PATH])) {
            return $transient;
        }

        $remote = $this->remote("version");
        
        if($remote === false || $remote->result == 0) {
            return $transient;
        }
        
        $obj = new stdClass();
        $obj->slug = self::SLUG;
        $obj->new_version = $remote->data->version;
        $obj->url = self::$url."/download/license/".wpjb_conf("license_key");
        $obj->package = self::$url."/download/license/".wpjb_conf("license_key");
        $obj->downloads = $remote->data->downloads;
        
        if($remote->data->downloads < 0) {
            $obj->upgrade_notice = "**WPJB-UPGRADE-NOTICE**";
        }
        
        if (version_compare($this->version, $remote->data->version, '<')) {
            $transient->response[self::PATH] = $obj;
        }
        
        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $false
     * @param array $action
     * @param object $arg
     * @return bool|object
     */
    public function info($false, $action, $arg)
    {
        if (!isset($arg->slug) || $arg->slug != self::SLUG) {
            return false;
        }
        
        $request = $this->remote("info", array("license"=>wpjb_conf("license_key")));
        
        if(is_object($request) && isset($request->data)) {
            
            $data = $request->data;
            $data->sections = (array)$data->sections;
            
            return $data;
        } else {
            return false;
        }
    }
    
    public function upgradeNotice($param)
    {
        echo '<tr class="plugin-update-tr wpjb-update-error"><td colspan="3" class="plugin-update"><div class="wpjb-upgrade-error">'.self::_m().'</div></td></tr>';
    }
    
    public function adminEnqueueScripts($hook)
    {
        wp_register_style("wpjb-admin-upgrade-css", plugins_url()."/wpjobboard/public/css/admin-upgrade.css");
        wp_register_script("wpjb-admin-upgrade-js", plugins_url()."/wpjobboard/public/js/admin-upgrade.js", array("jquery"));
        wp_localize_script("wpjb-admin-upgrade-js", "wpjb_admin_upgrade_lang", array(
            "message" => self::_m()
        ));
        
        if($hook == "plugins.php" || $hook == "update-core.php") {
            wp_enqueue_script("wpjb-admin-upgrade-js");
            wp_enqueue_style("wpjb-admin-upgrade-css");
        }
        
        return $hook;
    }
    
    protected static function sort($a, $b) 
    {
        return version_compare($a->getVersion(), $b->getVersion());
    }
    
    public static function update()
    {
        $mask = dirname(__FILE__)."/*.php";
        $version = wpjb_conf("version", "4.0.0");

        if($version == Wpjb_Project::VERSION) {
            return;
        }

        $flist = wpjb_glob($mask);
        $uplist = array();
        
        foreach($flist as $file) {
            $name = pathinfo($file);
            $name = str_replace(".php", "", $name["basename"]);
            if(is_numeric($name)) {
                $name = "Wpjb_Upgrade_".$name;
                $update = new $name;
                if(!$update instanceof Wpjb_Upgrade_Abstract) {
                    continue;
                }

                if(version_compare($version, $update->getVersion()) === -1) {
                    $uplist[] = $update;
                }
            }
        }
        
        uasort($uplist, array(__CLASS__, "sort"));
        
        foreach($uplist as $update) {
            $update->execute();
        }

        $instance = Wpjb_Project::getInstance();
        $instance->setConfigParam("version", Wpjb_Project::VERSION);
        $instance->saveConfig();
    }
    
    public static function upgrade($version = null)
    {
        $mask = dirname(__FILE__)."/*.php";
        if($version === null) {
            $version = Wpjb_Project::getInstance()->conf("version");
        }

        if($version == Wpjb_Project::VERSION) {
            return;
        }

        $flist = glob($mask);
        if(!is_array($flist)) {
            $flist = array();
        }

        foreach($flist as $file) {
            $name = pathinfo($file);
            $name = str_replace(".php", "", $name["basename"]);
            if(is_numeric($name)) {
                $name = "Wpjb_Upgrade_".$name;
                $update = new $name;
                if(!$update instanceof Wpjb_Upgrade_Abstract) {
                    continue;
                }

                if(version_compare($version, $update->getVersion()) === -1) {
                    $update->execute();
                }
            }
        }

        $instance = Wpjb_Project::getInstance();
        $instance->setConfigParam("version", Wpjb_Project::VERSION);
        $instance->saveConfig();
    }




}

?>