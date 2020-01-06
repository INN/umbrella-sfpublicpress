<?php
/**
 * Enqueue specific styles and scripts for SFPP child theme
 */
function sfpublicpress_enqueue_styles(){
	wp_enqueue_style(
		'largo-child-styles',
		get_stylesheet_directory_uri() . '/css/child-style.css',
		filemtime( get_stylesheet_directory() . '/css/child-style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'sfpublicpress_enqueue_styles' ); 