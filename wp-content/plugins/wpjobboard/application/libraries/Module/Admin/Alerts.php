<?php
/**
 * Description of Payment
 *
 * @author greg
 * @package
 */

class Wpjb_Module_Admin_Alerts extends Wpjb_Controller_Admin
{
    public function init()
    {
        $this->_virtual = array(
            "redirectAction" => array(
                "accept" => array("query"),
                "object" => "alerts"
            ),
            "deleteAction" => array(
                "info" => __("Alert #%d deleted.", "wpjobboard"),
                "page" => "alerts"
            ),
            "_multiDelete" => array(
                "model" => "Wpjb_Model_Alert"
            ),
            "_multi" => array(
                "delete" => array(
                    "success" => __("Number of deleted payments: {success}", "wpjobboard")
                ),
                "markpaid" => array(
                    "success" => __("Number of payments marked as paid: {success}", "wpjobboard")
                ),
            )
        );
    }

    public function indexAction()
    {
        global $wpdb;
        
        $stat = (object)array("all"=>0, "daily"=>0, "weekly"=>0);
        $frequency = array("daily"=>1, "weekly"=>2);
        
        $page = (int)$this->_request->get("p", 1);
        if($page < 1) {
            $page = 1;
        }
        
        $q = $this->_request->get("query");
        $filter = $this->_request->get("filter", "all");
        $sort = $this->_request->get("sort", "created_at");
        $order = $this->_request->get("order", "desc");
        
        $this->view->sort = $sort;
        $this->view->order = $order;
        $this->view->query = $q;
        $this->view->filter = $filter;
        
        $param = array();
        
        if(!empty($q)) {
            $param["query"] = $q;
        }
        if(!empty($filter)) {
            $param["filter"] = $filter;
        } 
        
        $param["sort"] = $sort;
        $param["order"] = $order;
       
        $perPage = $this->_getPerPage();
        
        $query = new Daq_Db_Query();
        $query->select("*")
            ->from("Wpjb_Model_Alert t")
            ->order(esc_sql("$sort $order"))
            ->limitPage($page, $perPage);

        if($q) {
            $query->where("email LIKE ?", "%$q%");
        }
        if($filter && isset($frequency[$filter])) {
            $query->where("frequency = ?", $frequency[$filter]);
        } 
        
        $this->view->data = $query->execute();

        $query = new Daq_Db_Query();
        $total = $query->select("COUNT(*) AS total")
            ->from("Wpjb_Model_Alert t")
            ->limit(1);
        
        if($q) {
            $total->where("email LIKE ?", "%$q%");
        }

        $stat->all = $total->fetchColumn();
        $daily = clone $total;
        $stat->daily = $daily->where("frequency = 1")->fetchColumn();
        $weekly = clone $total;
        $stat->weekly = $weekly->where("frequency = 2")->fetchColumn();

        $this->view->stat = $stat;
        $this->view->param = $param;
        $this->view->current = $page;
        $this->view->total = ceil($stat->all/$perPage);
    }
    
    public function exportAction()
    {
        // Begin: indexAction
        global $wpdb;
        
        $stat = (object)array("all"=>0, "daily"=>0, "weekly"=>0);
        $frequency = array("daily"=>1, "weekly"=>2);
        
        $page = (int)$this->_request->get("p", 1);
        if($page < 1) {
            $page = 1;
        }
        
        $q = $this->_request->get("query");
        $filter = $this->_request->get("filter", "all");
        $sort = $this->_request->get("sort", "created_at");
        $order = $this->_request->get("order", "desc");
        
        $this->view->sort = $sort;
        $this->view->order = $order;
        $this->view->query = $q;
        $this->view->filter = $filter;
        
        $param = array();
        
        if(!empty($q)) {
            $param["query"] = $q;
        }
        if(!empty($filter)) {
            $param["filter"] = $filter;
        } 
        
        $param["sort"] = $sort;
        $param["order"] = $order;
       
        $perPage = $this->_getPerPage();

        $query = new Daq_Db_Query();
        $query->select("*")
            ->from("Wpjb_Model_Alert t")
            ->order($wpdb->escape("$sort $order"))
            ->limitPage($page, $perPage);

        if($q) {
            $query->where("email LIKE ?", "%$q%");
        }
        if($filter && isset($frequency[$filter])) {
            $query->where("frequency = ?", $frequency[$filter]);
        } 
        
        // End: indexAction
        
        header("Content-type: text/plain; charset=utf-8");
        //header('Content-Disposition: attachment; filename="alerts.csv";');
        
        $result = $query->select("t.id AS `id`")->fetchAll();

        $app = new Wpjb_Model_Alert();
        $csv = fopen("php://output", "w");
        $fields = array();

        foreach($app->getFieldNames() as $f) {
            if(!in_array($f, array("user_id", "params"))) {
                $fields[] = $f;
            }
            
        }

        fputcsv($csv, $fields);
        
        
        foreach($result as $r) {
            $app = new Wpjb_Model_Alert($r->id);
            $arr = $app->toArray();
            $param = unserialize($app->params);
            
            unset($arr["user_id"]);
            unset($arr["params"]);
            unset($arr["meta"]);
            
            fputcsv($csv, $arr);
            
            unset($app);
            unset($arr);
        }
        
        fclose($csv);
        
        exit;
    }


}

?>