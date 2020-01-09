<?php
/**
 * Functions related to modifications of the navigation
 *
 * @since Largo 0.6.4 Last Largo version where this was tested and checked
 * @see partials/nav-*
 */
/**
 * Dequeue Largo's various nav-related functions
 */
function sfpublicpress_nav_enqueues() {
	wp_dequeue_script( 'largo-navigation' );
	wp_register_script(
		'sfpublicpress-navigation',
		get_stylesheet_directory_uri() . '/js/navigation.js',
		array( 'jquery' ),
		filemtime( get_stylesheet_directory() . '/js/navigation.js' ),
	);
	wp_enqueue_script( 'sfpublicpress-navigation' );
}
add_action( 'wp_enqueue_scripts', 'sfpublicpress_nav_enqueues', 20 ); // largo is at 10