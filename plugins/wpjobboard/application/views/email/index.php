<div class="wrap">
    
<?php $this->_include("header.php") ?>
<h1>    
    <?php _e("Email Templates", "wpjobboard") ?> 
    <a class="add-new-h2" href="<?php echo wpjb_admin_url("email", "add"); ?>"><?php _e("Add New", "wpjobboard") ?></a> 
</h1>
<?php $this->_include("flash.php"); ?>

<form method="post" action="" id="posts-filter">
<input type="hidden" name="action" id="wpjb-action-holder" value="-1" />



<div class="clear"/>&nbsp;</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <th style="width:25%" class="" scope="col"><?php _e("Mail Title", "wpjobboard") ?></th>
            <th style="width:30%" class="" scope="col"><?php _e("Name", "wpjobboard") ?></th>
            <th style="width:20%" class="column-icon" scope="col"><?php _e("Mail From", "wpjobboard") ?></th>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php $i = 0; ?>
        <?php foreach($data as $j => $group): ?>
        <tr valign="top" class="author-self status-publish iedit">
            <td colspan="3"><h3 style="font-weight:normal"><?php echo $desc[$j] ?></h3></td>
        </tr>
        <?php $i++; ?>
        <?php foreach($group as $item): ?>
	<tr valign="top" class="<?php if($i%2==0 || true): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <td class="post-title column-title">
                <strong><a title='<?php _e("Edit", "wpjobboard") ?>  "(<?php esc_attr_e($item->mail_title) ?>)"' href="<?php echo wpjb_admin_url("email", "edit", $item->getId()); ?>" class="row-title"><?php esc_html_e($item->mail_title) ?></a></strong>
                <?php if($item->sent_to == 5): ?>
                <span class="row-actions"><a href="<?php esc_attr_e(wpjb_admin_url("email", "delete", $item->id, array("noheader"=>1))) ?>" title="<?php _e("Delete", "wpjobboard") ?>" class="wpjb-delete"><?php _e("Delete", "wpjobboard") ?></a></span>
                <?php endif; ?>
            </td>
            <td class=""><code><?php esc_html_e($item->name) ?></code></td>
            <td class="author column-author"><?php esc_html_e($item->mail_from) ?></td>
        </tr>
        <?php $i++; ?>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>



</form>

</div>
    
<?php $this->_include("footer.php"); ?>

