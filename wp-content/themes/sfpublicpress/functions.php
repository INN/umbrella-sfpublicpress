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
		'/inc/image-remover.php',
		// homepage
		'/homepages/layout.php',
		// widgets
		'/inc/widgets/class-sfpp-projects-widget.php',
		'/inc/widgets/sfpublicpress-promo-box.php',
		'/inc/widgets/sfpublicpress-podcasts.php',
		'/inc/widgets/sfpublicpress-subscribe.php',
    );
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

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

		if( function_exists( 'largo_has_avatar' ) && function_exists( 'largo_get_user_avatar_id' ) ) {
			if( largo_has_avatar( $user->user_email ) ) {
				$avatar = wp_get_attachment_image( largo_get_user_avatar_id( $user->ID ), 96, false, array( 'alt' => $user->display_name ) );
			}
		}

    }

	return $avatar;
	
}
add_filter( 'pre_get_avatar' , 'sfpp_largo_custom_avatar', 10 , 3 );

/**
 * Output a donate button from theme options
 * used by default in the global nav area. 
 * Overrides default largo_donate_button
 * 
 * Copied from Largo at https://github.com/INN/largo/blob/512da701664b329f2f92244bbe54880a6e146431/inc/nav-menus.php#L2-L16
 *
 * @since 1.0
 */
function largo_donate_button () {
	if ( $donate_link = of_get_option( 'donate_link' ) ) {
		printf('<button class="donate-btn PicoPlan"><i class="icon-heart"></i>%1$s</button> ',
			esc_html( of_get_option( 'donate_button_text' ) )
		);
	}
}

/**
 * Add support for post excerpts to the 'page' post type
 *
 * @link https://www.wp-code.com/wordpress-snippets/add-excerpts-to-wordpress-pages/
 * @link https://github.com/INN/umbrella-sfpublicpress/issues/121
 */
add_action( 'init', function() {
	add_post_type_support( 'page', 'excerpt' );
});

/**
 * Overwrite largo_gallery_enqueue functionality so that no
 * navis slideshow/lightbox CSS or JS is loaded into the theme
 * 
 * @see https://github.com/INN/largo/blob/590181982d22a5444eb3c5ccca58ea8b56db12f7/inc/enqueue.php#L80-L122
 * @see https://github.com/INN/umbrella-sfpublicpress/issues/139
 * 
 * Will eventually be added back once a design is approved by SFPP
 */
function largo_gallery_enqueue() {
	return;
}

/**
 * Display the custom "shirttail" field under the post content if it exists
 * using the 'largo_after_post_content' hook
 * 
 * Going forward the "shirttail" content will be added into posts via blocks, 
 * but this is a workaround for older posts where this field was imported
 * 
 * @see https://github.com/INN/umbrella-sfpublicpress/issues/144
 */
function sfpp_after_post_content() {
	
	global $post;

	if( get_post_meta( $post->ID, 'wpcf-shirttail' ) ) {
		printf(
			'<div class="wpcf-shirttail">
				<hr/>
				%1$s
			</div>',
			get_post_meta( $post->ID, 'wpcf-shirttail', true )
		);
	}

}
add_action( 'largo_after_post_content', 'sfpp_after_post_content' );

/**
 * Creates a redirect using the redirection plugin if a page
 * is loaded that starts with /category/ or /tag/ and ends up throwing a 404.
 * 
 * @required https://wordpress.org/plugins/redirection/
 * @see https://redirection.me/developer/rest-api/#api-Redirect-CreateRedirect
 * @see https://github.com/INN/umbrella-sfpublicpress/issues/97
 */
function sfpp_category_tag_404_override() {

    global $wp_query;

    if( is_404() && ! is_admin() ) {

		// find out if the user is trying to load a term archive
		$maybe_term = explode( '/', $_SERVER['REQUEST_URI'] );

		// if category or tag is the 1st index in the uri, we can continue
		if( 'category' == $maybe_term[1] || 'tag' == $maybe_term[1] ) {

			// make sure all of our expected redirection stuff is available to use
			if( class_exists( 'Red_Item' ) && file_exists( WP_PLUGIN_DIR . '/redirection/models/group.php' ) ) {

				// include file that's needed because ??? 
				// plugin author states it needs included: https://wordpress.org/support/topic/red_itemcreate-throws-error/
				include_once WP_PLUGIN_DIR . '/redirection/models/group.php';

				// let's make sure the user doesn't actually experience a 404
				status_header( 301 );
				$wp_query->is_404=false;

				// set up all of our relevant info we need to creat the redirect in the plugin
				$redirect_info = array(
					'url' => $_SERVER['REQUEST_URI'],
					'action_code' => 301,
					'match_data' => array(
						'flag_query' => 'pass'
					),
					'action_data' => array( 
						'url' => '/categories/' 
					),
					'action_type' => 'url',
					'match_type' => 'url',
					'title' => $maybe_term[2],
					'group_id' => 1
				);

				// actually create the redirect
				Red_Item::create( $redirect_info );

				// redirect to where we want to go
				wp_safe_redirect( '/categories/' );
			
			}

		}
		
	}
	
}
add_filter('template_redirect', 'sfpp_category_tag_404_override' );