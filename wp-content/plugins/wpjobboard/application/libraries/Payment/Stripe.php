<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stripe
 *
 * @author Grzegorz
 */
class Wpjb_Payment_Stripe extends Wpjb_Payment_Abstract
{
    public function __construct(Wpjb_Model_Payment $data = null)
    {
        $this->_default = array(
            "disabled" => "0"
        );
        
        $this->_data = $data;
    }
    
    public function getEngine()
    {
        return "Stripe";
    }
    
    public function getForm()
    {
        return "Wpjb_Form_Admin_Config_Stripe";
    }
    
    public function getTitle()
    {
        return "Stripe (Credit Card)";
    }
    
    public function processTransaction()
    {
        $path = Wpjb_List_Path::getPath("vendor");
        
        include_once $path."/Stripe/Stripe.php";

        $secret_key = $this->conf("secret_key");
        $amount = ($this->_data->payment_sum-$this->_data->payment_paid)*100;
        $currency = strtolower($this->_data->payment_currency);
        $token = $this->_post["token"];
        $id = $this->_data->id;

        Stripe::setApiKey($secret_key);
        
        $charge = Stripe_Charge::create(array(
            "amount" => $amount, 
            "currency" => $currency,
            "card" => $token,
            "description" => sprintf(__("Payment ID: %d", "wpjobboard"), $id),
            "receipt_email" => $this->_data->email
        ));

        $charge;
        
        return array(
            "external_id" => $charge->id,
            "paid" => $charge->amount/100
        );
    }
    
    public function bind(array $post, array $get)
    {
        $this->setObject(new Wpjb_Model_Payment($post["id"]));
        
        parent::bind($post, $get);
    }
    
    public function render()
    {
        $id = $this->_data->id;
        $amount = ($this->_data->payment_sum-$this->_data->payment_paid)*100;
        $currency = $this->_data->payment_currency;
        $site_name = get_bloginfo('name');
        $publishable_key = $this->conf("publishable_key");
        $ajaxurl = admin_url("admin-ajax.php");
        
        wp_enqueue_script("wpjb-stripe");
        
        $html = '
<style type="text/css">
  .form-row > label > span {
    display: block;
    width: 200px;
    float: left;
    line-height: 2em;
  }
</style>
<script type="text/javascript">
  jQuery(function($) {
    Stripe.setPublishableKey("'.$publishable_key.'");
  });
  var WPJB_PAYMENT_ID = '.$id.';
  if (typeof ajaxurl === "undefined") {
    ajaxurl = "'.$ajaxurl.'";
  }
</script>
<form action="" method="POST" id="payment-form">
  <h3>'.__("Credit Card", "wpjobboard").'</h3>
  <div class="payment-errors"></div>

  <div class="form-row">
    <label>
      <span>'.__("Card Number", "wpjobboard").'</span>
      <input type="text" size="20" data-stripe="number"/>
    </label>
  </div>

  <div class="form-row">
    <label>
      <span>'.__("CVC", "wpjobboard").'</span>
      <input type="text" size="4" data-stripe="cvc" maxlength="4" />
    </label>
  </div>

  <div class="form-row">
    <label><span>'.__("Expiration (MM/YYYY)", "wpjobboard").'</span></label>
    <input type="text" size="2" data-stripe="exp-month" maxlength="2" />
    <span> / </span>
    <input type="text" size="4" data-stripe="exp-year" maxlength="4" />
  </div>

  <button type="submit">'.__("Submit Payment", "wpjobboard").'</button>
</form>';

        
        return $html;
        
    }
    
    public function getIcon() 
    {
        return "wpjb-icon-cc-stripe";
    }
}

?>
