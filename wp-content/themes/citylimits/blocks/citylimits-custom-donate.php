<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package citylimits
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function citylimits_custom_donate_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = get_template_directory() . '/blocks';

	$index_js = 'citylimits-custom-donate/index.js';
	wp_register_script(
		'citylimits-custom-donate-block-editor',
		get_template_directory_uri() . "/blocks/$index_js",
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$index_js" )
	);

	$editor_css = 'citylimits-custom-donate/editor.css';
	wp_register_style(
		'citylimits-custom-donate-block-editor',
		get_template_directory_uri() . "/blocks/$editor_css",
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'citylimits-custom-donate/style.css';
	wp_register_style(
		'citylimits-custom-donate-block',
		get_template_directory_uri() . "/blocks/$style_css",
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'citylimits/citylimits-custom-donate', array(
		'editor_script' => 'citylimits-custom-donate-block-editor',
		'editor_style'  => 'citylimits-custom-donate-block-editor',
		'style'         => 'citylimits-custom-donate-block',
	) );
}
add_action( 'init', 'citylimits_custom_donate_block_init' );
