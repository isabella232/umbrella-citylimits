<div class="wrap">
<?php $this->_include("header.php"); ?>
    
<h1>
<?php if($form->getId()>0): ?>
    <?php _e("Edit Application", "wpjobboard"); ?> (ID: <?php echo $form->getId() ?>)
<?php else: ?>
    <?php _e("Add New Application", "wpjobboard"); ?>
<?php endif; ?>
    <a class="add-new-h2" href="<?php esc_html_e(wpjb_admin_url("application", "add")) ?>"><?php _e("Add New", "wpjobboard") ?></a>
</h1>
<?php $this->_include("flash.php"); ?>

<script type="text/javascript">
    Wpjb.Id = <?php echo $form->getObject()->getId() ?>;
</script>

<form action="" method="post" class="wpjb-form" enctype="multipart/form-data">
<div class="metabox-holder has-right-sidebar" id="poststuff" >
<?php //if($form->getId()>0): ?>
<div class="inner-sidebar wpjb-sticky" id="side-info-column" style="">
<div class="meta-box-sortables ui-sortable" id="side-sortables"><div class="postbox " id="submitdiv">
<div class="handlediv"><br></div><h3 class="hndle"><span><?php _e("Application", "wpjobboard") ?></span></h3>
<div class="inside">
<div id="submitpost" class="submitbox">


<div id="minor-publishing">

<?php if($user instanceof WP_User): ?>
<div class="misc-pub-section wpjb-mini-profile">
    <div class="wpjb-avatar">
    <?php echo get_avatar($form->getObject()->user_id) ?>
    </div>
    <strong><?php esc_html_e($user->display_name) ?></strong><br/>
    <p><?php _e("Login", "wpjobboard") ?>: <b><?php esc_html_e($user->user_login) ?></b></p>
    <p><?php _e("ID", "wpjobboard") ?>: <b><?php echo $user->ID ?></b></p>

        
    <br class="clear" />
        
    <p><a href="<?php esc_attr_e(admin_url("user-edit.php?user_id={$user->ID}")) ?>" class="button"><?php _e("view linked user account", "wpjobboard") ?></a></p>
    <p><a href="<?php esc_attr_e(wpjb_admin_url("resumes", "edit", $resumeId)) ?>" class="button"><?php _e("view user resume") ?></a></p>
</div>
<?php endif; ?>
    
<div class="misc-pub-section wpjb-inline-section curtime">
    <span id="timestamp"><?php _e("Application Sent", "wpjobboard") ?>: <b><?php esc_html_e(wpjb_date($form->getObject()->applied_at)) ?></b></span>

</div>
<div class="misc-pub-section wpjb-inline-section wpjb-inline-suggest">
    <span><?php _e("User", "wpjobboard") ?>: <b class="wpjb-inline-label">&nbsp;</b></span>
    <a class="wpjb-inline-edit hide-if-no-js" href="#"><?php _e("Edit") ?></a> 
    <div class="wpjb-inline-field wpjb-inline-select hide-if-js">

        <?php echo $form->getElement("user_id_text")->render(); ?>
        <a href="#" class="wpjb-inline-cancel"><?php _e("Cancel", "wpjobboard") ?></a>
        <small class="wpjb-autosuggest-help" ><?php _e("start typing user: name, login or email in the box above, some suggestions will appear.", "wpjobboard") ?></small>
    </div>
</div>
<div class="misc-pub-section wpjb-inline-section">
    <span><?php _e("Job", "wpjobboard") ?>: <b class="wpjb-inline-label">&nbsp;</b></span>
    <a class="wpjb-inline-edit hide-if-no-js" href="#"><?php _e("Edit") ?></a> |
    <a class="hide-if-no-js wpjb-linked-job-view" href="<?php esc_attr_e(wpjb_admin_url("job", "edit", $form->getObject()->job_id)) ?>"><?php _e("View", "wpjobboard") ?></a>
    <div class="wpjb-inline-field wpjb-inline-select hide-if-js">
        <?php echo $form->getElement("job_id")->render(); ?>
        <a href="#" class="wpjb-inline-cancel"><?php _e("Cancel", "wpjobboard") ?></a>
    </div>
    <?php if($form->getElement("job_id")->hasErrors()): ?>
    <div style="border:1px solid #FFABA8; background-color:#FFEBE8; padding:4px; margin:4px 0 4px 0">
        <ul style="margin:0;padding:0">
            <?php foreach($form->getElement("job_id")->getErrors() as $err): ?>
            <li style="margin:0"><strong><?php esc_html_e($err) ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif ?>
</div>
<div class="misc-pub-section wpjb-inline-section misc-pub-section-last">
    <span><?php _e("Application Status", "wpjobboard") ?>: <b class="wpjb-inline-label">&nbsp;</b></span>
    <a class="wpjb-inline-edit hide-if-no-js" href="#"><?php _e("Edit") ?></a>
    <div class="wpjb-inline-field wpjb-inline-select hide-if-js">
        <?php echo $form->getElement("status")->render(); ?>
        <a href="#" class="wpjb-inline-cancel"><?php _e("Cancel", "wpjobboard") ?></a>
    </div>
</div>


    
</div>


<div id="major-publishing-actions">  
    <?php if($form->getId()>0): ?>
    <div id="delete-action">
        <a href="<?php esc_attr_e(wpjb_admin_url("application", "delete", $form->getObject()->id, array("noheader"=>1))) ?>" class="submitdelete deletion wpjb-delete-item-confirm"><?php _e("Delete", "wpjobboard") ?></a>
    </div>
    <div id="publishing-action">
        <input type="submit" accesskey="p" tabindex="5" value="<?php _e("Update application", "wpjobboard") ?>" class="button-primary" id="publish" name="publish">
    </div>
    <?php else: ?>
    <div id="publishing-action">
        <input type="submit" accesskey="p" tabindex="5" value="<?php _e("Add application", "wpjobboard") ?>" class="button-primary" id="publish" name="publish">
    </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>
</div>

</div>
</div>
</div>


    <?php do_action("wpja_minor_section_apply", $form) ?>
    
    
</div> 

    
<?php //endif; ?>
    <div id="post-body">
        <div id="post-body-content">
        <?php echo daq_form_layout($form) ?>

        <p class="submit">
            <input type="submit" value="<?php _e("Save Application", "wpjobboard") ?>" class="button-primary" name="Submit"/>
        </p>
        </div>
    </div>
    
</div>
</form>

<?php $this->_include("footer.php"); ?>