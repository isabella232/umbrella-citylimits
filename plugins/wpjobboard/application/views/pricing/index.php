<div class="wrap">

<?php $this->_include("header.php") ?>
<h1><?php _e("Pricing", "wpjobboard") ?> </h1>

<div class="clear">&nbsp;</div>

<div class="wpjb-config-list">
    <div class="wpjb-pricing-box">
        <h3><?php _e("Single Job Posting", "wpjobboard") ?></h3>
        <a href="<?php echo wpjb_admin_url("pricing", "list", null, array("listing"=>"single-job")) ?>" class="button wpjb-pricing-button"><?php _e("View All", "wpjobboard") ?> (<?php echo $pricing[Wpjb_Model_Pricing::PRICE_SINGLE_JOB] ?>)</a>
        <a href="<?php echo wpjb_admin_url("pricing", "add", null, array("listing"=>"single-job")) ?>" class="button wpjb-pricing-button"><?php _e("Add New ...", "wpjobboard") ?></a>
    </div>
    
     <div class="wpjb-pricing-box">
        <h3><?php _e("Single Resume Access", "wpjobboard") ?></h3>
        <a href="<?php echo wpjb_admin_url("pricing", "list", null, array("listing"=>"single-resume")) ?>" class="button wpjb-pricing-button"><?php _e("View All", "wpjobboard") ?> (<?php echo $pricing[Wpjb_Model_Pricing::PRICE_SINGLE_RESUME] ?>)</a>
        <a href="<?php echo wpjb_admin_url("pricing", "add", null, array("listing"=>"single-resume")) ?>" class="button wpjb-pricing-button"><?php _e("Add New ...", "wpjobboard") ?></a>
    </div>
    
    <div class="wpjb-pricing-box">
        <h3><?php _e("Employer Membership Packages", "wpjobboard") ?></h3>
        <a href="<?php echo wpjb_admin_url("pricing", "list", null, array("listing"=>"employer-membership")) ?>" class="button wpjb-pricing-button"><?php _e("View All", "wpjobboard") ?> (<?php echo $pricing[Wpjb_Model_Pricing::PRICE_EMPLOYER_MEMBERSHIP] ?>)</a>
        <a href="<?php echo wpjb_admin_url("pricing", "add", null, array("listing"=>"employer-membership")) ?>" class="button wpjb-pricing-button"><?php _e("Add New ...", "wpjobboard") ?></a>
    </div>
</div>

<?php $this->_include("footer.php"); ?>

</div>