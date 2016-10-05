<?php 

/**
 * Job list item
 * 
 * This template is responsible for displaying job list item on job list page
 * (template index.php) it is alos used in live search
 * 
 * @author Greg Winiarski
 * @package Templates
 * @subpackage JobBoard
 */

 /* @var $job Wpjb_Model_Job */

?>

    <div class="wpjb-grid-row wpjb-click-area <?php wpjb_job_features($job); ?>">
        <div class="wpjb-grid-col wpjb-col-logo">
            <?php if($job->doScheme("company_logo")): ?>
            <?php elseif($job->getLogoUrl()): ?>
            <div class="wpjb-img-36">
                <img src="<?php echo $job->getLogoUrl("36x36") ?>" alt="" class="wpjb-img-36" style="" />
            </div>
            <?php else: ?>
            <div class="wpjb-img-36 wpjb-icon-none">
                <span class="wpjb-glyphs wpjb-icon-building wpjb-icon-36"></span>
            </div>
            <?php endif; ?>
        </div>
    
        <div class="wpjb-grid-col wpjb-col-40 wpjb-col-title">
            <?php if($job->doScheme("job_title")): else: ?>
            <span class="wpjb-line-major">
                <a href="<?php echo wpjb_link_to("job", $job) ?>"><?php esc_html_e($job->job_title) ?></a>
            </span>
            <?php endif; ?>
            
            <?php if($job->doScheme("company_name")): else: ?>
            <span class="wpjb-sub wpjb-sub-small"><?php esc_html_e($job->company_name) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="wpjb-grid-col wpjb-col-35 wpjb-col-location wpjb-line-with-icon-left">
            <span class="wpjb-line-major">
                <span class="wpjb-glyphs wpjb-icon-location"><?php esc_html_e($job->locationToString()) ?></span>
            </span>
            
            <?php if(isset($job->getTag()->type[0])): ?>
            <span class="wpjb-sub wpjb-sub-small" style="color:#<?php echo $job->getTag()->type[0]->meta->color ?>">
                <?php esc_html_e($job->getTag()->type[0]->title) ?>
            </span>
            <?php endif; ?>
        </div>
        
        <div class="wpjb-grid-col wpjb-col-15 wpjb-grid-col-right wpjb-grid-col-last">
            <span class="wpjb-line-major">
                <?php echo wpjb_date_display("M, d", $job->job_created_at, false); ?>
            </span>
            
            <span class="wpjb-sub">
                <?php if($job->isNew()): ?><span class="wpjb-bulb"><?php _e("new", "wpjobboard") ?></span><?php endif; ?>
            </span>
            
        </div>
    </div>