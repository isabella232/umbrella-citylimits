<div class="wrap">
    
<?php $this->_include("header.php") ?>
<h1><?php _e("E-mail Alerts / Subscribtions", "wpjobboard") ?> </h1>
<?php $this->_include("flash.php"); ?>

<script type="text/javascript">
    Wpjb.DeleteType = "alert";
</script>

<form method="post" action="<?php esc_attr_e(wpjb_admin_url("alerts", "redirect", null, array("noheader"=>1))) ?>" id="posts-filter">

<ul class="subsubsub">
    <li><a <?php if($filter == "all"): ?>class="current"<?php endif; ?> href="<?php esc_attr_e(wpjb_admin_url("alerts", "index", null)) ?>"><?php _e("All", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->all ?>)</span> | </li>
    <li><a <?php if($filter == "daily"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("alerts", "index", null, array("filter"=>"daily"))) ?>"><?php _e("Daily", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->daily ?>)</span> | </li>
    <li><a <?php if($filter == "weekly"): ?>class="current"<?php endif; ?>href="<?php esc_attr_e(wpjb_admin_url("alerts", "index", null, array("filter"=>"weekly"))) ?>"><?php _e("Weekly", "wpjobboard") ?></a><span class="count">(<?php echo (int)$stat->weekly ?>)</span> </li>
</ul>
    
<p class="search-box">
    <label for="post-search-input" class="hidden">&nbsp;</label>
    <input type="text" value="<?php esc_html_e($query) ?>" name="query" id="post-search-input" class="search-input"/>
    <input type="submit" class="button" value="<?php esc_attr_e("Search by Email", "wpjobboard") ?>" />
</p>
    
<div class="tablenav">

    <div class="alignleft actions">
        <select name="action" id="wpjb-action1">
            <option selected="selected" value=""><?php _e("Bulk Actions") ?></option>
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
            <th style="" class="" scope="col"><?php _e("Email", "wpjobboard") ?></th>
            <th style="" class="<?php wpjb_column_sort($sort=="created_at", $order) ?>" scope="col">
                <a href="<?php esc_attr_e(wpjb_admin_url("alerts", "index", null, array_merge($param, array("sort"=>"created_at", "order"=>wpjb_column_order($sort=="created_at", $order))))) ?>">
                    <span><?php _e("Created At", "wpjobboard") ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th style="" class="<?php wpjb_column_sort($sort=="last_run", $order) ?>" scope="col">
                <a href="<?php esc_attr_e(wpjb_admin_url("alerts", "index", null, array_merge($param, array("sort"=>"last_run", "order"=>wpjb_column_order($sort=="last_run", $order))))) ?>">
                    <span><?php _e("Last Run", "wpjobboard") ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th style="" class="" scope="col"><?php _e("Frequency", "wpjobboard") ?></th>
            <th style="" class="" scope="col"><?php _e("Params", "wpjobboard") ?></th>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php foreach($data as $i => $item): ?>
	<tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <th class="check-column" scope="row">
                <input type="checkbox" value="<?php esc_attr_e($item->getId()) ?>" name="item[]"/>
            </th>
            <td class="post-title column-title">
                <strong><?php esc_html_e($item->email) ?></strong>
                <div class="row-actions">
                    <span class=""><a href="<?php esc_attr_e(wpjb_admin_url("alerts", "delete", $item->getId(), array("noheader"=>1))) ?>" title="<?php _e("Delete", "wpjobboard") ?>" class="wpjb-delete"><?php _e("Delete", "wpjobboard") ?></a> </span>
                </div>
            </td>

            <td class="date column-date">
                <?php esc_html_e(wpjb_date($item->created_at)) ?>
            </td>
            <td class="date column-date">
                <?php if($item->last_run == "0000-00-00 00:00:00"): ?>
                <?php _e("Never", "wpjobboard") ?>
                <?php else: ?>
                <?php esc_html_e(wpjb_date($item->last_run)) ?><br/>
                <small><?php printf("%s ago", daq_time_ago_in_words($item->time->last_run)) ?></small>
                <?php endif; ?>
            </td>
            <td class="date column-date">
                <?php if($item->frequency == 1): ?>
                <?php _e("Daily", "wpjobboard") ?>
                <?php else: ?>
                <?php _e("Weekly", "wpjobboard") ?>
                <?php endif; ?>
            </td>
            <td>
                <?php $alert = unserialize($item->params); ?>
                <?php $pc = 0; ?>
                <?php foreach($alert as $k => $vArr): ?>
                <?php if(in_array($k, array("filter")) || empty($vArr)) continue; ?>
                <?php 
                
                    $value = array();
                
                    foreach((array)$vArr as $vk => $v) {
                        switch($k) {
                            case "country": 
                                $v = Wpjb_List_Country::getByCode($v);
                                $v = $v["name"]; 
                                break;
                            case "type":
                            case "category":
                                $v = new Wpjb_Model_Tag($v);
                                $v = $v->title;
                                break;
                            case "posted":
                                $v = sprintf(__("%d days ago.", "wpjobboard"), $v);
                                break;
                            case "meta":
                                foreach($vArr as $vk => $vm) {
                                    echo "<strong>$vk</strong>: ".esc_html(join(", ", (array)$vm))."<br/>";
                                }
                                continue 3;
                                break;
                        } 
                        
                        $value[] = $v;
                    }
                ?>
                <?php echo "<strong>$k</strong>: ".esc_html(join(", ", $value))."<br/>"; ?>
                <?php $pc++; ?>
                
                
                <?php endforeach; ?>
                
            </td>

        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="tablenav">
    <div class="tablenav-pages">
        <?php
        echo paginate_links( array(
            'base' => wpjb_admin_url("alerts", "index", null, $param)."%_%",
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
        </select>
        
        <input type="submit" class="button-secondary action" id="wpjb-doaction2" value="<?php _e("Apply", "wpjobboard") ?>" />
        

    </div>
    
    <div class="alignleft actions">
        
        <?php $p2 = $param; $p2["noheader"] = "1"; ?>
        <a href="<?php esc_attr_e(wpjb_admin_url("alerts", "export", null, $p2))  ?>" title="<?php _e("Export to CSV", "wpjobboard") ?>"><img src="<?php esc_attr_e(plugins_url()."/wpjobboard/application/public/csv.png") ?>" style="margin-top:5px" alt="<?php _e("Export to CSV", "wpjobboard") ?>" /></a>
        
    </div>

    <br class="clear"/>
</div>


</form>


<?php $this->_include("footer.php"); ?>

</div>