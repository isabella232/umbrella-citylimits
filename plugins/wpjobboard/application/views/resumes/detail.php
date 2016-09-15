<div class="wrap">
<?php $this->_include("header.php"); ?>
<h1>
    <?php esc_html_e($title) ?>

    &raquo; <a href="<?php esc_attr_e(wpjb_admin_url("resumes", "edit", $resume->id)) ?>"><?php echo ($user->first_name || $user->last_name) ? esc_html(trim($user->first_name." ".$user->last_name)) : esc_html("ID: ".$item->getId()) ?></a>

</h1>

<?php $this->_include("flash.php"); ?>


<form action="" method="post" enctype="multipart/form-data" class="wpjb-form">

    <div class="metabox-holder has-right-sidebar" id="poststuff" >
        <div id="post-body">
            <div id="post-body-content">
                
            <?php daq_form_layout($form) ?>

            </div>
        </div>

        <br class="clear" />
        

        <p class="submit">
            <input type="submit" value="<?php _e("Add", "wpjobboard") ?>" class="button-primary" name="Save"/>
            <input type="submit" value="<?php _e("Add and Go back", "wpjobboard") ?>" class="button" name="SaveClose"/>
        </p>

        
    </div>



<?php $this->_include("footer.php"); ?>