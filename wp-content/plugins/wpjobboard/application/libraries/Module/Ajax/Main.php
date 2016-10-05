<?php
/**
 * Description of Main
 *
 * @author greg
 * @package 
 */

class Wpjb_Module_Ajax_Main
{
    public static function slugifyAction()
    {
        $list = array("job" => 1, "type" => 1, "category" => 1, 'resume' => 1, 'company' => 1);

        $id = Daq_Request::getInstance()->post("id");
        $title = Daq_Request::getInstance()->post("title");
        $model = Daq_Request::getInstance()->post("object");

        if(!isset($list[$model])) {
            die;
        }

        die(Wpjb_Utility_Slug::generate($model, $title, $id));
    }
    
    public function hideAction()
    {
        $config = Wpjb_Project::getInstance();
        $config->setConfigParam("activation_message_hide", 1);
        $config->saveConfig();
        
        exit(1);
    }

    public function cleanupAction()
    {

    }
    
    public function googleapiAction()
    {
        $address = Daq_Request::getInstance()->getParam("address", "London, United Kingdom");
        
        $query = http_build_query(array(
            "address" => $address,
            "sensor" => "false",
            "key" => wpjb_conf("google_api_key")
        ));
        $url = "https://maps.googleapis.com/maps/api/geocode/json?".$query;
        
        $response = wp_remote_get($url);
        if($response instanceof WP_Error) {
            $result = json_encode(array(
                "status" => "ERROR",
                "error_message"=>$response->get_error_message()
            ));
        } else {
            $result = $response["body"];
        }
        
        echo $result;
        die;
    }
}

?>