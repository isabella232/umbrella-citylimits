<?php
/**
 * Description of Project
 *
 * @author greg
 * @package 
 */

class Wpjb_Project extends Daq_ProjectAbstract
{
    protected static $_instance = null;
    
    /**
     *
     * @var Wpjb_Utility_HelpScreen
     */
    public $helpScreen = null;
    
    /**
     *
     * @var Wpjb_Payment_Factory
     */
    public $payment = null;
    
    /**
     * List of shortcodes currenty being run
     *
     * @var array
     */
    protected $_shortcode = array();

    /**
     * Version is modified by build script.
     */
    const VERSION = "4.3.4";

    /**
     * Returns instance of self
     *
     * @return Wpjb_Project
     */
    public static function getInstance()
    {
        if(self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function run()
    {
        add_filter("no_texturize_tags", array($this, "nonoTags"));
        add_filter('query_vars', array($this, "queryVars"));
        add_filter('redirect_canonical', array($this, "redirectCanonical"));
        add_action('wp_enqueue_scripts', array($this, "addScriptsFront"), 20);
        add_action("admin_bar_menu", array($this, "adminBarMenu"), 1000 );
        add_filter("template_redirect", array($this, "templateRedirect"));
        
        add_action('deleted_user', array($this, "deletedUser"));
        
        add_filter("init", array($this, "init"));
        add_filter('init', array($this, "actions"), 15);
        add_action("admin_menu", array($this, "addAdminMenu"));
        add_action("admin_enqueue_scripts", array($this, "adminEnqueueScripts"));
        add_action("admin_print_scripts", array($this, "addScripts"));
        add_action("admin_print_styles", array($this, "adminPrintStyles"));
        add_action('edit_post', array($this, "editPost"));
        add_action('wp_footer', array($this, "ttFix"));
       
        // rewrites
        add_filter('rewrite_rules_array', array($this, "rewrite"));
        add_action('generate_rewrite_rules', array($this, "generateRewriteRules"));
        add_filter('query_vars', "wpjb_rewrite_query_vars");
        
        // shortcodes 
        add_shortcode('wpjb_jobs_search', 'wpjb_jobs_search');
        add_shortcode('wpjb_jobs_list', 'wpjb_jobs_list');
        add_shortcode('wpjb_resumes_search', 'wpjb_resumes_search');
        add_shortcode('wpjb_resumes_list', 'wpjb_resumes_list');
        add_shortcode('wpjb_employers_search', 'wpjb_employers_search');
        add_shortcode('wpjb_employers_list', 'wpjb_employers_list');
        add_shortcode('wpjb_apply_form', 'wpjb_apply_form');
        add_shortcode('wpjb_employer_panel', 'wpjb_employer_panel');
        add_shortcode('wpjb_employer_register', 'wpjb_employer_register');
        add_shortcode('wpjb_candidate_panel', 'wpjb_candidate_panel');
        add_shortcode('wpjb_candidate_register', 'wpjb_candidate_register');
        add_shortcode('wpjb_title', 'wpjb_title');
        add_shortcode('wpjb_alerts', 'wpjb_alerts');
        add_shortcode('wpjb_flash', 'wpjb_flash');
        add_shortcode('wpjb_if', 'wpjb_if');
        add_shortcode('wpjb_login', 'wpjb_login');
        add_shortcode('wpjb_jobs_add', 'wpjb_jobs_add');
        add_shortcode('wpjb_map', 'wpjb_map');
        
        // events
        add_action("wpjb_event_import", "wpjb_event_import");
        add_action("wpjb_event_expiring_jobs", "wpjb_event_expiring_jobs");
        add_action("wpjb_event_subscriptions_daily", "wpjb_event_subscriptions_daily");
        add_action("wpjb_event_subscriptions_weekly", "wpjb_event_subscriptions_weekly");
        
        /* add_action('wp_dashboard_setup', array(self::$_instance, "addDashboardWidgets")); */
        
        $mode = $this->conf("urls_mode", array(1));
        $uses_cpt = $this->conf("urls_cpt", null);
        
        if($mode == 2) {
            $rewrite = new Wpjb_Utility_Rewrite;

            add_filter("the_title", array($rewrite, "titles"), 10, 2);
            add_filter("wp_title", array($rewrite, "titles"), 1000);
        } else {
            add_filter("wp_title", array($this, "injectTitle"));
            add_filter("single_post_title", array($this, "injectTitle"));
            add_filter('the_title', array($this, "theTitle"), 10, 2);
            add_filter('wp', array($this, "execute"));
        }
        
        if($uses_cpt) {
            $this->setEnv("uses_cpt", true);
            add_action("admin_print_scripts-post.php", array($this, "cptPrintScripts"), 11 );
            add_action('admin_print_styles-post.php', array($this, "cptPrintStyles"), 11 );
            add_filter("init", array($this, "cptInit"));
            add_filter("the_content", array($this, "theContentCpt"));
            add_action('template_redirect', array($this, "cptDisableArchive"));
        } else {
            $this->setEnv("uses_cpt", false);
            add_action("plugins_loaded", array("Wpjb_Utility_Yoast", "connect"), 20);
            add_action("plugins_loaded", array("Wpjb_Utility_Genesis", "connect"), 20);
        }

        if(is_admin()) {
            Wpjb_Utility_WPSuperCache::connect();
            Wpjb_Utility_W3TotalCache::connect();
            
            $so = new Wpjb_Utility_ScreenOptions();
            add_filter('screen_settings', array($so, "screenSettings"), 10, 2 );
            add_filter('set-screen-option', array($so, "setScreenOptions"), 11, 3);
            add_filter('screen_options_show_screen', array($so, "showScreen"), 10, 2);
            
            Wpjb_Upgrade_Manager::connect(self::VERSION);
            
            if(wpjb_conf("version")) {
                Wpjb_Upgrade_Manager::update();
            }
            
            if(wpjb_conf("activation_message_hide", 0) == 0) {
                add_action("admin_notices", "wpjb_activation_message");
            }
        }
        
        // workarounds
        add_filter("wp_list_pages_excludes", array($this, "theTitleDisable"));
        add_filter("wp_list_pages", array($this, "theTitleEnable"));
        //add_filter("widget_posts_args", array($this, "theTitleDisable"));
        
        $this->_init();
        
        $this->getAdmin()->getView()->slot("logo", "settings.png");
        
        $this->payment = new Wpjb_Payment_Factory(array(
            new Wpjb_Payment_Credits,
            new Wpjb_Payment_PayPal,
            new Wpjb_Payment_Stripe
        ));
        $this->payment->sort();
        
        if(!is_admin()) {
            foreach((array)$this->conf("front_recaptcha_enabled") as $hook) {
                add_filter($hook, array($this, "recaptcha"));
            }
        }
        
        $linkedin_share = $this->conf("linkedin_share");
        $linkedin_apply = $this->conf("linkedin_apply");
        
        if($this->conf("posting_tweet")) {
            add_filter("wpjb_job_published", array("Wpjb_Service_Twitter", "tweet"));
        }
        if($this->conf("facebook_share")) {
            add_filter("wpjb_job_published", array("Wpjb_Service_Facebook", "share"));
        }
        if(isset($linkedin_share[0]) && $linkedin_share[0]==1) {
            add_filter("wpjb_job_published", array("Wpjb_Service_Linkedin", "share"));
        }
        if(isset($linkedin_apply[0]) && $linkedin_apply[0]==1) {
            add_action("wpjb_tpl_single_actions", array("Wpjb_Service_Linkedin", "apply"), 5);
            add_filter("wp", array("Wpjb_Service_Linkedin", "dispatch"), 20);
            add_action("wpja_minor_section_apply", array("Wpjb_Service_Linkedin", "sectionApply"));
        }
        
        add_action("wpjb_job_published", "wpjb_mobile_notification_jobs");
        
        $backfill = $this->conf("indeed_backfill", array());
        if(in_array("enabled-list", $backfill) || in_array("enabled-search", $backfill)) {
            Wpjb_Service_Indeed::connect();
        }

    }
    
    public function theTitleDisable($x = null)
    {
        $this->_doTitle = false;
        return $x;
    }
    
    public function theTitleEnable($x = null)
    {
        $this->_doTitle = true;
        return $x;
    }
    
    public function doTitle()
    {
        return $this->_doTitle;
    }

    public function init()
    {   
        global $wp, $wp_rewrite;

        if(!$this->conf("front_hide_bookmarks") && current_user_can("manage_resumes")) {
            add_action("wpjb_tpl_single_actions", array("Wpjb_Model_Shortlist", "displaySingleJob"), 5);
        }

        if($this->conf("count_date") != date_i18n("Y-m-d")) {
            $this->scheduleEvent();
        }
        
        $r = Daq_Request::getInstance();
        if($r->get("page")=="wpjb-config" && $r->get("action")=="edit" && $r->get("form")=="facebook" && !session_id()) {
            session_start();
        }
        if($this->conf("facebook_share") && $r->get("page")=="wpjb-job" && $r->get("action")=="add"  && !session_id()) {
            session_start();
        }
        
        if(!is_user_logged_in()) {
            wpjb_transient_id();
        }
        
        load_plugin_textdomain("wpjobboard", false, "wpjobboard/languages");
        
        wp_register_script('wpjb-js', plugins_url().'/wpjobboard/public/js/frontend.js', array("jquery"), self::VERSION );
        wp_register_script('wpjb-alert', plugins_url().'/wpjobboard/public/js/frontend-alert.js', array("jquery"), self::VERSION, true);
        wp_register_style( 'wpjb-css', plugins_url()."/wpjobboard/public/css/frontend.css", array('wpjb-glyphs'), self::VERSION );
        wp_register_style( 'wpjb-glyphs', plugins_url()."/wpjobboard/public/css/wpjb-glyphs.css", array() );
        
        wp_register_script( "wpjb-suggest", plugins_url()."/wpjobboard/public/js/wpjb-suggest.js", array("jquery"), false, true );
        wp_register_script( "wpjb-color-picker", plugins_url()."/wpjobboard/application/views/jquery.colorPicker.js" );
        
        wp_register_script("wpjb-vendor-plupload", includes_url()."/js/plupload/plupload.full.min.js", array(), null, true);

        wp_register_script("wpjb-vendor-datepicker", plugins_url()."/wpjobboard/application/vendor/date-picker/js/datepicker.js", array("jquery"));
        wp_register_style("wpjb-vendor-datepicker", plugins_url()."/wpjobboard/application/vendor/date-picker/css/datepicker.css");

        wp_register_script("wpjb-admin", plugins_url()."/wpjobboard/application/views/admin.js", array("jquery"));
        wp_register_script("wpjb-admin-job", plugins_url()."/wpjobboard/public/js/admin-job.js");
        wp_register_script("wpjb-admin-resume", plugins_url()."/wpjobboard/public/js/admin-resume.js");
        wp_register_script("wpjb-admin-config-urls", plugins_url()."/wpjobboard/public/js/admin-config-urls.js");
        wp_register_script("wpjb-admin-export", plugins_url()."/wpjobboard/public/js/admin-export.js");
        wp_register_script("wpjb-plupload", plugins_url()."/wpjobboard/public/js/wpjb-plupload.js", array("wpjb-vendor-plupload"), null, true);
    
        wp_register_script("wpjb-vendor-selectlist", plugins_url()."/wpjobboard/application/vendor/select-list/jquery.selectlist.pack.js", array("jquery"), null, true);
        
        wp_register_style("wpjb-admin-css", plugins_url()."/wpjobboard/application/views/admin.css");
        
        wp_register_script("wpjb-vendor-ve", plugins_url()."/wpjobboard/application/vendor/visual-editor/visual-editor.js", array("jquery"));
        wp_register_style("wpjb-vendor-ve-css", plugins_url()."/wpjobboard/application/vendor/visual-editor/visual-editor.css");
        
        wp_register_script("wpjb-vendor-stripe", "https://js.stripe.com/v2/");
        wp_register_script("wpjb-stripe", plugins_url()."/wpjobboard/public/js/wpjb-stripe.js", array("jquery", "wpjb-vendor-stripe"));
        
        wp_register_script("wpjb-paypal-reply", plugins_url()."/wpjobboard/public/js/wpjb-paypal-reply.js");
    }
    
    public function cptInit()
    {
        $cpt = new Wpjb_Utility_Cpt;
        $cpt->init();

        add_action("wpjb_job_saved", array($cpt, "link"));
        add_action("wpjb_company_saved", array($cpt, "link"));
        add_action("wpjb_resume_saved", array($cpt, "link"));
    }
    
    public function cptDisableArchive()
    {
        $arr = array(
            "job" => wpjb_link_to("home"),
            "resume" => get_permalink( wpjb_conf("urls_link_resume") ),
            "company" => home_url()
        );
        
        foreach($arr as $key => $redirect) {
            if(is_post_type_archive( $key )) {
                wp_redirect( $redirect );
                exit;
            }
        }
    }
    
    public function cptPrintStyles()
    {
        global $post_type;
        
        switch($post_type) {
            case 'job': $from = "Wpjb_Model_Job"; break;
            case 'company': $from = "Wpjb_Model_Company"; break;
            case 'resume': $from = "Wpjb_Model_Resume"; break;
            default: return; break;
        }
        
        wp_enqueue_style( 'wpjb-admin-cpt-css', plugins_url()."/wpjobboard/public/css/admin-cpt.css");
    }
    
    public function cptPrintScripts()
    {
        global $post_type, $post;
        
        $cpt = array(
            "href" => "",
            "url" => "",
            "go_back" => __("Go back to basic options &raquo;", "wpjobboard")
        );
        
        switch($post_type) {
            case 'job': 
                $from = "Wpjb_Model_Job"; 
                $page = "job";
                break;
            case 'company': 
                $from = "Wpjb_Model_Company"; 
                $page = "employers";
                break;
            case 'resume': 
                $from = "Wpjb_Model_Resume"; 
                $page = "resumes";
                break;
            default: 
                return;
                break;
        }
        
        $query = new Daq_Db_Query;
        $query->from("$from t");
        $query->where("post_id = ?", $post->ID);
        $query->limit(1);
        
        $result = $query->execute();
        
        if(!isset($result[0])) {
            return;
        }
        
        $object = $result[0];
        
        wp_enqueue_script( 'wpjb-admin-cpt', plugins_url()."/wpjobboard/public/js/admin-cpt.js", array("jquery"));
        
        $cpt["href"] = "wpjb-".$page;
        $cpt["url"] = wpjb_admin_url($page, "edit", $object->id);
        
        echo '<script type="text/javascript">'.PHP_EOL;
        echo 'var WPJB_CPT = '.json_encode($cpt).';'.PHP_EOL;
        echo '</script>'.PHP_EOL;
        
    }
    
    public static function scheduleEvent()
    {
        $select = new Daq_Db_Query();
        $select = $select->select("t2.tag_id AS `id`, COUNT(*) AS `cnt`");
        $select->from("Wpjb_Model_Job t1");
        $select->join("t1.tagged t2", "object = 'job'");
        $select->where("t1.is_active = 1");
        $select->where("t1.job_expires_at >= ?", date("Y-m-d"));
        $select->group("t2.tag_id");

        $all = array();
        
        foreach($select->fetchAll() as $r) {
            $all[$r->id] = $r->cnt;
        }

        $conf = self::getInstance();
        $conf->setConfigParam("count", $all);
        $conf->setConfigParam("count_date", date_i18n("Y-m-d"));
        $conf->saveConfig();
    }

    public function deletedUser($id)
    {
        foreach(array("Wpjb_Model_Company", "Wpjb_Model_Resume") as $class) {
            $query = new Daq_Db_Query();
            $result = $query->select()
                ->from("Wpjb_Model_Company t")
                ->where("user_id = ?", $id)
                ->limit(1)
                ->execute();

            if(isset($result[0])) {
                $object = $result[0];
                $object->delete();
            }
        }

    }

    public function addAdminMenu()
    {
        $ini = Daq_Config::parseIni(
            $this->path("app_config")."/admin-menu.ini",
            $this->path("user_config")."/admin-menu.ini",
            true
        );

        $ini = apply_filters("wpjb_admin_menu", $ini);
        
        $jLogo = plugins_url()."/wpjobboard/public/images/admin-icons/job_board_16x16_color.png";
        $cLogo = plugins_url()."/wpjobboard/public/images/admin-icons/settings_16x16px_color.png";
        
        $list = new Daq_Db_Query();
        $list->select("COUNT(*) as `cnt`");
        $list->from("Wpjb_Model_Application t");
        $list->where("status = 1");
        $applications = $list->fetchColumn();
        if(isset($ini["applications"]["page_title"])) {
            $warning = __("new applications", "wpjobboard");
            $ini["applications"]["menu_title"]  = $ini["applications"]["page_title"];
            $ini["applications"]["menu_title"] .= " <span class='update-plugins wpjb-bubble-applications count-$applications' title='$warning'><span class='update-count'>".$applications."</span></span>";
        }
        
        $pending = wpjb_find_jobs(array("filter"=>"awaiting", "count_only"=>true));
        if(isset($ini["jobs"]["page_title"])) {
            $warning = __("jobs awaiting approval", "wpjobboard");
            $ini["jobs"]["menu_title"]  = $ini["jobs"]["page_title"];
            $ini["jobs"]["menu_title"] .= " <span class='update-plugins wpjb-bubble-jobs count-$pending' title='$warning'><span class='update-count'>".$pending."</span></span>";
        }
        
        
        $query = new Daq_Db_Query();
        $query->select();
        $query->from("Wpjb_Model_Company t")->join("t.user u")->select("COUNT(*) AS cnt")->limit(1);
        $pending = $query->where("t.is_verified=?", Wpjb_Model_Company::ACCESS_PENDING)->fetchColumn();
        if(isset($ini["companies"]["page_title"])) {
            $warning = __("employers requesting approval", "wpjobboard");
            $ini["companies"]["menu_title"]  = $ini["companies"]["page_title"];
            $ini["companies"]["menu_title"] .= " <span class='update-plugins wpjb-bubble-companies count-$pending' title='$warning'><span class='update-count'>".$pending."</span></span>";
        }
         
         
        /*
        $query = new Daq_Db_Query();
        $query->select()->from("Wpjb_Model_Resume t")->join("t.users t2")->order("t.updated_at DESC");
        $query->select("COUNT(*) AS cnt")->limit(1);
        $pending = $query->where("t.is_approved=?", Wpjb_Model_Resume::RESUME_PENDING)->fetchColumn();
        if(isset($ini["resumes_manage"]["page_title"])) {
            $warning = __("resumes pending approval", "wpjobboard");
            $ini["resumes_manage"]["menu_title"]  = $ini["resumes_manage"]["page_title"];
            $ini["resumes_manage"]["menu_title"] .= "<span class='update-plugins wpjb-bubble-resumes count-$pending' title='$warning'><span class='update-count'>".$pending."</span></span>";
        }
        */
        
        //$this->helpScreen = new Wpjb_Utility_HelpScreen;
        
        foreach($ini as $key => $conf) {
            
            if(isset($conf['parent'])) {
                
                if(isset($conf["menu_title"])) {
                    $menu_title = $conf["menu_title"];
                } else {
                    $menu_title = $conf["page_title"];
                }
                
                $id = add_submenu_page(
                    "wpjb-".ltrim($ini[$conf['parent']]['handle'], "/"),
                    $conf['page_title'],
                    $menu_title,
                    $conf['access'],
                    "wpjb-".ltrim($conf['handle'], "/"),
                    array($this, "dispatch")
                );

                //$this->helpScreen->addPage($key, $id);
                // for future use (maybe 4.1)
                // add_action("load-$id", array($this->helpScreen, "load_".$key));
                
            } else {
                
                if($key == "job_board") {
                    $logo = $jLogo;
                    $logo = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCIgWw0KCTwhRU5USVRZIG5zX2V4dGVuZCAiaHR0cDovL25zLmFkb2JlLmNvbS9FeHRlbnNpYmlsaXR5LzEuMC8iPg0KCTwhRU5USVRZIG5zX2FpICJodHRwOi8vbnMuYWRvYmUuY29tL0Fkb2JlSWxsdXN0cmF0b3IvMTAuMC8iPg0KCTwhRU5USVRZIG5zX2dyYXBocyAiaHR0cDovL25zLmFkb2JlLmNvbS9HcmFwaHMvMS4wLyI+DQoJPCFFTlRJVFkgbnNfdmFycyAiaHR0cDovL25zLmFkb2JlLmNvbS9WYXJpYWJsZXMvMS4wLyI+DQoJPCFFTlRJVFkgbnNfaW1yZXAgImh0dHA6Ly9ucy5hZG9iZS5jb20vSW1hZ2VSZXBsYWNlbWVudC8xLjAvIj4NCgk8IUVOVElUWSBuc19zZncgImh0dHA6Ly9ucy5hZG9iZS5jb20vU2F2ZUZvcldlYi8xLjAvIj4NCgk8IUVOVElUWSBuc19jdXN0b20gImh0dHA6Ly9ucy5hZG9iZS5jb20vR2VuZXJpY0N1c3RvbU5hbWVzcGFjZS8xLjAvIj4NCgk8IUVOVElUWSBuc19hZG9iZV94cGF0aCAiaHR0cDovL25zLmFkb2JlLmNvbS9YUGF0aC8xLjAvIj4NCl0+DQo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM6eD0iJm5zX2V4dGVuZDsiIHhtbG5zOmk9IiZuc19haTsiIHhtbG5zOmdyYXBoPSImbnNfZ3JhcGhzOyINCgkgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjU5MS44IDk3OC4zIDE2IDE4Ig0KCSBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDU5MS44IDk3OC4zIDE2IDE4IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxtZXRhZGF0YT4NCgk8c2Z3ICB4bWxucz0iJm5zX3NmdzsiPg0KCQk8c2xpY2VzPjwvc2xpY2VzPg0KCQk8c2xpY2VTb3VyY2VCb3VuZHMgIGhlaWdodD0iNDguMSIgd2lkdGg9IjU5LjgiIHk9Ijg5NS42IiB4PSI1NDkiIGJvdHRvbUxlZnRPcmlnaW49InRydWUiPjwvc2xpY2VTb3VyY2VCb3VuZHM+DQoJPC9zZnc+DQo8L21ldGFkYXRhPg0KPGcgaWQ9IkxpdmVsbG9fMyIgZGlzcGxheT0ibm9uZSI+DQo8L2c+DQo8ZyBpZD0iTGl2ZWxsb18yIj4NCgk8Zz4NCgkJPHBhdGggZmlsbD0iIzlBOTk5OSIgZD0iTTYwNi44LDk4Mi4zdi0xaC0xdjF2MmgtMTJ2LTJ2LTFoLTF2MWgtMXYxM2gxdjFoMTR2LTFoMXYtMTNINjA2Ljh6IE02MDUuOCw5ODYuM3YxaC0xMnYtMUg2MDUuOHoNCgkJCSBNNTkzLjgsOTkwLjN2LTFoMTJ2MUg1OTMuOHogTTYwNS44LDk5Mi4zdjFoLTEydi0xSDYwNS44eiIvPg0KCQk8cGF0aCBmaWxsPSIjOUE5OTk5IiBkPSJNNjAyLjgsOTgwLjN2LTFoLTJ2LTFoLTJ2MWgtMnYxaC0ydjNoMTB2LTNINjAyLjh6IE02MDAuOCw5ODIuM2gtMnYtMmgyVjk4Mi4zeiIvPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K";
                } else {
                    $logo = $cLogo;
                    $logo = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCIgWw0KCTwhRU5USVRZIG5zX2V4dGVuZCAiaHR0cDovL25zLmFkb2JlLmNvbS9FeHRlbnNpYmlsaXR5LzEuMC8iPg0KCTwhRU5USVRZIG5zX2FpICJodHRwOi8vbnMuYWRvYmUuY29tL0Fkb2JlSWxsdXN0cmF0b3IvMTAuMC8iPg0KCTwhRU5USVRZIG5zX2dyYXBocyAiaHR0cDovL25zLmFkb2JlLmNvbS9HcmFwaHMvMS4wLyI+DQoJPCFFTlRJVFkgbnNfdmFycyAiaHR0cDovL25zLmFkb2JlLmNvbS9WYXJpYWJsZXMvMS4wLyI+DQoJPCFFTlRJVFkgbnNfaW1yZXAgImh0dHA6Ly9ucy5hZG9iZS5jb20vSW1hZ2VSZXBsYWNlbWVudC8xLjAvIj4NCgk8IUVOVElUWSBuc19zZncgImh0dHA6Ly9ucy5hZG9iZS5jb20vU2F2ZUZvcldlYi8xLjAvIj4NCgk8IUVOVElUWSBuc19jdXN0b20gImh0dHA6Ly9ucy5hZG9iZS5jb20vR2VuZXJpY0N1c3RvbU5hbWVzcGFjZS8xLjAvIj4NCgk8IUVOVElUWSBuc19hZG9iZV94cGF0aCAiaHR0cDovL25zLmFkb2JlLmNvbS9YUGF0aC8xLjAvIj4NCl0+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxpdmVsbG9fMSIgeG1sbnM6eD0iJm5zX2V4dGVuZDsiIHhtbG5zOmk9IiZuc19haTsiIHhtbG5zOmdyYXBoPSImbnNfZ3JhcGhzOyINCgkgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0MCA0MCINCgkgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNDAgNDAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPG1ldGFkYXRhPg0KCTxzZncgIHhtbG5zPSImbnNfc2Z3OyI+DQoJCTxzbGljZXM+PC9zbGljZXM+DQoJCTxzbGljZVNvdXJjZUJvdW5kcyAgaGVpZ2h0PSIyMDAiIHdpZHRoPSI0NzAuOCIgeT0iLTExNDkuMiIgeD0iNTI5LjkiIGJvdHRvbUxlZnRPcmlnaW49InRydWUiPjwvc2xpY2VTb3VyY2VCb3VuZHM+DQoJPC9zZnc+DQo8L21ldGFkYXRhPg0KPGc+DQoJPHBhdGggZmlsbD0iIzlBOTk5OSIgZD0iTTM4LjYsMzguNkwzOC42LDM4LjZjLTEuOSwxLjktNS4yLDEuNy02LjQsMC41Yy0xLjktMS45LTE4LTIwLjMtMTgtMjAuM2wwLDBjLTEuNiwwLjgtMy41LDEuMi01LjUsMQ0KCQljLTQuNC0wLjUtOC4xLTQtOC43LTguM0MtMC4xLDkuOCwwLDguMiwwLjUsNi44YzAuMS0wLjMsMC41LTAuNCwwLjgtMC4ybDYsNmMwLjMsMC4zLDAuOCwwLjMsMSwwbDQuMS00LjFjMC4zLTAuMywwLjMtMC44LDAtMQ0KCQlsLTYtNkM2LjIsMS4zLDYuMywwLjksNi42LDAuOGMxLjctMC42LDMuNi0wLjgsNS41LTAuM2MzLjgsMC44LDYuOCwzLjksNy41LDcuN2MwLjQsMi4yLDAsNC40LTAuOSw2LjJsMCwwYzAsMCwxOC40LDE2LDIwLjQsMTcuOQ0KCQlDNDAuMywzMy41LDQwLjQsMzYuOCwzOC42LDM4LjZ6IE0zNC44LDMzLjJjLTAuOSwwLTEuNiwwLjctMS42LDEuNnMwLjcsMS42LDEuNiwxLjZjMC45LDAsMS42LTAuNywxLjYtMS42UzM1LjYsMzMuMiwzNC44LDMzLjJ6Ig0KCQkvPg0KCTxnPg0KCQk8cGF0aCBmaWxsPSIjOUE5OTk5IiBkPSJNMjAuMywxNGMxLjMsMS4xLDQuMiwzLjYsNy4zLDYuNGwxMC4yLTkuOWMyLjYtMi42LDIuOS02LjYsMC41LTguOWMtMi4zLTIuMy02LjMtMi4xLTksMC41TDIxLDEwLjgNCgkJCUMyMC45LDExLjksMjAuNywxMywyMC4zLDE0eiIvPg0KCTwvZz4NCgk8Zz4NCgkJPHBhdGggZmlsbD0iIzlBOTk5OSIgZD0iTTE0LjIsMjAuN2MtMS40LDEuNi0yLjgsMy4zLTMuMywzLjhjLTEuNSwwLjctMy44LDAuMi01LjIsMS42QzQuMiwyNy42LTAuOSwzNiwwLjIsMzcuMXMxLjMsMS4yLDEuMywxLjINCgkJCWMwLDAsMC4xLDAuMSwxLjMsMS4yYzEuMSwxLjEsOS41LTMuOSwxMS01LjRjMS41LTEuNCwwLjktMy43LDEuNi01LjJjMC41LTAuNSwyLTEuNywzLjUtM0MxNi45LDIzLjgsMTUuMiwyMS45LDE0LjIsMjAuN3oiLz4NCgk8L2c+DQo8L2c+DQo8L3N2Zz4NCg==";
                }
                
                add_menu_page(
                    $conf['page_title'],
                    $conf['page_title'],
                    $conf['access'],
                    "wpjb-".ltrim($conf['handle'], "/"),
                    array($this, "dispatch"),
                    $logo,
                    $conf['order']
                );
            }
        }
    }

    public function adminEnqueueScripts($hook) 
    {
        if(!stripos($hook, "_wpjb-")) {
            return;
        }
        
        $js_date_max = wpjb_date(WPJB_MAX_DATE);
        $js_date_format = str_replace(array("J"), array("B"), wpjb_date_format());
        
        wp_enqueue_script("wpjb-admin");
        wp_enqueue_script("wpjb-vendor-selectlist");
        
        wp_enqueue_style("wpjb-admin-css");
        wp_localize_script("wpjb-plupload", "wpjb_plupload_lang", array(
            "dispose_message" => __("Click here to dispose this message", "wpjobboard"),
            "x_more_left" => __("%d more left", "wpjobboard"),
            "preview" => __("Preview", "wpjobboard"),
            "delete_file" => __("Delete", "wpjobboard")
        ));
        wp_localize_script("wpjb-admin", "wpjb_admin_lang", array(
            "date_format" => $js_date_format,
            "max_date" => $js_date_max,
            "confirm_item_delete" => __("Are you sure you want to delete this item?", "wpjobboard")
        ));
        wp_localize_script("wpjb-vendor-selectlist", "daq_selectlist_lang", array(
            "hint" => __("Select options ...", "wpjobboard")
        ));
        
        list($x, $page) = explode("_wpjb-", $hook);
        
        $request = Daq_Request::getInstance();
        $action = $request->get("action");

        if($page == "job" && in_array($action, array("add", "edit"))) {
            $lang = array(
                "date_format" => $js_date_format,
                "max_date" => $js_date_max,
                "free_listing" => __("None (free listing)", "wpjobboard"),
                "yesterday" => __("yesterday", "wpjobboard"),
                "immediately" => __("immediately", "wpjobboard"),
                "tomorrow" => __("tomorrow", "wpjobboard"),
                "day" => __("%d day", "wpjobboard"),
                "days" => __("%d days", "wpjobboard")
            );
            wp_enqueue_script("wpjb-admin-job");
            wp_enqueue_script("wpjb-suggest");
            wp_enqueue_script("wpjb-vendor-datepicker");
            
            wp_enqueue_style("wpjb-vendor-datepicker");
            
            wp_localize_script("wpjb-admin-job", "wpjb_admin_job_lang", $lang);

        } elseif($page == "custom" && $action == "edit") {
            
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('thickbox');
            
            wp_enqueue_script("wpjb-vendor-ve");
            wp_enqueue_style("wpjb-vendor-ve-css");
        } elseif($page == "jobType") {
            wp_enqueue_script("wpjb-color-picker", null, null, null, true);
        } elseif($page == "resumes") {
            $lang = array(
                "date_format" => $js_date_format,
                "max_date" => $js_date_max,
            );
            
            wp_enqueue_script("wpjb-admin-resume");
            wp_enqueue_script("wpjb-vendor-datepicker");
            wp_enqueue_style("wpjb-vendor-datepicker");
            
            wp_localize_script("wpjb-admin-resume", "wpjb_admin_resume_lang", $lang);
            
        } elseif($page == "import" && in_array($action, array("xml", "csv"))) {
            wp_enqueue_script("wpjb-vendor-plupload");
        } elseif($page == "memberships" || $page == "discount") {
            wp_enqueue_script("wpjb-vendor-datepicker");
            wp_enqueue_script("suggest");
            wp_enqueue_style("wpjb-vendor-datepicker");
        } elseif($page == "config" && $action == "edit" && $request->get("form")=="urls") {
            wp_enqueue_script("wpjb-admin-config-urls");
        } elseif($page == "application" && in_array($action, array("add", "edit"))) {
            wp_enqueue_script("suggest");
        }
    }
    
    public function addScripts()
    {
        $l10n = array(
            "slug_save" => __("save", "wpjobboard"),
            "slug_cancel" => __("cancel", "wpjobboard"),
            "slug_change" => __("change", "wpjobboard"),
            "remove" => __("Do you really want to delete", "wpjobboard"),
            "selectAction" => __("Select action first", "wpjobboard"),
            
        );
        
        wp_localize_script("wpjb-admin", "WpjbAdminLang", $l10n);

    }   
    
    public function adminPrintStyles() 
    {
        echo '<style type="text/css">#adminmenu .toplevel_page_wpjb-config .wp-menu-image.svg, #adminmenu .toplevel_page_wpjb-job .wp-menu-image.svg { background-size: 17px auto; }</style>'.PHP_EOL;
    }
    
    public function enqueueScripts()
    {
        if(!is_wpjb() && !is_wpjr()) {
            return;
        }

        
 
    }
    
    public function addScriptsFront()
    {
        $js_date_max_o = new DateTime(WPJB_MAX_DATE);
        $js_date_max = $js_date_max_o->format(wpjb_date_format());
        $js_date_format = str_replace(array("J"), array("B"), wpjb_date_format());
        $object = array(
            "AjaxRequest" => Wpjb_Project::getInstance()->getUrl()."/plain/discount",
            "Protection" => Wpjb_Project::getInstance()->conf("front_protection", "pr0t3ct1on"),
            "no_jobs_found" => __('No job listings found', 'wpjobboard'),
            "no_resumes_found" => __('No resumes found', 'wpjobboard'),
            "load_x_more" => __('Load %d more', 'wpjobboard'),
            "date_format" => $js_date_format,
            "max_date" => $js_date_max
        );
        
        wp_localize_script("wpjb-js", "WpjbData", $object);
        wp_enqueue_style('wpjb-css');
        
        wp_localize_script("wpjb-vendor-selectlist", "daq_selectlist_lang", array(
            "hint" => __("Select options ...", "wpjobboard")
        ));
        
        wp_localize_script("wpjb-stripe", "wpjb_stripe", array(
            "payment_accepted" => __("Payment completed successfully.", "wpjobboard")
        ));
        
        wp_localize_script("wpjb-plupload", "wpjb_plupload_lang", array(
            "dispose_message" => __("Click here to dispose this message", "wpjobboard"),
            "x_more_left" => __("%d more left", "wpjobboard"),
            "preview" => __("Preview", "wpjobboard"),
            "delete_file" => __("Delete", "wpjobboard")
        ));
        
        if((!is_wpjb() && !is_wpjr()) && !$this->shortcodeIs()) {
            return;
        }

        wp_enqueue_script('wpjb-js');
        wp_enqueue_script("wpjb-vendor-selectlist");

    }

    public function addDashboardWidgets()
    {
        if(!current_user_can("edit_dashboard")) {
            return;
        }

        wp_add_dashboard_widget('wpjb_dashboard_stats', __("Job Board Stats", "wpjobboard"), array("Wpjb_Dashboard_Stats", "render"));
    }

    public function install()
    {
        global $wpdb, $wp_rewrite, $wp_roles;
        
        if(stripos(PHP_OS, "win")!==false || true) {
            $mods = explode(",", $wpdb->get_var("SELECT @@session.sql_mode"));
            $mods = array_map("trim", $mods);
            $invalid = array(
                "STRICT_TRANS_TABLES", "STRICT_ALL_TABLES", "TRADITIONAL"
            );
            foreach($invalid as $m) {
                if(in_array($m, $mods)) {
                    $wpdb->query("SET @@session.sql_mode='' ");
                    break;
                }
            }
        }
        
        $db = Daq_Db::getInstance();
        if($db->getDb() === null) {
            $db->setDb($wpdb);
        }

        global $wp_roles;
        remove_role("employer");
        
        add_role("employer", "Employer", array("read"=>true, "manage_jobs"=>true));
        $wp_roles->add_cap("administrator", "manage_jobs");
        $wp_roles->add_cap("administrator", "manage_resumes");
        $wp_roles->add_cap("subscriber", "manage_resumes");
        
        wp_clear_scheduled_hook("wpjb_event_expiring_jobs");
        wp_schedule_event(current_time('timestamp'), "daily", "wpjb_event_expiring_jobs");
        
        wp_clear_scheduled_hook("wpjb_event_subscriptions_daily");
        wp_schedule_event(current_time('timestamp'), "hourly", "wpjb_event_subscriptions_daily");
        
        wp_clear_scheduled_hook("wpjb_event_subscriptions_weekly");
        wp_schedule_event(current_time('timestamp'), "hourly", "wpjb_event_subscriptions_weekly");

        $instance = self::getInstance();
        $appj = $instance->getApplication("frontend");
        $appr = $instance->getApplication("resumes");

        $config = $instance;
        
        $cpt = new Wpjb_Utility_Cpt;
        $cpt->init();
        
        /* @var $wp_rewrite wp_rewrite */
        $wp_rewrite->flush_rules();

        if($this->conf("first_run")!==null) {
            return true;
        }
        
        $config->setConfigParam("urls_mode", "2");
        $config->setConfigParam("urls_cpt", "1");

        $config->setConfigParam("first_run", 0);
        $config->setConfigParam("front_show_related_jobs", 1);
        $config->setConfigParam("show_maps", 1);
        $config->setConfigParam("cv_enabled", 1);
        $config->saveConfig();

        $file = $this->path("install") . "/install.sql";
        $queries = explode("; --", file_get_contents($file));

        foreach($queries as $query) {
            $query = str_replace('{$wpdb->prefix}', $wpdb->prefix, $query);
            $query = str_replace('{$wpjb->prefix}', $wpdb->prefix, $query);
            $wpdb->query($query);
        }

        $email = get_option("admin_email");
        $query =  new Daq_Db_Query();
        $result = $query->select("*")->from("Wpjb_Model_Email t")->execute();
        foreach($result as $r) {
            if($r->mail_from == "") {
                $r->mail_from = $email;
                $r->save();
            }
        }

        $config = Wpjb_Project::getInstance();
        $config->saveConfig();

        Wpjb_Upgrade_Manager::update();
        
        $manager = new Wpjb_Upgrade_Manager;
        $manager->version = self::VERSION;
        $manager->remote("version");

        $ptmp = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => '',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => ''
        );
        $pages = array(
            "urls_link_job" => array(
                "id" => null,
                "post_parent" => null,
                "post_title" => "Jobs",
                "post_content" => "[wpjb_jobs_list]",
            ),
            "urls_link_job_add" => array(
                "id" => null,
                "post_parent" => "urls_link_job",
                "post_title" => "Post a Job",
                "post_content" => "[wpjb_jobs_add]",
            ),
            "urls_link_job_search" => array(
                "id" => null,
                "post_parent" => "urls_link_job",
                "post_title" => "Advanced Search",
                "post_content" => "[wpjb_jobs_search]",
            ),
            "urls_link_resume" => array(
                "id" => null,
                "post_parent" => null,
                "post_title" => "Resumes",
                "post_content" => "[wpjb_resumes_list]",
            ),
            "urls_link_resume_search" => array(
                "id" => null,
                "post_parent" => "urls_link_resume",
                "post_title" => "Advanced Search",
                "post_content" => "[wpjb_resumes_search]",
            ),
            "urls_link_emp_panel" => array(
                "id" => null,
                "post_parent" => null,
                "post_title" => "Employer Panel",
                "post_content" => "[wpjb_employer_panel]",
            ),
            "urls_link_emp_reg" => array(
                "id" => null,
                "post_parent" => "urls_link_emp_panel",
                "post_title" => "Employer Registration",
                "post_content" => "[wpjb_employer_register]",
            ),
            "urls_link_cand_panel" => array(
                "id" => null,
                "post_parent" => null,
                "post_title" => "Candidate Panel",
                "post_content" => "[wpjb_candidate_panel]",
            ),
            "urls_link_cand_reg" => array(
                "id" => null,
                "post_parent" => "urls_link_cand_panel",
                "post_title" => "Candidate Registration",
                "post_content" => "[wpjb_candidate_register]",
            ),
            
        );
        
        foreach($pages as $key => $pdata) {
            
            $parr = $ptmp;
            if($pdata['post_parent']) {
                $parr['post_parent'] = $pages[$pdata['post_parent']]['id'];
            }
            $parr['post_title'] = $pdata['post_title'];
            $parr['post_content'] = $pdata['post_content'];

            $id = wp_insert_post($parr);

            $pages[$key]['id'] = $id;
            
            $config->setConfigParam($key, (int)$id);
            $config->saveConfig();
            
        }
        
        return true;
    }

    public static function uninstall()
    {
        return true;
    }

    public function deactivate()
    {
        
    }


    public function adminBarMenu()
    {
        global $wp_admin_bar, $post_type;
        
        if ((!is_super_admin() && !current_user_can("edit_pages")) || !is_admin_bar_showing()) {
            return;
        }

        if((is_wpjb() || is_wpjr()) && $this->env("mode")==1) {
            $wp_admin_bar->remove_menu("edit");
            $wp_admin_bar->remove_menu("comments");
        }
        
        $object = null;
        $router = $this->router();
        if($post_type == "job") {
            $query = new Daq_Db_Query;
            $query->from("Wpjb_Model_Job t");
            $query->where("post_id = ?", get_the_ID());
            $query->limit(1);
            $result = $query->execute();
            $object = !empty($result)  ? $result[0] : null;
        } elseif(is_wpjb() && $router->isResolved() && $router->isRoutedTo("index.single") && is_object($this->getApplication("frontend")->controller)) {
            $object = $this->getApplication("frontend")->controller->getObject();
        }
        
        if($object) {
            $wp_admin_bar->remove_menu("edit");
            $wp_admin_bar->add_menu(array(
                'id' => 'edit-job',
                'title' => '<span class="ab-icon wpjb-glyphs wpjb-icon-briefcase"></span><span class="ab-label">'.__("Edit Job", "wpjobboard").'</span>',
                'href' => wpjb_admin_url("job", "edit", $object->id)
            ));
        }
        
        $object = null;
        $router = $this->router("resumes");
        if($post_type == "resume") {
            $query = new Daq_Db_Query;
            $query->from("Wpjb_Model_Resume t");
            $query->where("post_id = ?", get_the_ID());
            $query->limit(1);
            $result = $query->execute();
            $object = !empty($result)  ? $result[0] : null;
        } elseif(is_wpjr() && $router->isResolved() && $router->isRoutedTo("index.view") && is_object($this->getApplication("resumes")->controller)) {
            $object = $this->getApplication("resumes")->controller->getObject();
        }
        
        if($object) {
            $wp_admin_bar->remove_menu("edit");
            $wp_admin_bar->add_menu(array(
                'id' => 'edit-resume',
                'title' => '<span class="ab-icon wpjb-glyphs wpjb-icon-user"></span><span class="ab-label">'.__("Edit Resume", "wpjobboard").'</span>',
                'href' => wpjb_admin_url("resumes", "edit", $object->id)
            ));
        }
        
        $object = null;
        $router = $this->router();
        if($post_type == "company") {
            $query = new Daq_Db_Query;
            $query->from("Wpjb_Model_Company t");
            $query->where("post_id = ?", get_the_ID());
            $query->limit(1);
            $result = $query->execute();
            $object = !empty($result)  ? $result[0] : null;
        } elseif(is_wpjb() && $router->isResolved() && $router->isRoutedTo("index.company") && is_object($this->getApplication("frontend")->controller)) {
            $object = $this->getApplication("frontend")->controller->getObject();
        }
        
        if($object) {
            $wp_admin_bar->remove_menu("edit");
            $wp_admin_bar->add_menu(array(
                'id' => 'edit-employer',
                'title' => '<span class="ab-icon wpjb-glyphs wpjb-icon-building"></span><span class="ab-label">'.__("Edit Employer", "wpjobboard").'</span>',
                'href' => wpjb_admin_url("employers", "edit", $object->id)
            ));
        }

    }
    
    public function editPost($post_id)
    {
        global $wp_rewrite;
        
        foreach($this->_apps() as $app) {
            /* @var $app Daq_Application */
            $id = $this->conf($app->getOption("link_name"));
            if($id == $post_id) {
                $wp_rewrite->flush_rules();
            }
        }
    }
    
    public function recaptcha($form)
    {
        $form->addGroup("recaptcha", __("Captcha", "wpjobboard"), 1000);
        
        $e = $form->create("recaptcha_response_field");
        $e->setRequired(true);
        $e->addValidator(new Daq_Validate_Callback("wpjb_recaptcha_check"));
        $e->setRenderer("wpjb_recaptcha_form");
        $e->setLabel(__("Captcha", "wpjobboard"));
        
        $form->addElement($e, "recaptcha");
        
        if($form instanceof Wpjb_Form_Apply) {
            $form->removeElement("protection");
        }
        
        return $form;
    }

    public function generateRewriteRules() 
    {
        global $wp_rewrite;
        
        $non_wp_rules = array(
            '([_0-9a-zA-Z-]+/)?uploads/wpjobboard/application/(.+)' => 'wp-content/plugins/wpjobboard/restrict.php?url=application/$2'
        );
        $wp_rewrite->non_wp_rules = $non_wp_rules + $wp_rewrite->non_wp_rules;

    }
    
    public function shortcodeStart($name)
    {
        $sh = $this->env("doing_shortcode", array());
        $sh[] = $name;
        $this->setEnv("doing_shortcode", $sh);
    }
    
    public function shortcodeEnd($name)
    {
        $sh = $this->env("doing_shortcode", array());
        $c = count($sh)-1;
        
        if($sh[$c] !== $name) {
            throw new Exception(sprintf("Incorrect name. You can end only the last shortcode [%s]", $sh[$c]));
        } else {
            array_pop($sh);
        }
        
        $this->setEnv("doing_shortcode", $sh);
    }
    
    public function shortcodeIs($name = null, $current = true) {
        
        $sh = $this->env("doing_shortcode", array());
        $c = count($sh)-1;
        
        if(is_null($name)) {
            return !empty($sh);
        } elseif($current) {
            return isset($sh[$c]) && $sh[$c] == $name;
        } elseif(!$current) {
            return in_array($name, $sh);
        } else {
            return false;
        }
    }
    
    public function templateRedirect($template)
    {
        $wpjb = get_query_var("wpjobboard");

        if($wpjb) {
            $this->getApplication("api")->dispatch($wpjb);
            exit;
        }
        
        $rewrite = new Wpjb_Utility_Rewrite;
        $route = $rewrite->resolve();

        $logoutArr = array("employer_logout", "logout");
        
        if(in_array($route["route"], $logoutArr)) {
            
            if(current_user_can("manage_jobs")) {
                $this->shortcodeStart("wpjb_employer_panel");
                $redirect = wpjb_link_to("employer_login");
                $this->shortcodeEnd("wpjb_employer_panel");
            } else {
                $this->shortcodeStart("wpjb_candidate_panel");
                $redirect = wpjr_link_to("login");
                $this->shortcodeEnd("wpjb_candidate_panel");
            }
            
            $logout = array(
                "redirect_to" => $redirect,
                "message" => __("You have been logged out.", "wpjobboard")
            );
            
            $logout = apply_filters("wpjb_logout", $logout, "employer");
            
            wp_logout();
            
            if($logout["message"]) {
                $flash = new Wpjb_Utility_Session;
                $flash->addInfo($logout["message"]);
                $flash->save();
            }
            
            wp_safe_redirect($logout["redirect_to"]);
            exit;
        }
        
        if($route["route"] == "step_reset") {
            
            $instance = Wpjb_Project::getInstance();
            $instance->shortcodeStart("wpjb_jobs_add");
            
            $id = "wpjb_session_".str_replace("-", "_", wpjb_transient_id());
            $transient = get_transient($id);

            if($transient !== false) {
                $transient["job"] = null;
                $transient["job_id"] = null;
                set_transient($id, $transient, 3600);
            }

            $flash = new Wpjb_Utility_Session();
            $flash->addInfo(__("Form has been reset.", "wpjobboard"));
            $flash->save();
            
            $link = wpjb_link_to("step_add");
            $instance->shortcodeEnd("wpjb_jobs_add");
            
            wp_redirect($link);
            exit;
        }

        return $template;
    }
    
    public function actions()
    {

        if(!isset($_REQUEST["_wpjb_action"]) || !is_string($_REQUEST["_wpjb_action"])) {
            return;
        }
        
        switch($_POST["_wpjb_action"]) {
            case "apply":
                $form = new Wpjb_Form_Apply();
                $request = Daq_Request::getInstance();
                $flash = new Wpjb_Utility_Session();
                
                $job = new Wpjb_Model_Job($request->post("_job_id"));
                
                if(!$job->exists() || !in_array(Wpjb_Model_Job::STATUS_ACTIVE, $job->status())) {
                    $flash->addError(__("Cannot apply, the job does not exist or is inactive.", "wpjobboard"));
                    $flash->save();
                    wp_redirect(wpjb_link_to("job", $job));
                    exit;
                }
                
                $ctrl = new Wpjb_Module_Frontend_Index();
                $ctrl->setView(Wpjb_Project::getInstance()->getApplication("frontend")->getView());
                
                $valid = $form->isValid($request->getAll());
                $can_apply = apply_filters("wpjb_user_can_apply", true, $job, $ctrl);
                
                if(!$valid || !$can_apply) {
                    break;
                }
                
                $user = null;
                if($job->user_id) {
                    $user = new WP_User($job->user_id);
                }

                $form->setJobId($job->getId());
                $form->setUserId(get_current_user_id());
                $form->save();
                
                $var = $form->getValues();
                
                $application = new Wpjb_Model_Application($form->getObject()->id);
                $job->applications++;
                
                // notify employer
                $files = array();
                foreach($application->getFiles() as $f) {
                    $files[] = $f->dir;
                }
                
                // notify admin
                $mail = Wpjb_Utility_Message::load("notify_admin_new_application");
                $mail->assign("job", $job);
                $mail->assign("application", $application);
                $mail->assign("resume", Wpjb_Model_Resume::current());
                $mail->addFiles($files);
                $mail->setTo(get_option("admin_email"));
                $mail->send();
                
                // notify employer
                $public_ids = array();
                foreach(wpjb_get_application_status() as $application_status) {
                    if($application_status["public"] == 1) {
                        $public_ids[] = $application_status["id"];
                    }
                }
                $notify = null;
                if($job->company_email) {
                    $notify = $job->company_email;
                } elseif($user && $user->user_email) {
                    $notify = $user->user_email;
                }
                if($notify == get_option("admin_email") || !in_array($application->status, $public_ids)) {
                    $notify = null;
                }
                $mail = Wpjb_Utility_Message::load("notify_employer_new_application");
                $mail->assign("job", $job);
                $mail->assign("application", $application);
                $mail->assign("resume", Wpjb_Model_Resume::current());
                $mail->addFiles($files);
                $mail->setTo($notify);
                if($notify !== null) {
                    $mail->send();
                }
                
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

                $flash->addInfo(__("Your application has been sent.", "wpjobboard"));
                $flash->save();
                
                wp_redirect(wpjb_link_to("job", $job)."#wpjb-sent");
                exit;
                
                break;
            case "preview":
                break;
            case "reset":
                
                break;
            case "login":
                
                $form = new Wpjb_Form_Login();
                $user = $form->isValid(Daq_Request::getInstance()->post());
                $flash = new Wpjb_Utility_Session();
                
                if($user instanceof WP_Error) {
                    foreach($user->get_error_messages() as $error) {
                        $flash->addError($error);
                    }
                } elseif($user === false) {
                    $flash->addError(__("Incorrect username or password", "wpjobboard"));
                } else {
                    $flash->addInfo(__("You have been logged in.", "wpjobboard"));

                    $r = trim($form->value("redirect_to"));
                    if(!empty($r)) {
                        $redirect = $r;
                    } else if($user->has_cap("manage_jobs")) {
                        $redirect = wpjb_link_to("employer_home");
                    } else if($user->has_cap("manage_resumes")) {
                        $redirect = wpjr_link_to("myresume_home");
                    } else {
                        $redirect = home_url();
                    }

                    // @todo: apply some filters maybe??
                    
                    $flash->save();
                    
                    wp_redirect($redirect);
                    exit;
                }
                break;
            case "delete_job":
                $request = Daq_Request::getInstance();
                $flash = new Wpjb_Utility_Session();
                $form = new Wpjb_Form_Frontend_DeleteJob($request->post("job_id"));
                $job = $form->getObject();
                
                if($job->employer_id != Wpjb_Model_Company::current()->id) {
                    $flash->addError(__("You do not own this job.", "wpjobboard"));
                    $flash->save();
                    break;
                }
                
                if($form->isValid($request->post())) {
                    $flash->addInfo(__("Job has been deleted.", "wpjobboard"));
                    $flash->save();
                    $job->delete();
                    wp_redirect($form->value("redirect_to"));
                    exit;
                } else {
                    $flash->addError(__("There are errors in your form", "wpjobboard"));
                }


                break;
            case "add_education":
            case "add_experience":
                $request = Daq_Request::getInstance();
                $flash = new Wpjb_Utility_Session();
                
                if(is_admin()) {
                    return;
                }
                if($request->post("resume_id") != Wpjb_Model_Resume::current()->id) {
                    return;
                }

                if($request->post("_wpjb_action") == "add_experience") {
                    $form = "Wpjb_Form_Resumes_Experience";
                    $info = __("New work experience has been added.", "wpjobboard");
                    $error = __("There are errors in your form", "wpjobboard");
                } else {
                    $form = "Wpjb_Form_Resumes_Education";
                    $info = __("New education has been added.", "wpjobboard");
                    $error = __("There are errors in your form", "wpjobboard");
                }
                
                $form = new $form();
                $isValid = $form->isValid($request->getAll());
                
                if($isValid) {
                    $flash->addInfo($info);
                    $form->save();

                    $resume = Wpjb_Model_Resume::current();
                    $resume->modified_at = date("Y-m-d H:i:s");
                    $resume->save();

                    if($this->conf("urls_mode", array(1)) == 2) {
                        $this->shortcodeStart("wpjb_employer_panel");
                        $redirect = rtrim(get_permalink($request->post("_page_id")), "/").wpjr_link_to("myresume_detail_edit", $form->getObject());
                        $this->shortcodeEnd("wpjb_employer_panel");
                    } else {
                        $redirect = wpjr_link_to("myresume_detail_edit", $form->getObject());
                    }
                    
                    $flash->save();
                    wp_redirect($redirect);
                    exit;
                    
                } else {
                    $flash->addError($error);
                }
                
                break;
            case "reg_candidate":
                
                $form = new Wpjb_Form_Resumes_Register();
                $request = Daq_Request::getInstance();
                $flash = new Wpjb_Utility_Session();

                $isValid = $form->isValid($request->getAll());
                
                if(!$isValid) {
                    return;
                }

                $form->save();
               
                $url = wpjr_link_to("login");
                $username = $form->value("user_login");
                $password = $form->value("user_password");
                $email = $form->value("user_email");
                
                $mail = Wpjb_Utility_Message::load("notify_canditate_register");
                $mail->setTo($email);
                $mail->assign("username", $username);
                $mail->assign("password", $password);
                $mail->assign("login_url", $url);
                $mail->send();

                do_action("wpjb_user_registered", "candidate");

                $form = new Wpjb_Form_Resumes_Login();
                if($form->hasElement("recaptcha_response_field")) {
                    $form->removeElement("recaptcha_response_field");
                }
                
                $form->isValid(array(
                    "user_login" => $username,
                    "user_password" => $password,
                    "remember" => 0
                ));
                
                $flash->addInfo(__("You have been registered.", "wpjobboard"));
                $flash->save();
                
                wp_redirect(wpjr_link_to("myresume_home"));
                exit;
                
                break;
            case "reg_employer":
                $form = new Wpjb_Form_Frontend_Register();
                $request = Daq_Request::getInstance();
                $flash = new Wpjb_Utility_Session();
                
                $isValid = $form->isValid($request->getAll());
                if(!$isValid) {
                    return;
                }

                 $form->save();

                 $username = $form->value("user_login");
                 $password = $form->value("user_password");
                 $email = $form->value("user_email");

                 $mail = Wpjb_Utility_Message::load("notify_employer_register");
                 $mail->setTo($email);
                 $mail->assign("username", $username);
                 $mail->assign("password", $password);
                 $mail->assign("login_url", wpjb_link_to("employer_login"));
                 $mail->send();

                 do_action("wpjb_user_registered", "employer");
                 
                 $form = new Wpjb_Form_Login;
                 if($form->hasElement("recaptcha_response_field")) {
                     $form->removeElement("recaptcha_response_field");
                 }
                 $form->isValid(array(
                     "user_login" => $username,
                     "user_password" => $password,
                     "remember" => false
                 ));
                 
                 $flash->addInfo(__("You have been registered successfully", "wpjobboard"));
                 $flash->save();

                 wp_redirect(wpjb_link_to("employer_home"));
                 exit;
         
                break;
            default:
                // do nothing
        }
    }
    
    public function theContentCpt($content)
    {
        $app = null;
        $result = null;
        
        if (is_singular('job') && in_the_loop()) {
            $app = "frontend";
            $result = array(
                "param" => array("post_id"=>  get_the_ID()),
                "route" => "job",
                "module" => "index",
                "action" => "single",
                "path" => null,
                "object" => null
            );

            $object = new stdClass();
            $object->objClass = "Wpjb_Model_Job";
            $result["object"] = $object;
            
        } elseif(is_singular('company') && in_the_loop()) {
            $app = "frontend";
            $result = array(
                "param" => array("post_id"=>  get_the_ID()),
                "route" => "company",
                "module" => "index",
                "action" => "company",
                "path" => null,
                "object" => null
            );

            $object = new stdClass();
            $object->objClass = "Wpjb_Model_Company";
            $result["object"] = $object;
        } elseif(is_singular('resume') && in_the_loop()) {
            $app = "resumes";
            $result = array(
                "param" => array("post_id"=>  get_the_ID()),
                "route" => "resume",
                "module" => "index",
                "action" => "view",
                "path" => null,
                "object" => null
            );

            $object = new stdClass();
            $object->objClass = "Wpjb_Model_Resume";
            $result["object"] = $object;
        }
        
        if(!$result) {
            return $content;
        }
        
        try {
            $instance = Wpjb_Project::getInstance();
            $instance->shortcodeStart(__FUNCTION__);
            
            $instance->getApplication($app)->dispatch(null, $result);
            $instance->addScriptsFront();
            $instance->shortcodeEnd(__FUNCTION__);
            
            return $instance->getApplication($app)->content;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }
    
    public function ttFix()
    {   
        $theme = wp_get_theme();
        if($theme->get_template() == "twentytwelve") {
            echo '<style type="text/css">';
            echo '.wpjb-form select { padding: 0.428571rem }'.PHP_EOL;
            echo '#wpjb-main img { border-radius: 0px; box-shadow: 0 0px 0px rgba(0, 0, 0, 0) }'.PHP_EOL;
            echo 'table.wpjb-table { font-size: 13px }'.PHP_EOL;
            echo '.entry-content .wpjb a:visited { color: #21759b }'.PHP_EOL;
            echo 'footer.entry-meta { display: none }'.PHP_EOL;
            echo '.nav-single { display: none }'.PHP_EOL;
            echo '.wpjb-col-title a { text-decoration: none; color: #21759b !important; }'.PHP_EOL;
            echo '.wpjb-widget .wpjb-custom-menu-link a { text-decoration: none; }'.PHP_EOL;
            echo '</style>';
        }
    }
}





?>
