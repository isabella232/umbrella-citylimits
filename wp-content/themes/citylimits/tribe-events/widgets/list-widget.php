<?php
/**
 * Events List Widget Template
 * This is the template for the output of the events list widget.
 * All the items are turned on and off through the widget admin.
 * There is currently no default styling, which is needed.
 *
 * This view contains the filters required to create an effective events list widget view.
 *
 * You can recreate an ENTIRELY new events list widget view by doing a template override,
 * and placing a list-widget.php file in a tribe-events/widgets/ directory
 * within your theme directory, which will override the /views/widgets/list-widget.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters (TO-DO)
 *
 * @version 4.4
 * @return string
 *
 * @package TribeEventsCalendar
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_plural = tribe_get_event_label_plural();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

$posts = tribe_get_list_widget_events();

// Check if any event posts are found.
if ( $posts ) : ?>

	<!-- <ol class="tribe-list-widget"> -->
		<?php
		// Setup the post data for each event.
		foreach ( $posts as $post ) :
			setup_postdata( $post );
			?>
			<div class="type-tribe_events <?php tribe_events_event_classes() ?>"><div class="tribe-mini-calendar-event">
				<?php
				if (
					get_post_thumbnail_id( $post )
				) {
					/**
					 * Fire an action before the list widget featured image
					 */
					do_action( 'tribe_events_list_widget_before_the_event_image' );

					/**
					 * Allow the default post thumbnail size to be filtered
					 *
					 * @param $size
					 */
					$thumbnail_size = apply_filters( 'tribe_events_list_widget_thumbnail_size', 'post-thumbnail' );
					?>
					<div class="tribe-event-image left-align">
						<a href="<?php echo esc_url( tribe_get_event_link() ); ?>" rel="bookmark"><?php the_post_thumbnail( $thumbnail_size ); ?></a>
					</div>
					<?php

					/**
					 * Fire an action after the list widget featured image
					 */
					do_action( 'tribe_events_list_widget_after_the_event_image' );
				}
				?>

				<?php do_action( 'tribe_events_list_widget_before_the_event_title' ); ?>
				<!-- Event Title -->
				<h5 class="tribe-event-title">
					<a href="<?php echo esc_url( tribe_get_event_link() ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h5>

				<?php do_action( 'tribe_events_list_widget_after_the_event_title' ); ?>
				<!-- Event Time -->

				<?php do_action( 'tribe_events_list_widget_before_the_meta' ) ?>
				<!--
				<div class="tribe-event-meta">
					<?php echo tribe_events_event_schedule_details() . ' | ' . tribe_get_venue(); ?>
				</div>
				-->
				<div class="tribe-event-excerpt">
					<?php echo tribe_events_get_the_excerpt(); ?>
				</div>

				<?php do_action( 'tribe_events_list_widget_after_the_meta' ) ?>
			</div></div>
		<?php
		endforeach;
		?>
	<!-- </ol>.tribe-list-widget -->

	<p class="tribe-events-widget-link">
		<a href="<?php echo esc_url( tribe_get_events_link() ); ?>" rel="bookmark"><?php printf( esc_html__( 'View All %s', 'the-events-calendar' ), $events_label_plural ); ?></a>
	</p>

<?php
// No events were found.
else : ?>
	<p><?php printf( esc_html__( 'There are no upcoming %s at this time.', 'the-events-calendar' ), $events_label_plural_lowercase ); ?></p>
<?php
endif;
