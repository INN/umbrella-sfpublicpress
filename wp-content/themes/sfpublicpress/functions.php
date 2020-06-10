<?php
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
		'/inc/metaboxes.php',
		// homepage
		'/homepages/layout.php',
		// widgets
		'/inc/widgets/class-sfpp-projects-widget.php',
		'/inc/widgets/sfpublicpress-promo-box.php',
		'/inc/widgets/sfpublicpress-podcasts.php',
		'/inc/widgets/sfpublicpress-subscribe.php',
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

/**
 * Filter the get_avatar function to allow it to return the custom
 * largo_avatar metafield value if it exists.
 * 
 * @see: https://github.com/INN/largo/issues/1864
 * 
 * @param string $avatar HTML for the user's avatar
 * @param mixed $id_or_email The (gr)avatar to retrieve
 * @param array $args Arguements passed to get_avatar_url()
 * 
 * @return string $avatar HTML for the user's avatar
 */
function sfpp_largo_custom_avatar( $avatar, $id_or_email, $args ) {

    $user = false;

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {

            $id = (int) $id_or_email->user_id;
			$user = get_user_by( 'id' , $id );
			
        }

    } else {

		$user = get_user_by( 'email', $id_or_email );	
		
    }

    if ( $user && is_object( $user ) ) {

		if( function_exists( 'largo_get_user_avatar_id' ) ) {
			$avatar = wp_get_attachment_image( largo_get_user_avatar_id( $user->ID ), 96, false, array( 'alt' => $user->display_name ) );
		}

    }

	return $avatar;
	
}
add_filter( 'pre_get_avatar' , 'sfpp_largo_custom_avatar', 10 , 3 );