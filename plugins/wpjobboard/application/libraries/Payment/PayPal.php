<?php
/*
 * lwdpt_1228422945_per@jadamspam.pl
 */

/**
 * Description of PayPal
 *
 * @author greg
 */
class Wpjb_Payment_PayPal extends Wpjb_Payment_Abstract
{
    const ENV_SANDBOX = 1;
    const ENV_PRODUCTION = 2;


    /**
     * PayPal enviroment
     *
     * @var integer one of PayPal::ENV_<ENV>
     */
    private $_env;

    /**
     * Job object
     *
     * @var Wpjb_Model_Job
     */
    protected $_data = null;

    public function __construct(Wpjb_Model_Payment $data = null)
    {
        $this->_default = array(
            "paypal_env" => wpjb_conf("paypal_env", self::ENV_PRODUCTION),
            "paypal_email" => wpjb_conf("paypal_email"),
        );
        
        $env = $this->conf("paypal_env");
        $this->setEnviroment($env);
        $this->_data = $data;
    }

    public function getEngine()
    {
        return "PayPal";
    }

    public function getTitle()
    {
        return "PayPal";
    }
    
    public function getForm()
    {
        return "Wpjb_Form_Admin_Config_Paypal";
    }

    public function setEnviroment($env = self::ENV_PRODUCTION)
    {
        $this->_env = $env;
    }

    public function getDomain()
    {
        if($this->_env == self::ENV_PRODUCTION)
        {
            return "www.paypal.com";
        }
        else
        {
            return "www.sandbox.paypal.com";
        }
    }

    /**
     * Depending on settings return either sandbox or production URL
     *
     * @return string
     */
    public function getUrl()
    {
        return "https://" . $this->getDomain() . "/cgi-bin/webscr";
    }

    /**
     * Returns PayPal eMail to which money will be sent.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->conf("paypal_email");
    }

    /**
     * Procesess PayPal transaction.
     *
     * @param array $ppData
     * @return boolean
     */
    public function processTransaction()
    {
        $post = $this->_post;
        $get = $this->_get;
        
        $ppData = $post;
        
        $req = 'cmd=_notify-validate';

        foreach ($ppData as $key => $value) {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

        // post back to PayPal system to validate
        $header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        
        $header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n";
        $header .= "Host: " . $this->getDomain() . "\r\n"; 
        $header .= "Connection: close\r\n\r\n";
        
        $fp = fsockopen ('ssl://'.$this->getDomain(), 443, $errno, $errstr, 30);

        $verified = false;
        if (!$fp) {
            throw new Exception("There was a HTTP error while connecting to PayPal server", 1);
        } else {
            fputs ($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets ($fp, 1024);
                if (strcmp ($res, "VERIFIED") == 0) {
                    $verified = true;
                } else if (strcmp ($res, "INVALID") == 0) {
                    throw new Exception("PayPal sent INVALID response.", 2);
                }
            }
            fclose ($fp);
        }

        if($ppData['payment_status'] != 'Completed') {
            throw new Exception("Invalid payment status [".$ppData["payment_status"]."]", 3);
        }
        if($ppData['receiver_email'] != $this->getEmail()) {
            throw new Exception("Receiver email is invalid [".$ppData['receiver_email']."]", 4);
        }
        if($this->_data->payment_sum != $ppData['mc_gross'] - $ppData['tax']) {
            $sum = $this->_data->payment_sum;
            $msg = sprintf("Expected amount %2.f given %2.f.", $sum, $ppData['mc_gross']);
            throw new Exception($msg);
        }
        $curr = $this->_data->payment_currency;
        if($curr != $ppData['mc_currency']) {
            $msg = sprintf("Expected currency %s given %s.", $curr, $ppData['mc_currency']);
            throw new Exception($msg);
        }
        
        return array(
            "external_id" => $ppData["txn_id"],
            "paid" => $ppData["mc_gross"]
        );
    }

    public function render()
    {
        $arr = array(
            "action" => "wpjb_payment_accept",
            "engine" => $this->getEngine(),
            "id" => $this->_data->id
        );
        
        $instance = Wpjb_Project::getInstance();
        
        if($this->_data->object_type == 1) {
            $completeUrl = wpjb_link_to("step_complete", $this->_data);
        } elseif($this->_data->object_type == 2 && wpjb_conf("urls_cpt")) {
            $completeUrl = get_permalink();
        } elseif($this->_data->object_type == 2) {
            $completeUrl = wpjr_link_to("resume", $this->_data);
        } else {
            $completeUrl = wpjb_link_to("step_complete", $this->_data);
        }
        
        $notify = admin_url('admin-ajax.php')."?".http_build_query($arr);
        $complete = $completeUrl;
        $amount = $this->_data->payment_sum-$this->_data->payment_paid;
        $currency = $this->_data->payment_currency;
        $product = str_replace("{num}", $this->_data->getId(), __("Job Board order #{num} at: ", "wpjobboard"));
        $product.= get_bloginfo("name");

        $html = "";
        $html.= '<form action="'.$this->getUrl().'" method="post">';
        $html.= '<input type="hidden" name="cmd" value="_xclick">';
        $html.= '<input type="hidden" name="business" value="'.$this->getEmail().'">';
        $html.= '<input type="hidden" name="lc" value="US">';
        $html.= '<input type="hidden" name="notify_url" value="'.$notify.'">';
        $html.= '<input type="hidden" name="return" value="'.$complete.'">';
        $html.= '<input type="hidden" name="item_name" value="'.$product.'">';
        $html.= '<input type="hidden" name="amount" value="'.$amount.'">';
        $html.= '<input type="hidden" name="currency_code" value="'.$currency.'">';
        $html.= '<input type="hidden" name="custom" value="'.$this->_data->id.'">';
        $html.= '<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">';
        $html.= '<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="">';
        $html.= '<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">';
        $html.= '</form>';

        return $html;
    }
    
    public function bind(array $post, array $get)
    {
        $this->setObject(new Wpjb_Model_Payment($get["id"]));
        
        parent::bind($post, $get);
    }
    
    public function progressAction()
    {
        wp_enqueue_script("wpjb-paypal-reply");
        
        ?>

        <!-- START: PayPal overlay -->
        <div id="wpjb-paypal-overlay" class="wpjb wpjb-overlay">
            <div class="wpjb-paypal-reply-pending">
                <h2><?php _e("PayPal", "wpjobboard") ?></h2>
                <div class="wpjb-paypal-reply-message">
                    <?php _e("We are waiting for payment confirmation from PayPal, this should take less than a minute. Please wait.", "wpjobboard") ?>
                </div>
                <div>
                    <img src="<?php echo admin_url() ?>/images/wpspin_light-2x.gif" alt="" />
                </div>
            </div>
            
            <div class="wpjb-paypal-reply-failed">
                <h2><?php _e("PayPal", "wpjobboard") ?></h2>
                <div class="wpjb-paypal-reply-message"></div>
                <div><span class="wpjb-glyphs wpjb-icon-attention"></span></div>
            </div>
            
            <div class="wpjb-paypal-reply-complete">
                <h2><?php _e("PayPal", "wpjobboard") ?></h2>
                <div class="wpjb-paypal-reply-message"></div>
                <div><span class="wpjb-glyphs wpjb-icon-ok"></span></div>
            </div>
            
            <div class="wpjb-paypal-reply-timedout">
                <h2><?php _e("PayPal", "wpjobboard") ?></h2>
                <div class="wpjb-paypal-reply-message">
                    <?php _e("Timed out. PayPal either did not send payment confirmation or it could not be proccesed. Please try to refresh this page in a couple of minutes or contact our support.", "wpjobboard") ?>
                </div>
                <div><span class="wpjb-glyphs wpjb-icon-attention"></span></div>
            </div>
        </div>
        <!-- END: Subscribe overlay -->     
             
        <?php
    }
    
    public function getIcon() 
    {
        return "wpjb-icon-paypal";
    }

}

?>