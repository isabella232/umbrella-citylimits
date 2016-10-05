<?php echo $theme->before_widget ?>
<?php if($is_smart): ?>

<div class="wpjb-widget-smart-alert">
	<h3 class="widgettitle"><?php esc_html_e($title) ?></h3>
	<div><span><?php _e("Like this job search results?", "wpjobboard") ?></span></div>
	<a href="#" class="wpjb-subscribe wpjb-button"><?php _e("Subscribe Now ...", "wpjobboard") ?></a>
</div>

<?php else: ?>

<div class="wpjb-widget-alert">
	<h3 class="widgettitle"><?php esc_html_e($title) ?></h3>
	<form action="<?php esc_attr_e(wpjb_link_to("alert_confirm")) ?>" method="post">
	<input type="hidden" name="add_alert" value="1" />
	<div id="wpjb_widget_alerts" class="wpjb_widget form-group">
		<input type="text" style="width:90%" name="keyword" placeholder="<?php _e("Keyword", "wpjobboard") ?>" value="" />
		<input type="text" style="width:90%" name="email" value="" placeholder="<?php _e("E-mail", "wpjobboard") ?>" />
		<input type="submit" class="btn wpjb-button" value="<?php _e("Add Alert", "wpjobboard") ?>" />
	</div>
	</form>
</div>

<?php endif; ?>
<?php echo $theme->after_widget ?>
