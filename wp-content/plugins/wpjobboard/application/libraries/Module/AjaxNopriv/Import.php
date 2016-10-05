<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Import
 *
 * @author Grzegorz
 */
class Wpjb_Module_AjaxNopriv_Import 
{
    public function apiAction()
    {
        $request = Daq_Request::getInstance();
        $username = $request->post("username");
        $password = $request->post("password");
        $content = $request->post("xml");
        
        $xml = new SimpleXMLElement("<wpjb></wpjb>");
        $user = wp_authenticate($username, $password);
        
        if(!wpjb_conf("xml_import_enabled")) {
            $xml->result = 0;
            $xml->message = __("XML import is disabled.", "wpjobboard");
            echo $xml->asXML();
            exit;
        }
        
        if($user instanceof WP_Error) {
            $xml->result = 0;
            $xml->message = $user->get_error_message();
            echo $xml->asXML();
            exit;
        }
        
        if(!$user->has_cap("import")) {
            $xml->result = 0;
            $xml->message = __("User does not have import capability.");
            echo $xml->asXML();
            exit;
        }
        
        $import = simplexml_load_string($content);
        $result = array("inserted"=>0, "updated"=>0);

        // Meta Tags
        if(!empty($import->metas->meta)) {
            $result = array("inserted"=>0, "updated"=>0, "exists"=>0);
            foreach ($import->metas->meta as $item) {
                $result[Wpjb_Model_Meta::import($item)]++;
            }
        }

        // Jobs
        if(!empty($import->jobs->job)) {
            $result = array("inserted"=>0, "updated"=>0);
            foreach ($import->jobs->job as $job) {
                $result[Wpjb_Model_Job::import($job)]++;
            }
        }

        // Applications
        if(!empty($import->applications->application)) {
            $result = array("inserted"=>0, "updated"=>0);
            foreach ($import->applications->application as $item) {
                $result[Wpjb_Model_Application::import($item)]++;
            }
        }

        // Companies
        if(!empty($import->companies->company)) {
            $result = array("inserted"=>0, "updated"=>0);
            foreach ($import->companies->company as $item) {
                $result[Wpjb_Model_Company::import($item)]++;
            }
        }

        // Candidates
        if(!empty($import->candidates->candidate)) {
            $result = array("inserted"=>0, "updated"=>0);
            foreach ($import->candidates->candidate as $item) {
                $result[Wpjb_Model_Resume::import($item)]++;
            }
        }

        unset($import);
        
        $xml->result = 1;
        echo $xml->asXML();
        
        exit;
        
    }
}

?>
