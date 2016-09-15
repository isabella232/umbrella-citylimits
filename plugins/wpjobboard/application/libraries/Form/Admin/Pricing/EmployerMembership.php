<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmployerMembership
 *
 * @author Grzegorz
 */
class Wpjb_Form_Admin_Pricing_EmployerMembership extends Wpjb_Form_Admin_Pricing
{

    public function init()
    {
        $e = $this->create("price_for", "hidden");
        $e->setValue(Wpjb_Model_Pricing::PRICE_EMPLOYER_MEMBERSHIP);
        $this->addElement($e);
        
        $e = $this->create("visible");
        /* @var $e Daq_Form_Element */
        $e->setRequired(true);
        $e->setValue($this->_object->meta->visible);
        $e->setLabel(__("Recurrence", "wpjobboard"));
        $e->addFilter(new Daq_Filter_Int());
        $e->setHint(__("How many days the membership will be valid.", "wpjobboard"));
        $e->setBuiltin(false);
        $e->setOrder(102);
        $this->addElement($e);
        
        $data = unserialize($this->getObject()->meta->package->value());
        
        $price = array(
            array(
                "title" => __("Job Posting", "wpjobboard"),
                "price_for" => Wpjb_Model_Pricing::PRICE_SINGLE_JOB,
                "hint" => __("Select which Job Postings will be included in this package and how many times Employer will be able to use them.", "wpjobboard"),
                "value" => $data[Wpjb_Model_Pricing::PRICE_SINGLE_JOB],
                
            ),
            array(
                "title" => __("Resumes Access", "wpjobboard"),
                "price_for" => Wpjb_Model_Pricing::PRICE_SINGLE_RESUME,
                "hint" => "",
                "value" => $data[Wpjb_Model_Pricing::PRICE_SINGLE_RESUME],
            ),
        );
        
        foreach($price as $p) {
            
            $pfor = $p["price_for"];
            
            $query = new Daq_Db_Query();
            $query->from("Wpjb_Model_Pricing t");
            $query->where("price_for = ?", $pfor);
            
            
            $e = $this->create("items_".$pfor, "checkbox");
            $e->setLabel($p["title"]);
            $e->setHint($p["hint"]);
            foreach($query->execute() as $item) {
                $e->addOption($item->id, $item->id, $item->title);
            }
            $e->setRenderer("wpjb_admin_pricing_render");
            $e->setOrder(103);
            $e->setMaxChoices(100);
            $e->setValue($p["value"]);
            $this->addElement($e);
            
            $e = $this->create("items_".$pfor."_usage", "checkbox");
            $this->addElement($e, "_internal");
        }
        
        parent::init();
        
    }
    
    public function save()
    {
        parent::save();
        
        $object = $this->getObject();
        
        $data = array(
            Wpjb_Model_Pricing::PRICE_SINGLE_JOB => array(),
            Wpjb_Model_Pricing::PRICE_SINGLE_RESUME => array(),
        );
        
        foreach(array_keys($data) as $key) {
            $post = $this->value("items_".$key);
            foreach((array)$post as $id => $usage) {
                if($usage["status"] != "disabled") {
                    $data[$key][$id] = $usage;
                }
            }
        }
        
        $meta = $object->meta->package->getFirst();
        $meta->object_id = $object->getId();
        $meta->value = serialize($data);
        $meta->save();
    }
    
}

?>
