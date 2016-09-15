<?php
/**
 * Description of AddJob
 *
 * @author greg
 * @package 
 */

class Wpjb_Form_AddJob extends Wpjb_Form_Abstract_Job
{
    protected $_approved = null;
    
    public function setDefaults(array $default)
    {
        foreach($default as $k => $v) {
            if($this->hasElement($k)) {
                $this->getElement($k)->setValue($v);
            }
        }
    }
    
    protected function _getListingArr()
    {
        $listing = array();
        $query = new Daq_Db_Query();
        $result = $query->select("t.*")
            ->from("Wpjb_Model_Pricing t")
            ->order("title")
            ->where("is_active = 1")
            ->where("price_for IN(?)", Wpjb_Model_Pricing::PRICE_SINGLE_JOB)
            ->execute();
        
        foreach($result as $pricing) {
            $listing[] = array(
                "id" => $pricing->id,
                "key" => $pricing->price_for."_0_".$pricing->id,
                "title" => $pricing->title
            );
        }
        
        if(!Wpjb_Model_Company::current()) {
            return $listing;
        }
        
        foreach(Wpjb_Model_Company::current()->membership() as $membership) {
            $package = new Wpjb_Model_Pricing($membership->package_id);
            $data = $membership->package();
            
            if(!isset($data[Wpjb_Model_Pricing::PRICE_SINGLE_JOB])) {
                continue;
            }
            
            foreach($data[Wpjb_Model_Pricing::PRICE_SINGLE_JOB] as $id => $use) {
                
                $pricing = new Wpjb_Model_Pricing($id);
                
                if(!$pricing->exists()) {
                    continue;
                }
                
                $listing[] = array(
                    "id" => $package->id,
                    "key" => $package->price_for."_".$membership->id."_".$pricing->id,
                    "title" => $package->title." / ".$pricing->title
                );
            }
            
        }

        return $listing;
    }
    
    public function init()
    {
        parent::init();

        $this->_approved = (bool)$this->getObject()->is_approved;
        $this->removeElement("id");

        $this->addGroup("_internal");
        
        $e = $this->create("coupon");
        $e->setLabel(__("Discount Code", "wpjobboard"));
        $this->addElement($e, "coupon");

        $payment = Wpjb_Project::getInstance()->payment;
        $enabled = count($payment->getEnabled());
        if($enabled>1) {
            $v = array();
            $e = $this->create("payment_method", Daq_Form_Element::TYPE_SELECT);
            $e->setRequired(true);
            $e->setLabel(__("Payment Method", "wpjobboard"));
            foreach($payment->getEnabled() as $key => $engine) {
                /* @var $engine Wpjb_Payment_Interface */
                $engine = new $engine;
                $pTitle = $engine->getCustomTitle();
                $e->addOption($key, $key, $pTitle);
                $v[] = $key;
            }
            $e->addValidator(new Daq_Validate_InArray($v));
            $this->addElement($e, "coupon");
        } elseif($enabled == 1) {
            $engine = current($payment->getEnabled());
            $engine = new $engine;
            $e = $this->create("payment_method", Daq_Form_Element::TYPE_HIDDEN);
            $e->setValue($engine->getEngine());
            $e->addValidator(new Daq_Validate_InArray(array($engine->getEngine())));
            $e->setRequired(true);
            $this->addElement($e, "_internal");
        }
        
        $e = $this->create("listing", Daq_Form_Element::TYPE_RADIO);
        $e->setRequired(true);
        $e->setLabel(__("Listing Type", "wpjobboard"));
        $val = null;
        foreach($this->_getListingArr() as $listing) {
            $e->addOption($listing["key"], $listing["key"], $listing["title"]);
            
        }
        
        $e->setRenderer("wpjb_form_helper_listing");
        $e->addValidator(new Wpjb_Validate_MembershipLimit(Wpjb_Model_Pricing::PRICE_SINGLE_JOB));
        $this->addElement($e, "coupon");
        
        add_filter("wpjb_form_init_job", array($this, "apply"), 9);
        apply_filters("wpjb_form_init_job", $this);

    }

    public function isValid(array $values)
    { 
        if(isset($values["listing"]) && $this->hasElement("coupon")) {
            $listing = new Wpjb_Model_Pricing($values["listing"]);
            $validator = new Wpjb_Validate_Coupon($listing->currency, Wpjb_Model_Pricing::PRICE_SINGLE_JOB);

            $this->getElement("coupon")->addValidator($validator);
        }

        return parent::isValid($values);
    }

    public function buildModel()
    {
        $object = new Wpjb_Model_Job;
        $varList = $this->getValues();
        foreach($object->getFieldNames() as $f) {
            if(isset($varList[$f])) {
                $v = (array)$varList[$f];
                $object->$f = $v[0];
            }
        }
        
        $company = Wpjb_Model_Company::current();
        
        if($company) {
            $object->employer_id = $company->id;
        }
        
        $object->job_created_at = date("Y-m-d");
        
        if($this->hasElement("job_description")) {
            $model = new Wpjb_Model_MetaValue;
            $model->meta_id = $this->getObject()->meta->job_description_format->id;
            $model->object_id = 0;
            $model->value = $this->getElement("job_description")->usesEditor() ? "html" : "text";
            $object->meta->job_description_format->addValue($model);
        }
        
        foreach($varList as $k => $val) {
            $f = $this->getElement($k);
            
            if($f->isBuiltin() || !$this->getObject()->meta->$k) {
                continue;
            }
            
            $meta = $this->getObject()->meta->$k;
            $metaId = $meta->id;
            
            $valNew = (array)$val;
            
            $countC = count($meta->getValues());
            $countN = count($val);
            $max = max(array($countC,$countN));
            
            for($i=0; $i<$max; $i++) {
                $model = new Wpjb_Model_MetaValue;
                $model->meta_id = $metaId;
                $model->object_id = 0;
                $model->value = $valNew[$i];
                $object->meta->$k->addValue($model);

            }

        }
        
        foreach($this->_tags as $k) {
            
            if(!$this->hasElement($k)) {
                continue;
            }
            
            $f = $this->getElement($k);
            
            // $update: list of wpjb_tag.id
            $update = (array)$f->getValue();
            // $current: list of object wpjb_tag.id
            $current = $this->getObject()->getTagIds($k);
            $new = array_diff($update, $current);
            
            
            foreach($new as $id) {
                
                $list = new Daq_Db_Query();
                $list->select();
                $list->from("Wpjb_Model_Tag t");
                $list->where("type = ?", $k);
                $list->where("id = ?", $id);
                $list->limit(1);
                
                $result = $list->execute();
                
                if(empty($result)) {
                    continue;
                }
                
                $tag = $result[0];
                
                $tagged = new Wpjb_Model_Tagged;
                $tagged->tag_id = $id;
                $tagged->object = "job";
                $tagged->object_id = 0;
                
                $tag->fakeLoad($tagged);
                
                $object->addTag($tag);
            }
        }
        
        return $object;

    }

    public function save($append = array())
    {
        $price_for = "";
        $membership_id = null;
        $membership = null;
        $method = wpjb_default_payment_method();
        
        if($this->value("payment_method")) {
            $method = $this->value("payment_method");
        } 
        
        if($this->value("listing")) {
            list($price_for, $membership_id, $pricing_id) = explode("_", $this->value("listing"));
            $listing = new Wpjb_Model_Pricing($pricing_id);
            
            if($this->value("coupon")) {
                $listing->applyCoupon($this->value("coupon"));
            }
        } else {
            $listing = Daq_Db_Query::create();
            $listing->from("Wpjb_Model_Pricing t");
            $listing->where("price_for = ?", Wpjb_Model_Pricing::PRICE_SINGLE_JOB);
            $listing->limit(1);
            $result = $listing->execute();
            $listing = $result[0];
        }
        
        $moderation = (array)wpjb_conf("posting_moderation", 0);
        $moderated = 1;
        if($membership_id>0 && in_array(3, $moderation)) {
            $moderated = 0; // package
        } elseif(!$membership_id>0 && $listing->getTotal()>0) {
            $moderated = 0; // paid | always moderate until paid
        } elseif(!$membership_id>0 && $listing->getTotal()==0 && in_array(1, $moderation)) {
            $moderated = 0; // free
        }
        
        $x = $listing->meta->visible->value();
        
        if(Wpjb_Model_Company::current()) {
            $employer_id = Wpjb_Model_Company::current()->id;
        } else {
            $employer_id = null;
        }
        
        parent::save(array(
            "job_slug" => Wpjb_Utility_Slug::generate(Wpjb_Utility_Slug::MODEL_JOB, $this->value("job_title")),
            "job_created_at" => date("Y-m-d H:i:s"),
            "job_expires_at" => date("Y-m-d H:i:s", strtotime("now +$x days")),
            "employer_id" => $employer_id,
            "is_featured" => (int)$listing->meta->is_featured->value(),
            "is_approved" => $moderated,
            "is_active" => $moderated,
            "is_filled" => 0
        ));
        
        if($this->hasElement("job_description")) {
            $meta = $this->getObject()->meta->job_description_format->getFirst();
            $meta->value = $this->getElement("job_description")->usesEditor() ? "html" : "text";
            $meta->save();            
        }
        
        $create_payment = false;
        
        if($membership_id) {
            $membership = new Wpjb_Model_Membership($membership_id);
            $membership->inc($pricing_id);
            $membership->save();
            
            $create_payment = true;

            $f_is_valid = 1;
            $f_paid_at = date("Y-m-d H:i:s");
            $f_engine = "Credits";
            $f_payment_sum = 0;
            $f_payment_discount = 0;
            $f_payment_currency = wpjb_default_currency();
        } elseif($listing->getTotal() > 0) {

            $create_payment = true;
            
            $f_is_valid = 0;
            $f_paid_at = date("Y-m-d H:i:s");
            $f_engine = $method;
            $f_payment_sum = $listing->getTotal();
            $f_payment_discount = $listing->getDiscount();
            $f_payment_currency = $listing->currency;
        }

        if($create_payment) {
            $payment = new Wpjb_Model_Payment;
            $payment->object_type = Wpjb_Model_Payment::JOB;
            $payment->object_id = $this->getId();
            $payment->user_id = wp_get_current_user()->ID;
            $payment->email = $this->value("company_email");
            $payment->external_id = "";
            $payment->is_valid = $f_is_valid;
            $payment->message = "";
            $payment->created_at = date("Y-m-d H:i:s");
            $payment->paid_at = $f_paid_at;
            $payment->payment_paid = 0;
            $payment->engine = $f_engine;
            $payment->payment_sum = $f_payment_sum;
            $payment->payment_discount = $f_payment_discount;
            $payment->payment_currency = $f_payment_currency;
            $payment->save();
        }
        
        if($listing && $listing->getCoupon()) {
            $listing->getCoupon()->used++;
            $listing->getCoupon()->save();
        }

        $temp = wpjb_upload_dir("job", "", null, "basedir");
        $finl = dirname($temp)."/".$this->getId();
        wpjb_rename_dir($temp, $finl);
        
        $this->getObject()->tag(true);
        $this->getObject()->meta(true);
        
        do_action("wpjb_job_saved", $this->getObject());
        
        if(!$this->_approved && $this->getObject()->is_approved) {
            do_action("wpjb_job_published", $this->getObject());
        }
        
        
        apply_filters("wpjb_form_save_job", $this);
    }
}

?>