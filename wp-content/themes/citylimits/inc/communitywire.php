<?php

// Register Custom Post Type for CommunityWire Announcements
function create_communitywire_post_type() {

    $labels = array(
        'name'                  => 'CommunityWire Announcements',
        'singular_name'         => 'CommunityWire Announcement',
        'menu_name'             => 'CommunityWire Announcements',
        'name_admin_bar'        => 'CommunityWire Announcements',
        'archives'              => 'CommunityWire Announcements Archives',
        'all_items'             => 'All CommunityWire Announcements',
        'add_new_item'          => 'Add New Announcement',
    );
    $rewrite = array(
        'slug'                  => 'communitywire',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'CommunityWire Announcement',
        'description'           => 'Announcements submitted from the community',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'     => 'dashicons-calendar',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite
    );
    register_post_type( 'communitywire', $args );

}
add_action( 'init', 'create_communitywire_post_type', 0 );

add_action( 'gform_after_submission', function ( $entry ) {
    if ( ! function_exists( 'tribe_create_event' ) ) {
        return;
    }
 
    $start_date = rgar( $entry, '4' );
    $start_time = rgar( $entry, '5' );
    $end_date   = rgar( $entry, '6' );
    $end_time   = rgar( $entry, '7' );
 
    $args = array(
        'post_title'            => rgar( $entry, '1' ),
        'post_content'          => rgar( $entry, '2' ),
        'EventAllDay'           => (bool) rgar( $entry, '3.1' ),
        'EventHideFromUpcoming' => (bool) rgar( $entry, '3.2' ),
        'EventShowInCalendar'   => (bool) rgar( $entry, '3.3' ),
        'feature_event'         => (bool) rgar( $entry, '3.4' ),
        'EventStartDate'        => $start_date,
        'EventStartTime'        => $start_time ? Tribe__Date_Utils::reformat( $start_time, 'H:i:s' ) : null,
        'EventEndDate'          => $end_date,
        'EventEndTime'          => $end_time ? Tribe__Date_Utils::reformat( $end_time, 'H:i:s' ) : null,
        'tax_input'    => array(
            Tribe__Events__Main::TAXONOMY => array( 16100 ),
        ),
    );
 
    GFCommon::log_debug( 'gform_after_submission: tribe_create_event args => ' . print_r( $args, 1 ) );
    $event_id = tribe_create_event( $args );
    GFCommon::log_debug( 'gform_after_submission: tribe_create_event result => ' . var_export( $event_id, 1 ) );
} );