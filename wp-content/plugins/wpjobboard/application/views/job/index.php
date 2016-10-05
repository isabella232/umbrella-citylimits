<div class="wrap">
    
<?php $this->_include("header.php") ?>
<h1><?php _e("Jobs", "wpjobboard") ?> <a class="add-new-h2" href="<?php esc_html_e(wpjb_admin_url("job", "add")) ?>"><?php _e("Add New", "wpjobboard") ?></a> </h1>
<?php $this->_include("flash.php"); ?>

<script type="text/javascript">
    Wpjb.DeleteType = "<?php _e("job", "wpjobboard") ?>";
</script>

<form method="post" action="<?php esc_attr_e(wpjb_admin_url("job", "redirect", null, array("noheader"=>1))) ?>" id="posts-filter">
<input type="hidden" name="filter" value="<?php esc_attr_e($filter) ?>" />

<?php if(!empty($query)): ?>
<div class="updated fade below-h2" style="background-color: rgb(255, 251, 204);">
    <p>
        <?php printf(__("Your jobs list is filtered using following prarmeters <strong>%s</strong>.", "wpjobboard"), $rquery) ?>&nbsp;
        <?php _e("Click here to", "wpjobboard") ?>&nbsp;<a href="<?php esc_attr_e(wpjb_admin_url("job", "index")); ?>"><?php _e("browse all jobs", "wpjobboard") ?></a>.
    </p>
</div>
<?php endif; ?>

<ul class="subsubsub">
    <li><a <?php if($filter == "all"): ?>class="current"<?php endif; ?> href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"")))) ?>"><?php _e("All", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->all ?>)</span> | </li>
    <li><a <?php if($filter == "active"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"active")))) ?>"><?php _e("Active", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->active ?>)</span> | </li>
    <li><a <?php if($filter == "unread"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"unread")))) ?>"><?php _e("Unread", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->unread ?>)</span> | </li>
    <li><a <?php if($filter == "awaiting"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"awaiting")))) ?>"><?php _e("Awaiting Approval", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->awaiting ?>)</span> | </li>
    <li><a <?php if($filter == "inactive"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"inactive")))) ?>"><?php _e("Inactive", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->inactive ?>)</span> | </li>
    <li><a <?php if($filter == "expiring"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"expiring")))) ?>"><?php _e("Expiring Soon", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->expiring ?>)</span> | </li>
    <li><a <?php if($filter == "expiried"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("filter"=>"expired")))) ?>"><?php _e("Expired", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->expired ?>)</span> </li>

</ul>

<p class="search-box">
    <label for="post-search-input" class="hidden"><?php _e("Search Jobs", "wpjobboard") ?>:</label>
    <input type="text" value="<?php esc_html_e($query) ?>" name="query" id="post-search-input" class="search-input"/>
    <input type="submit" class="button" value="<?php _e("Search Jobs", "wpjobboard") ?>" />
</p>

<div class="tablenav">

<div class="alignleft actions">
    <select id="wpjb-action1" name="action">
        <option selected="selected" value=""><?php _e("Bulk Actions", "wpjobboard") ?></option>
        <option value="delete"><?php _e("Delete", "wpjobboard") ?></option>
        <option value="activate"><?php _e("Activate", "wpjobboard") ?></option>
        <option value="deactivate"><?php _e("Deactivate", "wpjobboard") ?></option>
        <?php /*
        <option value="">---</option>
        <option value="read"><?php _e("Mark as read", "wpjobboard") ?></option>
        <option value="unread"><?php _e("Mark as unread", "wpjobboard") ?></option>
         */ ?>
    </select>

    <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "wpjobboard") ?>" />
</div>
<div class="alignleft actions">
    <select name="posted">
        <option value=""><?php _e("View all dates", "wpjobboard") ?></option>
        <?php foreach($months as $k => $v): ?>
        <option value="<?php esc_attr_e($k) ?>" <?php if($posted==$k): ?>selected="selected"<?php endif; ?>><?php esc_html_e($v) ?></option>
        <?php endforeach; ?>
    </select>

    <input type="submit" class="button-secondary" value="<?php _e("Filter", "wpjobboard") ?>" id="post-query-submit"/>

</div>
    
</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <th class="manage-column column-cb check-column" scope="col"><input type="checkbox"/></th>
            
            <?php if($screen->show("job", "id")): ?>
            <th scope="col" style="width:25px">
                <?php _e("ID") ?>
            </th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_logo")): ?>
            <th class="" scope="col" style="width:50px"><?php _e("Logo", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_title")): ?>
            <th class="wpjb-column-jobtitle sortable <?php wpjb_column_sort($sort=="job_title", $order) ?>" scope="col">
                <a href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("sort"=>"job_title", "order"=>wpjb_column_order($sort=="job_title", $order))))) ?>">
                    <span><?php _e("Position Title", "wpjobboard") ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_name")): ?>
            <th class="wpjb-column-createdby" scope="col"><?php _e("Company Name", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_email")): ?>
            <th class="" scope="col"><?php _e("Contact Email", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_url")): ?>
            <th class="" scope="col"><?php _e("Website", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "__location")): ?>
            <th class="" scope="col"><?php _e("Location", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_country")): ?>
            <th class="y" scope="col"><?php _e("Country", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_state")): ?>
            <th class="" scope="col"><?php _e("State", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_zip_code")): ?>
            <th class="" scope="col"><?php _e("Zip-Code", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_city")): ?>
            <th class="" scope="col"><?php _e("City", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "type")): ?>
            <th class="" scope="col"><?php _e("Job Type", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "category")): ?>
            <th class="" scope="col"><?php _e("Category", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "__price")): ?>
            <th class="wpjb-column-price" scope="col"><?php _e("Price", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "payment_method")): ?>
            <th class="" scope="col"><?php _e("Payment Method", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_created_at")): ?>
            <th class="sortable  <?php wpjb_column_sort($sort=="job_created_at", $order) ?>" scope="col">
                <a href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("sort"=>"job_created_at", "order"=>wpjb_column_order($sort=="job_created_at", $order))))) ?>">
                    <span><?php _e("Created", "wpjobboard") ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_expires_at")): ?>
            <th class="wpjb-column-expires sortable  <?php wpjb_column_sort($sort=="job_expires_at", $order) ?>" scope="col">
                <a href="<?php esc_attr_e(wpjb_admin_url("job", "index", null, array_merge($param, array("sort"=>"job_expires_at", "order"=>wpjb_column_order($sort=="job_expires_at", $order))))) ?>">
                    <span><?php _e("Expires", "wpjobboard") ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_description")): ?>
            <th class="" scope="col"><?php _e("Description", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php if($screen->show("job", "__applications")): ?>
            <th class="wpjb-column-applicants" scope="col"><?php _e("Applications", "wpjobboard") ?></th>
            <?php endif; ?>
            
            <?php do_action("wpjb_custom_columns_head", "job") ?>
            
            <?php if($screen->show("job", "__status")): ?>
            <th class="wpjb-column-status" scope="col"><?php _e("Status", "wpjobboard") ?></th>
            <?php endif; ?>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php foreach($result->job as $i => $item): ?>
        <tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?> author-self status-publish iedit <?php if($item->requiresAdminAction()): ?>wpjb-unread<?php endif; ?>">
            <th class="check-column" scope="row">
                <input type="checkbox" value="<?php echo $item->id ?>" name="item[]"/>
            </th>
            
            <?php if($screen->show("job", "id")): ?>
            <td>
                <?php esc_html_e($item->id) ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_logo")): ?>
            <td>
                <?php if($item->getLogoUrl()): ?>
                <img src="<?php esc_attr_e($item->getLogoUrl("48x48")) ?>" alt="" />
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_title")): ?>
            <td class="post-title column-title">
                <strong><a title="<?php _e("Edit", "wpjobboard") ?>" href="<?php esc_attr_e(wpjb_admin_url("job", "edit", $item->id)) ?>" class="wpjb-row-title"><?php esc_html_e($item->job_title) ?></a></strong>
                <div class="row-actions">
                    <span><a href="<?php esc_attr_e(wpjb_link_to("job", $item)) ?>"><?php _e("View", "wpjobboard") ?></a> | </span>
                    <span class="edit"><a title="<?php _e("Edit", "wpjobboard") ?>" href="<?php esc_attr_e(wpjb_admin_url("job", "edit", $item->id)) ?>"><?php _e("Edit", "wpjobboard") ?></a> | </span>
                    <span><a title="<?php _e("Applicants", "wpjobboard") ?>" href="<?php esc_attr_e(wpjb_admin_url("application", "index", null, array("query"=>"job:".$item->id))) ?>"><?php _e("Applicants", "wpjobboard") ?></a> <span style="color:#666">(<?php echo $item->applications ?>)</span> | </span>
                    <span class=""><a href="<?php esc_attr_e(wpjb_admin_url("job", "delete", $item->id, array("noheader"=>1))) ?>" title="<?php _e("Delete", "wpjobboard") ?>" class="wpjb-delete"><?php _e("Delete", "wpjobboard") ?></a></span>
                </div>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_name")): ?>
            <td>
                <?php if($item->employer_id): ?>
                <?php esc_html_e($item->company_name) ?><a style="margin-left:2px" class="row-actions" href="<?php esc_attr_e(wpjb_admin_url("employers", "edit", $item->employer_id)) ?>" title="<?php _e("Edit employer profile ...", "wpjobboard") ?>"><img src="<?php esc_attr_e(plugins_url("wpjobboard/application/public/symbolic-link.png")) ?>" alt="<?php _e("link", "wpjobboard") ?>" /></a>
                <?php else: ?>
                <?php esc_html_e($item->company_name) ?>
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_email")): ?>
            <td>
                <a href="mailto:<?php esc_attr_e($item->company_email) ?>"><?php esc_html_e($item->company_email) ?></a>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "company_url")): ?>
            <td>
                <a href="<?php esc_attr_e($item->company_url) ?>"><?php esc_html_e($item->company_url) ?></a>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "__location")): ?>
            <td>
                <?php esc_html_e($item->locationToString()) ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_country")): ?>
            <td>
                <?php 
                    $country = Wpjb_List_Country::getByCode($item->job_country);
                    esc_html_e( $country ? $country["name"] : "—")
                ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_state")): ?>
            <td>
                <?php esc_html_e($item->job_state) ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_zip_code")): ?>
            <td>
                <?php esc_html_e($item->job_zip_code) ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_city")): ?>
            <td>
                <?php esc_html_e($item->job_city) ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "type")): ?>
            <td>
                <?php if(isset($item->tag->type[0])): ?>
                <a href="<?php esc_attr_e($item->tag->type[0]->url()) ?>" title="<?php _e("Edit job type", "wpjobboard") ?>">
                    <?php esc_html_e($item->tag->type[0]->title) ?>
                </a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "category")): ?>
            <td>
                <?php if(isset($item->tag->category[0])): ?>
                <a href="<?php esc_attr_e($item->tag->category[0]->url()) ?>" title="<?php _e("Edit category", "wpjobboard") ?>">
                    <?php esc_html_e($item->tag->category[0]->title) ?>
                </a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "__price")): ?>
            <td>
                <?php $payment = $item->getPayment(true); ?>
                <?php if($payment->id && $payment->engine!="Credits"): ?>
                <span class="<?php if($payment->is_valid == 1): ?>wpjb-price-paid<?php else: ?>wpjb-price-topay<?php endif; ?> wpjb-price">
                    <?php esc_html_e(wpjb_price($payment->payment_sum, $payment->payment_currency)) ?>
                </span>
                <?php elseif($payment->id && $payment->engine=="Credits"): ?>
                <?php _e("package", "wpjobboard") ?>
                <?php else: ?>
                <?php _e("free", "wpjobboard") ?>
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "payment_method")): ?>
            <td>
                <?php $payment = $item->getPayment(true); ?>
                <?php esc_html_e( $payment->id ? $payment->engine : "—") ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_created_at")): ?>
            <td>
                <?php esc_html_e(wpjb_date($item->job_created_at)) ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_expires_at")): ?>
            <td>
                <?php if($item->job_expires_at == WPJB_MAX_DATE): ?>
                <?php esc_html_e("Never", "wpjobboard") ?>
                <?php else: ?>
                <?php esc_html_e(wpjb_date($item->job_expires_at)) ?><br/>
                <small>
                    <?php if(time()>wpjb_time($item->job_expires_at." 23:59:59")): ?>
                    <?php echo daq_time_ago_in_words(wpjb_time($item->job_expires_at." 23:59:59")) ?> <?php _e("ago", "wpjobboard") ?> .
                    <?php else: ?>
                    <?php _e("in", "wpjobboard") ?> <?php echo daq_time_ago_in_words(wpjb_time($item->job_expires_at." 23:59:59")) ?>.
                    <?php endif; ?>
                </small>
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "__applications")): ?>
            <td>
                <a href="<?php esc_attr_e(wpjb_admin_url("application", "index", null, array("query"=>"job:".$item->id))) ?>">
                    <?php echo $item->applications ?> 
                </a>
                
                <?php if($item->newApplications() > 0): ?>
                <a href="<?php esc_attr_e(wpjb_admin_url("application", "index", null, array("query"=>"job:".$item->id, "filter"=>"new"))) ?>">
                    <strong><?php printf(str_replace(" ", "&nbsp;", __("(%d new)", "wpjobboard")), $item->newApplications()) ?></strong>
                </a>
                <?php endif; ?>
            </td>
            <?php endif; ?>
            
            <?php if($screen->show("job", "job_description")): ?>
            <td>
                <?php echo substr(strip_tags($item->job_description), 0, 120) ?>
            </td>
            <?php endif; ?>
            
            <?php do_action("wpjb_custom_columns_body", "job", $item) ?>
            
            <?php if($screen->show("job", "__status")): ?>
            <td>
                <?php 
                
                $color = array(
                    Wpjb_Model_Job::STATUS_ACTIVE => "wpjb-bulb-active",
                    Wpjb_Model_Job::STATUS_AWAITING => "wpjb-bulb-awaiting",
                    Wpjb_Model_Job::STATUS_PAYMENT => "wpjb-bulb-awaiting",
                    Wpjb_Model_Job::STATUS_EXPIRED => "wpjb-bulb-expired",
                    Wpjb_Model_Job::STATUS_EXPIRING => "wpjb-bulb-expiring",
                    Wpjb_Model_Job::STATUS_INACTIVE => "wpjb-bulb-expired",
                    Wpjb_Model_Job::STATUS_NEW => "wpjb-bulb-new"
                ); 
                
                
                $text = array(
                    Wpjb_Model_Job::STATUS_ACTIVE => __("Active", "wpjobboard"),
                    Wpjb_Model_Job::STATUS_AWAITING => __("Awaiting Approval", "wpjobboard"),
                    Wpjb_Model_Job::STATUS_PAYMENT => __("Awaiting Payment", "wpjobboard"),
                    Wpjb_Model_Job::STATUS_EXPIRED => __("Expired", "wpjobboard"),
                    Wpjb_Model_Job::STATUS_EXPIRING => __("Expiring", "wpjobboard"),
                    Wpjb_Model_Job::STATUS_INACTIVE => __("Inactive", "wpjobboard"),
                    Wpjb_Model_Job::STATUS_NEW => __("New", "wpjobboard")
                ); 
                
                
                
                $st = array();
                
                foreach($item->status() as $status) {
                    $c = $color[$status];
                    $t = $text[$status];
                    $st[] = "<span class=\"wpjb-bulb  $c\">$t</span>";
                }
                
                echo join(" ", $st);
                
                ?>
               
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>

</table>

<div class="tablenav">
    <div class="tablenav-pages">
        <?php
            echo paginate_links( array(
                'base' => wpjb_admin_url("job", "index", null, $param)."%_%",
                'format' => '&p=%#%',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $result->pages,
                'current' => $result->page,
                'add_args' => false
            ));
        ?>
    </div>


    <div class="alignleft actions">
        <select id="wpjb-action2" name="action2">
            <option selected="selected" value=""><?php _e("Bulk Actions", "wpjobboard") ?></option>
            <option value="delete"><?php _e("Delete", "wpjobboard") ?></option>
            <option value="activate"><?php _e("Activate", "wpjobboard") ?></option>
            <option value="deactivate"><?php _e("Deactivate", "wpjobboard") ?></option>
            <?php /*
            <option value="">---</option>
            <option value="read"><?php _e("Mark as read", "wpjobboard") ?></option>
            <option value="unread"><?php _e("Mark as unread", "wpjobboard") ?></option>
             */ ?>
        </select>
        <input type="submit" class="button-secondary action" id="wpjb-doaction2" value="<?php _e("Apply", "wpjobboard") ?>"/>

        <br class="clear"/>
    </div>
    
    <div class="alignleft actions">
        <a href="#" class="button-secondary action wpjb-modal-window-toggle" style="margin-top:0">
            <span class="dashicons dashicons-archive" style="vertical-align: middle; padding-bottom: 4px;"></span> 
            <?php _e("Export ...", "wpjobboard") ?>
        </a>
    </div>

    <br class="clear"/>
</div>


</form>

<?php

wpjb_export_ui(
    "wpjb_export_jobs",
    array(
        "job" => array(
            "title" => __("Jobs", "wpjobboard"),
            "callback" => "dataJob",
            "checked" => true,
            "prefix" => "job",
        ),
        "company" => array(
            "title" => __("Companies", "wpjobboard"),
            "callback" => "dataEmployer",
            "checked" => false,
            "prefix" => "company",
        )
    )
);

?>

<?php $this->_include("footer.php"); ?>
