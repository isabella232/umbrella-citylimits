<?php

function wpjb_title() {
    
    $title = "";
    
    if(is_wpjb() || is_wpjr()) {
        $title = "<h2>".esc_html(Wpjb_Project::getInstance()->title)."</h2>";
    }
    
    return $title;
}

function wpjb_jobs_search() {
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve("search");
    $route = $instance->router()->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);


    $instance->getApplication("frontend")->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication("frontend")->content;
}

function wpjb_jobs_list($atts) {
    global $wp, $wp_rewrite;

    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $request = Daq_Request::getInstance();
    
    $slug = get_query_var("wpjbslug");
    $tag = get_query_var("wpjbtag");
    $category = null;
    $type = null;
    
    if(!empty($slug)) {
        
        switch(get_query_var("wpjbtag")) {
            case "category": $tag = Wpjb_Model_Tag::TYPE_CATEGORY; break;
            case "type": $tag = Wpjb_Model_Tag::TYPE_TYPE; break;
            default: $tag = null;
        }
        
        $query = new Daq_Db_Query;
        $query->from("Wpjb_Model_Tag t");
        $query->where("slug = ?", $slug);
        $query->where("type = ?", $tag);
        $query->limit(1);
        
        $result = $query->execute();
        $model = $result[0];
        
        switch($result[0]->type) {
            case Wpjb_Model_Tag::TYPE_CATEGORY: $category = $result[0]->id; break;
            case Wpjb_Model_Tag::TYPE_TYPE: $type = $result[0]->id; break;
        }
    }
    
    $page = $request->get("pg", get_query_var("paged", 1));
    if($page < 1) {
        $page = 1;
    }
    
    if(is_home() || is_front_page()) {
        $page = get_query_var("page", $page);
    }
    
    $params = shortcode_atts(array(
        "filter" => "active",
        "query" => null,
        "category" => $category,
        "type" => $type,
        "country" => null,
        "state" => null,
        "city" => null,
        "posted" => null,
        "location" => null,
        "is_featured" => null,
        "employer_id" => null,
        "meta" => array(),
        "hide_filled" => wpjb_conf("front_hide_filled", false),
        "sort" => null,
        "order" => null,
        "sort_order" => "t1.is_featured DESC, t1.job_created_at DESC, t1.id DESC",
        "search_bar" => wpjb_conf("search_bar", "disabled"),
        "pagination" => true,
        "standalone" => false,
        "id__not_in" => null,
        'page' => $page,
        'count' => wpjb_conf("front_jobs_per_page", 20),
        'page_id' => get_the_ID()
    ), $atts);
    
    foreach((array)$atts as $k=>$v) {
        if(stripos($k, "meta__") === 0) {
            $params["meta"][substr($k, 6)] = $v;
        }
    }
    
    $plist = array("query", "location", "country", "state", "city", "type", "category");
    foreach($plist as $p) {
        if($request->get($p)) {
            $params[$p] = $request->get($p);
        }
    }
    
    $init = array();
    foreach(array_keys((array)$atts) as $key) {
        if(isset($params[$key]) && !in_array($key, array("search_bar"))) {
            $init[$key] = $params[$key];
        }
    }
    
    if(!empty($category)) {
        $permalink = wpjb_link_to("category", $model);
    } elseif(!empty($type)) {
        $permalink = wpjb_link_to("type", $model);
    } else {
        $permalink = get_the_permalink();
    }
    
    $view = Wpjb_Project::getInstance()->getApplication("frontend")->getView();
    $view->atts = $atts;
    $view->param = $params;
    $view->pagination = $params["pagination"];
    $view->url = $permalink;
    $view->query = "";
    $view->shortcode = true;
    $view->search_bar = $params["search_bar"];
    $view->search_init = $init;
    $view->page_id = $params["page_id"];
    if ( get_option('permalink_structure') ) {
        $view->format = 'page/%#%/';
    } else {
        $view->format = '&paged=%#%';
    }
    
    Wpjb_Project::getInstance()->placeHolder = $view;
    
    wp_enqueue_style("wpjb-css");
    wp_enqueue_script('wpjb-js');
    
    ob_start();
    $view->render("index.php");
    $render = ob_get_clean();
    
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $render;
}

function wpjb_employers_search() {
    
}

function wpjb_employers_list($atts = array()) {
    $request = Daq_Request::getInstance();
    
    $page = $request->get("pg", get_query_var("paged", 1));
    if($page < 1) {
        $page = 1;
    }
    
    $params = shortcode_atts(array(
        "filter" => "active",
        "query" => null,
        "location" => null,
        "meta" => array(),
        "sort" => null,
        "order" => null,
        "search_bar" => wpjb_conf("search_bar", "disabled"),
        "sort_order" => "t1.company_name ASC",
        "pagination" => true,
        'page' => $page,
        'count' => 20,
        'page_id' => get_the_ID()
    ), $atts);
    
    foreach((array)$atts as $k=>$v) {
        if(stripos($k, "meta__") === 0) {
            $params["meta"][substr($k, 6)] = $v;
        }
    }
    
    $init = array();
    foreach(array_keys((array)$atts) as $key) {
        if(isset($params[$key]) && !in_array($key, array("search_bar"))) {
            $init[$key] = $params[$key];
        }
    }
    
    if($request->get("query")) {
        $params["query"] = $request->get("query");
    }
    if($request->get("location")) {
        $params["location"] = $request->get("location");
    }
    
    if ( get_option('permalink_structure') ) {
        $format = 'page/%#%/';
    } else {
        $format = '&paged=%#%';
    }
    
    $view = Wpjb_Project::getInstance()->getApplication("frontend")->getView();
    $view->param = $params;
    $view->url = get_the_permalink();
    $view->query = "";
    $view->shortcode = true;
    $view->format = $format;
    $view->page_id = $params["page_id"];
    $view->search_bar = $params["search_bar"];
    $view->search_init = $init;
    $view->pagination = $params["pagination"];
    
    Wpjb_Project::getInstance()->placeHolder = $view;
    
    wp_enqueue_style("wpjb-css");
    
    ob_start();
    $view->render("employers.php");
    return ob_get_clean();
}

function wpjb_resumes_search() {
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve("search");
    $route = $instance->router()->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);


    $instance->getApplication("resumes")->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication("resumes")->content;
}

function wpjb_resumes_list($atts) {
    
    $request = Daq_Request::getInstance();
    
    $page = $request->get("pg", get_query_var("paged", 1));
    if($page < 1) {
        $page = 1;
    }
    
    $params = shortcode_atts(array(
        "filter" => "active",
        "query" => null,
        "fullname" => null,
        "category" => null,
        "type" => null,
        "country" => null,
        "posted" => null,
        "location" => null,
        "is_featured" => null,
        "meta" => array(),
        "sort" => null,
        "order" => null,
        "sort_order" => "t1.modified_at DESC, t1.id DESC",
        'page' => $page,
        'count' => 20,
        'search_bar' => wpjb_conf("cv_search_bar", "disabled"),
        'page_id' => get_the_ID()
    ), $atts);
    
    foreach((array)$atts as $k=>$v) {
        if(stripos($k, "meta__") === 0) {
            $params["meta"][substr($k, 6)] = $v;
        }
    }
    
    $can_browse = wpjr_can_browse();
    
    $view = Wpjb_Project::getInstance()->getApplication("resumes")->getView();
    $view->param = $params;
    $view->url = get_the_permalink();
    $view->query = "";
    $view->shortcode = true;
    $view->format = '?pg=%#%';
    $view->search_bar = $params["search_bar"];
    $view->page_id = $params["page_id"];
    $view->can_browse = $can_browse;
    
    
    if(!$can_browse) {
        if(Wpjb_Project::getInstance()->placeHolder === null) {
            Wpjb_Project::getInstance()->placeHolder = new stdClass();
        }

        Wpjb_Project::getInstance()->placeHolder->_flash = $view->_flash;
        Wpjb_Project::getInstance()->placeHolder->_flash->addError(wpjr_can_browse_err());
        
        if(wpjb_conf("cv_privacy") == 1) {
            wpjb_flash();
            return false;
        }
    }
    
    Wpjb_Project::getInstance()->placeHolder = $view;
    
    wp_enqueue_style("wpjb-css");
    
    ob_start();
    $view->render("index.php");
    return ob_get_clean();
}

function wpjb_apply_form() {
    
    $request = Daq_Request::getInstance();
    $job = new Wpjb_Model_Job();
    $view = Wpjb_Project::getInstance()->getApplication("frontend")->getView();
    
    wp_enqueue_script("jquery");
    wp_enqueue_script("wpjb-js");
    wp_enqueue_script("wpjb-plupload");
    wp_enqueue_style("wpjb-css");
    
    $form = new Wpjb_Form_Apply();
    $form->getElement("_wpjb_action")->setValue("apply_general");
    
    $can_apply = true;
    
    if($request->post("_wpjb_action")=="apply_general" && $can_apply) {

        if($form->isValid($request->getAll())) {
            // send
            $var = $form->getValues();

            $user = null;
            if($job->user_id) {
                $user = new WP_User($job->user_id);
            }

            $form->setJobId($job->getId());
            $form->setUserId(Wpjb_Model_Resume::current()->user_id);

            $form->save();
            $application = new Wpjb_Model_Application($form->getObject()->id);

            // notify employer
            $files = array();
            foreach($application->getFiles() as $f) {
                $files[] = $f->dir;
            }

            // notify admin
            $mail = Wpjb_Utility_Message::load("notify_admin_general_application");
            $mail->assign("application", $application);
            $mail->assign("resume", Wpjb_Model_Resume::current());
            $mail->addFiles($files);
            $mail->setTo(get_option("admin_email"));
            $mail->send();

            // notify applicant
            $notify = null;
            if(isset($var["email"]) && $var["email"]) {
                $notify = $var["email"];
            } elseif(wp_get_current_user()->ID > 0) {
                $notify = wp_get_current_user()->user_email;
            }
            $mail = Wpjb_Utility_Message::load("notify_applicant_applied");
            $mail->setTo($notify);
            $mail->assign("job", $job);
            $mail->assign("application", $application);
            if($notify !== null) {
                $mail->send();
            }

            $view->_flash->addInfo(__("Your application has been sent.", "wpjobboard"));
            $form = new Wpjb_Form_Apply();
            
        } else {
            $view->_flash->addError(__("There are errors in your form.", "wpjobboard"));
        }

    } elseif(Wpjb_Model_Resume::current()) {
        $resume = Wpjb_Model_Resume::current();
        if(!is_null($resume) && $form->hasElement("email")) {
            $form->getElement("email")->setValue($resume->user->user_email);
        }
        if(!is_null($resume) && $form->hasElement("applicant_name")) {
            $form->getElement("applicant_name")->setValue($resume->user->first_name." ".$resume->user->last_name);
        }
    }
    
    $view->form = $form;
    $view->submit = __("Send Application", "wpjobboard");
    $view->action = "";
    $view->shortcode = true;
    
    if(Wpjb_Project::getInstance()->placeHolder === null) {
        Wpjb_Project::getInstance()->placeHolder = new stdClass();
    }
    
    Wpjb_Project::getInstance()->placeHolder->_flash = $view->_flash;
    
    ob_start();
    $view->render("../default/form.php");
    return ob_get_clean();
}

function wpjb_jobs_add() {
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve("step_add");
    $route = $instance->router()->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);

    $instance->getApplication("frontend")->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication("frontend")->content;
}

function wpjb_employer_panel() {
    global $wp, $wp_query, $wp_rewrite;
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);

    if(get_query_var("wpjbstep")) {
        return wpjb_jobs_add();
    }
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve("employer_home");
    $route = $instance->router()->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);

    $instance->getApplication("frontend")->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication("frontend")->content;
}

function wpjb_employer_register() {
    $app = "frontend";
    $narrow = null;
    $default = "employer_new";
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve($default, $narrow);
    $route = $instance->router($app)->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);

    $instance->getApplication($app)->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication($app)->content;
}

function wpjb_candidate_register() {
    $app = "resumes";
    $narrow = "candidate";
    $default = "register";
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve($default, $narrow);
    $route = $instance->router($app)->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);

    $instance->getApplication($app)->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication($app)->content;
}

function wpjb_candidate_panel() {
    
    $app = "resumes";
    $narrow = "candidate";
    $default = "myresume_home";
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve($default, $narrow);
    $route = $instance->router($app)->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);

    $instance->getApplication($app)->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication($app)->content;
}

function wpjb_alerts() {
    
    $instance = Wpjb_Project::getInstance();
    $instance->shortcodeStart(__FUNCTION__);
    
    $rewrite = new Wpjb_Utility_Rewrite;
    $resolved = $rewrite->resolve("alert_confirm");
    $route = $instance->router()->getRoute($resolved["route"]);
    $result = $rewrite->convertRoute($route, $resolved);
    
    $instance->getApplication("frontend")->dispatch(null, $result);
    $instance->addScriptsFront();
    $instance->shortcodeEnd(__FUNCTION__);
    
    return $instance->getApplication("frontend")->content;
}

function wpjb_if($atts, $content = "") {

    $atts = shortcode_atts(array(
        'is_routed_to' => '',
        'module' => 'frontend'
    ), $atts, 'wpjb_if' );
    
    if(wpjb_is_routed_to($atts["is_routed_to"], $atts["module"])) {
        return $content;
    }
    
    return "";
}

/**
 * Displays login form
 * 
 * @param array $atts Shortcode attributes
 *      @param: zoom Int 
 * @return void
 */
function wpjb_login($atts) {
    
    $view = Wpjb_Project::getInstance()->getApplication("frontend")->getView();
    
    if(get_current_user_id()) {
        $m = __('You are already logged in. Go to <a href="%1$s">Client Panel</a> or <a href="%2$s">Logout</a>.', "wpjobboard");
        
        if(current_user_can("manage_jobs")) {
            $url1 = wpjb_link_to("employer_home");
            $url2 = wpjb_link_to("employer_logout");
        } else {
            $url1 = wpjr_link_to("myresume_home");
            $url2 = wpjr_link_to("logout");
        }
        
        $flash = new Wpjb_Utility_Session();
        $flash->addInfo(sprintf($m, esc_attr($url1), esc_attr($url2)));
        $flash->save();
        
        ob_start();
        wpjb_flash();
        return ob_get_clean();
    }
    
    $params = shortcode_atts(array(
        "links" => array()
    ), $atts);
    
    if(!is_array($params["links"])) {
        $params["links"] = array_map("trim", explode(",", $params["links"]));
    }
    
    $form = new Wpjb_Form_Login();
    $form->getElement("redirect_to")->setValue("");

    $buttons = array();
    
    if(in_array("employer_reg", $params["links"])) {
        $buttons[] = array(
            "tag" => "a", 
            "href" => wpjb_link_to("employer_new"), 
            "html" => __("Employer Registration", "wpjobboard")
        );
    }
    
    if(in_array("candidate_reg", $params["links"])) {
        $buttons[] = array(
            "tag" => "a", 
            "href" => wpjr_link_to("register"), 
            "html" => __("Candidate Registration", "wpjobboard")
        );
    } 
    
    $view->action = "";
    $view->form = $form;
    $view->submit = __("Login", "wpjobboard");
    $view->buttons = $buttons;
    $view = apply_filters("wpjb_shortcode_login", $view);
    
    ob_start();
    $view->render("./../default/form.php");
    return ob_get_clean();
}

function wpjb_map($atts = array()) {
    
    wp_enqueue_script( 'jquery' );
    
    $params = shortcode_atts(array(
        "data" => "jobs",
        "center" => "",
        "auto_locate" => 0,
        "zoom" => 12,
        "width" => "100%",
        "height" => "400px"
    ), $atts);
    
    if($params["auto_locate"]) {
        $init_func = "wpjb_map_init_auto_locate";
    } else {
        $init_func = "wpjb_map_init";
    }
    
    ob_start();
    
    ?>

    <script type="text/javascript" src="<?php echo (is_ssl() ? "https": "http") ?>://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
    <script type='text/javascript' src="<?php echo (is_ssl() ? "https": "http") ?>://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>
    <script type="text/javascript" src="<?php echo (is_ssl() ? "https": "http") ?>://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>

    <script type="text/javascript">
    // jQuery.extend()
        
    if (typeof ajaxurl === 'undefined') {
        ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
    }    
    var wpjbDefaults = {
        zoom: <?php echo (int)$params["zoom"] ?>,
        address: '<?php esc_html_e($params["center"]) ?>',
        auto_locate: '<?php esc_html_e($params["auto_locate"]) ?>',
        objects: '<?php esc_html_e($params["data"]) ?>',
        
        images: {
            closeBoxURL: "<?php echo plugins_url() ?>/wpjobboard/public/images/map-close.png",
            pin: "<?php echo plugins_url() ?>/wpjobboard/public/images/map/map-marker.png",
            loader: '<?php echo admin_url() ?>/images/wpspin_light-2x.gif'
            
        },
        
        mapOptions: {},
        markerOptions: {},
        infoBoxOptions: {},
        mcOptions: {
            styles: [
                {
                    height: 53,
                    width: 53,
                    textSize: 20,
                    textColor: "white",
                    url: "<?php echo plugins_url() ?>/wpjobboard/public/images/map/clusterer-1.png"
                    
                },
                {
                    height: 59,
                    width: 59,
                    textSize: 20,
                    textColor: "white",
                    url: "<?php echo plugins_url() ?>/wpjobboard/public/images/map/clusterer-2.png"
                },
                {
                    height: 66,
                    width: 66,
                    textSize: 22,
                    textColor: "white",
                    url: "<?php echo plugins_url() ?>/wpjobboard/public/images/map/clusterer-3.png"
                    
                },
                {
                    height: 78,
                    width: 78,
                    textSize: 22,
                    textColor: "white",
                    url: "<?php echo plugins_url() ?>/wpjobboard/public/images/map/clusterer-4.png"
                },
                {
                    height: 90,
                    width: 90,
                    textSize: 24,
                    textColor: "white",
                    url: "<?php echo plugins_url() ?>/wpjobboard/public/images/map/clusterer-5.png"
                    
                }
            ],
            gridSize: 50
        }

    };

    var wpjbMap = null; 
    var wpjbMarkers = [];
    var wpjbMarkerClusterer = null;
    var wpjbInfoWindow = null;
    var wpjbMapCallbacks = {
        loadData: {}
    };


    function wpjb_map_init() {
        
        jQuery(".wpjb-map-overlay").css("visibility", "visible");
        jQuery.ajax({
          url:"<?php echo (is_ssl() ? "https": "http") ?>://maps.googleapis.com/maps/api/geocode/json?address="+wpjbDefaults.address+"&sensor=false",
          type: "POST",
          success: function(res) {
              wpjb_map_initialize(res.results[0].geometry.location);
          }
        });
    }
    
    function wpjb_map_init_auto_locate() {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    
                    var pos = new google.maps.LatLng(
                        position.coords.latitude,
                        position.coords.longitude
                    );

                    wpjb_map_initialize({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                    
                }, function() {
                    wpjb_map_init();
                }
            );
        } else {
          // Browser doesn't support Geolocation
          wpjb_map_init();
        }
    }

    function wpjb_map_initialize(geoLoc) {
        
        //var geoLoc = res.results[0].geometry.location;
        var mapOptions = {
            zoom: wpjbDefaults.zoom,
            center: new google.maps.LatLng(geoLoc.lat, geoLoc.lng),
            panControl: false,
            loginControl: false,
            streetViewControl: false,
            mapTypeControl: true,
            mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.DEFAULT,
              mapTypeIds: [
                google.maps.MapTypeId.ROADMAP,
                google.maps.MapTypeId.TERRAIN
              ]
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.LEFT_CENTER
            }
        };
        
        wpjbMap = new google.maps.Map(document.getElementById('wpjb-map-canvas'), mapOptions);
        wpjbMarkerClusterer = new MarkerClusterer(wpjbMap, wpjbMarkers, wpjbDefaults.mcOptions);
        wpjbInfoWindow = new google.maps.InfoWindow();
        wpjbInfoWindow = new InfoBox({
            boxClass: "wpjb-map-infobox",
            content: document.getElementById("wpjb-map-infobox"),
            disableAutoPan: false,
            maxWidth: 150,
            pixelOffset: new google.maps.Size(20, -74),
            zIndex: null,
            closeBoxMargin: "5px 0 0 0",
            closeBoxURL: wpjbDefaults.images.closeBoxURL,
            infoBoxClearance: new google.maps.Size(1, 1)
        });
        
        wpjb_map_load_data();
    }
    
    function wpjb_map_load_data() {
        jQuery(".wpjb-map-overlay").css("visibility", "visible");

        var data = {
            action: "wpjb_map_data",
            objects: wpjbDefaults.objects
        };
        
        var callbacks = jQuery.Callbacks();
        jQuery.each(wpjbMapCallbacks.loadData, function(index, cb) {
            callbacks.add( cb );
        });

        callbacks.fire( data );
        callbacks.empty();
        
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            dataType: "json",
            success: wpjb_map_load_data_success
        });
    }
    
    function wpjb_map_load_data_success(response) {
        
        wpjbMarkers = [];
        wpjbMarkerClusterer.clearMarkers();

        
        jQuery.each(response, function(index, item) {

            var marker = new google.maps.Marker({
                title: item.properties.title,
                wpjbObject: item.properties.object,
                wpjbId: item.properties.id,
                position: new google.maps.LatLng(item.geometry.coordinates[1], item.geometry.coordinates[0]),
                map: wpjbMap,
                icon: wpjbDefaults.images.pin,
                animation: google.maps.Animation.DROP

            });
            
            google.maps.event.addListener(marker, 'click', function() {
                
                // AJAX LOAD DATA
                wpjbInfoWindow.close();
                wpjbInfoWindow.setContent('<img src="'+wpjbDefaults.images.loader+'" alt="" />');
                wpjbInfoWindow.open(wpjbMap, marker);
                
                var data = {
                    action: "wpjb_map_details",
                    object: marker.wpjbObject,
                    id: marker.wpjbId
                };
                
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                    dataType: "html",
                    success: wpjb_map_load_details_success
                });
            });
            
            wpjbMarkers.push(marker);
            
            // @todo: create some cool events
            
        });
        
        wpjbMarkerClusterer.addMarkers(wpjbMarkers);
        
        jQuery(".wpjb-map-overlay").css("visibility", "hidden");
    }

    function wpjb_map_load_details_success(response) {
        wpjbInfoWindow.setContent(response);
    }

    google.maps.event.addDomListener(window, 'load', <?php echo $init_func ?>);



    </script>

    <div class="wpjb-map-holder">
        <div class="wpjb-map-overlay standard ">&nbsp</div>
        <div id="wpjb-map-canvas" class="wpjb-google-map" style="height:<?php esc_attr_e($params["height"]) ?>; width:<?php esc_attr_e($params["width"]) ?>; margin:0; padding:0"></div>
    </div>
    
    <?php
    
    return ob_get_clean();
}

?>
