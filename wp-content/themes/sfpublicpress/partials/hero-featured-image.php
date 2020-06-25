<?php
/**
 * Replaces Largo's partials/hero-featured-image so that if the image hasn't a caption, we try its description
 *
 * @link https://github.com/INN/umbrella-sfpublicpress/issues/106
 */

if ( empty( $thumb_meta['caption'] ) ) {
	/*
	 * caption is drawn from the post_excerpt of the attachment:
	 * https://github.com/INN/largo/blob/v0.6.4/inc/featured-media.php#L125-L130
	 * but because of how SFPP was migrated,
	 * many captions ended up in the description.
	 * the description is not the post_excerpt, but the post_content.
	 */

	// get the attachment again.
	$thumb_id = get_post_thumbnail_id( $the_post->ID );
	if ( ! empty( $thumb_id ) ) {
		$thumb_content = get_post( $thumb_id );
		// replace the variable that Largo uses.
		$thumb_meta['caption'] = $thumb_content->post_content;
	}
}

// thus endeth the modifications.
?>
<div class="<?php echo $classes; ?>">
	<?php echo get_the_post_thumbnail($the_post->ID, 'full'); ?>
	<?php if (!empty($thumb_meta)) {
		if (!empty($thumb_meta['credit'])) {
			if (!empty($thumb_meta['credit_url'])) { ?>
				<p class="wp-media-credit"><a href="<?php echo $thumb_meta['credit_url']; ?>"><?php echo $thumb_meta['credit'];
				if (!empty($thumb_meta['organization'])) { ?>/<?php echo $thumb_meta['organization']; } ?></a></p>
			<?php } else { ?>
			<p class="wp-media-credit"><?php echo $thumb_meta['credit'];
				if (!empty($thumb_meta['organization'])) { ?>/<?php echo $thumb_meta['organization']; } ?></p>
			<?php }
		}

		if (!empty($thumb_meta['caption'])) { ?>
			<p class="wp-caption-text"><?php echo $thumb_meta['caption']; ?></p>
		<?php }
	} ?>
</div>
