<?php

/**
 * Description of ${name}
 *
 * @author ${user}
 * @package 
 */
abstract class Daq_Widget_Abstract extends WP_Widget
{
    protected $_context = null;
    
    protected $_viewAdmin = null;
    
    protected $_viewFront = null;
    
    /**
     * Widget options default values
     *
     * @var array 
     */
    protected $_defaults = array(
        "hide" => 0,
        "hide_empty" => 0
    );
    
    /**
     * Frontend view object
     *
     * @var Daq_View
     */
    public $view = null;
    
    public function __construct($id_base = false, $name, $widget_options = array(), $control_options = array()) 
    {
        parent::__construct($id_base, $name, $widget_options, $control_options);
    }
    
    public function form($instance)
    {
        $defaults = array_merge(array("title" => $this->name), $this->_defaults);
	$instance = wp_parse_args((array)$instance, $defaults);

        $view = $this->_context->getAdmin()->getView();
        $view->widget = $this;
        $view->instance = $instance;
        $view->render("widget/".$this->_viewAdmin);
    }
    
    public function widget($args, $instance) 
    {
        $this->view = new Daq_View();
        $this->view->addDir("TEMPLATEPATH/wpjobboard/widget");
        $this->view->addDir(Wpjb_Project::getInstance()->env("template_base")."/widget");
        $this->view->theme = (object)$args;
        $this->view->title = apply_filters('widget_title', $instance['title']);
        $this->view->param = (object)$instance;
        $this->_filter();
        
        $this->view = apply_filters("daq_widget_view", $this->view);

        $this->view->render($this->_viewFront);
    }
    
    protected function _filter()
    {
        return null;
    }
    
    protected function _get($key, $default)
    {
        if(isset($this->view->param->$key) && !empty($this->view->param->$key)) {
            return $this->view->param->$key;
        } else {
            return $default;
        }
    }
}
?>
