<div class="wrap">

<?php $this->_include("header.php") ?>
    
<?php if($listing == "single-job"): ?>
<h1>
    <?php _e("Pricing (single job posting)", "wpjobboard") ?> 
    <a class="add-new-h2" href="<?php echo wpjb_admin_url("pricing", "add", null, array("listing"=>"single-job")) ?>"><?php _e("Add New", "wpjobboard") ?></a> 
</h1>
<?php elseif($listing == "single-resume"): ?>
<h1>
    <?php _e("Pricing (single resume access)", "wpjobboard") ?> 
    <a class="add-new-h2" href="<?php echo wpjb_admin_url("pricing", "add", null, array("listing"=>"single-resume")) ?>"><?php _e("Add New", "wpjobboard") ?></a> 
</h1>
<?php elseif($listing == "employer-membership"): ?>
<h1>
    <?php _e("Pricing (employer-membership)", "wpjobboard") ?> 
    <a class="add-new-h2" href="<?php echo wpjb_admin_url("pricing", "add", null, array("listing"=>"employer-membership")) ?>"><?php _e("Add New", "wpjobboard") ?></a> 
</h1>
<?php endif; ?>

<?php $this->_include("flash.php"); ?>
    
<script type="text/javascript">
    Wpjb.DeleteType = "<?php _e("listing", "wpjobboard") ?>";
</script>

<form method="post" action="<?php esc_attr_e(wpjb_admin_url("pricing", "redirect", null, array("noheader"=>1))) ?>" id="posts-filter">
<input type="hidden" name="listing" value="<?php esc_attr_e($listing) ?>" />

<div class="tablenav">

<div class="alignleft actions">
    <select name="action" id="wpjb-action1">
        <option selected="selected" value=""><?php _e("Bulk Actions", "wpjobboard") ?></option>
        <option value="delete"><?php _e("Delete", "wpjobboard") ?></option>
        <option value="activate"><?php _e("Activate", "wpjobboard") ?></option>
        <option value="deactivate"><?php _e("Deactivate", "wpjobboard") ?></option>
    </select>

    <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "wpjobboard") ?>"/>

</div>

<div class="clear"/>&nbsp;</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"/></th>
            <th style="" class="column-comments" scope="col"><?php _e("Id", "wpjobboard") ?></th>
            <th style="" class="" scope="col"><?php _e("Title", "wpjobboard") ?></th>
            <th style="" class="" scope="col"><?php _e("Price", "wpjobboard") ?></th>
            <?php if($listing == "single-job"): ?>
            <th style="" class="" scope="col"><?php _e("Visible", "wpjobboard") ?></th>
            <th style="" class="column-icon" scope="col"><?php _e("Featured", "wpjobboard") ?></th>
            <?php endif; ?>
            <th style="" class="fixed column-icon" scope="col"><?php _e("Active", "wpjobboard") ?></th>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php foreach($data as $i => $item): ?>
	<tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <th class="check-column" scope="row">
                <input type="checkbox" value="<?php echo $item->getId() ?>" name="item[]"/>
            </th>
            <td class=""><?php echo $item->getId() ?></td>
            <td class="post-title column-title">
                <strong><a title='<?php _e("Edit", "wpjobboard") ?>  "(<?php echo($item->title) ?>)"' href="<?php echo wpjb_admin_url("pricing", "edit", $item->id, array("listing"=>$listing)); ?>" class="wpjb-row-title"><?php echo esc_html($item->title) ?></a></strong>
                <div class="row-actions">
                    <span class="edit"><a title="<?php _e("Edit", "wpjobboard") ?>" href="<?php echo wpjb_admin_url("pricing", "edit", $item->id, array("listing"=>$listing)); ?>"><?php _e("Edit", "wpjobboard") ?></a> | </span>
                    <span class=""><a href="<?php echo wpjb_admin_url("pricing", "delete", $item->id, array("listing"=>$listing,"noheader"=>1)) ?>" title="<?php _e("Delete", "wpjobboard") ?>" class="wpjb-delete"><?php _e("Delete", "wpjobboard") ?></a> | </span>
                </div>
            </td>
            
            <td class=""><?php echo wpjb_price($item->price, $item->currency) ?></td>
            <?php if($listing == "single-job"): ?>
            <td class=""><?php echo ($item->meta->visible->value()==0) ? __("<i>always</i>", "wpjobboard") : ($item->meta->visible->value()." ".__("days", "wpjobboard")) ?></td>
            <td class=""><?php echo ($item->meta->is_featured->value()) ? __("Yes", "wpjobboard") : __("No", "wpjobboard") ?></td>
            <?php endif; ?>
            <td class=""><?php echo ($item->is_active) ? __("Yes", "wpjobboard") : __("No", "wpjobboard") ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="tablenav">
    <div class="tablenav-pages">
        <?php
            echo paginate_links( array(
                'base' => wpjb_admin_url("pricing", "list", null, array("listing"=>$listing))."%_%",
                'format' => '&p=%#%',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total,
                'current' => $current,
                'add_args' => false
            ));
        ?>
    </div>


    <div class="alignleft actions">
        <select name="action2" id="wpjb-action2">
            <option selected="selected" value=""><?php _e("Bulk Actions", "wpjobboard") ?></option>
            <option value="delete"><?php _e("Delete", "wpjobboard") ?></option>
            <option value="activate"><?php _e("Activate", "wpjobboard") ?></option>
            <option value="deactivate"><?php _e("Deactivate", "wpjobboard") ?></option>
        </select>
        <input type="submit" class="button-secondary action" id="wpjb-doaction2" value="<?php _e("Apply", "wpjobboard") ?>"/>

        <br class="clear"/>
    </div>

    <br class="clear"/>
</div>


</form>


<?php $this->_include("footer.php"); ?>