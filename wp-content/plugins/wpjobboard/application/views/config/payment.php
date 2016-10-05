<div class="wrap">
 
<?php $this->_include("header.php"); ?>
    
<h1>
    <?php esc_html_e($title) ?>
    <a class="add-new-h2" href="<?php echo wpjb_admin_url("config"); ?>"><?php _e("Go back &raquo;", "wpjobboard") ?></a> 
</h1>

    <?php $this->_include("flash.php"); ?>

<form action="" method="post" class="wpjb-form">
    <table class="form-table">
        <tbody>
            <?php echo daq_form_layout_config($form) ?>
        </tbody>
    </table>

    <p class="submit">
    <input type="submit" value="<?php _e("Update", "wpjobboard") ?>" class="button-primary" name="Submit"/>
    </p>

</form>

<?php $this->_include("footer.php"); ?>