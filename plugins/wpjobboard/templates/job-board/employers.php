<?php

/**
 * Jobs list
 * 
 * This template file is responsible for displaying list of jobs on job board
 * home page, category page, job types page and search results page.
 * 
 * 
 * @author Greg Winiarski
 * @package Templates
 * @subpackage JobBoard
 * 
 * @var $param array List of job search params
 * @var $search_bar string Either enabled or disabled
 * @var $search_init array Array of initial search params (used only with live search)
 * @var $pagination bool Show or hide pagination
 */

?>

<div class="wpjb wpjb-page-employers-index">

    <?php wpjb_flash(); ?>

    <?php if($search_bar != "disabled"): ?>
    <div id="wpjb-top-search" class="wpjb-layer-inside">
    <form action="<?php esc_attr_e(wpjb_link_to("search")) ?>" method="GET" id="wpjb-top-search-form">
        <?php global $wp_rewrite, $post ?>
        <?php if(!$wp_rewrite->using_permalinks()): ?>
        <input type="hidden" name="page_id" value="<?php echo $post->ID ?>" />
        <?php endif; ?>
        <input autocomplete="off" type="text" class="wpjb-top-search-query wpjb-ls-query" name="query" value="<?php esc_attr_e(isset($param["query"]) ? $param["query"] : "") ?>" placeholder="<?php esc_attr_e("What?", "wpjobboard") ?>" />
        <input autocomplete="off" type="text" class="wpjb-top-search-location wpjb-ls-location" name="location" value="<?php esc_attr_e(isset($param["location"]) ? $param["location"] : "") ?>" placeholder="<?php esc_attr_e("Where?", "wpjobboard") ?>" />

        <div class="wpjb-top-search-submit" style="">
            <input type="submit" value="<?php esc_attr_e("Filter Results", "wpjobboard") ?>" />
        </div>
    </form>
    </div>
    <?php if($search_bar == "enabled-live"): ?>
        <script type="text/javascript">
            jQuery(function($) {
                //WPJB_SEARCH_CRITERIA = <?php echo json_encode($search_init) ?>;
                wpjb_ls_jobs_init();
            });
        </script>
    <?php endif; ?>
    
    <?php endif; ?>
    
    
    
    <div class="wpjb-job-list wpjb-grid">
    
        <?php $result = Wpjb_Model_Company::search($param) ?>
        <?php if ($result->count) : foreach($result->company as $company): ?>
        <?php /* @var $job Wpjb_Model_Job */ ?>
        <?php $this->company = $company; ?>
        <?php $this->render("employers-item.php") ?>
        <?php endforeach; else :?>
        <div class="wpjb-grid-row">
            <?php _e("No employers found.", "wpjobboard"); ?>
        </div>
        <?php endif; ?>
    

    </div>
 

    <?php if($pagination): ?>
    <div class="wpjb-paginate-links">
        <?php wpjb_paginate_links($url, $result->pages, $result->page, $query, $format) ?>
    </div>
    <?php endif; ?>

    
</div>
