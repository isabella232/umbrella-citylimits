<?php

/**
 * Company job applications
 * 
 * Template displays job applications
 * 
 * 
 * @author Greg Winiarski
 * @package Templates
 * @subpackage JobBoard
 * 
 */

 /* @var $applicantList array List of applications to display */
 /* @var $job string Wpjb_Model_Job */

?>

<div class="wpjb wpjb-page-job-applications">

    <?php wpjb_flash(); ?>
    <?php wpjb_breadcrumbs($breadcrumbs) ?>

    <div class="wpjb-grid wpjb-grid-compact">
        <?php if (!empty($applicantList)): ?>
        <div class="wpjb-grid-row wpjb-grid-head">
            <div class="wpjb-col-35"><?php _e("Applicant name", "wpjobboard") ?></div>
            <div class="wpjb-col-30"><?php _e("E-mail", "wpjobboard") ?></div>
            <div class="wpjb-col-20"><?php _e("Freshness", "wpjobboard") ?></div>
            <div class="wpjb-col-10"><?php _e("Status", "wpjobboard") ?></div>
            <div class="wpjb-col-5">&nbsp;</div>
        </div>
        <?php foreach($applicantList as $application): ?>
        <div class="wpjb-grid-row">
            <div class="wpjb-col-35">
                <a href="<?php echo wpjb_link_to("job_application", $application) ?>">
                    <?php if($application->applicant_name): ?>
                    <?php esc_html_e($application->applicant_name) ?>
                    <?php else: ?>
                    <?php _e("ID"); echo ": "; echo $application->id; ?>
                    <?php endif; ?>
                </a>
            </div>
            <div class="wpjb-col-30">
                <a class="wpjb-mail" href="mailto:<?php esc_attr_e($application->email) ?>"><?php esc_html_e($application->email) ?></a>
            </div>
            <div class="wpjb-col-20">
                <?php echo wpjb_time_ago($application->applied_at) ?>
            </div>
            <div class="wpjb-col-10">
                <?php echo wpjb_application_status($application->status) ?>
            </div>
            <div class="wpjb-col-5 wpjb-grid-col-right">
                <div class="wpjb-dropdown-wrap">
                    <img id="wpjb-dropdown-<?php echo $application->id ?>-img" src="<?php echo wpjb_img("cog.png") ?>" alt="" />
                    <ul id="wpjb-dropdown-<?php echo $application->id ?>" class="wpjb-dropdown">
                        <?php foreach($public_ids as $k): ?>
                        <li><a href="<?php echo wpjb_api_url("action/application", array("id"=>$application->id, "status"=>$k, "redirect_to"=>wpjb_link_to("job_applications", $job))) ?>"><?php esc_html_e(wpjb_application_status($k)) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="wpjb-grid-row">
            <div class="wpjb-col-100 wpjb-grid-col-center">
                <?php _e("No applicants found.", "wpjobboard"); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    



</div>

<script type="text/javascript">
    jQuery(function(){    
        jQuery(".wpjb-dropdown-wrap").wpjb_menu({
            position: "right"
        });
    });
</script>