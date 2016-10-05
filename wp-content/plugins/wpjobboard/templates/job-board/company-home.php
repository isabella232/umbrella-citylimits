
<div class="wpjb wpjb-page-company-home">

    <?php wpjb_flash() ?>
    
    <div class="wpjb-boxes">
        
        <div class="wpjb-boxes-group">
            <span class="wpjb-boxes-group-text"><?php _e("Manage", "wpjobboard") ?></span>
        </div>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->add) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-plus"></span>
            <span class="wpjb-box-title"><?php _e("Post a Job", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->jobs) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-briefcase"></span>
            <span class="wpjb-box-title"><?php _e("Company Jobs", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->edit) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-pencil-squared"></span>
            <span class="wpjb-box-title"><?php _e("Edit Profile", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->membership) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-users"></span>
            <span class="wpjb-box-title"><?php _e("Membership", "wpjobboard") ?></h4>
        </a>
        
        <div class="wpjb-boxes-group">
            <span class="wpjb-boxes-group-text"><?php _e("Account", "wpjobboard") ?></span>
        </div>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->logout) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-off"></span>
            <span class="wpjb-box-title"><?php _e("Logout", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->password) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-asterisk"></span>
            <span class="wpjb-box-title"><?php _e("Change Password", "wpjobboard") ?></h4>
        </a>
        
        <a class="wpjb-box wpjb-layer-inside" href="<?php esc_attr_e($urls->delete) ?>">
            <span class="wpjb-box-icon wpjb-glyphs wpjb-icon-trash"></span>
            <span class="wpjb-box-title"><?php _e("Delete Account", "wpjobboard") ?></h4>
        </a>
    </div>

</div>