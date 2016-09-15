<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Contact
 *
 * @author Grzegorz
 */
class Wpjb_Form_Resumes_Purchase extends Daq_Form_Abstract
{
    protected function _listings()
    {
        $price_for_c = Wpjb_Model_Pricing::PRICE_SINGLE_RESUME;
        
        $listing = array();
        $query = new Daq_Db_Query();
        $result = $query->select("t.*")
            ->from("Wpjb_Model_Pricing t")
            ->order("title")
            ->where("is_active = 1")
            ->where("price_for IN(?)", $price_for_c)
            ->execute();
        
        foreach($result as $pricing) {
            $listing[] = array(
                "id" => $pricing->id,
                "key" => $pricing->price_for."_0_".$pricing->id,
                "title" => $pricing->title
            );
        }
        
        if(!Wpjb_Model_Company::current()) {
            return $listing;
        }
        
        foreach(Wpjb_Model_Company::current()->membership() as $membership) {
            $package = new Wpjb_Model_Pricing($membership->package_id);
            $data = $membership->package();
            
            if(!isset($data[$price_for_c])) {
                continue;
            }
            
            foreach($data[$price_for_c] as $id => $use) {
                
                $pricing = new Wpjb_Model_Pricing($id);
                
                if(!$pricing->exists()) {
                    continue;
                }
                
                $listing[] = array(
                    "id" => $package->id,
                    "key" => $package->price_for."_".$membership->id."_".$pricing->id,
                    "title" => $package->title." / ".$pricing->title
                );
            }
            
        }

        return $listing;
    }
    
    public function init() 
    {
        $this->addGroup("purchase", __("Purchase", "wpjobboard"));
        
        $e = $this->create("purchase", "hidden");
        $e->setValue(1);
        $this->addElement($e, "_internal");
        
        $e = $this->create("listing_type", "radio");
        $e->setLabel(__("Listing Type", "wpjobboard"));
        $e->setRequired(true);
        $listings = $this->_listings();
        foreach($listings as $p) {
            $e->addOption($p["key"], $p["key"], $p["title"]);
        }
        $e->setRenderer("wpjb_form_helper_resume_listing");
        $e->addValidator(new Wpjb_Validate_MembershipLimit(Wpjb_Model_Pricing::PRICE_SINGLE_RESUME));
        $this->addElement($e, "purchase");
        
        $e = $this->create("payment_method", "select");
        $e->setLabel(__("Payment Method", "wpjobboard"));
        $e->setRequired(true);
        $factory = Wpjb_Project::getInstance()->payment;
        $engines = $factory->getEnabled();
        foreach($engines as $engine) {
            $engine = new $engine;
            $e->addOption($engine->getEngine(), $engine->getEngine(), $engine->getCustomTitle());
        }
        if(!empty($engines)) {
            $e->setValue(key($engines));
        }
        $this->addElement($e, "purchase");
        
        $e = $this->create("email");
        $e->setLabel(__("Your Email", "wpjobboard"));
        $e->setRequired(true);
        $e->addValidator(new Daq_Validate_Email);
        $e->setValue(wp_get_current_user()->user_email);
        $this->addElement($e, "purchase");
        
        apply_filters("wpjr_form_init_purchase", $this);
    }
}

?>
