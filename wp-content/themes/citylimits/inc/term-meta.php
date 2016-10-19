<?php
/**
 * Functions related to term metadata
 *
 * This file contains the "Status" term meta for the 'neighborhoods' taxonomy
 * It does not do it in a Largo way, but in a post-Wordpress-4.4 Wordpress way.
 */

add_action( 'neighborhoods_edit_form_fields', 'cl_status_edit_status_form', 10, 2 );

/**
 * The form for the "status" term meta of neightborhoods
 */
function cl_status_edit_status_form( $tag, $taxonomy ) {
	$statuses = cl_status_get_statuses();
	$current_status = cl_status_get_status( $tag, $taxonomy );

	?>
		<tr class="form-field term-group">
			<th scope="row">
				<label for="neighborhood-status"><?php _e('Neighborhood Zoning Status', 'citylimits'); ?></label>
			</th>
			<td>

				<select class="postform" id="neighborhood-status" name="neighborhood-status">
					<option value=''><?php _e("No status set", 'citylimits'); ?></option>
					<?php 
						foreach ( $statuses as $id => $values ) {
							printf(
								'<option value="%1$s" ' . selected( $current_status, $id ) . '>%2$s</option>',
								$id,
								__( $values['color'], 'citylimits' )
							);
						}
					?>
				</select>
			</td>
		</tr>

	<?php
}

/**
 * Get the status for a neighborhood
 */
function cl_status_get_status( $term, $taxonomy ) {
	return get_term_meta( $term->term_id, 'neighborhood-status', true );
}

/**
 * Return array of valid statuses
 * @return array of id => array ( 'color' => color, 'label' => label text )
 */
function cl_status_get_statuses() {
	$temporary_statuses = array(
		'red' => array(
			'color' => 'red',
			'label' => 'Red'
		),
		'yellow' => array(
			'color' => 'yellow',
			'label' => 'Yellow'
		),
		'green' => array(
			'color' => 'green',
			'label' => 'Green',
		)
	);

	return $temporary_statuses;
}

function citylimits_update_project_status( $term_id, $tt_id ){
	if ( isset( $_POST['neighborhood-status'] ) && '' !== $_POST['neighborhood-status'] ) {
		$group = sanitize_title( $_POST['neighborhood-status'] );
		update_term_meta( $term_id, 'neighborhood-status', $group );
	}
}
add_action( 'edited_neighborhoods', 'citylimits_update_project_status', 10, 2 );
