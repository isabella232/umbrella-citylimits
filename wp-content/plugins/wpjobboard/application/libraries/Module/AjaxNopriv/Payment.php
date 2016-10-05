<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Payment
 *
 * @author Grzegorz
 */
class Wpjb_Module_AjaxNopriv_Payment 
{
    public function acceptAction()
    {
        $request = Daq_Request::getInstance();
        $engine = $request->getParam("engine");
        
        $class = Wpjb_Project::getInstance()->payment->getEngine($engine);
        
        $payment = new $class();
        $payment->bind($request->post(), $request->get());
        
        $object = $payment->getObject();
        
        /* @var $payment Wpjb_Payment_Interface */
        
        try {
            
            $result = $payment->processTransaction();
            
            $object->payment_paid = $result["paid"];
            $object->external_id = $result["external_id"];
            $object->is_valid = 1;
            $object->paid_at = date("Y-m-d H:i:s");
            $object->message = "";
            $object->save();
            
            $object->accepted();
            
            $mail = Wpjb_Utility_Message::load("notify_admin_payment_received");
            $mail->setTo(get_option("admin_email"));
            $mail->assign("payment", $object);
            $mail->send();
            
        } catch(Exception $e) {

            if($object->id>0) {
                $object->is_valid = -1;
                $object->message = $e->getMessage();
                $object->save();
            }
            
            $result = array(
                "result" => "fail",
                "message" => $e->getMessage()
            );
            
        }
        
        if($request->getParam("echo") == "1") {
            echo json_encode($result);
        }
        
        die;
    }
    
    public function checkAction() {
        
        $request = Daq_Request::getInstance();
        $response = new stdClass();
        $response->status = 0; // -1: stop, 0: continue, 1: done
        $response->message = null;
        
        $redirect_url = "";
        $payment_id = $request->post("payment_id");
        
        $query = new Daq_Db_Query();
        $query->from("Wpjb_Model_Payment t");
        $query->where("id = ?", $payment_id);
        $query->limit(1);

        $payments = $query->execute();

        if( ! isset($payments[0]) ) {
            $response->status = -1;
            $response->message = sprintf( __( "Payment with ID = %d does not exist.", "wpjobboard" ), $payment_id );
            echo json_encode( $response );
            exit;
        }

        $payment = $payments[0];
        
        if( $payment->user_id > 0 && $payment->user_id != get_current_user_id() ) {
            $response->status = -1;
            $response->message = __( "This payment does not belong to you.", "wpjobboard" );
            echo json_encode( $response );
            exit;
        }

        if( $payment->is_valid == 1 ) {
            $response->status = 1;
            $response->message = "";
        } elseif( $payment->is_valid == -1 ) {
            $response->status = -1;
            $response->message = sprintf( __( "Payment failed with message '%s'.", "wpjobboard" ), $payment->message );
            echo json_encode( $response );
            exit;
        } else {
            echo json_encode( $response );
            exit; 
        }

        // Success!
        
        switch($payment->object_type) {
            case Wpjb_Model_Payment::RESUME:
                $redirect_url = wpjr_link_to("resume", new Wpjb_Model_Resume($payment->object_id));
                $glue = stripos($redirect_url, "?") === -1 ? "?" : "&";
                
                if(!get_current_user_id()) {
                    $redirect_url .= $glue . "hash=" . md5("{$payment->id}|{$payment->object_id}|{$payment->object_type}|{$payment->paid_at}");
                }
                $response->message = sprintf( __( 'Payment completed successfully. Click <a href="%s"><strong>HERE</strong></a> to view resume.', 'wpjobboard' ), $redirect_url );
                
                break;
        }

        
        echo json_encode($response);
        exit;
    }
}

?>
