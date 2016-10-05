<?php
/**
 * Description of Category
 *
 * @author greg
 * @package
 */

class Wpjb_Module_Admin_Application extends Wpjb_Controller_Admin
{
    public function init()
    {
        $this->view->slot("logo", "user_app.png");
        $this->_virtual = array(
           "redirectAction" => array(
               "accept" => array("query", "posted", "job", "filter"),
               "object" => "application"
           ),
           "addAction" => array(
                "form" => "Wpjb_Form_Admin_Application",
                "info" => __("New application has been created.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard"),
                "url" => wpjb_admin_url("application", "edit", "%d")
            ),
            "editAction" => array(
                "form" => "Wpjb_Form_Admin_Application",
                "info" => __("Form saved.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard")
            ),
            "deleteAction" => array(
                "info" => __("Application #%d deleted.", "wpjobboard"),
                "page" => "application"
            ),
            "_delete" => array(
                "model" => "Wpjb_Model_Application",
                "info" => __("Application deleted.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard")
            ),
            "_multi" => array(
                "delete" => array(
                    "success" => __("Number of deleted applications: {success}", "wpjobboard")
                ),
            ),
            "_multiDelete" => array(
                "model" => "Wpjb_Model_Application"
            )
        );
        
        foreach(wpjb_get_application_status() as $key => $data) {
            $callback = array($this, "_multi".ucfirst($data["key"]));
            $success = "";
            
            if(isset($data["callback"]["multi"]) && is_callable($data["callback"]["multi"])) {
                $callback = $data["callback"]["multi"];
            }
            
            if(isset($data["labels"]["multi_success"]) && !empty($data["labels"]["multi_success"])) {
                $success = $data["labels"]["multi_success"];
            }
            
            $this->_virtual["_multi"][$data["key"]] = array(
                "callback" => $callback,
                "success" => $success
            );
        }
    }

    protected function _multiDelete($id)
    {

        try {
            $model = new Wpjb_Model_Application($id);
            $model->delete();
            return true;
        } catch(Exception $e) {
            // log error
            return false;
        }
    }

    public function indexAction()
    {
        global $wpdb;
        
        $screen = new Wpjb_Utility_ScreenOptions;
        $this->view->screen = $screen;
        
        $query = $this->_request->get("query");
        
        $this->view->rquery = $this->readableQuery($query);
        $param = $this->deriveParams($query, new Wpjb_Model_Application);
        $param["filter"] = $this->_request->get("filter", "all");
        $param["page"] = (int)$this->_request->get("p", 1);
        $param["count"] = $screen->get("application", "count", 20);
        $param["posted"] = null;
        
        if(!isset($param["job"])) {
            $param["job"] = null;
        }
        
        if($this->_request->get("posted")) {
            $p = $this->_request->get("posted");
            $df = date("Y-m-01", strtotime($p));
            $param["date_from"] = $df;
            $param["date_to"] = date("Y-m-t", strtotime($df));
            $param["posted"] = $this->_request->get("posted");;
        }
        
        $result = Wpjb_Model_Application::search($param);
        
        $this->view->search = $param;
        $this->view->data = $result->application;
        $this->view->show = $param["filter"];
        $this->view->current = $param["page"];
        $this->view->total = $result->pages;
        $this->view->param = array("filter"=>$param["filter"], "posted"=>$param["posted"], "job"=>$param["job"], "query"=>$query);
        $this->view->query = $this->_request->get("query");
        
        foreach($param as $k=>$v) {
            $this->view->$k = $v;
        }
        
        $name = new Wpjb_Model_Application();
        $name = $name->tableName();
        /* @var $wpdb wpdb */
        $result = $wpdb->get_results("
            SELECT DATE_FORMAT(applied_at, '%Y-%m') as dt
            FROM $name GROUP BY dt ORDER BY applied_at DESC
        ");

        $months = array();
        foreach($result as $r) {
            $months[$r->dt] = date("Y, F", strtotime($r->dt));
        }

        $this->view->months = $months;
        
    }
    
    public function editAction() 
    {
        parent::editAction();
        
        $uid = $this->view->form->getObject()->user_id;

        if($uid > 0) {
            $this->view->user = new WP_User($uid);

            $query = new Daq_Db_Query();
            $query->select("t.id");
            $query->from("Wpjb_Model_Resume t");
            $query->where("user_id = ?", $uid);
            $query->limit(1);
            $this->view->resumeId = $query->fetchColumn();
        } else {
            $this->view->user = null;
            $this->view->resumeId = null;
        }
        
    }
    
    protected function _multiAccepted($id)
    {
        $object = new Wpjb_Model_Application($id);
        $object->status = Wpjb_Model_Application::STATUS_ACCEPTED;
        $object->save();
        
        return true;
    }
    
    protected function _multiRejected($id)
    {
        $object = new Wpjb_Model_Application($id);
        $object->status = Wpjb_Model_Application::STATUS_REJECTED;
        $object->save();
        
        return true;
    }
    
    protected function _multiRead($id)
    {
        $object = new Wpjb_Model_Application($id);
        $object->status = Wpjb_Model_Application::STATUS_READ;
        $object->save();
        
        return true;
    }

}

?>