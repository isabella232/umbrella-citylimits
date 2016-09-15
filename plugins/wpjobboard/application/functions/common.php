<?php

function wpjb_price($amount, $currency) {
    $currency = Wpjb_List_Currency::getByCode($currency);
    
    $amount = number_format($amount, $currency["decimal"], ".", "");
    
    if(isset($currency["symbol"])) {
        return $currency["symbol"].$amount;
    } else {
        return $amount." ".$currency["code"];
    }
    
}

function wpjb_form_get_listings() {
    
    $query = new Daq_Db_Query();
    $query->select();
    $query->from("Wpjb_Model_Pricing t");
    $query->where("price_for = ?", Wpjb_Model_Pricing::PRICE_SINGLE_JOB);
    $query->where("is_active = 1");
    $result = $query->execute();
    $arr = array();
    
    foreach($result as $p) {
        $arr[] = array(
            "key" => $p->id,
            "value" => $p->id,
            "description" => $p->title
        );
    }
    
    return apply_filters("wpjb_form_get_listings", $arr);
}

/**
 * Returns allowed categories
 *
 * @return array
 */
function wpjb_form_get_categories() {
    $select = Daq_Db_Query::create();
    $select->from("Wpjb_Model_Tag t");
    $select->where("type = ?", Wpjb_Model_Tag::TYPE_CATEGORY);
    $select->order("title ASC");
    $list = $select->execute();
    $arr = array();
    
    foreach($list as $item) {
        $arr[] = array(
            "key" => $item->id,
            "value" => $item->id,
            "description" => $item->title
        );
    }
    
    return apply_filters("wpjb_form_get_categories", $arr);
}

/**
 * Returns allowed job types
 *
 * @return array 
 */
function wpjb_form_get_jobtypes() {
    $select = Daq_Db_Query::create();
    $select->from("Wpjb_Model_Tag t");
    $select->where("type = ?", Wpjb_Model_Tag::TYPE_TYPE);
    $select->order("title ASC");
    $list = $select->execute();
    $arr = array();
    
    foreach($list as $item) {
        $arr[] = array(
            "key" => $item->id,
            "value" => $item->id,
            "description" => $item->title
        );
    }
    
    return apply_filters("wpjb_form_get_jobtypes", $arr);
}

function wpjb_form_get_countries() {
    $arr = array();
    foreach(Wpjb_List_Country::getAll() as $listing) {
        $arr[] = array(
            "key" => $listing['code'], 
            "value" => $listing['code'], 
            "description" => $listing['name']
        );
    }
    
    return apply_filters("wpjb_form_get_countries", $arr);
}

function wpjb_upload_id($id = null) {
    if(!empty($id)) {
        $unique = $id;
    } elseif(get_current_user_id()>0) {
        $unique = "tmp_u_".get_current_user_id();
    } elseif(wpjb_transient_id()) {
        $unique = "tmp_s_".wpjb_transient_id();
    } else {
        return null;
    }
    
    return $unique;
}

function wpjb_upload_dir($object, $field, $id = null, $index = null) {
    
    global $blog_id;
    
    if($blog_id > 1) {
        $bid = "-".$blog_id;
    } else {
        $bid = "";
    }
    
    $unique = wpjb_upload_id($id);
    
    $dir = wp_upload_dir();
    $d = array();
    $d["baseurl"] = $dir["baseurl"]."/wpjobboard{$bid}/{$object}/{$unique}/{$field}";
    $d["basedir"] = $dir["basedir"]."/wpjobboard{$bid}/{$object}/{$unique}/{$field}";
    $d["dir"] = "{$object}/{$unique}/{$field}";

    if(isset($d[$index])) {
        return $d[$index];
    } else {
        return $d;
    }
}

function wpjb_bubble_delete($path) {
        
    $path = rtrim($path, "/")."/";
    $files = wpjb_glob($path . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            wpjb_bubble_delete($file);
        } else {
            unlink($file);
        }
    }
    
    if(is_dir($path)) {
        rmdir($path);
    }

}

function wpjb_recursive_delete($dirname)
{ 
    if(is_dir($dirname)) {
        $dir_handle=opendir($dirname);
    }
    
    while($file=readdir($dir_handle)) {
        if($file!="." && $file!="..") {
            if(!is_dir($dirname."/".$file)) {
                unlink ($dirname."/".$file);
            } else {
                wpjb_recursive_delete($dirname."/".$file);
            }
        }
    }
    
    closedir($dir_handle);
    rmdir($dirname);
    
    return true;
}

function wpjb_get_application_status($status = null) {
    
    $defaults = array(
        1 => array(
            "id" => 1,
            "key" => "new",
            "color" => null,
            "bulb" => "wpjb-bulb-new",
            "label" => __("New", "wpjobboard"),
            "public" => 1,
            "notify_applicant_email" => null,
            "labels" => array(
            )
        ),
        3 => array(
            "id" => 3,
            "key" => "read",
            "color" => null,
            "bulb" => "wpjb-bulb-new",
            "label" => __("Read", "wpjobboard"),
            "public" => 1,
            "notify_applicant_email" => null,
            "labels" => array(
                "multi_success" => __("Number of applications marked as read: {success}", "wpjobboard"),
            )
        ),
        0 => array(
            "id" => 0,
            "key" => "rejected",
            "color" => null,
            "bulb" => "wpjb-bulb-rejected",
            "label" => __("Rejected", "wpjobboard"),
            "public" => 1,
            "notify_applicant_email" => "notify_applicant_status_change",
            "labels" => array(
                "multi_success" => __("Number of rejected applications: {success}", "wpjobboard"),
            )
        ),
        2 => array(
            "id" => 2,
            "key" => "accepted",
            "color" => null,
            "bulb" => "wpjb-bulb-accepted",
            "label" => __("Accepted", "wpjobboard"),
            "public" => 1,
            "notify_applicant_email" => "notify_applicant_status_change",
            "labels" => array(
                "multi_success" => __("Number of accepted applications: {success}", "wpjobboard"),
            )
        )
    );
    
    $filtered = apply_filters("wpjb_application_status", $defaults);
    
    if($status === null) {
        return $filtered;
    }
    
    if(!array_key_exists($status, $filtered)) {
        return null;
    } else {
        return $filtered[$status];
    }
}

function wpjb_application_status($s = null, $bulb = false) {
    
    $list = wpjb_get_application_status();
    $status = array();
    $bb = array();
    $cc = array();
    
    foreach($list as $k => $data) {
        $status[$k] = $data["label"];
        $bb[$k] = $data["bulb"];
        $cc[$k] = $data["color"];
    }
    
    if($s === null) {
        return $status;
    } elseif(!$bulb) {
        return $status[$s];
    } elseif($bb[$s]) {
        $st = esc_html($status[$s]);
        return "<span class=\"wpjb-bulb {$bb[$s]}\">{$st}</span>";
    } elseif($cc[$s]) {
        $st = esc_html($status[$s]);
        return "<span class=\"wpjb-bulb\" style=\"background-color:{$cc[$s]}\">{$st}</span>";
    }
}

function wpjb_application_status_default() {
    return apply_filters("wpjb_application_status_default", 1);
}

function wpjb_date_format() {
    return apply_filters("wpjb_date_format", "Y/m/d");
}

function wpjb_default_currency() {
    return "USD";
}

function wpjb_default_payment_method() {
    return "PayPal";
}

function wpjb_option($param, $default = null) {
    return Wpjb_Project::getInstance()->conf($param, $default);
}

function wpjb_date($date, $format = null) {
    
    if(!$format) {
        $format = wpjb_date_format();
    }
    
    $ts = time();
    $format = apply_filters("wpjb_date", $format);
    
    $offset = get_option("gmt_offset");
    
    if(stripos($offset, "-") !== 0) {
        $offset = "+".$offset;
    }

    $date = new DateTime($date);
    $date->setTime(date("H", $ts), date("i", $ts), date("s", $ts));
    $date->modify($offset." hours");
    
    return $date->format($format);
}

function wpjb_time($date) {
    $date = new DateTime($date);
    return $date->format("U");
}

function wpjb_transient_id() {
    
    $sid = "wpjb_transient_id";
    $id = null;
    if(!headers_sent() && (!isset($_COOKIE[$sid]) || empty($_COOKIE[$sid]))) {
        $id = strval(time()."-".str_pad(rand(0, 9999), 4, "0", STR_PAD_LEFT));
        setcookie($sid, $id, time()+86400, COOKIEPATH, COOKIE_DOMAIN, false);
    } elseif(isset($_COOKIE[$sid])) {
        $id = $_COOKIE[$sid];
    }
    
    return $id;
}

function wpjb_form_field_upload(Daq_Form_Element $e, $form = null) {
    $limit = $e->getMaxFiles();
    ?>

    <div id="<?php echo $e->getName() ?>" class="wpjb-upload-list wpjb-none">
    <div class="wpjb-upload-actions">
        
        <a href="#<?php echo $limit ?>" id="wpjb-upload-<?php echo $e->getName() ?>" class="button">
            <span class="wpjb-upload-empty wpjb-glyphs wpjb-icon-upload">&nbsp;<?php _e("upload file ...", "wpjobboard") ?></span>
        </a>
        
        <span id="wpjb-upload-limit-<?php echo $e->getName() ?>" style="opacity:0.5">
            <span class="limit-reached"><?php _e("Limit reached, delete at least one file below to add more.", "wpjobboard") ?></span>
            &nbsp;<span class="limit"></span>
        </span>
    </div>
    </div>

    <div class="wpjb-upload-default">
        <?php for($i=0; $i<$limit && $i<6; $i++): ?>
            <input type="file" name="<?php esc_attr_e($e->getName()) ?>[]" class="<?php esc_attr_e($e->getClasses()) ?>" /><br/>
        <?php endfor; ?>
    </div>

    <?php 
            
        $path = $e->getUploadPath();
        $upload = wpjb_upload_dir($path["object"], $path["field"], $form->getId());
        $basedir = $upload["basedir"];
        $url = $upload["baseurl"];
        $dir = $upload["dir"];
        $size = '10mb';
        
        foreach($e->getValidators() as $k => $v) {
            if($k == "Daq_Validate_File_Size") {
                $size = $v->getSize();
                break;
            }
        }
    ?>
    
    <script type="text/javascript">
    if (typeof ajaxurl === 'undefined') {
        ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
    }
    if (typeof wpjb_plupload_icons == 'undefined') {
        wpjb_plupload_icons = "<?php esc_attr_e(plugins_url()."/wpjobboard/public/images") ?>";
    }
    
    jQuery(function($) {
        wpjb_plupload({
            runtimes : 'gears,html5,flash,silverlight,browserplus',
            browse_button : "wpjb-upload-<?php echo $e->getName() ?>",
            container : '<?php echo $e->getName() ?>',
            max_file_size : '<?php echo $size ?>',
            url : "<?php echo admin_url('admin-ajax.php') ?>",
            flash_swf_url : '<?php echo includes_url() ?>/js/plupload/plupload.flash.swf',
            silverlight_xap_url : '<?php echo includes_url() ?>/js/plupload/plupload.silverlight.xap',
            //filters : [{title : "Filter", extensions : "jpg,gif,png"}],
            multipart_params: {
                action: "wpjb_main_upload",
                form: "<?php esc_html_e(get_class($form)) ?>",
                field: "<?php esc_html_e($e->getName()) ?>",
                id: <?php echo $form->getId() ? $form->getId() : "null" ?>
            }
        });
        
        <?php foreach(wpjb_glob("$basedir/[!_]*") as $file): ?>
        <?php $f = basename($file); ?>
        <?php $size = filesize($file) ?>
        <?php $arr = array("name"=>$f, "url"=>$url."/".$f, "path"=>$dir."/".$f, "size"=>$size) ?>
        $("#<?php echo $e->getName() ?>").append(wpjb_pluploader_add_file(<?php echo json_encode($arr) ?>));
        <?php endforeach; ?>
        wpjb_plupload_handle_limit(jQuery("#wpjb-upload-<?php echo $e->getName() ?>"));   
        $(".wpjb-upload-list, .wpjb-upload-actions").removeClass("wpjb-none");
        $(".wpjb-upload-default").remove();
        wpjb_plupload_refresh();
    });
    </script>
    
    <?php wp_enqueue_script("wpjb-plupload"); ?>

    <?php
}

function wpjb_subscribe() {
    
    $instance = Wpjb_Project::getInstance();
    
    $view = Wpjb_Project::getInstance()->getApplication("frontend")->getView();
    $view->param = $instance->env("search_params");
    $view->feed_url = $instance->env("search_feed_url");
    $view->render("subscribe.php");
}

function wpjb_meta_register($object, $name, $params = array()) {
    $query = new Daq_Db_Query();
    $query->from("Wpjb_Model_Meta t");
    $query->where("meta_object = ?", $object);
    $query->where("name = ?", $name);
    $query->limit(1);
    
    $result = $query->execute();
    
    if(isset($result[0])) {
        $meta = $result[0];
    } else {
        $meta = new Wpjb_Model_Meta;
        $meta->meta_object = $object;
        $meta->name = $name;
        $meta->meta_type = 2;
        
        if(!empty($params) && is_array($params)) {
            $meta->meta_value = serialize($params);
        }
        $meta->save();
    }
    
    return $meta;
}

function wpjb_meta_unregister($object, $name) {
    $query = new Daq_Db_Query();
    $query->from("Wpjb_Model_Meta t");
    $query->where("meta_object = ?", $object);
    $query->where("name = ?", $name);
    $query->limit(1);
    
    $result = $query->execute();
    
    if(isset($result[0])) {
        $meta = $result[0];
        
        $query = new Daq_Db_Query();
        $query->from("Wpjb_Model_MetaValue t");
        $query->where("meta_id = ?", $meta->id);
        
        $result = $query->execute();
        foreach($result as $mv) {
            $mv->delete();
        }
        
        $meta->delete();
    }
}

function wpjb_glob($pattern, $flags = 0) {
    $list = glob($pattern, $flags);
    
    if(empty($list)) {
        return array();
    } else {
        return $list;
    }
}

function wpjb_rename_dir($old, $new) {
    
    $old = rtrim($old, "/");
    $new = rtrim($new, "/");
    
    if(!is_dir($old)) {
        return null;
    }
    
    rename($old, $new);
    
    $wpupload = wp_upload_dir();
    $stat = @stat($wpupload["basedir"]);
    $perms = $stat['mode'] & 0007777;
    chmod($new, $perms);
    
    foreach(wpjb_glob($new) as $sub) {
        chmod($sub, $perms);
    }
}

function wpjb_bulb($object) {
    
    if($object instanceof Wpjb_Model_Job) {
        $data = array(
            Wpjb_Model_Job::STATUS_ACTIVE => array("class"=>"wpjb-bulb-active", "title"=>__("Active", "wpjobboard")),
            Wpjb_Model_Job::STATUS_AWAITING => array("class"=>"wpjb-bulb-awaiting", "title"=>__("Awaiting Approval", "wpjobboard")),
            Wpjb_Model_Job::STATUS_PAYMENT => array("class"=>"wpjb-bulb-awaiting", "title"=>__("Awaiting Payment", "wpjobboard")),
            Wpjb_Model_Job::STATUS_EXPIRED => array("class"=>"wpjb-bulb-expired", "title"=>__("Expired", "wpjobboard")),
            Wpjb_Model_Job::STATUS_EXPIRING => array("class"=>"wpjb-bulb-expiring", "title"=>__("Expiring", "wpjobboard")),
            Wpjb_Model_Job::STATUS_INACTIVE => array("class"=>"wpjb-bulb-expired", "title"=>__("Inactive", "wpjobboard")),
            Wpjb_Model_Job::STATUS_NEW => array("class"=>"wpjb-bulb-new", "title"=>__("New", "wpjobboard")),
        );
    } else {
        throw new Exception("Invalid object type [".get_class($object)."]");
    }
    
    $st = array();
    $ignore = array(
        Wpjb_Model_Job::STATUS_NEW,
        Wpjb_Model_Job::STATUS_PAYMENT,
        Wpjb_Model_Job::STATUS_AWAITING,
    );

    foreach($object->status() as $status) {
        
        if(!is_admin() && in_array($status, $ignore)) {
            continue;
        }
        
        $c = $data[$status]["class"];
        $t = $data[$status]["title"];
        $st[] = "<span class=\"wpjb-bulb  $c\">$t</span>";
    }

    return $st;
                
}

function wpjb_google_map_url($object) {
    
    $key = wpjb_conf("google_api_key");
    
    if($key) {
    
        $mode = "place";

        $query = http_build_query(array(
            "key" => $key,
            "q" => $object->location(),
            "zoom" => "15"
        ));

        return "https://www.google.com/maps/embed/v1/$mode?$query";
        
    } else {
        
        $query = http_build_query(array(
            "ie" => "UTF8",
            "t" => "m",
            "near" => $object->location(),
            "ll" => $object->getGeo()->lnglat,
            "spn" => "0.107734,0.686646",
            "z" => "15",
            "output" => "embed",
            "iwloc" => "near",
        ));
        
        if(is_ssl()) {
            $protocol = "https";
        } else {
            $protocol = "http";
        }

        return "$protocol://maps.google.com/?$query";
    }
}

function wpjb_scheme_get($scheme, $path) {
    return wpjb_array_path($scheme["field"], $path);
}

function wpjb_array_path($arr, $path) {
    
    if(stripos($path, ".")===false) {
        if(isset($arr[$path])) {
            return $arr[$path];
        } else {
            return null;
        }
    }
    
    list($top, $rest) = explode(".", $path);

    if(isset($arr[$top])) {
        return wpjb_array_path($arr[$top], $rest);
    }
    
}

function wpjb_scheme_handle($scheme, $name) {
    
    if(wpjb_scheme_get($scheme, $name.".visibility")>0) {
        return true;
    } elseif(wpjb_scheme_get($scheme, $name.".render_callback")) {
        call_user_func(wpjb_scheme_get($scheme, $name.".render_callback"));
        return true;
    }
    
    return false;
}



function wpjb_custom_menu_key_to_url($key) {
    list($k, $v) = explode(":", $key);

    if($v == "step_add") {
        $id = wpjb_conf("urls_link_job_add");
    } else {
        $id = null;
    }

    switch($k) {
        case "frontend":
            $url = wpjb_link_to($v, null, array(), $id);
            break;
        case "resumes":
            $url = wpjr_link_to($v, null, array(), null);
            break;
        case "page":
            $url = get_permalink($v);
            break;
        case "http":
        case "https":
            $url = $key;
            break;
    }

    return $url;
}



function wpjb_custom_menu_show_link($link) {

    if(!isset($link["menu-item-visibility"])) {
        return false;
    }

    $v = $link["menu-item-visibility"];

    if(isset($v["unregistered"]) && $v["unregistered"]==1 && get_current_user_id()<1) {
        return true;
    }

    if(isset($v["loggedin"]) && $v["loggedin"]==1 && get_current_user_id()>0) {
        return true;
    }

    if(isset($v["candidate"]) && $v["candidate"]==1 && current_user_can("manage_resumes")) {
        return true;
    }

    if(isset($v["employer"]) && $v["employer"]==1 && current_user_can("manage_jobs")) {
        return true;
    }

    return false;
}



?>
