<?php
/**
 * Create custom post type for CommunityWire announcement type
 */
function create_posttype() {
    register_post_type( 'movies',
        array(
            'labels' => array(
                'name' => __( 'Movies' ),
                'singular_name' => __( 'Movie' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'movies'),
        )
    );
}

add_action( 'init', 'create_posttype' );