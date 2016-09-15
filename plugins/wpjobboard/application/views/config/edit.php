<div class="wrap">
 
<?php $this->_include("header.php"); ?>
    
<h1>
    <?php esc_html_e($form->name) ?>
    <a class="add-new-h2" href="<?php echo wpjb_admin_url("config"); ?>"><?php _e("Go back &raquo;", "wpjobboard") ?></a> 
</h1>

<?php $this->_include("flash.php"); ?>
    
<?php if($show_form && in_array($section, array("urls"))): ?>
<form action="" method="post" enctype="multipart/form-data" class="wpjb-form">
    <div class="metabox-holder has-right-sidebar" id="poststuff" >
            <div id="post-body">
            <div id="post-body-content">

            <?php daq_form_layout($form, array("exclude_fields"=>"payment_method", "exclude_groups"=>"_internal")) ?>

            </div>
            <p class="submit">
                <input type="submit" value="<?php _e("Update", "wpjobboard") ?>" class="button-primary" name="Submit"/>
            </p>
        </div>
    </div>
</form>
    
<?php elseif($show_form): ?>
    
<form action="<?php esc_attr_e($submit_action) ?>" method="post" class="wpjb-form">
    <table class="form-table">
        <tbody>
            <?php echo daq_form_layout_config($form) ?>
        </tbody>
    </table>

    <p class="submit">
    <input type="submit" value="<?php esc_attr_e($submit_title) ?>" class="button-primary" name="Submit"/>
    <?php do_action("wpjb_config_edit_buttons") ?>
    
    <?php if($section == "twitter"): ?>
    <input type="submit" value="<?php _e("Save and send test tweet", "wpjobboard") ?>" class="" name="saventest"/>
    <?php endif; ?>
    </p>

</form>
<?php endif; ?>


    
<?php $this->_include("footer.php"); ?>
