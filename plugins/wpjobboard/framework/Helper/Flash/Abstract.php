<?php
/**
 * Description of Flash
 *
 * @author greg
 * @package
 */

abstract class Daq_Helper_Flash_Abstract
{
    protected $_ns = null;

    protected $_save = true;

    protected $_info = array();

    protected $_error = array();
    
    protected $_loaded = false;

    public function __construct($namespace = null)
    {
        $this->_ns = $namespace;
    }

    abstract public function load();

    public function addInfo($info)
    {
        $this->load();
        $this->_info[] = $info;
        $this->save();
    }

    public function getInfo()
    {
        $this->load();
        return array_unique($this->_info);
    }

    public function addError($error)
    {
        $this->load();
        $this->_error[] = $error;
        $this->save();
    }

    public function getError()
    {
        $this->load();
        return array_unique($this->_error);
    }

    public function dispose()
    {
        $this->_save = false;
    }

    abstract public function save();
}

?>