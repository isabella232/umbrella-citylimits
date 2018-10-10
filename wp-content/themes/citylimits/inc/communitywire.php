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
        'supports'              => array( ),
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
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'communitywire', $args );

}
add_action( 'init', 'create_communitywire_post_type', 0 );