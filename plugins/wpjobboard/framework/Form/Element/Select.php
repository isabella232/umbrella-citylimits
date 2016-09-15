<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Select
 *
 * @author greg
 */
class Daq_Form_Element_Select extends Daq_Form_Element_Multi implements Daq_Form_Element_Interface
{
    protected $_maxChoices = 1;
    
    protected $_emptyOption = false; 
    
    public final function getType()
    {
        return "select";
    }
    
    public function setEmptyOption($option)
    {
        $this->_emptyOption = (bool)$option;
    }
    
    public function hasEmptyOption()
    {
        return $this->_emptyOption;
    }
    
    public function render()
    {
        $html = "";
        $name = $this->getName();
        $multiple = false;
        $classes = $this->getClasses();
        
        if($this->getMaxChoices()>1) {
            $max = $this->getMaxChoices();
            $name .= "[]";
            $multiple = "multiple";
            $classes = "$classes daq-multiselect daq-max-choices[$max]";
        }
        
        $options = array(
            "id" => $this->getName(),
            "name" => $name,
            "class" => $classes,
            "multiple" => $multiple
        );
        
        $options += $this->getAttr();
        
        if($this->hasEmptyOption() && $this->getMaxChoices()<=1) {
            $html .= '<option value="">&nbsp;</option>'; 
        }
        
        foreach($this->getOptions() as $k => $v) {
            $selected = null;
            if(in_array($v["value"], (array)$this->getValue())) {
                $selected = "selected";
            }
            $o = new Daq_Helper_Html("option", array(
                "value" => $v["value"],
                "selected" => $selected,
            ), $v["desc"]);

            $html .= $o->render();
        }
        
        $input = new Daq_Helper_Html("select", $options, $html);
        
        return $input->render();
    }

    public function overload(array $data)
    {
        parent::overload($data);
        
        if(isset($data["select_choices"]) && $data["select_choices"]) {
           $this->setMaxChoices($data["select_choices"]); 
        }
        if(isset($data["empty_option"]) && $data["empty_option"]) {
            $this->setEmptyOption($data["empty_option"]);
        }
    }
    
    public function dump()
    {
        $dump = parent::dump();
        $dump->empty_option = $this->_overload["empty_option"];
        
        return $dump;
    }
    
    public function validate()
    {
        // check if only empty option is selected.
        if($this->hasEmptyOption()) {
            $value = (array)$this->getValue();
            if(count($value) == 1 && $value[0] == "") {
                return true;
            }
        }
        
        
        return parent::validate();
        
    }
}

?>
