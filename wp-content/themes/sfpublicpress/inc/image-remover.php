<?php
/**
 * To remove copies of the post featured image that are found in the post content
 *
 * @link https://github.com/INN/umbrella-sfpublicpress/issues/63
 */

/**
 * SFPP Image Remover
 *
 * A singleton to prevent accidental multiple registration of its hooks
 *
 */
class SFPP_Image_Remover {
	// this is a Singleton class
	private static $instance = null;

	// where the meta is stored
	public static $meta_key = 'sfpp_featured_image_removed_from_post_content';

	// register all the things
	private function __construct() {
		add_action( 'the_content', array( $this, 'filter_the_content' ) );
	}

	/**
	* Primary filter upon the post content
	*
	* This doesn't filter the $content passed to it, because that may have been modified by other filters.
	* Instead, it grabs the raw post_content and filters that instead.
	*
	* @param String $content A post_content, possibly processed by other filters
	* @return that same post_content
	*/
	public static function filter_the_content( $content ) {
		// https://developer.wordpress.org/reference/hooks/the_content/#usage
		if ( ! in_the_loop() || ! is_main_query() || ! is_singular() ) {
			return $content;
		}

		// but what if this is a widget within the main loop?
		$qo = get_queried_object();
		if ( ! is_a( $qo, 'WP_Post' ) || get_the_ID() !== $qo->ID ) {
			return $content;
		}

		// check if it has already been run against this post,
		// in which case we need not bother running the expensive filter.
		$meta = get_post_meta( get_the_ID(), $this::$meta_key, true );
		if ( ! empty( $meta ) ) {
			return $content;
		}

		// check if this post is set to hide its thumbnail,
		// in which case we ought not remove the image.
		$hide_thumbnail = get_post_meta( get_the_ID(), 'featured-image-display', true );
		if ( ! empty( $hide_thumbnail ) ) {
			return $content;
		}

		// check if the post has a thumbnail,
		// because if it hasn't then there's no need to remove the image.
		$thumbnail_id = get_post_thumbnail_id( get_the_id() );
		if ( empty( $thumbnail_id ) ) {
			return $content;
		}

		// @todo are there any other conditions under which we should NOT run the filter?

		// run the filter
		$this::munge( get_the_ID() );

		return $content;
	}

	/**
	 * Wrapper for generating the modified post content and saving it.
	 *
	 * @param Int $id The ID of the post to munge
	 * @return '1'|String|false false if something went wrong; 1 if the post did not need to be edited, HTML if that was removed from the post
	 */
	private static function munge( $id ) {
		$post = get_post( $id );
		// default is to not update the post content, unless it was saved
		$maybe_save = false;
		// don't exonerate a post unless we're sure
		$maybe_clear = false;
		// a place to save what was removed from the post;
		$clipping = '';

		// get the thumbnail ID
		$thumbnail_id = get_post_thumbnail_id( $id );
		// get the image metadata
		$thumbnail_metadata = wp_get_attachment_metadata( $thumbnail_id );

		// get the raw post content
		$working_post_content = $original_post_content = $post->post_content;

		// search
		// replace
		// compare working post content with original post content
		if ( $working_post_content !== $original_post_content ) {
			$maybe_save = true;
			$maybe_clear = $clipping;
		} else {
			$maybe_clear = '1';
		}

		// if the post content has changed, save it
		if ( $maybe_save = true ) {
			// https://developer.wordpress.org/reference/functions/wp_update_post/
			wp_update_post( $post );
		}

		if ( !empty( $maybe_clear ) ) {
			update_post_meta( $id, $this::$meta_key, $maybe_clear );
		}

		return $maybe_clear;
	}

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new SFPP_Image_Remover();
		}
		
		return self::$instance;
	}
}
SFPP_Image_Remover::get_instance();
