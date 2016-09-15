<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Radio
 *
 * @author greg
 */
class Daq_Form_Element_Radio extends Daq_Form_Element_Multi implements Daq_Form_Element_Interface
{
    protected $_maxChoices = 1;
    
    public final function getType()
    {
        return "radio";
    }
    
    public function setMaxChoices($choices) 
    {
        if($choices > 1) {
            throw new Exception("Radio input cannot have more than one selected option.");
        }
        
        parent::setMaxChoices($choices);
    }
    
    public function render() 
    {
        $options = array(
            "id" => $this->getName(),
            "name" => $this->getName(),
            "class" => $this->getClasses()
        );
        
        $options += $this->getAttr();
        
        $html = array();
        
        foreach($this->getOptions() as $k => $v) {
            
            $id = $this->getName()."-".$v["key"];
            $checked = null;
            
            if(in_array($v["value"], (array)$this->getValue())) {
                $checked = "checked";
            }
            
            $o = new Daq_Helper_Html("input", array(
                "type" => "radio",
                "value" => $v["value"],
                "checked" => $checked,
                "name" => $this->getName(),
                "classes" => $this->getClasses(),
                "id" => $id
            ));

            $l = new Daq_Helper_Html("label", array("for"=>$id), $v["desc"]);
            $text = $o->render()." ".$l->render();
            $html[] = $text;
        }
        
        return join("<br/>", $html);
    }
}

?>
