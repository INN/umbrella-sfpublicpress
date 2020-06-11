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
		add_action( 'the_content', array( 'SFPP_Image_Remover', 'filter_the_content' ) );
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
		$meta = get_post_meta( get_the_ID(), SFPP_Image_Remover::$meta_key, true );
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
		SFPP_Image_Remover::munge( get_the_ID() );

		return $content;
	}

	/**
	 * Wrapper for generating the modified post content and saving it.
	 *
	 * @param Int $id The ID of the post to munge
	 * @return '1'|String|false false if something went wrong; 1 if the post did not need to be edited, HTML if that was removed from the post
	 */
	private static function munge( $id ) {
		/*
		 * Setup
		 */

		// get the raw post content.
		$this_post = get_post( $id );
		$working_post_content = $original_post_content = $this_post->post_content;

		// default is to not update the post content, unless it was saved.
		$maybe_save = false;
		// don't exonerate a post unless we're sure.
		$maybe_clear = false;

		// will contain the selectors by which we remove elements from the page
		$strings_to_remove = array();
		// there's no variable for holding the removed strings;
		// instead we diff the working post content from the original post content.

		// get the thumbnail ID.
		$thumbnail_id = get_post_thumbnail_id( $id );
		// get the post thumbnail image metadata.
		$thumbnail_metadata = wp_get_attachment_metadata( $thumbnail_id );

		/*
		 * Search for things to remove
		 */

		// search by URL match for the original image URL
		// search by URL match for any image size of the original image URL
		// this catches Image Blocks and img tags and old-style captioned images
		$search_array[] = $thumbnail_metadata['file'];

		foreach ( $thumbnail_metadata['sizes'] as $size => $array ) {
			$search_array[] = $array['file'];
		}
		error_log(var_export( $search_array, true));

		foreach ( $search_array as $search_string ) {
			$return = stripos( $working_post_content, $search_string );
			error_log(var_export( $return, true));
			if ( false !== $return ) {
				$strings_to_remove[] = $search_string;
			}
		}

		/*
		 * Remove
		 */

		if ( ! empty( $strings_to_remove ) ) {
			// we have the string to remove
			if ( has_blocks( $id ) ) {
				// do this the block way
				// carefully remove Gutenberg blocks? something with `parse_blocks`
			} else {
				// do this the non-block way
				// then strip resultant empty paragraph tags
			}
		}


		// if we have found things to replace, either regex or spin up a DOMDocument to replace the things
		// @todo need example post IDs

		// here we should update $this_post->post_content with the new version

		// compare working post content with original post content, and save what has changed
		if ( $working_post_content !== $original_post_content ) {
			$maybe_save = true;
			// @todo we may need to append `\n` to both post_contents to get a meaningful diff: https://www.php.net/manual/en/ref.xdiff.php#51588
			$maybe_clear = xdiff_string_diff( $original_post_content . "\n" , $working_post_content . "\n" );
		} else {
			$maybe_clear = '1';
		}


		// if the post content has changed, save it
		if ( true === $maybe_save ) {
			// convert the WP_Post $this_post to an array,
			// using technique from wp_update_post,
			// so that we may set the post_content
			$postarr = get_object_vars( $this_post );
			$postarr = wp_slash( $postarr );
			$postarr['post_content'] = wp_slash( $working_post_content );

			if ( WP_DEBUG ) {
				$log = sprintf(
					'post %1$s: post content to save: %2$s',
					$id,
					var_export( $postarr, true )
				);
				error_log(var_export( $log, true));
			} else {
				// https://developer.wordpress.org/reference/functions/wp_update_post/
				wp_update_post( $this_post );
			}
		}

		if ( !empty( $maybe_clear ) ) {
			if ( WP_DEBUG ) {
				$log = sprintf(
					'post %1$s: $maybe_clear: %2$s',
					$id,
					$maybe_clear
				);
				error_log(var_export( $log, true));
			} else {
				update_post_meta( $id, SFPP_Image_Remover::$meta_key, $maybe_clear );
			}
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
