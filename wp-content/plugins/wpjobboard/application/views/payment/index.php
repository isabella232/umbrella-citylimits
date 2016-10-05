<div class="wrap">
<?php $this->_include("header.php"); ?>
    
<h1>
    <?php _e("Payments", "wpjobboard"); ?>
</h1>

<?php $this->_include("flash.php"); ?>
    
<div class="clear">&nbsp;</div>

<form action="<?php esc_attr_e(wpjb_admin_url("payment", "redirect", null, array("noheader"=>1))) ?>" method="post">
    
<p class="search-box">
    <label for="post-search-input" class=""><?php _e("Find Payment by ID", "wpjobboard") ?>:</label>
    <input type="text" value="<?php esc_html_e($payment_id) ?>" name="payment_id" id="post-search-input" class="search-input"/>
    <input type="submit" class="button" value="<?php _e("Search", "wpjobboard") ?>"/>
</p>
    
<div class="tablenav">

<div class="alignleft actions">
    <select id="wpjb-action1" name="action">
        <option selected="selected" value=""><?php _e("Bulk Actions", "wpjobboard") ?></option>
        <option value="markpaid"><?php _e("Mark as Paid", "wpjobboard") ?></option>
        <option value="delete"><?php _e("Delete", "wpjobboard") ?></option>
    </select>

    <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "wpjobboard") ?>" />
</div>
    
</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"/></th>
            <th style="width:2.2em" class="" scope="col"><?php _e("ID", "wpjobboard") ?></th>
            <th style="width:20%" class="" scope="col"><?php _e("Payment For", "wpjobboard") ?></th>
            <th style="width:75px" class="" scope="col"><?php _e("Created At", "wpjobboard") ?></th>
            <th style="" class="" scope="col"><?php _e("User", "wpjobboard") ?></th>
            <th style="" class="" scope="col"><?php _e("External Id", "wpjobboard") ?></th>
            <th style="width:75px" class="column-icon" scope="col"><?php _e("To Pay", "wpjobboard") ?></th>
            <th style="width:75px" class="fixed column-icon" scope="col"><?php _e("Paid", "wpjobboard") ?></th>
            <th><?php _e("Message", "wpjobboard") ?></th>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php foreach($data as $i => $item): ?>
	<tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <th class="check-column" scope="row">
                <input type="checkbox" value="<?php echo $item->getId() ?>" name="item[]"/>
            </th>
            <td><?php esc_html_e($item->id) ?></td>
            <td class="post-title column-title">
                <?php if($item->object_type == Wpjb_Model_Payment::JOB): ?>
                <?php $job = new Wpjb_Model_Job($item->object_id) ?>
                <strong>
                    <a href="<?php echo wpjb_admin_url("job", "edit", $item->object_id); ?>" ><?php _e("Job", "wpjobboard") ?> &quot;<?php esc_html_e($job->job_title) ?>&quot; (ID: <?php echo $job->id ?>)</a>
                    <img src="<?php esc_attr_e(plugins_url("wpjobboard/application/public/symbolic-link.png")) ?>" alt="<?php _e("link", "wpjobboard") ?>" />
                </strong>
                <?php elseif($item->object_type == Wpjb_Model_Payment::RESUME): ?>
                <?php $resume = new Wpjb_Model_Resume($item->object_id) ?>
                <strong>
                    <a href="<?php echo wpjb_admin_url("resumes", "edit", $item->object_id) ?>"><?php _e("Resume", "wpjobboard") ?> &quot;<?php esc_html_e($resume->getSearch(true)->fullname) ?>&quot; (ID: <?php echo $resume->id ?>)</a>
                    <img src="<?php esc_attr_e(plugins_url("wpjobboard/application/public/symbolic-link.png")) ?>" alt="<?php _e("link", "wpjobboard") ?>" />
                </strong>
                <?php elseif($item->object_type == Wpjb_Model_Payment::MEMBERSHIP): ?>
                <?php $member = new Wpjb_Model_Membership($item->object_id) ?>
                <strong>
                    <a href="<?php echo wpjb_admin_url("memberships", "edit", $item->object_id) ?>"><?php _e("Membership", "wpjobboard") ?> &quot;<?php esc_html_e($member->getPricing(true)->title) ?>&quot; (ID: <?php echo $member->id ?>)</a>
                    <img src="<?php esc_attr_e(plugins_url("wpjobboard/application/public/symbolic-link.png")) ?>" alt="<?php _e("link", "wpjobboard") ?>" />
                </strong>
                <?php endif; ?>
                
                <div class="row-actions">
                    <span><a href="<?php esc_attr_e(wpjb_admin_url("payment", "markpaid", $item->getId(), array("noheader"=>1))) ?>" class="wpjb-payment-mark-paid"><?php _e("Mark as Paid", "wpjobboard") ?></a> | </span>
                    <span><a href="<?php esc_attr_e(wpjb_admin_url("payment", "delete", $item->getId(), array("noheader"=>1))) ?>" class="wpjb-delete"><?php _e("Delete", "wpjobboard") ?></a> | </span>
                </div>
                
            </td>
            <td class="">
                <?php echo wpjb_date($item->created_at) ?>
            </td>
            <td class="">
                <?php if($item->user_id < 1): ?>
                <?php _e("Anonymous", "wpjobboard") ?>
                <?php else: ?>
                <a href="user-edit.php?user_id=<?php echo $item->user_id ?>"><?php echo esc_html($item->getUser()->display_name." (ID: ".$item->getUser()->getId().")") ?></a>
                <?php endif; ?>
            </td>
            <td class=""><?php esc_html_e($item->engine.": ". ($item->external_id ? $item->external_id : "-")) ?></td>
            <td class=""><?php echo wpjb_price($item->payment_sum, $item->payment_currency) ?></td>
            <td class="" style="color:<?php if($item->payment_sum==$item->payment_paid): ?>green<?php else: ?>red<?php endif; ?>">
                <?php echo wpjb_price($item->payment_paid, $item->payment_currency) ?>
                <?php if($item->payment_sum==$item->payment_paid): ?>
                <small><?php echo wpjb_date($item->paid_at) ?></small>
                <?php endif; ?>
            </td>
            <td class=""><?php esc_html_e($item->message ? $item->message : "—") ?></td>

        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

    
<div class="tablenav">
    <div class="tablenav-pages">
        <?php
        echo paginate_links( array(
            'base' => wpjb_admin_url("payment", "index", null)."%_%",
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
        <select id="wpjb-action2" name="action2">
            <option selected="selected" value=""><?php _e("Bulk Actions", "wpjobboard") ?></option>
            <option value="markpaid"><?php _e("Mark as Paid", "wpjobboard") ?></option>
            <option value="delete"><?php _e("Delete", "wpjobboard") ?></option>
        </select>

        <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "wpjobboard") ?>" />

    </div>
    
    <br class="clear"/>
</div>
</form>

    
</div>

<?php $this->_include("footer.php"); ?>