<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Facebook
 *
 * @author Grzegorz
 */

class Wpjb_Service_Facebook 
{
    public static function share($object)
    {
        try {
            $post = self::_share($object);
            
            //$meta = $object->meta->facebook_share_id->getFirst();
            //$meta->value = $post["id"];
            //$meta->save();
            
        } catch(Exception $e) {
            // @todo: log error
        }
    }
    
    public static function shareTest($object) 
    {
        self::_share($object);
    }
    
    protected static function _share($object)
    {
        $facebook = self::facebook();
        $parameters = array(
            'access_token' => wpjb_conf("facebook_access_token"),
            'message' => wpjb_conf("facebook_share_message"),
            'link' => $object->url(),
            'name' => $object->job_title, 
            'caption' => wpjb_conf("facebook_share_caption")
        );
        
        if($object->job_description) {
            $parameters["description"] = strip_tags($object->job_description);
        }
        
        if($object->getLogoUrl()) {
            $parameters["picture"] = $object->getLogoUrl();
        }
        
        $parser = new Daq_Tpl_Parser();
        $parser->assign("job", $object);
        
        $parameters["message"] = $parser->draw($parameters["message"]);
        $parameters["name"] = $parser->draw($parameters["name"]);
        $parameters["caption"] = $parser->draw($parameters["caption"]);
        
        $newpost = $facebook->api(
           '/me/feed',
           'POST',
           apply_filters("wpjb_facebook_share_params", $parameters, $object)
        );

        return $newpost;
    }

    /**
     * 
     * @return Facebook
     */
    public static function facebook($param = array())
    {
        $path = Wpjb_List_Path::getPath("vendor");
        
        if(!class_exists("FacebookApiException")) {
            require_once $path."/facebook/base_facebook.php";
        }
        if(!class_exists("Facebook")) {
            require_once $path."/facebook/facebook.php";
        }
        
        $default = array(
          'appId'  => wpjb_conf("facebook_app_id"),
          'secret' => wpjb_conf("facebook_app_secret"),
          'cookie' => false
        );
        
        foreach($default as $key => $value) {
            if(!isset($param[$key])) {
                $param[$key] = $value;
            }
        }

        $facebook = new Facebook($param);
        
        return $facebook;
    }

}

?>
