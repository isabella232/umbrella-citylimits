<div class="wrap">
<?php $this->_include("header.php"); ?>
    
<h1>
    <?php _e("Delete Candidates", "wpjobboard"); ?>
</h1>

<?php $this->_include("flash.php"); ?>
    
    <form action="<?php esc_attr_e(wpjb_admin_url("resumes", "remove", null, array("noheader"=>1))) ?>" method="post">
    <p><?php _e("You have specified these candidates for deletion", "wpjobboard") ?>:</p>
    <ul>
        <?php foreach($list as $item): ?>
        <li>
            <input type="hidden" name="users[]" value="<?php esc_attr_e($item->id) ?>">
            ID #<?php esc_attr_e($item->id) ?>:
            <strong><?php esc_html_e(trim($item->user->first_name." ".$item->user->last_name)) ?></strong>
            <?php _e("linked user account", "wpjobboard") ?>
            <a href="<?php esc_attr_e(admin_url("user-edit.php?user_id={$item->user->ID}")) ?>"><strong><?php esc_html_e($item->user->user_nicename) ?></strong></a>
        </li>
        <?php endforeach; ?>
    </ul>
	
    <fieldset>
        <p><legend><?php _e("What should be done with accounts owned by these users?", "wpjobboard") ?></legend></p>
	<ul style="list-style:none;">
            <li>
                <label for="delete_option0">
                    <input type="radio" id="delete_option0" name="delete_option" value="partial" checked="checked" />
                    <?php _e("Delete Candidates only.", "wpjobboard") ?>
                </label>
            </li>
            <li>
                <label for="delete_option1">
                    <input type="radio" id="delete_option1" name="delete_option" value="full" /> 
                    <?php _e("Delete Candidates AND their linked accounts.", "wpjobboard") ?>
                </label>
            </li>
	</ul>
    </fieldset>
    
    <fieldset class="wpjb-applications-delete">
        <p><legend><?php _e("What should be done with applications owned by these users?", "wpjobboard") ?></legend></p>
	<ul style="list-style:none;">
            <li>
                <label for="applications_option0">
                    <input type="radio" id="applications_option0" name="applications_option" value="unassign" checked="checked" />
                    <?php _e("Unassign Applications.", "wpjobboard") ?>
                </label>
            </li>
            <li>
                <label for="applications_option1">
                    <input type="radio" id="applications_option1" name="applications_option" value="delete" /> 
                    <?php _e("Delete Applications.", "wpjobboard") ?>
                </label>
            </li>
	</ul>
    </fieldset>
	
    <p class="submit">
        <input type="submit" id="submit" class="button" value="<?php _e("Confirm Deletion", "wpjobboard") ?>" />
    </p>
    </form>

</div>

<script type="text/javascript">

jQuery(function($) {
    $("#delete_option0").click(function() {
        $(".wpjb-applications-delete").hide();
    });
    
    $("#delete_option1").click(function() {
        $(".wpjb-applications-delete").show();
    });
    
    $("input[name=delete_option]:checked").click();
});

</script>