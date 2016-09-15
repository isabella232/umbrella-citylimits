<div class="wrap">
    
<?php $this->_include("header.php") ?>
    <h1>
        <?php _e("Edit Email Template | ID: ", "wpjobboard"); echo $form->getObject()->id; ?> 
        <a class="add-new-h2" href="<?php esc_attr_e(wpjb_admin_url("email")) ?>"><?php _e("Go back &raquo;", "wpjobboard") ?></a> 
    </h1>
<?php $this->_include("flash.php"); ?>

<form action="" method="post" class="wpjb-form">

    <?php echo daq_form_layout_config($form) ?>
    
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><?php _e("Variables", "wpjobboard") ?></th>
                <td>
                    <div id="wpjb-mail-var-wrap">
    
                        <?php foreach($vars as $var): ?>
                        <?php if(!in_array($var["var"], $objects)) continue; ?>
                        <div class="widget wpjb-mail-var-widget">	
                             <div class="widget-top">
                                <div class="widget-title-action">
                                    <a href="#available-widgets" class="widget-action hide-if-no-js"></a>
                                </div>
                                <div class="widget-title"><h4><?php esc_html_e($var["title"]) ?></h4></div>
                            </div>

                            <div class="widget-inside">
                                <div class="widget-content" style="">
                                    <h3><?php _e("Variables", "wpjobboard") ?></h3>
                                    <?php foreach($var["item"] as $k => $v): ?>
                                    <?php if(is_array($v)) continue; ?>
                                    <p>
                                        <attr title="<?php echo ucfirst(str_replace("_", " ", $k)) ?>"><img class="wpjb-mail-var-helper" src="<?php esc_attr_e(plugins_url()."/wpjobboard/public/images/question-white.png") ?>" alt="" /></attr>
                                        <span class="wpjb-bulb wpjb-mail-var" title="<?php _e("Click to insert into template", "wpjobboard") ?>">{$<?php echo $var["var"].".".$k ?>}</span>
                                    </p>
                                    <?php endforeach; ?>
                                    
                                    <?php if(!empty($var["item"]["meta"])): ?>
                                    <h3><?php _e("Custom Fields", "wpjobboard") ?></h3>
                                    <?php foreach($var["item"]["meta"] as $k => $v): ?>
                                    <?php foreach(array("name", "title", "value", "values") as $m): ?>
                                    <p>
                                        <attr title="<?php echo ucfirst(str_replace("_", " ", $k." - ".$m)) ?>"><img class="wpjb-mail-var-helper" src="<?php esc_attr_e(plugins_url()."/wpjobboard/public/images/question-white.png") ?>" alt="" /></attr>
                                        <span class="wpjb-bulb wpjb-mail-var" title="<?php _e("Click to insert into template", "wpjobboard") ?>">{$<?php echo $var["var"].".meta.".$k.".".$m ?>}</span>
                                    </p>
                                    <?php endforeach; ?>
                                    <hr style="width:95%" />
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    
                                    
                                    <?php if(isset($var["item"]["tag"])): ?>
                                    <h3><?php _e("Tags (categories and job types)", "wpjobboard") ?></h3>
                                    <?php foreach($var["item"]["tag"] as $k): ?>
                                    <?php foreach(array("id", "type", "slug", "title") as $t): ?>
                                    <p>
                                        <attr title="<?php echo ucfirst(str_replace("_", " ", $k)) ?>"><img class="wpjb-mail-var-helper" src="<?php esc_attr_e(plugins_url()."/wpjobboard/public/images/question-white.png") ?>" alt="" /></attr>
                                        <span class="wpjb-bulb wpjb-mail-var" title="<?php _e("Click to insert into template", "wpjobboard") ?>">{$<?php echo $var["var"].".tag.".$k.".0.".$t ?>}</span>
                                    </p>
                                    <?php endforeach; ?>
                                    <hr style="width:95%" />
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    
                                    <?php $detail = "experience" ?>
                                    <?php foreach(array("experience", "education") as $detail): ?>
                                    <?php if(isset($var["item"][$detail])): ?>
                                    <h3><?php echo ucfirst($detail) ?></h3>
                                    <?php foreach(array_keys($var["item"][$detail]) as $k): ?>
                                    <?php if(!is_scalar($k)) continue; ?>
                                    <p>
                                        <attr title="<?php echo ucfirst(str_replace("_", " ", $k)) ?>"><img class="wpjb-mail-var-helper" src="<?php esc_attr_e(plugins_url()."/wpjobboard/public/images/question-white.png") ?>" alt="" /></attr>
                                        <span class="wpjb-bulb wpjb-mail-var" title="<?php _e("Click to insert into template", "wpjobboard") ?>">{$<?php echo $var["var"].".$detail.0.".$k ?>}</span>
                                    </p>
                                    
                                    <?php endforeach; ?>
                                    <hr style="width:95%" />
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if($customs): ?>
                        <div class="widget wpjb-mail-var-widget">	
                             <div class="widget-top">
                                <div class="widget-title-action">
                                    <a href="#available-widgets" class="widget-action hide-if-no-js"></a>
                                </div>
                                <div class="widget-title"><h4><?php _e("Custom Variables", "wpjobboard") ?></h4></div>
                            </div>

                            <div class="widget-inside">
                                <div class="widget-content">
                                    <h3><?php _e("Variables", "wpjobboard") ?></h3>
                                    <?php foreach($customs as $k => $v): ?>
                                    <p>
                                        <attr title="<?php echo $v ?>"><img class="wpjb-mail-var-helper" src="<?php esc_attr_e(plugins_url()."/wpjobboard/public/images/question-white.png") ?>" alt="" /></attr>
                                        <span class="wpjb-bulb wpjb-mail-var" title="<?php _e("Click to insert into template", "wpjobboard") ?>">{$<?php echo $k ?>}</span>
                                    </p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit">
    <input type="submit" value="<?php _e("Save Changes", "wpjobboard") ?>" class="button-primary" name="Submit"/>
    </p>

</form>
    

    
</div>
    <script type="text/javascript">
    jQuery(function() {
        jQuery(".wpjb-mail-var").click(function() {
            
            var tpl = jQuery(this).text();
            
            if(jQuery(".wpjb-mail-body-select").val() == "text/plain") {
                var textarea = jQuery('#mail_body_text');
                var pos = textarea.prop("selectionStart");
                var txt = textarea.val();
                var t1 = txt.slice(pos);
                var t2 = txt.slice(0, pos);

                textarea.val(t2+tpl+t1);
                textarea.focus();
                textarea[0].setSelectionRange(tpl.length+pos, tpl.length+pos); 
            } else {
                var ed = tinyMCE.get('mail_body_html');  
                ed.execCommand('mceInsertContent', false, tpl); 
                ed.focus();
            }
            
            return false;
        });
        
        jQuery(".wpjb-mail-body-select").change(function() {
            if(jQuery(this).val() == "text/plain") {
                jQuery("#wp-mail_body_html-wrap").closest("tr").hide();
                jQuery("#mail_body_text").closest("tr").show();
                var tr = jQuery("#mail_body_text").closest("tr").show();
            } else {
                jQuery("#wp-mail_body_html-wrap").closest("tr").show();
                jQuery("#mail_body_text").closest("tr").hide();
                var tr = jQuery("#wp-mail_body_html-wrap").closest("tr").show();
            }

        });
        
        jQuery(".wpjb-mail-body-select").change();
        jQuery("#wp-mail_body_html-wrap").css("width", "600px");
        
        jQuery(".widget-top").click(function() {
            jQuery(this).closest("div.widget").find(".widget-inside").toggle();
            return false;
        });


    });

    

    </script>
    
<?php $this->_include("footer.php"); ?>