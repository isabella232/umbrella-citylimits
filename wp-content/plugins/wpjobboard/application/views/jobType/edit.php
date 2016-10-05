<div class="wrap">
    
<?php $this->_include("header.php") ?>
    <h1>
        <?php if($form->getObject()->id): ?>
        <?php _e("Edit Job Type | ID: ", "wpjobboard"); echo $form->getObject()->id; ?> 
        <?php else: ?>
        <?php _e("Add Job Type", "wpjobboard"); ?>
        <?php endif; ?>
        <a class="add-new-h2" href="<?php echo wpjb_admin_url("jobType"); ?>"><?php _e("Go back &raquo;", "wpjobboard") ?></a> 
    </h1>
    
<?php $this->_include("flash.php"); ?>

<form action="" method="post" class="wpjb-form">
    
    <table class="form-table">
        <tbody>
        <?php echo daq_form_layout_config($form); ?>
        </tbody>
    </table>

    <p class="submit">
        <input type="submit" value="<?php _e("Save Changes", "wpjobboard") ?>" class="button-primary" name="Submit" />
    </p>
</form>

<?php wp_enqueue_script("wpjb-color-picker", null, null, null, true) ?>
<script type="text/javascript">
    jQuery(function() {
        jQuery(".wpjb-color-picker").val("#"+jQuery(".wpjb-color-picker").val().replace("#", ""));
        jQuery(".wpjb-color-picker").colorPicker();
        jQuery(".color_picker").after("<div class=\"color-picker-legend\"><?php _e("Click to select job type \\\"color\\\"", "wpjobboard") ?></div>");
        
        var a = jQuery("<a href=\"#\"><?php _e("Reset color", "wpjobboard") ?></a>");
        a.click(function(e) {
            e.preventDefault();
            jQuery(".wpjb-color-picker").val("-1");
            jQuery(".color_picker").css("background-color", "#FFFFFF");
            return false;
        });
        jQuery(".color_picker").after(a);

    });
    
</script>

<?php $this->_include("footer.php"); ?>