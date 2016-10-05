<?php
/**
 * Description of Payment
 *
 * @author greg
 * @package 
 */

class Wpjb_Model_Payment extends Daq_Db_OrmAbstract
{
    const JOB = 1;
    const RESUME = 2;
    const MEMBERSHIP = 3;
    
    const FOR_JOB = 1;
    const FOR_RESUMES = 2;
    const FOR_MEMBERSHIP = 3;

    protected $_name = "wpjb_payment";

    protected function _init()
    {
        $this->_reference["user"] = array(
            "localId" => "user_id",
            "foreign" => "Wpjb_Model_User",
            "foreignId" => "ID",
            "type" => "ONE_TO_ONE"
        );
    }

    public function toPay()
    {
        if($this->payment_sum == 0) {
            return null;
        }

        $curr = Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
        return $curr.$this->payment_sum;
    }

    public function paid()
    {
        if($this->payment_sum == 0) {
            return null;
        }

        $curr = Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
        return $curr.$this->payment_paid;
    }
    
    public function getPrice()
    {
        return ($this->payment_sum+$this->payment_discount);
    }
    
    public function getDiscount()
    {
        return $this->payment_discount;
    }
    
    public function getTotal()
    {
        return $this->payment_sum;
    }
    
    public function accepted()
    {
        $id = $this->object_id;
        
        if($this->object_type == 1) {
            $object = new Wpjb_Model_Job($id);
        } elseif($this->object_type == 2) {
            $object = new Wpjb_Model_Resume($id);
        } elseif($this->object_type == self::MEMBERSHIP) {
            $object = new Wpjb_Model_Membership($id);
        }
        
        if(!$object->id) {
            return;
        }
        
        $object->paymentAccepted($this);
        
        do_action("wpjb_payment_complete", $this, $object);
    }
}

?>