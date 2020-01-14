<?php
/**
 * Functions related to meta boxes
 */

/**
 * Output the subtitle metabox.
 *
 * @link Copied from https://github.com/INN/umbrella-publicsource/blob/10f4b08d8bf807cd5d15d26322714b9371e6036a/wp-content/themes/theme-publicsource/inc/metaboxes.php
 */
function subtitle_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	?>
		<label for="subtitle"><?php esc_html_e( 'Subtitle', 'largo' ); ?></label>
		<textarea name="subtitle" id="subtitle" class="widefat" rows="2" cols="20"><?php
			// PHP open/close are at the textarea boundary so we don't prepend/append this with tabs.
			if ( isset( $values['subtitle'] ) ) {
				echo wp_kses_post( $values['subtitle'][0] );
			}
		?></textarea>
		<p><small><?php esc_html_e( 'HTML tags that are allowed in posts are allowed in this area.', 'largo' ); ?></small></p>
	<?php
}
/**
 * Register our subtitle metabox
 *
 */
add_action(
	'init',
	function() {
		largo_add_meta_box(
			'subtitle',
			'Subtitle',
			'subtitle_meta_box_display',
			'post',
			'normal',
			'high'
		);
		largo_register_meta_input( 'subtitle', 'wp_filter_post_kses' );
	}
);