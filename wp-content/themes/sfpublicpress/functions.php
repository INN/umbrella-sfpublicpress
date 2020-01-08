<?php
define( 'SHOW_GLOBAL_NAV', false );
define( 'SHOW_CATEGORY_RELATED_TOPICS', false );
/**
 * Include theme files
 *
 * Based off of how Largo loads files: https://github.com/INN/Largo/blob/master/functions.php#L358
 *
 * 1. hook function Largo() on after_setup_theme
 * 2. function Largo() runs Largo::get_instance()
 * 3. Largo::get_instance() runs Largo::require_files()
 *
 * This function is intended to be easily copied between child themes, and for that reason is not prefixed with this child theme's normal prefix.
 *
 * @link https://github.com/INN/Largo/blob/master/functions.php#L145
 */
function largo_child_require_files() {

	$includes = array(
		'/inc/enqueue.php',
		'/inc/block-color-palette.php',
		'/inc/navigation.php',
    );
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
		$includes[] = '/inc/gravityforms/events-calendar.php';
    }
    
	foreach ( $includes as $include ) {
		require_once( get_stylesheet_directory() . $include );
    }
    
}
add_action( 'after_setup_theme', 'largo_child_require_files' );