<?php

/**
 * Company job stats
 * 
 * Template displays company jobs stats
 * 
 * 
 * @author Greg Winiarski
 * @package Templates
 * @subpackage JobBoard
 * 
 */

 /* @var $jobList array List of jobs to display */
 /* @var $browse string One of: active; expired */
 /* @var $routerIndex string */
 /* @var $expiredCount int Total number of company expired jobs */
 /* @var $activeCount int Total number of company active jobs */

?>

<div class="wpjb wpjb-page-company-panel">

    <?php wpjb_flash(); ?>
    <?php wpjb_breadcrumbs($breadcrumbs) ?>
    
    
    <ul class="wpjb-tabs">
        <li class="wpjb-tab-link <?php if($browse == "active"):?>current<?php endif; ?>">
            <a href="<?php echo wpjb_link_to("employer_panel") ?>"><?php _e("Active listings", "wpjobboard"); ?></a> <?php echo " ($activeCount)" ?>
        </li>
        <li class="wpjb-tab-link <?php if($browse != "active"):?>current<?php endif; ?>">
            <a href="<?php echo wpjb_link_to("employer_panel_expired") ?>"><?php _e("Expired listings", "wpjobboard"); ?></a> <?php echo "($expiredCount)" ?>
        </li>
    </ul>
    
    <div class="wpjb-grid wpjb-grid-compact wpjb-tab-content">
        <?php if(!empty($result->job)): ?>
        <div class="wpjb-grid-row wpjb-grid-head">
            <div class="wpjb-col-25"><?php _e("Expires", "wpjobboard") ?></div>
            <div class="wpjb-col-50"><?php _e("Title", "wpjobboard") ?></div>
            <div class="wpjb-col-20"><?php _e("Applications", "wpjobboard") ?></div>
            <div class="wpjb-col-5">&nbsp;</div>
        </div>
        <?php foreach($result->job as $job): ?>
        <div class="wpjb-grid-row <?php wpjb_job_features($job); wpjb_panel_features($job) ?>">
            <div class="wpjb-col-25 wpjb-glyphs wpjb-icon-calendar">
                <?php if($job->job_expires_at === WPJB_MAX_DATE): ?>
                    <?php _e("Never", "wpjobboard") ?>
                <?php elseif($job->expired()): ?>
                    <?php _e("Expired", "wpjobboard") ?>
                <?php else: ?>
                    <?php esc_html_e(wpjb_date_display(get_option("date_format"), $job->job_expires_at)) ?>
                <?php endif; ?>
            </div>
            <div class="wpjb-col-50">
                <a href="<?php esc_attr_e(wpjb_link_to("job_edit", $job)); ?>"><?php esc_html_e($job->job_title) ?></a>
            </div>
            <div class="wpjb-col-20 wpjb-glyphs wpjb-icon-history">
                <a href="<?php echo wpjb_link_to("job_applications", $job) ?>">
                <?php $applications = Wpjb_Model_Application::search(array("job"=>$job->id, "count_only"=>true, "filter"=>"public")) ?>
                <?php if($applications) : ?>
                <?php printf( _n("1 application", "%d applications", $applications, "wpjobboard"), $applications ) ?>
                <?php else: ?>
                <?php _e("0 applications", "wpjobboard") ?>
                <?php endif; ?>
                </a>
            </div>
            <div class="wpjb-col-5 wpjb-grid-col-right">
                    <div class="company-panel-dropdown">
                        <img id="wpjb-dropdown-<?php echo $job->id ?>-img" src="<?php echo wpjb_img("cog.png") ?>" alt="" />
                        <ul id="wpjb-dropdown-<?php echo $job->id ?>" class="wpjb-dropdown">
                            <li><a href="<?php echo wpjb_link_to("job", $job) ?>"><?php _e("View job", "wpjobboard") ?></a></li>
                            <li><a href="<?php echo wpjb_link_to("job_edit", $job); ?>"><?php _e("Edit job", "wpjobboard") ?></a></li>
                            <li><a href="<?php echo wpjb_link_to("step_add", null, array("republish"=>$job->id)) ?>"><?php _e("Republish", "wpjobboard") ?></a></li>
                            <li><a href="<?php echo wpjb_link_to("job_delete", $job) ?>"><?php _e("Delete ...", "wpjobboard") ?></a></li>
                            <li><hr/></li>
                            <li><a href="<?php echo wpjb_link_to("job_applications", $job) ?>"><?php _e("Applicants", "wpjobboard") ?></a></li>
                            <li><hr/></li>
                            <?php if($job->is_filled): ?>
                            <li><a href="<?php echo wpjb_api_url("action/job", array("id"=>$job->id, "do"=>"unfill", "redirect_to"=>$url)) ?>"><?php _e("Mark as not filled", "wpjobboard") ?></a></li>
                            <?php else: ?>
                            <li><a href="<?php echo wpjb_api_url("action/job", array("id"=>$job->id, "do"=>"fill", "redirect_to"=>$url)) ?>"><?php _e("Mark as filled", "wpjobboard") ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="wpjb-grid-row">
            <div class="wpjb-col-100 wpjb-col-center">
                <?php _e("No job listings found.", "wpjobboard"); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="wpjb-paginate-links">
        <?php wpjb_paginate_links($url, $result->pages, $result->page) ?>
    </div>


</div>

<script type="text/javascript">

    // 
    jQuery(function(){   
        
        jQuery(".company-panel-dropdown").wpjb_menu({
            position: "right"
        });
    });

</script>
