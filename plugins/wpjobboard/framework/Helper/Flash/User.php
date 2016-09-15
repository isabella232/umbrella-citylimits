<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author greg
 */
class Daq_Helper_Flash_User extends Daq_Helper_Flash_Abstract 
{
    protected $_new = false;
    protected $_id = null;

    public function load() 
    {
        if($this->_loaded) {
            return;
        }
        
        $id = get_current_user_id();
        $flash = get_user_meta($id, $this->_ns, true);
        $this->_id = $id;
        
        if($flash === "") {
            $this->_new = true;
        } 
        
        if(empty($flash)) {
            $this->_info = array();
            $this->_error = array();
        } else {
            $this->_info = $flash["info"];
            $this->_error = $flash["error"];
        }
        
        $this->_loaded = true;
    }
    
    public function dispose() 
    {
        $this->_info = array();
        $this->_error = array();
    }
    
    public function save() 
    {   
        
        $flash = array(
            "info" => $this->_info,
            "error" => $this->_error
        );

        $id = $this->_id;
        
        if(empty($this->_info) && empty($this->_error)) {
            delete_user_meta($id, $this->_ns);
        } elseif($this->_new) {
            add_user_meta($id, $this->_ns, $flash, true);
        } else {
            update_user_meta($id, $this->_ns, $flash);
        }
        

    }
}

?>
