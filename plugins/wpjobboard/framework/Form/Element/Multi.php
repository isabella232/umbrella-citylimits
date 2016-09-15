<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Multi
 *
 * @author greg
 */
abstract class Daq_Form_Element_Multi extends Daq_Form_Element 
{
    protected $_maxChoices = 0;
    
    /**
     * Allowed: default, callback, choices
     *
     * @var string
     */
    protected $_fillMethod = "default";
    
    protected $_option = array();
    
    public function setMaxChoices($choices) 
    {
        $this->_maxChoices = intval($choices);
    }
    
    public function getMaxChoices()
    {
        return $this->_maxChoices;
    }
    
    public function setFillMethod($method) 
    {
        if(!in_array($method, array("default", "callback", "choices"))) {
            throw new Exception("Unknown fill method [$method].");
        }
        
        $this->_fillMethod = $method;
    }
    
    public function getFillMethod() 
    {
        return $this->_fillMethod;
    }
    
    public function isMultiOption() 
    {
        return true;
    }
    
    public function addOption($key, $value, $desc)
    {
        $this->_option[] = array("key"=>$key, "value"=>$value, "desc"=>$desc);
    }
    
    public function addOptions($options)
    {
        foreach($options as $opt) {
            $this->addOption($opt["key"], $opt["value"], $opt["description"]);
        }
    }

    public function getOptions()
    {
        return $this->_option;
    }
    
    public function removeOption($key)
    {
        $c = count($this->_option);
        
        for($i=0; $i<$c; $i++) {
            if($this->_option[$i]["key"] == $key) {
                unset($this->_option[$i]);
                break;
            }
        }
    }
    
    public function getValueText($glue = ", ")
    {
        $arr = array();
        $value = (array)$this->getValue();
        foreach($this->getOptions() as $option) {
            if(in_array($option["value"], $value)) {
                $arr[] = $option["desc"];
            }
        }
        
        if(empty($arr)) {
            return null;
        } else {
            return implode($glue, $arr);
        }
    }
    
    public function overload(array $data) 
    {
        parent::overload($data);
        
        if(isset($data["fill_method"]) && $data["fill_method"] == "choices") {
            $this->_option = array();
            $options = explode("\n", $data["fill_choices"]);
            $options = array_map("trim", $options);
            foreach($options as $k => $option) {
                $this->addOption($k, $option, $option);
            }
        } elseif(isset($data["fill_method"]) && $data["fill_method"] == "callback") {
            if(isset($data["fill_callback"]) && is_callable($data["fill_callback"])) {
                $this->_option = array();
                $this->addOptions(call_user_func($data["fill_callback"]));
            }
        }
        
        if(isset($data["max_choices"]) && $data["max_choices"] > 0) {
            $this->setMaxChoices($data["max_choices"]);
        }
    }
    
    public function dump() 
    {
        $dump = parent::dump();
        $dump->fill_method = $this->_overload["fill_method"];
        $dump->fill_choices = $this->_overload["fill_choices"];
        $dump->fill_callback = $this->_overload["fill_callback"];
        $dump->select_choices = $this->getMaxChoices();
        
        if($dump->select_choices < 1) {
            $dump->select_choices = 1;
        }
        
        return $dump;
        
    }
    
    public function validate()
    {
        $this->_hasErrors = false;
        $count = 0;
        $arr = array();
        
        $value = (array)$this->getValue();
        foreach($value as $v) {
            
            if(is_array($v)) {
                $v = null;
            } elseif(is_string($v)) {
                $v = trim($v);
            }
            
            if(!empty($v)) {
                $count++;
                $arr[] = $v;
            }
        }

        if(empty($arr) && !$this->isRequired()) {
            return true;
        } else {
            $this->addValidator(new Daq_Validate_Required());
        }
        
        $choices = $this->getMaxChoices();
        if($choices > 0) {
            $this->addValidator(new Daq_Validate_Choices(null, $choices));
        }
        
        $allowed = array();
        foreach($this->getOptions() as $opt) {
            $allowed[] = trim($opt["value"]);
        }
        $this->addValidator(new Daq_Validate_InArray($allowed));

        foreach($this->getValidators() as $validate) {
            if(!$validate->isValid($arr)) {
                $this->_hasErrors = true;
                $this->_errors = $validate->getErrors();
                break;
            }
        }
        

        return !$this->_hasErrors;
    }
}

?>
