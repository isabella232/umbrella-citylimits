<?php
/**
 * Description of Payment
 *
 * @author greg
 * @package
 */

class Wpjb_Module_Admin_Payment extends Wpjb_Controller_Admin
{
    public function init()
    {
        $this->view->slot("logo", "payments.png");
        $this->_virtual = array(
            "redirectAction" => array(
                "accept" => array("payment_id"),
                "object" => "payment"
            ),
            "deleteAction" => array(
                "info" => __("Payment #%d deleted.", "wpjobboard"),
                "page" => "payment"
            ),
            "_multiDelete" => array(
                "model" => "Wpjb_Model_Payment"
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
    
    public function markpaidAction() 
    {
        $id = $this->_request->get("id");
        
        $this->redirectIf($id<1, wpjb_admin_url("payment"));

        $payment = new Wpjb_Model_Payment($id);
        
        if($payment->payment_paid == $payment->payment_sum) {
            $this->_addInfo(__("Payment was already marked as paid.", "wpjobboard"));
            $this->redirect(wpjb_admin_url("payment"));
        }
        
        $payment->payment_paid = $payment->payment_sum;
        $payment->paid_at = date("Y-m-d H:i:s");
        $payment->external_id = __("<Manually Accepted>", "wpjobboard");
        $payment->is_valid = 1;
        $payment->accepted();
        $payment->save();
        
        $this->_addInfo(__("Payment marked as paid", "wpjobboard"));
        
        $this->redirect(wpjb_admin_url("payment"));
    }

    public function indexAction()
    {
        $page = (int)$this->_request->get("p", 1);
        if($page < 1) {
            $page = 1;
        }
        
        $id = null;
        $this->view->payment_id = null;
        if($this->_request->get("payment_id")) {
            $id = (int)$this->_request->get("payment_id", 1);
            $this->view->payment_id = $id;
        }

        $this->view->id = $id;
        $perPage = $this->_getPerPage();

        $query = new Daq_Db_Query();
        $query = $query->select("t.*, t2.*")
            ->from("Wpjb_Model_Payment t")
            ->joinLeft("t.user t2")
            ->order("created_at DESC")
            ->limitPage($page, $perPage);

        if($id > 0) {
            $query->where("t.id = ?", $id);
            $query->orWhere("t.external_id LIKE ?", "%$id%");
        }
        $this->view->data = $query->execute();

        $query = new Daq_Db_Query();
        $total = $query->select("COUNT(*) AS total")
            ->from("Wpjb_Model_Payment t")
            ->joinLeft("t.user t2")
            ->limit(1);
        
        if($id > 0) {
            $query->where("t.id = ?", $id);
        }
        $total = $total->fetchColumn();

        $this->view->current = $page;
        $this->view->total = ceil($total/$perPage);
    }

    protected function _multiDelete($id)
    {

        try {
            $model = new Wpjb_Model_Payment($id);
            $model->delete();
            return true;
        } catch(Exception $e) {
            // log error
            return false;
        }
    }
    
    protected function _multiMarkpaid($id)
    {
        $payment = new Wpjb_Model_Payment($id);

        if($payment->payment_paid == $payment->payment_sum) {
            return false;
        }
        
        
        $payment->payment_paid = $payment->payment_sum;
        $payment->paid_at = date("Y-m-d H:i:s");
        $payment->save();
        return true;
    }

}

?>