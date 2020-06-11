<?php

/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'sfpublicpress_promo_box_widget' );
});

/*
Plugin: San Francisco Pulic Press Promo Box Widget
Description: A simple image widget that uses the native WordPress media manager to add image widgets to your site, adapted from Modern Tribe's Image Widget
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

/**
 * sfpublicpress_Image_Widget class
 **/
class sfpublicpress_promo_box_widget extends WP_Widget {

	// not really sure what this means?
	const VERSION = '4.0.7';

	const CUSTOM_IMAGE_SIZE_SLUG = 'sfpublicpress_promo_box_widget_custom';

	/**
	 * sfpublicpress Image Widget constructor
	 *
	 * @author Modern Tribe, Inc.
	 */
	function __construct() {
		$widget_ops = array(
			'classname' => 'widget_sfpublicpress-promo-box',
			'description'=> __('Widget to display promo boxes.', 'sfpublicpress')
		);

		parent::__construct( 'sfpublicpress-promo-box', __('San Francisco Public Press Promo Box Widget', 'sfpublicpress'), $widget_ops );
	}

	/**
	 * Widget frontend output
	 *
	 * @param array $args
	 * @param array $instance
	 * @author Modern Tribe, Inc.
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, self::get_defaults() );
		if ( !empty( $instance['imageurl'] ) || !empty( $instance['attachment_id'] ) ) {

            $title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Promo Box Title', 'sfpublicpress') : $instance['title'], $instance, $this->id_base);
            $instance['link'] = apply_filters( 'image_widget_image_link', esc_url( $instance['link'] ), $args, $instance );
			$instance['linktarget'] = apply_filters( 'image_widget_image_link_target', esc_attr( $instance['linktarget'] ), $args, $instance );

			if ( !defined( 'IMAGE_WIDGET_COMPATIBILITY_TEST' ) ) {
				$instance['attachment_id'] = ( $instance['attachment_id'] > 0 ) ? $instance['attachment_id'] : $instance['image'];
				$instance['attachment_id'] = apply_filters( 'image_widget_image_attachment_id', abs( $instance['attachment_id'] ), $args, $instance );
				$instance['size'] = apply_filters( 'image_widget_image_size', esc_attr( $instance['size'] ), $args, $instance );
			}
			$instance['imageurl'] = apply_filters( 'image_widget_image_url', esc_url( $instance['imageurl'] ), $args, $instance );

			//output the widget
            echo $args['before_widget'];
            
            echo '<div class="inner-widget-container">';
            echo '<div class="promo-box-content">';

            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            echo '<p>' . sanitize_text_field( $instance['text'] ) . '</p>';
            echo '</div>';

            echo '<div class="promo-box-image">';
            echo self::get_image_html( $instance, true );
            echo '</div>';
            echo '</div>';

			echo $args['after_widget'];
		}
	}

	/**
	 * Update widget options
	 *
	 * @param object $new_instance Widget Instance
	 * @param object $old_instance Widget Instance
	 * @return object
	 * @author Modern Tribe, Inc.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        $new_instance = wp_parse_args( (array) $new_instance, self::get_defaults() );
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['text'] = sanitize_text_field( $new_instance['text'] );
		$instance['link'] = esc_url_raw( $new_instance['link'] );

		// Reverse compatibility with $image, now called $attachement_id
		if ( !defined( 'IMAGE_WIDGET_COMPATIBILITY_TEST' ) && $new_instance['attachment_id'] > 0 ) {
			$instance['attachment_id'] = abs( $new_instance['attachment_id'] );
		} elseif ( $new_instance['image'] > 0 ) {
			$instance['attachment_id'] = $instance['image'] = abs( $new_instance['image'] );
		}
		$instance['imageurl'] = $new_instance['imageurl']; // deprecated
		$instance['aspect_ratio'] = $this->get_image_aspect_ratio( $instance );

		return $instance;
	}

	/**
	 * Form UI
	 *
	 * @param object $instance Widget Instance
	 * @author Modern Tribe, Inc.
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, self::get_defaults() );
        $id_prefix = $this->get_field_id(''); ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'sfpublicpress'); ?></label>
        <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:90%;" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e('Text:', 'sfpublicpress'); ?></label><br/>
        <textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" style="width:90%;"><?php echo esc_attr( $instance['text'] ); ?></textarea>
    </p>
	<div class="uploader">
		<input type="submit" class="button" name="<?php echo $this->get_field_name('uploader_button'); ?>" id="<?php echo $this->get_field_id('uploader_button'); ?>" value="<?php _e('Select an Image', 'largo'); ?>" onclick="imageWidget.uploader( '<?php echo $this->id; ?>', '<?php echo $id_prefix; ?>' ); return false;" />
		<div class="largo_preview" id="<?php echo $this->get_field_id('preview'); ?>">
			<?php echo $this->get_image_html($instance, false); ?>
		</div>
		<input type="hidden" id="<?php echo $this->get_field_id('attachment_id'); ?>" name="<?php echo $this->get_field_name('attachment_id'); ?>" value="<?php echo abs($instance['attachment_id']); ?>" />
		<input type="hidden" id="<?php echo $this->get_field_id('imageurl'); ?>" name="<?php echo $this->get_field_name('imageurl'); ?>" value="<?php echo $instance['imageurl']; ?>" />
	</div>
	<br clear="all" />
	<div id="<?php echo $this->get_field_id('fields'); ?>" <?php if ( empty($instance['attachment_id']) && empty($instance['imageurl']) ) { ?>style="display:none;"<?php } ?>>
		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link', 'sfpublicpress'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['link'])); ?>" /><br />
		<select name="<?php echo $this->get_field_name('linktarget'); ?>" id="<?php echo $this->get_field_id('linktarget'); ?>">
			<option value="_self"<?php selected( $instance['linktarget'], '_self' ); ?>><?php _e('Stay in Window', 'sfpublicpress'); ?></option>
			<option value="_blank"<?php selected( $instance['linktarget'], '_blank' ); ?>><?php _e('Open New Window', 'sfpublicpress'); ?></option>
		</select></p>
	</div>
<?php
	}

	/**
	 * Admin header css
	 *
	 * @author Modern Tribe, Inc.
	 */
	function admin_head() {
		?>
	<style type="text/css">
		.uploader input.button {
			width: 100%;
			height: 34px;
			line-height: 33px;
		}
		.largo_preview .aligncenter {
			display: block;
			margin-left: auto !important;
			margin-right: auto !important;
		}
		.largo_preview {
			overflow: hidden;
			max-height: 300px;
		}
		.largo_preview img {
			margin: 10px 0;
		}
	</style>
	<?php
	}

	/**
	 * Render an array of default values.
	 *
	 * @return array default values
	 */
	private static function get_defaults() {

		$defaults = array(
            'title' => '',
            'text' => '',
			'link' => '',
			'linktarget' => '',
			'image' => 0, // reverse compatible - now attachement_id
			'imageurl' => '', // reverse compatible.
		);

		if ( !defined( 'IMAGE_WIDGET_COMPATIBILITY_TEST' ) ) {
			$defaults['size'] = self::CUSTOM_IMAGE_SIZE_SLUG;
			$defaults['attachment_id'] = 0;
		}

		return $defaults;
	}

	/**
	 * Render the image html output.
	 *
	 * @param array $instance
	 * @param bool $include_link will only render the link if this is set to true. Otherwise link is ignored.
	 * @return string image html
	 */
	private function get_image_html( $instance, $include_link = true ) {

		// Backwards compatible image display.
		if ( $instance['attachment_id'] == 0 && $instance['image'] > 0 ) {
			$instance['attachment_id'] = $instance['image'];
		}

		$output = '';

		if ( $include_link && !empty( $instance['link'] ) ) {
			$attr = array(
				'href' => $instance['link'],
				'target' => $instance['linktarget'],
				'class' => 	$this->widget_options['classname'].'-image-link',
				'title' => ( !empty( $instance['alt'] ) ) ? $instance['alt'] : $instance['title'],
			);
			if ( isset( $instance['track'] ) && $instance['track'] ) $attr['class'] .= ' image-click-track';
			$attr = apply_filters('image_widget_link_attributes', $attr, $instance );
			$attr = array_map( 'esc_attr', $attr );
			$output = '<a';
			foreach ( $attr as $name => $value ) {
				$output .= sprintf( ' %s="%s"', $name, $value );
			}
			$output .= '>';
		}

		$size = $this->get_image_size( $instance );
		if ( is_array( $size ) ) {
			$instance['width'] = $size[0];
			$instance['height'] = $size[1];
		} elseif ( !empty( $instance['attachment_id'] ) ) {
			$image_details = wp_get_attachment_image_src( $instance['attachment_id'], $size );
			if ($image_details) {
				$instance['imageurl'] = $image_details[0];
				$instance['width'] = $image_details[1];
				$instance['height'] = $image_details[2];
			}
		}
		$instance['width'] = abs( $instance['width'] );
		$instance['height'] = abs( $instance['height'] );

		$attr = array();
		$attr['alt'] = $instance['title'];
		if (is_array($size)) {
			$attr['class'] = 'attachment-'.join('x',$size);
		} else {
			$attr['class'] = 'attachment-'.$size;
		}
		$attr['style'] = '';
		if (!empty($instance['align']) && $instance['align'] != 'none') {
			$attr['class'] .= " align{$instance['align']}";
		}
		$attr = apply_filters( 'image_widget_image_attributes', $attr, $instance );

		// If there is an imageurl, use it to render the image. Eventually we should kill this and simply rely on attachment_ids.
		if ( !empty( $instance['imageurl'] ) ) {
			// If all we have is an image src url we can still render an image.
			$attr['src'] = $instance['imageurl'];
			$attr = array_map( 'esc_attr', $attr );
			$hwstring = image_hwstring( $instance['width'], $instance['height'] );
			$output .= rtrim("<img $hwstring");
			foreach ( $attr as $name => $value ) {
				$output .= sprintf( ' %s="%s"', $name, $value );
			}
			$output .= ' />';
		} elseif( abs( $instance['attachment_id'] ) > 0 ) {
			$output .= wp_get_attachment_image($instance['attachment_id'], $size, false, $attr);
		}

		if ( $include_link && !empty( $instance['link'] ) ) {
			$output .= '</a>';
		}

		return $output;
	}

	/**
	 * Assesses the image size in case it has not been set or in case there is a mismatch.
	 *
	 * @param $instance
	 * @return array|string
	 */
	private function get_image_size( $instance ) {
		if ( !empty( $instance['size'] ) && $instance['size'] != self::CUSTOM_IMAGE_SIZE_SLUG ) {
			$size = $instance['size'];
		} elseif ( isset( $instance['width'] ) && is_numeric($instance['width']) && isset( $instance['height'] ) && is_numeric($instance['height']) ) {
			$size = array(abs($instance['width']),abs($instance['height']));
		} else {
			$size = 'full';
		}
		return $size;
	}

	/**
	 * Establish the aspect ratio of the image.
	 *
	 * @param $instance
	 * @return float|number
	 */
	private function get_image_aspect_ratio( $instance ) {
		if ( !empty( $instance['aspect_ratio'] ) ) {
			return abs( $instance['aspect_ratio'] );
		} else {
			$attachment_id = ( !empty($instance['attachment_id']) ) ? $instance['attachment_id'] : $instance['image'];
			if ( !empty($attachment_id) ) {
				$image_details = wp_get_attachment_image_src( $attachment_id, 'full' );
				if ($image_details) {
					return ( $image_details[1]/$image_details[2] );
				}
			}
		}
	}
}
