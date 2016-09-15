<?php
/**
 * Description of Job
 *
 * @author greg
 * @package 
 */

class Wpjb_Model_Job extends Daq_Db_OrmAbstract
{    
    
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_EXPIRING = 3;
    const STATUS_AWAITING = 4;
    const STATUS_NEW = 5;
    const STATUS_INACTIVE = 6;
    const STATUS_PAYMENT = 7;
    
    protected $_name = "wpjb_job";
    
    protected $_metaTable = "Wpjb_Model_Meta";
    
    protected $_metaName = "job";
    
    protected $_tagTable = array("scheme"=>"Wpjb_Model_Tag", "values"=>"Wpjb_Model_Tagged");
    
    protected $_tagName = "job";

    protected $_approve = false;

    protected $_newApps = null;
    
    private $_company_old = -1;
    
    public static $skip = array('geo_loc'=>false);

    protected function _init()
    {
        $this->_reference["tagged"] = array(
            "localId" => "id",
            "foreign" => "Wpjb_Model_Tagged",
            "foreignId" => "object_id",
            "type" => "ONE_TO_ONE"
        );
        $this->_reference["payment"] = array(
            "localId" => "id",
            "foreign" => "Wpjb_Model_Payment",
            "foreignId" => "object_id",
            "type" => "ONE_TO_ONE",
            "with" => "object_type = 1"
        );
        $this->_reference["search"] = array(
            "localId" => "id",
            "foreign" => "Wpjb_Model_JobSearch",
            "foreignId" => "job_id",
            "type" => "ONE_TO_ONE"
        );
        $this->_reference["company"] = array(
            "localId" => "employer_id",
            "foreign" => "Wpjb_Model_Company",
            "foreignId" => "id",
            "type" => "ONE_TO_ONE"
        );
        $this->_reference["meta"] = array(
            "localId" => "id",
            "foreign" => "Wpjb_Model_MetaValue",
            "foreignId" => "object_id",
            "type" => "ONE_TO_ONCE"
        );
    }
    
    protected function _load($id)
    {
        parent::_load($id);
        
        $this->_company_old = $this->employer_id;
    }

    public function save()
    {
        $this->job_modified_at = date("Y-m-d");
        $this->cache = "";
        
        if($this->is_active == 1) {
            $this->is_approved = 1;
        }
        
        $id = parent::save();
        
        $this->meta(true);
        
        if(!self::$skip['geo_loc']) {
            $this->geolocate(true);
        }

        Wpjb_Model_JobSearch::createFrom($this);
        
        $this->_companyChange($this->_company_old, $this->employer_id);
        
        do_action("wpjb_job_saved", $this);

        return $id;
    }
    
    private function _companyChange($old, $new)
    {
        if($old == $new && $old>0) {
            return;
        }
        
        if($old>0) {
            $company = new Wpjb_Model_Company($old);
            if($company->exists()) {
                $company->jobs_posted--;
                $company->save();
            }
        }
        if($new>0) {
            $company = new Wpjb_Model_Company($new);
            if($company->exists()) {
                $company->jobs_posted++;
                $company->save();
            }
        }
    }

    protected function _useCouponCode()
    {
        if($this->discount_id < 1) {
            return;
        }

        $discount = new Wpjb_Model_Discount($this->discount_id);
        if(!$this->id) {
            return;
        }

        $discount->used++;
        $discount->save();
    }

    public function getLogoUrl($resize = null)
    {
        global $wp_version;
        
        $upload = wpjb_upload_dir("job", "company-logo", $this->id);
        $file = glob($upload["basedir"]."/[!_]*");
        
        if(!isset($file[0])) {
            return null;
        }
        
        $filename = basename($file[0]);
        $altfile = "__".$resize."_".basename($file[0]);
        
        if($resize && version_compare($wp_version, "3.5.0")>=0) {
                
            if(!is_file($upload["basedir"]."/".$altfile)) {
                list($max_w, $max_h) = explode("x", $resize);
                $editor = wp_get_image_editor($upload["basedir"]."/".$filename);

                if(!is_wp_error($editor)) {
                    $editor->resize($max_w, $max_h, false);
                    $editor->set_quality(100);
                    $result = $editor->save($upload["basedir"]."/".$altfile);

                    rename($result["path"], $upload["basedir"]."/".$altfile);

                    $filename = $altfile;
                } // endif is_wp_error
            } else {
                $filename = $altfile;
            }
        } 
        
        return $upload["baseurl"]."/".$filename;
    }
    
    public function getLogoDir()
    {
        $upload = wpjb_upload_dir("job", "company-logo", $this->id);
        $file = glob($upload["basedir"]."/*");
        
        if(isset($file[0])) {
            return $upload["basedir"]."/".basename($file[0]);
        } else {
            return null;
        }
    }

    public function isFree()
    {
        if($this->payment_sum == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isNew()
    {
        $past = strtotime($this->job_created_at);
        $now = strtotime(date("Y-m-d H:i:s"));

        $config = Wpjb_Project::getInstance()->conf("front_marked_as_new", 7);
        if($now-$past < 24*3600*$config) {
            return true;
        } else {
            return false;
        }
    }

    public function paymentAmount()
    {
        if($this->payment_sum == 0) {
            return null;
        }

        $curr = Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
        return $curr.$this->payment_sum;
    }

    public function paymentPaid()
    {
        if($this->payment_sum == 0) {
            return null;
        }

        $curr = Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
        return $curr.$this->payment_paid;
    }

    public function paymentDiscount()
    {
        if($this->payment_sum == 0) {
            return null;
        }

        $curr = Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
        return $curr.$this->payment_discount;
    }

    public function paymentCurrency()
    {
        return Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
    }

    public function listingPrice()
    {
        $price = $this->payment_sum+$this->payment_discount;
        $curr = Wpjb_List_Currency::getCurrencySymbol($this->payment_currency);
        return $curr.number_format($price, 2);
    }

    public function locationToString()
    {
        $arr = array();
        $country = Wpjb_List_Country::getByCode($this->job_country);
        $country = trim($country['name']);

        if(strlen(trim($this->job_city))>0) {
            $arr[] = $this->job_city;
        }

        if($this->job_country == 840 && strlen(trim($this->job_state))>0) {
            $arr[] = $this->job_state;
        } else if(strlen($country)>0) {
            $arr[] = $country;
        }

        return apply_filters("wpjb_location_display", implode(", ", $arr), $this);
    }

    public function delete()
    {
        $query = new Daq_Db_Query();
        $object = $query->select()
            ->from("Wpjb_Model_JobSearch t")
            ->where("t.job_id = ?", $this->getId())
            ->limit(1)
            ->execute();

        if(!empty($object)) {
            $object[0]->delete();
        }
        
        $query = new Daq_Db_Query();
        $object = $query->select()
            ->from("Wpjb_Model_Payment t")
            ->where("t.object_id = ?", $this->getId())
            ->where("t.object_type = ?", Wpjb_Model_Payment::FOR_JOB)
            ->limit(1)
            ->execute();
        
        $query = new Daq_Db_Query();
        $object = $query->select()
            ->from("Wpjb_Model_Application t")
            ->where("t.job_id = ?", $this->getId())
            ->execute();
        foreach($object as $app) {
            $app->delete();
        }
        
        if(!empty($object)) {
            $object[0]->delete();
        }
        
        if($this->post_id > 0) {
            wp_delete_post($this->post_id, true);
        }
        
        $employer = new Wpjb_Model_Company($this->employer_id);
        if($employer->getId() > 0) {
            $employer->jobs_posted--;
            $employer->save();
        }

        foreach((array)$this->getTag() as $k => $tag) {
            foreach($tag as $tagged) {
                $tagged->getTagged()->delete();
            }
        }
        
        $payment = $this->getPayment(true);
        if(isset($payment->id) && $payment->id>0) {
            $payment->delete();
        }
        
        $dir = wpjb_upload_dir("job", "", $this->id, "basedir");
        if(is_dir($dir)) {
            wpjb_recursive_delete($dir);
        }
        
        Wpjb_Project::scheduleEvent();
        
        return parent::delete();
    }
    
    public function expired()
    {
        if(wpjb_time($this->job_expires_at)>time()) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Returns geolocation parameters for the job
     * 
     * @return stdClass 
     */
    public function getGeo()
    {
        $this->geolocate();
        
        $obj = new stdClass;
        $obj->geo_status = $this->meta->geo_status->value();
        $obj->geo_latitude = $this->meta->geo_latitude->value();
        $obj->geo_longitude = $this->meta->geo_longitude->value();
        
        $obj->status = $this->meta->geo_status->value();
        $obj->lnglat = $obj->geo_latitude.",".$obj->geo_longitude;
        
        return $obj;
    }
    
    public function geolocate($force = false) 
    {
        $arr = array(
            Wpjb_Service_GoogleMaps::GEO_MISSING,
            Wpjb_Service_GoogleMaps::GEO_FOUND,
        );
        
        if(in_array($this->meta->geo_status->value(), $arr) && !$force) {
            return;
        }
        
        $geo = Wpjb_Service_GoogleMaps::locate($this->location());

        $meta = $this->meta->geo_status->getFirst();
        $meta->value = $geo->geo_status;
        $meta->save();
        
        $meta = $this->meta->geo_latitude->getFirst();
        $meta->value = $geo->geo_latitude;
        $meta->save();
        
        $meta = $this->meta->geo_longitude->getFirst();
        $meta->value = $geo->geo_longitude;
        $meta->save();
        
    }
    
    public function location()
    {
        $country = Wpjb_List_Country::getByCode($this->job_country);
        $country = trim($country['name']);
        
        $addr = array(
            $this->job_city,
            $this->job_zip_code,
            $this->job_state,
            $country
        );
        
        $addr = apply_filters("wpjb_geolocate", $addr, $this);
        
        return join(", ", $addr);
    }
    
    public function status()
    {
        $active = (int)$this->is_active;
        $approved = (int)$this->is_approved;
        $expire = wpjb_time($this->job_expires_at." 23:59:59")/(3600*24);
        $today = time()/(3600*24);

        $status = array();
        
        $payment = $this->getPayment(true);
        $payment_exists = $payment->exists();
        
        if($active && $expire>=$today) {
            $status[] = self::STATUS_ACTIVE;
        }
        if(!$this->read) {
            $status[] = self::STATUS_NEW;
        }
        if($expire-$today>=0 && $expire-$today<=5) {
            $status[] = self::STATUS_EXPIRING;
        }
        if($expire<$today) {
            $status[] = self::STATUS_EXPIRED;
        }
        if(!$active) {
            $status[] = self::STATUS_INACTIVE;
        }
        if(!$approved && $payment_exists && $payment->payment_sum>0) {
            $status[] = self::STATUS_PAYMENT;
        } 
        if(!$approved && (!$payment_exists || $payment->payment_sum==0)) {
            $status[] = self::STATUS_AWAITING;
        }
        
        return $status;
    }
    
    public function requiresAdminAction()
    {
        $status = $this->status();
        
        if(in_array(self::STATUS_AWAITING, $status)) {
            return true;
        }
        if(in_array(self::STATUS_PAYMENT, $status)) {
            return true;
        }
    }


    public function newApplications()
    {
        if($this->_newApps !== null) {
            return $this->_newApps;
        }
        
        $query = Daq_Db_Query::create("COUNT(*) as `cnt`");
        $query->from("Wpjb_Model_Application t");
        $query->where("job_id = ?", $this->id);
        $query->where("status = 1");
        
        $this->_newApps = $query->fetchColumn();
        
        return $this->_newApps;
    }
    
    public function paymentAccepted()
    {
        $moderation = (array)wpjb_conf("posting_moderation", 0);
        
        do_action("wpjb_job_saved", $this);
        
        if(!in_array(2, $moderation)) {
            $this->is_active = 1;
            $this->is_approved = 1;
            $this->save();
            
            do_action("wpjb_job_published", $this);
        }
        
        $message = Wpjb_Utility_Message::load("notify_employer_job_paid");
        $message->assign("job", $this);
        $message->setTo($this->company_email);
        $message->send();
    }
    
    public function __get($key) 
    {
        if($key == "file") {
            $dir = wpjb_upload_dir("job", "", $this->id, "basedir");
            $files = new stdClass();
            
            foreach(wpjb_glob($dir."/*") as $path) {
                $basename = basename($path);
                $objectname = str_replace("-", "_", $basename);
                $files->$objectname = array();
                foreach(wpjb_glob($dir."/".$basename."/*") as $file) {
                    $obj = new stdClass();
                    $obj->basename = basename($file);
                    $obj->path = $file;
                    $obj->url = wpjb_upload_dir("job", $basename, $this->id, "baseurl")."/".$obj->basename;
                    $obj->size = filesize($obj->path);
                    $files->{$objectname}[] = $obj;
                }
            }
            return $files;
        } else {
            return parent::__get($key);
        }
    }
    
    public function export(Daq_Helper_Xml $xml = null) 
    {
        if($xml === null) {
            $xml = new Daq_Helper_Xml();
        }
        
        $xml->open("job");
        $xml->tagIf("id", $this->id);
        $xml->tagIf("employer_id", $this->employer_id);
        $xml->tagIf("job_title", $this->job_title);
        $xml->tagIf("job_slug", $this->job_slug);
        $xml->tagCIf("job_description", $this->job_description);
        $xml->tagIf("job_created_at", $this->job_created_at);
        $xml->tagIf("job_modified_at", $this->job_modified_at);
        $xml->tagIf("job_expires_at", $this->job_expires_at);
        $xml->tagIf("job_country", $this->job_country);
        $xml->tagIf("job_state", $this->job_state);
        $xml->tagIf("job_zip_code", $this->job_zip_code);
        $xml->tagIf("job_city", $this->job_city);
        $xml->tagIf("company_name", $this->company_name);
        $xml->tagIf("company_url", $this->company_url);
        $xml->tagIf("company_email", $this->company_email);
        $xml->tagIf("is_approved", $this->is_approved);
        $xml->tagIf("is_active", $this->is_active);
        $xml->tagIf("is_filled", $this->is_filled);
        $xml->tagIf("is_featured", $this->is_featured);
        $xml->tagIf("applications", "0");
        $xml->tagIf("read", $this->read);
        $xml->tagIf("cache", $this->cache);

        $xml->open("metas");
        foreach($this->meta as $key => $value) {
            foreach($value->values() as $v) {
                $type = $value->conf("type");
                
                if($v) {
                    $xml->open("meta");
                    $xml->tag("name", $key);
                    if($type == "ui-input-textarea") {
                        $xml->tagCIf("value", $v);
                    } else {
                        $xml->tag("value", $v);
                    }
                    $xml->close("meta");
                }
            }
        }
        $xml->close("metas");
        
        $xml->open("tags");
        foreach($this->tag as $key => $tags) {
            foreach($tags as $tag) {
                $xml->open("tag");
                $xml->tag("type", $key);
                $xml->tag("title", $tag->title);
                $xml->tag("slug", $tag->slug);
                $xml->close("tag");
            }
        }
        $xml->close("tags");
        
        $xml->open("files");
        foreach($this->file as $category => $files) {
            
            foreach($files as $file) {
                
                if(stripos($file->basename, "__") === 0) {
                    continue;;
                }

                $filename = $category . "/" . $file->basename;

                $xml->open("file");
                $xml->tag("path", $filename);
                $xml->tag("content", base64_encode(file_get_contents($file->path)));
                $xml->close("file");
            }
        }
        $xml->close("files");
        
        $post_meta = get_post_meta($this->post_id);
        if(is_array($post_meta)) {
            $xml->open("post");
            foreach($post_meta as $key => $meta) {

                if(stripos($key, "_") === 0) {
                    continue;
                }

                foreach($meta as $mv) {
                    $xml->open("meta");
                    $xml->tag("name", $key);
                    $xml->tag("value", $mv);
                    $xml->close("meta");
                }
            }
            $xml->close("post");
        }
        
        
        $xml->close("job");
    }
    
    public function csv($columns) {
        
        $o = array(
            "job" => $this,
            "company" => $this->getCompany(true)
        );
        
        $data = array_fill_keys($columns, "");
 
        foreach($columns as $column) {
            list($object, $property) = explode(".", $column);
            
            if($o[$object]->get($property)) {
                $data[$column] = $o[$object]->get($property);
            } elseif(isset($o[$object]->meta->$property) && is_object($o[$object]->meta->$property)) {
                $data[$column] = join(";", $o[$object]->meta->$property->values());
            } elseif(isset($o[$object]->file->{$property}[0])) {
                $data[$column] = $o[$object]->file->{$property}[0]->url;
            } elseif(isset($o[$object]->tag->{$property}[0])) {
                $data[$column] = $o[$object]->tag->{$property}[0]->title;
            }
            
        }
        
        return $data;
    }
    
    public static function import_new($item) 
    {
        $default = new stdClass();
        $default->employer_id = null;
        $default->job_slug = Wpjb_Utility_Slug::generate(Wpjb_Utility_Slug::MODEL_JOB, (string)$item->job_title);
        $default->job_created_at = date("Y-m-d");
        $default->job_modified_at = date("Y-m-d");
        $default->job_expires_at = date("Y-m-d", strtotime("today +30 day"));
        $default->is_approved = 1;
        $default->is_active = 1;
        $default->is_filled = 0;
        $default->is_featured = 0;
        $default->applications = 0;
        $default->read = 0;
        
        if(isset($item->id)) {
            $id = (int)$item->id;
        } else {
            $id = null;
        }
        
        $job = new Wpjb_Model_Job($id);
        $exists = $job->exists();
        $approved = $job->is_approved;
        
        if($exists) {
            foreach($job->getFieldNames() as $key) {
                if(!isset($item->$key)) {
                    $item->$key = $job->$key;
                }
            }
        } else {
            foreach($default as $key => $value) {
                if(!isset($item->$key)) {
                    $item->$key = $value;
                }
            }
        }
        
        $job->employer_id = (int)$item->employer_id;
        $job->job_title = (string)$item->job_title;
        $job->job_slug = (string)$item->job_slug;
        $job->job_description = (string)$item->job_description;
        $job->job_created_at = (string)$item->job_created_at;
        $job->job_modified_at = (string)$item->job_modified_at;
        $job->job_expires_at = (string)$item->job_expires_at;
        $job->job_country = (int)$item->job_country;
        $job->job_state = (string)$item->job_state;
        $job->job_zip_code = (string)$item->job_zip_code;
        $job->job_city = (string)$item->job_city;
        $job->company_name = (string)$item->company_name;
        $job->company_url = (string)$item->company_url;
        $job->company_email = (string)$item->company_email;
        $job->is_approved = (int)$item->is_approved;
        $job->is_active = (int)$item->is_active;
        $job->is_filled = (int)$item->is_filled;
        $job->is_featured = (int)$item->is_featured;
        $job->applications = (int)$item->applications;
        $job->read = (int)$item->read;
        $job->cache = "";
        $job->save();
        
        $id = $job->id;
        $key = "job";
          
        if(isset($item->meta)) {
            foreach($item->meta as $meta) {

                $meta->object = $key;
                $meta->object_id = $id;
                
                Wpjb_Model_MetaValue::import($meta);
            }
        }
        
        if(isset($item->tag)) {
            foreach($item->tag as $tagGroup) {
                foreach((array)$tagGroup as $tag) {
                    $tag->object = $key;
                    $tag->object_id = $id;

                    Wpjb_Model_Tagged::import($tag);
                }
            }
        } 
        
        if(isset($item->file)) {
            foreach($item->file as $file) {
                list($path, $filename) = explode("/", (string)$file->path);
                $upload = wpjb_upload_dir("job", $path, $job->id, "basedir");
                wp_mkdir_p($upload);
                file_put_contents($upload."/".$filename, base64_decode((string)$file->content));
            }
        }
        
        do_action("wpjb_job_saved", $this);
        
        if($job->is_approved && (!$exists || !$approved)) {
            do_action("wpjb_job_published", $this);
        }
        
        return $id;
    }
    
    public static function import($item) 
    {
        $default = new stdClass();
        $default->empployer_id = null;
        $default->job_slug = Wpjb_Utility_Slug::generate(Wpjb_Utility_Slug::MODEL_JOB, (string)$item->job_title);
        $default->job_created_at = date("Y-m-d");
        $default->job_modified_at = date("Y-m-d");
        $default->job_expires_at = date("Y-m-d", strtotime("today +30 day"));
        $default->is_approved = 1;
        $default->is_active = 1;
        $default->is_filled = 0;
        $default->is_featured = 0;
        $default->applications = 0;
        $default->read = 0;
        
        if(isset($item->id)) {
            $id = (int)$item->id;
        } else {
            $id = null;
        }
        
        $job = new Wpjb_Model_Job($id);
        $exists = $job->exists();
        $approved = $job->is_approved;
        
        if($exists) {
            foreach($job->getFieldNames() as $key) {
                if(!isset($item->$key)) {
                    $item->$key = $job->$key;
                }
            }
        } else {
            foreach($default as $key => $value) {
                if(!isset($item->$key)) {
                    $item->$key = $value;
                }
            }
        }
        
        $job->employer_id = (int)$item->employer_id;
        $job->job_title = (string)$item->job_title;
        $job->job_slug = (string)$item->job_slug;
        $job->job_description = (string)$item->job_description;
        $job->job_created_at = (string)$item->job_created_at;
        $job->job_modified_at = (string)$item->job_modified_at;
        $job->job_expires_at = (string)$item->job_expires_at;
        $job->job_country = (int)$item->job_country;
        $job->job_state = (string)$item->job_state;
        $job->job_zip_code = (string)$item->job_zip_code;
        $job->job_city = (string)$item->job_city;
        $job->company_name = (string)$item->company_name;
        $job->company_url = (string)$item->company_url;
        $job->company_email = (string)$item->company_email;
        $job->is_approved = (int)$item->is_approved;
        $job->is_active = (int)$item->is_active;
        $job->is_filled = (int)$item->is_filled;
        $job->is_featured = (int)$item->is_featured;
        $job->applications = (int)$item->applications;
        $job->read = (int)$item->read;
        $job->cache = "";
        $job->save();
        
        if(isset($item->metas->meta)) {
            foreach($item->metas->meta as $meta) {
                $name = (string)$meta->name;
                $value = (string)$meta->value;
                $varr = array();

                if($meta->values) {
                    foreach($meta->values->value as $v) {
                        $varr[] = (string)$v;
                    }
                } else {
                    $varr[] = (string)$meta->value;
                }
                
                $vlist = $job->meta->$name->getValues();
                $c = count($varr);
                
                for($i=0; $i<$c; $i++) {
                    if(isset($vlist[$i])) {
                        $vlist[$i]->value = $varr[$i];
                        $vlist[$i]->save();
                    } else {
                        $mv = new Wpjb_Model_MetaValue;
                        $mv->meta_id = $job->meta->$name->getId();
                        $mv->object_id = $job->id;
                        $mv->value = $varr[$i];
                        $mv->save();
                    }
                }
                

            }
        }
        
        if(isset($item->tags->tag)) {
            foreach($item->tags->tag as $tag) {

                if($tag->id) {
                    $tid = (int)$tag->id;
                } else {
                    $tid = self::_resolve($tag);
                }

                $tagged = new Wpjb_Model_Tagged;
                $tagged->tag_id = $tid;
                $tagged->object = "job";
                $tagged->object_id = $job->id;
                $tagged->save();
            }  
        }
        
        if(isset($item->files->file)) {
            foreach($item->files->file as $file) {
                list($path, $filename) = explode("/", (string)$file->path);
                $upload = wpjb_upload_dir("job", $path, $job->id, "basedir");
                wp_mkdir_p($upload);
                file_put_contents($upload."/".$filename, base64_decode((string)$file->content));
            }
        }
        
        $tmp = new Wpjb_Model_Job($job->id);
        
        if(isset($item->post)) {
            $tmp->cpt();
            $post_id = $tmp->post_id;
            
            foreach($item->post->meta as $meta) {
                update_post_meta($post_id, $item->post->meta->name, $item->post->meta->value);
            }
        }
      
        do_action("wpjb_job_saved", $tmp);
        
        if($job->is_approved && (!$exists || !$approved)) {
            do_action("wpjb_job_published", $tmp);
        }
        
        if($id) {
            return "updated";
        } else {
            return "inserted";
        }
    }
    
    public static function importCsv($data) 
    {
        $key = "job";
        $form = new Wpjb_Form_Admin_AddJob();
        $object = new self();
        $fields = $object->getFieldNames();
        
        $import = new stdClass();
        $import->metas = new stdClass();
        $import->metas->meta = array();
        $import->tags = new stdClass();
        $import->tags->tag = array();
        $import->files = new stdClass();
        $import->files->file = array();
        
        foreach($data as $temp => $value) {
            list($tkey, $k) = explode(".", $temp);
            
            if($tkey != $key) {
                continue;
            }
            
            if(in_array($k, $fields)) {
                $import->$k = $value;
            } elseif($object->meta->$k) {
                if(stripos($value, ";")) {
                    $value = explode(";", $value);
                } elseif(empty($value)) {
                    $value = array();
                } else {
                    $value = array($value);
                }
                
                foreach($value as $v) {
                    $meta = new stdClass();
                    $meta->name = $k;
                    $meta->value = $v;
                    $import->metas->meta[] = $meta;
                }
            } elseif($object->tag->$k) {
                $tag = new stdClass();
                $tag->type = $k;
                $tag->slug = Wpjb_Utility_Slug::generate($k, $value);
                $tag->title = $value;
                $import->tags->tag[] = $tag;
            } elseif($form->hasElement($k) && $form->getElement($k)->getType() == "file") {
                $content = null;
                
                if(filter_var($value, FILTER_VALIDATE_URL) || stripos($value, "http://")===0) {
                    $response = wp_remote_get($value);
                    $filename = basename($value);
                    if(is_array($response)) {
                        $content = base64_encode($response["body"]);
                    }  
                } elseif(function_exists("finfo_open")) {
                    $mime = finfo_buffer(finfo_open(), base64_decode($content), FILEINFO_MIME_TYPE);
                    if(stripos($mime, "image/") === 0) {
                        $filename = str_replace("/", ".", $mime);
                        $content = $value;
                    }
                }
                
                if($content) {
                    $file = new stdClass();
                    $file->path = str_replace("_", "-", $k) . "/" . $filename;
                    $file->content = $content;
                    $import->files->file[] = $file;
                }
            }
        }
        
        self::import($import);
    }
    
    protected static function _resolve($tag) 
    {
        $query = new Daq_Db_Query();
        $query->select();
        $query->from("Wpjb_Model_Tag t");
        $query->where("type = ?", $tag->type);
        $query->where("slug = ?", $tag->slug);
        $query->limit(1);
        
        $result = $query->execute();
        
        if(empty($result)) {
            $t = new Wpjb_Model_Tag;
            $t->type = $tag->type;
            $t->slug = $tag->slug;
            $t->title = $tag->title;
            $t->save();
        } else {
            $t = $result[0];
        }
        
        return $t->id;
    }
    
    public function toArray()
    {
        $arr = parent::toArray();
        
        $arr["url"] = wpjb_link_to("job", $this);
        $arr["admin_url"] = wpjb_admin_url("job", "edit", $this->id);
        
        $arr["tag"] = array();
        
        foreach($this->tag() as $key => $tag) {
            $arr["tag"][$key] = array();
            foreach($tag as $t) {
                $arr["tag"][$key][] = $t->toArray();
            }
        }
        
        $upload = wpjb_upload_dir("job", "", $this->id);

        foreach($this->file as $file => $flist) {
            foreach($flist as $data) {
                
                if(stripos($data->basename, "__") === 0) {
                    continue;
                }

                if(!isset($arr["file"])) {
                    $arr["file"] = array();
                }
                
                if(!isset($arr["file"][$file])) {
                    $arr["file"][$file] = array();
                }
                
                $data = array(
                    "basename" => $data->basename,
                    "url" => $data->url,
                    "size" => $data->size
                );
                
                $arr["file"][$file][] = $data;
            }
        }

        $arr["country"] = Wpjb_List_Country::getByCode($this->job_country);
        
        return $arr;
        
    }
    
    public function url()
    {
        return wpjb_link_to("job", $this);
    }
    
    public function doScheme($name)
    {
        $scheme = apply_filters("wpjb_scheme", get_option("wpjb_form_job"), $this);
        
        if(wpjb_scheme_get($scheme, $name.".visibility")>0) {
            return true;
        } elseif(wpjb_scheme_get($scheme, $name.".render_callback")) {
            call_user_func(wpjb_scheme_get($scheme, $name.".render_callback"), $this);
            return true;
        }

        return false;
    }
    
    public function cpt() 
    {
        if(!$this->post_id || !get_post($this->post_id)) {
            // create new
            $post_id = wp_insert_post(array(
                "post_title" => $this->job_title,
                "post_name" => $this->job_slug,
                "post_type" => "job",
                "post_status" => "publish",
                "comment_status" => "closed"
            ));
            
            $this->post_id = $post_id;
            $this->save();
            
        } else {
            
            $post_id = wp_update_post(array(
                "ID" => $this->post_id,
                "post_title" => $this->job_title,
                "post_name" => $this->job_slug,
                "post_modified" => current_time("mysql"),
                "post_modified_gmt" => current_time("mysql", true),
                "post_type" => "job",
            ));
        }
        
        do_action("wpjb_cpt", $this, $post_id);
        
        return $post_id;
    }
    
}

?>