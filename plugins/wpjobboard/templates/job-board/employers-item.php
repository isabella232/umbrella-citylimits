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

 /* @var $company Wpjb_Model_Company */

?>

    <div class="wpjb-grid-row">
        <div class="wpjb-grid-col wpjb-col-logo">
            <?php if($company->doScheme("company_logo")): ?>
            <?php elseif($company->getLogoUrl()): ?>
            <div class="wpjb-img-36">
                <img src="<?php echo $company->getLogoUrl("36x36") ?>" alt="" class="wpjb-img-36" />
            </div>
            <?php else: ?>
            <div class="wpjb-img-36 wpjb-icon-none">
                <span class="wpjb-glyphs wpjb-icon-building wpjb-icon-36"></span>
            </div>
            <?php endif; ?>
        </div>
    
        <div class="wpjb-grid-col wpjb-col-title wpjb-col-40">
            <span class="wpjb-line-major">
                <a href="<?php echo wpjb_link_to("company", $company) ?>"><?php esc_html_e($company->company_name) ?></a>
            </span>
            
            <span class="wpjb-sub wpjb-sub-small">
                <?php esc_html_e(sprintf(__("Active Jobs: %d", "wpjobboard"), wpjb_find_jobs(array("employer_id"=>$company->id, "count_only"=>true)))) ?>
            </span>
        </div>
        
        <div class="wpjb-grid-col wpjb-col-location wpjb-col-35">
            <span class="wpjb-line-major">
                <span class="wpjb-glyphs wpjb-icon-location"></span>
                <?php esc_html_e($company->locationToString()) ?>
            </span>
        </div>
        
        <div class="wpjb-grid-col wpjb-col-10 wpjb-grid-col-right wpjb-grid-col-last">
            <span class="wpjb-line-major">
                <?php echo wpjb_date_display("M, d", $company->getUser(true)->user_registered, false); ?>
            </span>
        </div>
    </div>

