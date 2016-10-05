<div class="wrap">
    <h1><?php _e("Health Check", "wpjobboard") ?> </h1>

</div>

<div id="dashboard-widgets-wrap">
    

<div class="clear">&nbsp;</div>

    <div class="metabox-holder columns-2" id="dashboard-widgets">
        <div class="postbox-container" id="postbox-container-1">
            
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox " id="">
                    <h3 class="hndle"><span><?php esc_html_e("Registered Pages", "wpjobboard") ?></span></h3>
                    <div class="inside">
                        <?php _e("Job Board URL", "wpjobboard") ?>
                        <a class="button button-highlighted" href="<?php esc_attr_e(admin_url("post.php?post=".wpjb_conf("link_jobs")."&action=edit")) ?>"><?php _e("Edit", "wpjobboard") ?></a>
                        
                        <a class="button" href="<?php echo wpjb_url() ?>"><?php echo wpjb_url() ?></a>
                        
                        <br/><br/>
                        
                        <?php _e("Resumes URL", "wpjobboard") ?>
                        <a class="button button-highlighted" href="<?php esc_attr_e(admin_url("post.php?post=".wpjb_conf("link_resumes")."&action=edit")) ?>"><?php _e("Edit", "wpjobboard") ?></a>
                        
                        <a class="button" href="<?php echo wpjr_url() ?>"><?php echo wpjr_url() ?></a>
                    </div>
                </div>
            </div>	
            
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox " id="">
                    <h3 class="hndle"><span><?php esc_html_e("Crashed Tables", "wpjobboard") ?></span></h3>
                    <div class="inside">
                        <?php if(empty($crashed)): ?>
                        <?php _e("All tables seem to be OK.", "wpjobboard"); ?>
                        <?php else: ?>
                        <?php foreach($crashed as $c): ?>
                            <?php echo $c ?><br/>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>	
            
            <div class="meta-box-sortables ui-sortable">

                <div class="postbox ">
                    <h3 class="hndle"><span><?php esc_html_e("Events", "wpjobboard") ?></span></h3>
                    <div class="inside" style="padding-top:0px">
                        <?php $next = wp_next_scheduled("wpjb_event_import"); ?>
                        <p>
                            <?php if($next): ?>
                            <span class="approved"><?php echo (sprintf(__("wpjb_event_import is enabled. Next <strong>expected</strong> run is scheduled on %s", "wpjobboard"), date("Y-m-d H:i:s", $next))) ?></span>
                            <?php else: ?>
                            <span class="warning"><?php _e('wpjb_event_import is disabled. This is ok as long as long as your <a href="admin.php?page=wpjb-import">schedule imports list</a> is empty.', "wpjobboard") ?></span>
                            <?php endif; ?>
                        </p>
                        
                        <?php $arr = array("wpjb_event_expiring_jobs", "wpjb_event_subscriptions_daily", "wpjb_event_subscriptions_weekly") ?>
                        <?php foreach($arr as $e): ?>
                        <?php $next = wp_next_scheduled($e); ?>
                        <p>
                            <?php if($next): ?>
                            <span class="approved"><?php echo (sprintf(__("%s is enabled. Next <strong>expected</strong> run is scheduled on %s", "wpjobboard"), $e, date("Y-m-d H:i:s", $next))) ?></span>
                            <?php else: ?>
                            <span class="spam"><?php echo sprintf(__('%s is disabled.', "wpjobboard"), $e) ?></span>
                            <?php endif; ?>
                        </p>
                        <?php endforeach; ?>
                        
                        
                    </div>
                </div>
            </div>
        </div>
            
        <div class="postbox-container" id="postbox-container-2">&nbsp;</div>


    </div>

    <div class="clear"></div>
    
</div>