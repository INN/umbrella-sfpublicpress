<?php
/**
 * Enqueue specific styles and scripts for SFPP child theme
 */
function sfpublicpress_enqueue_styles(){
    wp_enqueue_style(
		'typekit',
		'https://use.typekit.net/ogu0fkm.css'
	);
	wp_enqueue_style(
		'largo-child-styles',
        get_stylesheet_directory_uri() . '/css/child-style.css',
        array( 'largo-stylesheet', 'typekit' ),
		filemtime( get_stylesheet_directory() . '/css/child-style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'sfpublicpress_enqueue_styles' ); 