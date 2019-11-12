<?php
/**
 * Block color palette information
 */
/**
 * Define the block color palette
 *
 * If updating these colors, please update less/vars.less. Slugs should match LESS var names.
 *
 * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/
 * @return Array of Arrays
 */
function citylimits_block_colors() {
	return array(
		array(
			'name' => __( 'White', 'citylimits' ),
			'slug' => 'white',
			'color' => 'white',
		),
		array(
			'name' => __( 'Red', 'citylimits' ),
			'slug' => 'red',
			'color' => '#D41313',
		),
		array(
			'name' => __( 'Dark Grey', 'citylimits' ),
			'slug' => 'darkgrey',
			'color' => '#333',
		),
		array(
			'name' => __( 'Grey 1', 'citylimits' ),
			'slug' => 'grey1',
			'color' => '#c3c3c3',
		),
		array(
			'name' => __( 'Grey 2', 'citylimits' ),
			'slug' => 'grey2',
			'color' => '#e2e2e2',
		),
		array(
			'name' => __( 'Grey 3', 'citylimits' ),
			'slug' => 'grey3',
			'color' => '#666',
		),
		array(
			'name' => __( 'Tan', 'citylimits' ),
			'slug' => 'tan',
			'color' => '#f5f2ed',
		),
		array(
			'name' => __( 'Black', 'citylimits' ),
			'slug' => 'black',
			'color' => 'black',
		),
	);
}
add_theme_support( 'editor-color-palette', citylimits_block_colors() );
/**
 * Loop over the defined colors and create classes for them
 *
 * @uses citylimits_block_colors
 * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/
 */
function citylimits_block_colors_styles() {
	$colors = citylimits_block_colors();
	if ( is_array( $colors ) && ! empty( $colors ) ) {
		echo '<style type="text/css" id="citylimits_block_colors_styles">';
		foreach ( $colors as $color ) {
			if (
				is_array( $color )
				&& isset( $color['slug'] )
				&& isset( $color['color'] )
			) {
				printf(
					'.has-%1$s-background-color { background-color: %2$s; }',
					$color['slug'],
					$color['color']
				);
				printf(
					'.has-%1$s-color { color: %2$s; }',
					$color['slug'],
					$color['color']
				);
			}
		}
		echo '</style>';
	}
}
add_action( 'wp_print_styles', 'citylimits_block_colors_styles' );
