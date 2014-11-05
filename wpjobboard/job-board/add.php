<?php

/**
 * Add job form
 *
 * Template displays add job form
 *
 * @author Greg Winiarski
 * @package Templates
 * @subpackage JobBoard
 *
 */

 /* @var $form Wpjb_Form_AddJob */
 /* @var $can_post boolean User has job posting priviledges */

?>

<div id="wpjb-main" class="wpjb-page-add">

	<?php if (is_user_logged_in()) { ?>
		<?php if($can_post): ?>

		<?php wpjb_add_job_steps(); ?>
		<?php wpjb_flash() ?>
		<form class="wpjb-form" action="<?php echo wpjb_link_to("step_preview") ?>" method="post" enctype="multipart/form-data">

			<?php echo $form->renderHidden() ?>
			<?php foreach($form->getReordered() as $group): ?>

			<?php /* @var $group stdClass */ ?>
			<fieldset class="wpjb-fieldset-<?php esc_attr_e($group->getName()) ?>">
				<legend><?php esc_html_e($group->title) ?></legend>
				<?php foreach($group->getReordered() as $name => $field): ?>
				<?php /* @var $field Daq_Form_Element */ ?>
				<div class="<?php wpjb_form_input_features($field) ?>">

					<label class="wpjb-label">
						<?php esc_html_e($field->getLabel()) ?>
						<?php if($field->isRequired()): ?><span class="wpjb-required">*</span><?php endif; ?>
					</label>

					<div class="wpjb-field">
						<?php wpjb_form_render_input($form, $field) ?>
						<?php wpjb_form_input_hint($field) ?>
						<?php wpjb_form_input_errors($field) ?>
					</div>

				</div>
				<?php endforeach; ?>
			</fieldset>
			<?php endforeach; ?>

			<fieldset>
				<div>
					<legend class="wpjb-empty"></legend>

					<?php if($show_pricing): ?>
					<table id="wpjb_pricing">
						<tbody>
							<tr>
								<td><?php _e("Listing cost", "wpjobboard") ?>:</td>
								<td><span id="wpjb-listing-cost">-</span></td>
							</tr>
							<tr>
								<td><?php _e("Discount", "wpjobboard") ?>:</td>
								<td><span id="wpjb-listing-discount">-</span></td>
							</tr>
							<tr>
								<td><strong><?php _e("Total", "wpjobboard") ?>:</strong></td>
								<td><strong><span id="wpjb-listing-total">-</span></strong></td>
							</tr>
						</tbody>
					</table>
					<?php endif; ?>

					<input type="submit" class="wpjb-submit" name="wpjb_preview" id="wpjb_submit" value="<?php _e("Preview", "wpjobboard") ?>" />
					<?php _e("or", "wpjobboard") ?>
					<a href="<?php esc_attr_e(wpjb_link_to("step_reset")) ?>"><?php _e("Reset form", "wpjobboard") ?></a>
				</div>
			</fieldset>

		</form>
		<?php endif; ?>

	<?php } else { ?>
		<div class="wpjb-login-message">You must be logged in to create a job listing. <a href="<?php echo wp_login_url(site_url('/jobs/add/')); ?>">Click here to login</a>.</div>
	<?php } ?>
</div>
