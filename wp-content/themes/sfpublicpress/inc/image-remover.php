<?php
/**
 * To remove copies of the post featured image that are found in the post content
 *
 * @link https://github.com/INN/umbrella-sfpublicpress/issues/63
 */

/**
* Primary filter upon the post content
*/
function sfpp_image_remover_the_content( $content ) {
	// https://developer.wordpress.org/reference/hooks/the_content/#usage
	if ( ! in_the_loop() || ! is_main_query() || ! is_singular() ) {
		return $content;
	}

	// but what if this is a widget within the main loop?
	$qo = get_queried_object();
	if ( ! is_a( $qo, 'WP_Post' ) || get_the_ID() !== $qo->ID ) {
		return $content;
	}

	error_log(var_export( get_the_ID(), true));

	return $content;
}
add_action( 'the_content', 'sfpp_image_remover_the_content' );
