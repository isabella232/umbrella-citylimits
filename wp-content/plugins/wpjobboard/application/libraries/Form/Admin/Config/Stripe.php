<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stripe
 *
 * @author Grzegorz
 */
class Wpjb_Form_Admin_Config_Stripe extends Wpjb_Form_Abstract_Payment 
{
    public function init()
    {
        parent::init();
        
        $this->addGroup("stripe", __("Stripe", "wpjobboard"));
        
        $e = $this->create("secret_key");
        $e->setValue($this->conf("secret_key"));
        $e->setLabel(__("Secret Key", "wpjobboard"));
        $this->addElement($e, "stripe");
        
        $e = $this->create("publishable_key");
        $e->setValue($this->conf("publishable_key"));
        $e->setLabel(__("Publishable Key", "wpjobboard"));
        $this->addElement($e, "stripe");
    }
}

?>
