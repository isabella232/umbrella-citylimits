<?php
/**
 * Description of Listing
 *
 * @author greg
 * @package 
 */

class Wpjb_Module_Admin_Pricing extends Wpjb_Controller_Admin
{
    public function init()
    {
        $listing = $this->_request->get("listing");
        $form = "";
        
        if($listing == "single-job") {
            $form = "Wpjb_Form_Admin_Pricing_SingleJob";
        } elseif($listing == "single-resume") {
            $form = "Wpjb_Form_Admin_Pricing_SingleResume";
        } elseif($listing == "employer-membership") {
            $form = "Wpjb_Form_Admin_Pricing_EmployerMembership";
        }
        
        $this->_virtual = array(
           "addAction" => array(
                "form" => $form,
                "info" => __("New pricing option has been created.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard"),
                "url" => wpjb_admin_url("pricing", "edit", "%d", array("listing"=>$listing))
            ),
            "editAction" => array(
                "form" => "Wpjb_Form_Admin_Pricing",
                "info" => __("Pricing option has been saved.", "wpjobboard"),
                "error" => __("There are errors in your form.", "wpjobboard")
            ),
           "redirectAction" => array(
               "accept" => array("listing"),
               "object" => "pricing",
               "action" => "list"
           ),
            "_delete" => array(
                "model" => "Wpjb_Model_Pricing",
                "info" => __("Listing deleted.", "wpjobboard"),
                "error" => __("Listing could not be deleted.", "wpjobboard")
            ),
            "_multi" => array(
                "delete" => array(
                    "success" => __("Number of deleted listings: {success}", "wpjobboard")
                ),
                "activate" => array(
                    "success" => __("Number of activated listings: {success}", "wpjobboard")
                ),
                "deactivate" => array(
                    "success" => __("Number of deactivated listings: {success}", "wpjobboard")
                )
            ),
            "_multiDelete" => array(
                "model" => "Wpjb_Model_Pricing"
            )
        );
        
        $this->view->listing = $listing;
    }

    public function indexAction()
    {
        $query = new Daq_Db_Query();
        $query->select("COUNT(*) AS cnt, price_for AS price_for");
        $query->from("Wpjb_Model_Pricing t");
        $query->group("price_for");
        $result = $query->fetchAll();
        
        $pricing = array(
            Wpjb_Model_Pricing::PRICE_SINGLE_JOB => 0,
            Wpjb_Model_Pricing::PRICE_SINGLE_RESUME => 0,
            Wpjb_Model_Pricing::PRICE_EMPLOYER_MEMBERSHIP => 0
        );
        
        foreach($result as $r) {
            $pricing[$r->price_for] = $r->cnt;
        }
        
        $this->view->pricing = $pricing;
    }
    
    public function listAction()
    {
        $this->_delete();
        $this->_multi();
        
        switch($this->_request->getParam("listing")) {
            case "single-job":
                $priceFor = Wpjb_Model_Pricing::PRICE_SINGLE_JOB;
                break;
            case "single-resume": 
                $priceFor = Wpjb_Model_Pricing::PRICE_SINGLE_RESUME;
                break;
            case "employer-membership": 
                $priceFor = Wpjb_Model_Pricing::PRICE_EMPLOYER_MEMBERSHIP;
                break;
            default:
                throw new Exception("Unknown pricing type.");
        }
        
        $page = (int)$this->_request->get("p", 1);
        if($page < 1) {
            $page = 1;
        }
        $perPage = $this->_getPerPage();

        $query = new Daq_Db_Query();
        $this->view->data = $query->select("t.*")
            ->from("Wpjb_Model_Pricing t")
            ->where("price_for = ?", $priceFor)
            ->limitPage($page, $perPage)
            ->execute();

        $query = new Daq_Db_Query();
        $total = $query->select("COUNT(*) AS total")
            ->from("Wpjb_Model_Pricing t")
            ->where("price_for = ?", $priceFor)
            ->limit(1)
            ->fetchColumn();

        $this->view->listing = $this->_request->getParam("listing");
        $this->view->current = $page;
        $this->view->total = ceil($total/$perPage);
    }

    public function addAction() 
    {
        switch($this->_request->getParam("listing")) {
            case "single-job":
                $this->view->title = __("Add Pricing (single job post)", "wpjobboard");
                break;
            case "single-resume": 
                $this->view->title = __("Add Pricing (single resume access)", "wpjobboard");
                break;
            case "employer-membership": 
                $this->view->title = __("Add Pricing (employer membership)", "wpjobboard");
                break;
            default:
                throw new Exception("Unknown pricing type.");
        }
        
        parent::addAction();
    }
    
    public function editAction()
    {
        $id = $this->_request->getParam("id");
        
        switch($this->_request->getParam("listing")) {
            case "single-job":
                $form = new Wpjb_Form_Admin_Pricing_SingleJob($id);
                $addm = __("Add Pricing (single job post)", "wpjobboard");
                $editm= __("Edit Pricing  (single job post)", "wpjobboard");
                break;
            case "single-resume": 
                $form = new Wpjb_Form_Admin_Pricing_SingleResume($id);
                $addm = __("Add Pricing (single resume access)", "wpjobboard");
                $editm= __("Edit Pricing (single resume access)", "wpjobboard");
                break;
            case "employer-membership": 
                $form = new Wpjb_Form_Admin_Pricing_EmployerMembership($id);
                $addm = __("Add Pricing (employer membership)", "wpjobboard");
                $editm= __("Edit Pricing (employer membership)", "wpjobboard");
                break;
            default:
                throw new Exception("Unknown pricing type.");
        }

        if($this->isPost()) {
            $isValid = $form->isValid($this->_request->getAll());
            if($isValid) {
                $this->_addInfo(__("Form saved.", "wpjobboard"));
                $form->save();
            } else {
                $this->_addError(__("There are errors in your form.", "wpjobboard"));
            }
        }

        if($form->getObject()->exists()) {
            $this->view->title = $editm." (ID: ".$form->getObject()->id.")";
        } else {
            $this->view->title = $addm;
        }
        
        $this->view->listing = $this->_request->getParam("listing");
        $this->view->form = $form;
       
        
    }
    
    public function deleteAction() 
    {
        $id = $this->_request->getParam("id");
        $listing = $this->_request->getParam("listing");
        
        if($this->_multiDelete($id)) {
            $m = sprintf(__("Pricing option #%d deleted.", "wpjobboard"), $id);
            $this->view->_flash->addInfo($m);
        }
        wp_redirect(wpjb_admin_url("pricing", "list", null, array("listing"=>$listing)));
    }
    
    protected function _multiActivate($id)
    {
        $object = new Wpjb_Model_Pricing($id);
        $object->is_active = 1;
        $object->save();
        return true;
    }

    protected function _multiDeactivate($id)
    {
        $object = new Wpjb_Model_Pricing($id);
        $object->is_active = 0;
        $object->save();
        return true;
    }

}

?>