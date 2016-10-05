<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Checkbox
 *
 * @author greg
 */
class Daq_Form_Element_Checkbox extends Daq_Form_Element_Multi 
{
    public final function getType()
    {
        return "checkbox";
    }
    
    //put your code here
    public function render() 
    {
        $options = array(
            "id" => $this->getName(),
            "name" => $this->getName(),
            "class" => $this->getClasses()
        );
        
        $options += $this->getAttr();
        
        $html = array();
        $c = count($this->getOptions());
        
        foreach($this->getOptions() as $k => $v) {
            
            $id = $this->getName()."-".$v["key"];
            $checked = null;
            
            if(in_array($v["value"], (array)$this->getValue())) {
                $checked = "checked";
            }
            
            $o = new Daq_Helper_Html("input", array(
                "type" => "checkbox",
                "value" => $v["value"],
                "checked" => $checked,
                "name" => $this->getName()."[]",
                "class" => $this->getClasses(),
                "id" => $id
            ));

            $l = new Daq_Helper_Html("label", array("for"=>$id), $v["desc"]);
            $text = $o->render()." ".$l->render();
            $html[] = $text;

        }
        
        return join("<br/>", $html);
    }
    
    public function overload(array $data)
    {
        parent::overload($data);
        
        if(isset($data["select_choices"]) && $data["select_choices"]) {
           $this->setMaxChoices($data["select_choices"]); 
        }
    }
}

?>
