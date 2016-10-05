<div id="wpjb-main" class="wpjb-page-preview">

    <?php wpjb_flash(); ?>

    <?php if(wpjb_user_can_post_job()): ?>
    <?php wpjb_add_job_steps(); ?>
    <h2><?php esc_html_e($job->job_title) ?></h2>
    <?php wpjb_job_template(); ?>

    <div class="wpjb-next-prev-step">
		<a class="btn" href="<?php echo wpjb_link_to("step_add") ?>">&#171; <?php _e("Edit Listing", "wpjobboard") ?></a>
		<a class="btn" href="<?php echo wpjb_link_to("step_save"); ?>"><?php _e("Publish Listing", "wpjobboard") ?> &raquo;</a>
    </div>
    <?php endif; ?>

</div>
