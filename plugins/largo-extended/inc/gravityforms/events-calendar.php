<?php

/**
 * This extension depends on Gravity Forms and Events Calendar Pro plugins.
 */
if (is_plugin_active('events-calendar-pro/events-calendar-pro.php')) {

	define('EVENTS_CALENDAR_SUBMISSION_FORM_TITLE', 'Event calendar submission form');
	define('EVENTS_CALENDAR_SUBMISSION_FORM_FILE', __DIR__ . '/data/events_calendar_submission_form.xml');

	function largo_get_events_submission_form_data() {
		global $wp_filesystem;
		if (empty($wp_filesystem)) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			WP_Filesystem();
		}
		$fileContents= $wp_filesystem->get_contents(EVENTS_CALENDAR_SUBMISSION_FORM_FILE);
		$data = simplexml_load_string($fileContents);
		return $data;
	}

	function largo_verify_events_submission_form_structure($form, $form_id, $meta_name) {
		if ($form['title'] !== EVENTS_CALENDAR_SUBMISSION_FORM_TITLE)
			return $form;

		$form_xml = largo_get_events_submission_form_data();

		foreach ($form_xml->form->fields->field as $idx => $field) {
			$continue = false;

			// Find the corresponding field in the $form
			foreach ($form['fields'] as $key => $form_field) {
				if ((int)$form_field['id'] == (int)$field->attributes()->id) {
					$meta_key = $key;
					$meta_value = $form_field;
					break;
				} else {
					$continue = true;
				}
			}

			// One of the fields present in the xml file was deleted from the form.
			// Continue processing the next field.
			if ($continue)
				continue;

			// Verify its label is correct
			if ((string)$meta_value['label'] !== (string)$field->label) {
				$meta_value['label'] = (string)$field->label;
				$form['fields'][$meta_key] = $meta_value;
			}

			// Verify all other attributes are correct
			foreach ($field->attributes() as $attr => $value) {
				// Allow the price field to be changed or removed
				if ((string)$field->attributes()->type == 'product')
					break;

				if ((string)$meta_value[$attr] !== (string)$value)
					$meta_value[$attr] = $value;
			}
		}

		return $form;
	}
	add_action('gform_form_update_meta', largo_verify_events_submission_form_structure, 10, 3);

	function largo_check_for_event_form() {
		$arr = RGFormsModel::get_forms(null, 'title');
		$exists = array_shift(array_values(array_filter($arr, function($form) {
			if ($form->title == EVENTS_CALENDAR_SUBMISSION_FORM_TITLE)
				return $form;
		})));

		if (empty($exists))
			GFExport::import_file(EVENTS_CALENDAR_SUBMISSION_FORM_FILE);
	}
	add_action('gform_after_delete_form', 'largo_check_for_event_form', 10, 0);
	// TODO: Also need some way of inserting the form when largo-extended is activated

	function largo_change_event_post_data($data, $form, $entry) {
		if ($form['title'] !== EVENTS_CALENDAR_SUBMISSION_FORM_TITLE)
			return false;

		$data['post_type'] = 'tribe_events';

		return $data;
	}
	add_action("gform_post_data", "largo_change_event_post_data", 10, 3);

	function largo_event_submission($entry, $form) {
		if ($form['title'] !== EVENTS_CALENDAR_SUBMISSION_FORM_TITLE)
			return false;

		$post_id = $entry['post_id'];
		$data = array();

		$formDates = get_form_dates($form);
		$data['_EventStartDate'] = $formDates['start_date'];
		$data['_EventEndDate'] = $formDates['end_date'];

		$organizer_id = largo_process_event_organizer($form, $_POST);
		if (!empty($organizer_id))
			$data['_EventOrganizerID'] = $organizer_id;

		$data['_EventVenueID'] = largo_process_event_venue($form, $_POST);

		foreach ($data as $k => $v)
			update_post_meta($post_id, $k, $v);

		$event_category_input = array_shift(array_values(array_filter($form['fields'], function($field) {
			if ($field['label'] == 'Event category')
				return $field;
		})));

		$eventCategory = $_POST['input_' . $event_category_input['id']];
		if (!empty($eventCategory))
			wp_set_object_terms($post_id, $eventCategory, TribeEvents::TAXONOMY);
	}
	add_action("gform_post_submission", "largo_event_submission", 10, 2);

	function largo_process_event_organizer($form, $postData) {
		// Values that need to be set
		$values = array(
			'Organizer' => null,
			'Email' => null,
			'Website' => null,
			'Phone' => null
		);

		foreach ($form['fields'] as $k => $v) {
			if ($v['label'] == 'Organizer name')
				$values['Organizer'] = $postData['input_' . $v['id']];
			if ($v['label'] == 'Organizer email')
				$values['Email'] = $postData['input_' . $v['id']];
			if ($v['label'] == 'Organizer website')
				$values['Website'] = $postData['input_' . $v['id']];
			if ($v['label'] == 'Organizer phone')
				$values['Phone'] = $postData['input_' . $v['id']];
		}

		if (!empty($values['Organizer'])) {
			$ret = tribe_create_organizer($values);
			return $ret;
		}

		return null;
	}

	function largo_process_event_venue($form, $postData) {
		// Values that need to be set
		$values = array(
			'Venue' => null, // input_#
			'Address' => null, // input_#_1
			'City' => null, // input_#_3
			'State' => null, // input_#_4
			'Zip' => null, // input_#_5
			'Phone' => null // input_#
		);

		foreach ($form['fields'] as $k => $v) {
			if ($v['label'] == 'Venue name')
				$values['Venue'] = $postData['input_' . $v['id']];
			if ($v['label'] == 'Venue phone')
				$values['Phone'] = $postData['input_' . $v['id']];
			if ($v['label'] == 'Venue website')
				$values['URL'] = $postData['input_' . $v['id']];
			if ($v['label'] == 'Venue address') {
				$values['Address'] = $postData['input_' . $v['id'] . '_1'];
				$values['City'] = $postData['input_' . $v['id'] . '_3'];
				$values['State'] = $postData['input_' . $v['id'] . '_4'];
				$values['Zip'] = $postData['input_' . $v['id'] . '_5'];
			}
		}
		$ret = tribe_create_venue($values);
		return $ret;
	}

	function largo_populate_event_categories_dropdown($form) {
		if ($form['title'] !== EVENTS_CALENDAR_SUBMISSION_FORM_TITLE)
			return $form;

		foreach ($form['fields'] as $idx => $field) {
			if ($field['type'] == 'select' && $field['label'] == 'Event category') {
				$event_category_dropdown_idx = $idx;
				$event_category_dropdown = $field;
				break;
			}
		}

		$cats = (array) get_terms((array) TribeEvents::TAXONOMY, array('hide_empty' => false));
		$choices = array(
			array(
				'text' => 'None',
				'value' => ''
			)
		);
		foreach ($cats as $cat) {
			array_push($choices, array(
				'text' => $cat->name,
				'value' => $cat->slug
			));
		}

		$event_category_dropdown['choices'] = $choices;
		$form['fields'][$event_category_dropdown_idx] = $event_category_dropdown;
		return $form;
	}
	add_filter('gform_pre_render', 'largo_populate_event_categories_dropdown');

	function get_form_dates($form) {
		$start_date_input = array_shift(array_values(array_filter($form['fields'], function($field) {
			if ($field['dateType'] == 'datepicker' && $field['label'] == 'Start date')
				return $field;
		})));

		$start_time_input = array_shift(array_values(array_filter($form['fields'], function($field) {
			if ($field['label'] == 'Start time')
				return $field;
		})));

		$end_date_input = array_shift(array_values(array_filter($form['fields'], function($field) {
			if ($field['dateType'] == 'datepicker' && $field['label'] == 'End date')
				return $field;
		})));

		$end_time_input = array_shift(array_values(array_filter($form['fields'], function($field) {
			if ($field['label'] == 'End time')
				return $field;
		})));

		$startDate = date_parse($_POST['input_' . $start_date_input['id']]);
		$startDateString = $startDate['year'] . '-' .
			str_pad($startDate['month'], 2, "0", STR_PAD_LEFT) . '-' .
			str_pad($startDate['day'], 2, ")", STR_PAD_LEFT);

		$startTime = $_POST['input_' . $start_time_input['id']];
		$start_hour = str_pad($startTime[0], 2, "0", STR_PAD_LEFT);
		$start_min = str_pad($startTime[1], 2, "0", STR_PAD_LEFT);
		$start_mer = $startTime[2];

		$startDateTimeString = date("Y-m-d H:i:s",
			strtotime($startDateString . ' ' . $start_hour . ':' . $start_min . ' ' . $start_mer));

		$endDate = date_parse($_POST['input_' . $end_date_input['id']]);
		$endDateString = $endDate['year'] . '-' .
			str_pad($endDate['month'], 2, "0", STR_PAD_LEFT) . '-' .
			str_pad($endDate['day'], 2, ")", STR_PAD_LEFT);

		$endTime = $_POST['input_' . $end_time_input['id']];
		$end_hour = str_pad($endTime[0], 2, "0", STR_PAD_LEFT);
		$end_min = str_pad($endTime[1], 2, "0", STR_PAD_LEFT);
		$end_mer = $endTime[2];

		$endDateTimeString = date("Y-m-d H:i:s",
			strtotime($endDateString . ' ' . $end_hour . ':' . $end_min . ' ' . $end_mer));

		return array(
			'start_date' =>  $startDateTimeString,
			'end_date' => $endDateTimeString
		);
	}
}
