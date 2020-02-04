<?php

/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'sfpublicpress_subscribe_widget' );
});

/*
 * SFPublicPress Subscribe Widget
 */
class sfpublicpress_subscribe_widget extends WP_Widget {

	function __construct() {
		$widget_opts = array(
			'classname' => 'sfpublicpress-subscribe',
			'description'=> __('Call-to-action to sign up for the newsletter.', 'sfpublicpress')
		);
		parent::__construct( 'sfpublicpress-subscribe-widget', __('San Francisco Public Press Subscribe Widget', 'sfpublicpress'),$widget_opts);
	}

	function widget( $args, $instance ) {

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Subscribe to Our Newsletter', 'sfpublicpress') : $instance['title'], $instance, $this->id_base);

        echo $args['before_widget'];
        
        echo '<div class="inner-widget-container">';
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		?>
			<p><?php echo esc_html( $instance['cta_text'] ); ?></p>
			<div class="newsletter-form">
                <?php echo $instance['form_embed']; ?>
            </div>
		<?php

        echo '</div>';
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['cta_text'] = sanitize_text_field( $new_instance['cta_text'] );
		$instance['form_embed'] = $new_instance['form_embed'];
		return $instance;
	}
	function form( $instance ) {
		if ( of_get_option( 'form_embed' ) )
			$d = esc_attr( of_get_option( 'form_embed' ) );
		$defaults = array(
			'title' 			=> __('Subscribe to Our Newsletter', 'sfpublicpress'),
			'cta_text' 			=> __('Subscribe to our newsletter.', 'sfpublicpress'),
			'form_embed' 		=> $donate_btn_text,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'sfpublicpress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:90%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cta_text' ); ?>"><?php _e('Call-to-Action Text:', 'sfpublicpress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'cta_text' ); ?>" name="<?php echo $this->get_field_name( 'cta_text' ); ?>" value="<?php echo esc_attr( $instance['cta_text'] ); ?>" style="width:90%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'form_embed' ); ?>"><?php _e('Newsletter Form:', 'sfpublicpress'); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'form_embed' ); ?>" name="<?php echo $this->get_field_name( 'form_embed' ); ?>" style="width:90%;"><?php echo esc_attr( $instance['form_embed'] ); ?></textarea>
		</p>

		<?php
	}
}
