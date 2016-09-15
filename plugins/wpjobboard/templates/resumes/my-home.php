<div class="wpjb wpjr-page-my-home">

    <?php wpjb_flash() ?>
    
    <?php if(is_object(Wpjb_Model_Resume::current())): ?>
    <?php $completed = Wpjb_Model_Resume::current()->completed() ?>
    <div class="wpjb-layer-inside" style="padding:10px; overflow:hidden;clear:both;">
        <div style="text-align:center">
            <span style="font-size:24px;line-height:48px;text-align: center"><?php echo sprintf(__("Profile Completion (%d%%)", "wpjobboard"), $completed) ?></span>
        </div>

        <div style="">
            <div class="progress-bar blue stripes">
                <span style="width: <?php echo $completed ?>%"></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    
    <div class="wpjb-boxes">
        
        <div class="wpjb-boxes-group">
            <span class="wpjb-boxes-group-text"><?php _e("Manage", "wpjobboard") ?></span>
        </div>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e(wpjr_link_to("myresume")) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-doc-text"></span>
            <span class="wpjb-box-title"><?php _e("My Resume", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e(wpjr_link_to("myapplications")) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-inbox"></span>
            <span class="wpjb-box-title"><?php _e("My Applications", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e(wpjr_link_to("mybookmarks")) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-bookmark"></span>
            <span class="wpjb-box-title"><?php _e("My Bookmarks", "wpjobboard") ?></h4>
        </a>
        
        <div class="wpjb-boxes-group">
            <span class="wpjb-boxes-group-text"><?php _e("Account", "wpjobboard") ?></span>
        </div>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e(wpjr_link_to("logout")) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-off"></span>
            <span class="wpjb-box-title"><?php _e("Logout", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e(wpjr_link_to("myresume_password")) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-asterisk"></span>
            <span class="wpjb-box-title"><?php _e("Change Password", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e(wpjr_link_to("myresume_delete")) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-trash"></span>
            <span class="wpjb-box-title"><?php _e("Delete Account", "wpjobboard") ?></h4>
        </a>
    </div>

</div>