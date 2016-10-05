<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pricing
 *
 * @author greg
 */

class Wpjb_Model_Pricing extends Daq_Db_OrmAbstract
{
    const PRICE_SINGLE_JOB = 101;
    const PRICE_SINGLE_RESUME = 201;
    const PRICE_EMPLOYER_MEMBERSHIP = 250;
    
    protected $_name = "wpjb_pricing";

    /**
     * Meta table name
     * 
     * @var string
     */
    protected $_metaTable = "Wpjb_Model_Meta";
    
    /**
     * Meta table object key
     *
     * @var string 
     */
    protected $_metaName = "pricing";
    
    protected $_coupon = null;
    
    protected function _init()
    {
    }
    
    public function applyCoupon($code)
    {
        
        $v = new Wpjb_Validate_Coupon($this->currency);
        
        if(!$v->isValid($code)) {
            $msg = $v->getErrors();
            throw new Wpjb_Model_PricingException($msg[0]);
        }
        
        $query = new Daq_Db_Query();
        $query->select();
        $query->from("Wpjb_Model_Discount t");
        $query->where("code = ?", $code);
        $query->limit(1);
        
        $coupon = $query->execute();
        
        if($this->price_for != $coupon[0]->discount_for) {
            throw new Wpjb_Model_PricingException(__("Entered discount code cannot be applied to this item.", "wpjobboard"));
        }
        
        $this->_coupon = $coupon[0];
    }
    
    public function getCoupon()
    {
        return $this->_coupon;
    }
    
    public function getPrice() 
    {
        return $this->price;
    }
    
    public function getDiscount()
    {
        if($this->_coupon === null) {
            return 0;
        }
        
        $coupon = $this->_coupon;
        
        if($coupon->type == 1) {
            // %
            $d = round($this->price*($coupon->discount/100), 2);
        } else {
            // $
            $d = $coupon->discount;
        }
        
        return $d;

    }
    
    public function getTotal()
    {
        $result = $this->getPrice()-$this->getDiscount();
        
        if($result > 0) {
            return $result;
        } else {
            return 0;
        }
    }
}

?>
