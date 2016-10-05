<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of License
 *
 * @author Grzegorz
 */
class Wpjb_Validate_License
    extends Daq_Validate_Abstract implements Daq_Validate_Interface
{

    public function isValid($value)
    {
        $manager = new Wpjb_Upgrade_Manager;
        $request = $manager->remote("license", array("license"=>$value));

        if(!is_object($request)) {
            $this->setError(__("Could not connect to remote server, please try again later.", "wpjobboard"));
            return false;
        }
        
        if($request->result == 0) {
            $this->setError(sprintf(__("External Error: %s", "wpjobboard"), $request->message));
            return false;
        }
        
        return true;
    }
}

?>
