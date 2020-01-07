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
 * 
 */
function sfpublicpress_block_colors() {
	return array(
		array(
			'name' => __( 'White', 'sfpublicpress' ),
			'slug' => 'white',
			'color' => 'white',
        ),
        array(
			'name' => __( 'Black', 'sfpublicpress' ),
			'slug' => 'black',
			'color' => '#000',
        ),
		array(
			'name' => __( 'Gray', 'sfpublicpress' ),
			'slug' => 'gray',
			'color' => '#534B47',
		),
		array(
			'name' => __( 'Dark Gray', 'sfpublicpress' ),
			'slug' => 'darkgray',
			'color' => '#3D3D40',
		),
		array(
			'name' => __( 'Light Gray', 'sfpublicpress' ),
			'slug' => 'lightgray',
			'color' => '#D0D0D0',
        ),
		array(
			'name' => __( 'Brown', 'sfpublicpress' ),
			'slug' => 'brown',
			'color' => '#77726E',
		),
        array(
			'name' => __( 'Light Brown', 'sfpublicpress' ),
			'slug' => 'lightbrown',
			'color' => '#CEC1B9',
        ),
        array(
			'name' => __( 'Orange', 'sfpublicpress' ),
			'slug' => 'orange',
			'color' => '#F57A1F',
        ),
        array(
			'name' => __( 'Dark Orange', 'sfpublicpress' ),
			'slug' => 'darkorange',
			'color' => '#B85A14',
        ),
        array(
			'name' => __( 'Light Orange', 'sfpublicpress' ),
			'slug' => 'lightorange',
			'color' => '#F9A65D',
        ),
        array(
			'name' => __( 'Blue', 'sfpublicpress' ),
			'slug' => 'blue',
			'color' => '#80BFEB',
        ),
        array(
			'name' => __( 'Green', 'sfpublicpress' ),
			'slug' => 'green',
			'color' => '#53B99D',
        ),
        array(
			'name' => __( 'Dark Green', 'sfpublicpress' ),
			'slug' => 'darkgreen',
			'color' => '#478070',
        ),
	);
}
add_theme_support( 'editor-color-palette', sfpublicpress_block_colors() );
/**
 * Loop over the defined colors and create classes for them
 *
 * @uses sfpublicpress_block_colors
 * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/
 */
function sfpublicpress_block_colors_styles() {
	$colors = sfpublicpress_block_colors();
	if ( is_array( $colors ) && ! empty( $colors ) ) {
		echo '<style type="text/css" id="sfpublicpress_block_colors_styles">';
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
add_action( 'wp_print_styles', 'sfpublicpress_block_colors_styles' );