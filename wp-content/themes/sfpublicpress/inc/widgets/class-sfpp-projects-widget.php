<?php
/**
 * Classes and the widget for the San Francisco Public Press projects widget.
 *
 * @link https://github.com/INN/umbrella-sfpublicpress/issues/19
 */

/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'sfpp_projects_widget' );
});

/**
 * The widget that displays the projects.
 *
 * Projects are chosen in the widget admin from the list of series.
 * Project series landing page image is used for the first project.
 * Project icon is used for the following project.
 *
 */
class sfpp_projects_widget extends WP_Widget {
	/**
	 * Constructor
	 */
	function __construct() {
		$widget_ops = array(
			'classname' => 'sfpp-projects',
			'description' => esc_html__( 'Display selected projects in a widget.', 'sfpp' ),
		);
		parent::__construct( 'sfpp-projects-widget', esc_html__( 'Projects List (San Francisco Public Press)', 'sfpp', $widget_ops ) );
	}

	/**
	 * The widget form:
	 * - the title
	 * - the big project
	 * - four lesser projects
	 * - more link
	 *
	 * @todo reduce code duplication on 2-5
	 *
	 */
	public function form( $instance ) {
		$defaults = array(
			'title' => __( 'Projects', 'sfpp' ),
			'proj1' => null,
			'proj2' => null,
			'proj3' => null,
			'proj4' => null,
			'proj5' => null,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'largo' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:90%;" type="text" />
			</p>


			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj1' ) ); ?>"><?php esc_html_e( 'Big Project', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'id' => $this->get_field_id( 'proj1' ),
							'name' => $this->get_field_name( 'proj1' ),
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj1'],
						)
					);
				?>
				<small><?php esc_html_e( 'This series will be displayed in the widget with the featured image from its series landing page.', 'sfpp' ); ?></small>
			</p>

			<p>Projects after the first project are displayed as the project name.</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj2' ) ); ?>"><?php esc_html_e( 'Second project:', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'id' => $this->get_field_id( 'proj2' ),
							'name' => $this->get_field_name( 'proj2' ),
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj2'],
						)
					);
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj3' ) ); ?>"><?php esc_html_e( 'Third project:', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'id' => $this->get_field_id( 'proj3' ),
							'name' => $this->get_field_name( 'proj3' ),
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj3'],
							'show_option_none' => '(none)',
							'option_none_value' => '',
						)
					);
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj4' ) ); ?>"><?php esc_html_e( 'Fourth project:', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'id' => $this->get_field_id( 'proj4' ),
							'name' => $this->get_field_name( 'proj4' ),
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj4'],
							'show_option_none' => '(none)',
							'option_none_value' => '',
						)
					);
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj5' ) ); ?>"><?php esc_html_e( 'Fifth project.', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'id' => $this->get_field_id( 'proj5' ),
							'name' => $this->get_field_name( 'proj5' ),
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj5'],
							'show_option_none' => '(none)',
							'option_none_value' => '',
						)
					);
				?>
			</p>

			<p><strong><?php _e( 'More Link', 'largo' ); ?></strong><br /><small><?php _e( 'If you would like to add a more link at the bottom of the widget, add the link text and url here.', 'largo' ); ?></small></p>

			<p>
				<label for="<?php echo $this->get_field_id( 'linktext' ); ?>"><?php _e( 'Link text:', 'largo' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'linktext' ); ?>" name="<?php echo $this->get_field_name( 'linktext' ); ?>" type="text" value="<?php echo esc_attr( $instance['linktext'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'linkurl' ); ?>"><?php _e( 'URL:', 'largo' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'linkurl' ); ?>" name="<?php echo $this->get_field_name( 'linkurl' ); ?>" type="text" value="<?php echo esc_attr( $instance['linkurl'] ); ?>" />
			</p>

		<?php
	}

	/**
	 * Save the widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['linktext'] = sanitize_text_field( $new_instance['linktext'] );
		$instance['linkurl'] = esc_url_raw( $new_instance['linkurl'] );

		foreach ( array( 'proj1', 'proj2', 'proj3', 'proj4', 'proj5', ) as $key ) {
			if ( ! isset( $new_instance[$key] ) || empty( $new_instance[$key] ) ) {
				$instance[$key] = null;
			} else {
				$instance[$key] = (int) sanitize_key( $new_instance[$key] ); // it's expected to be the term ID.
			}
		}

		return $instance;
	}

	/**
	 * Widget output
	 *
	 *
	 * @param Array $args Sidebar arguments.
	 * @param $instance Saved values for this widget from db.
	 */
	public function widget( $args, $instance ) {
		// Add the link to the title.
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );


		/*
		 * here begins the widget output
		 */
		echo wp_kses_post( $args['before_widget'] );


		if ( ! empty( $title ) ) {
			echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title'];
		}

		foreach ( array( 'proj1' ) as $key ) {
			if ( isset( $instance[$key] ) && ! empty( $instance[$key] ) ) {
				$term = get_term( $instance[$key], 'series' );
				// function defined here: https://github.com/INN/largo/blob/512da701664b329f2f92244bbe54880a6e146431/inc/taxonomies.php#L369
				$page = largo_get_series_landing_page_by_series( $term );

				printf(
					'<section class="%1$s">',
					$key
				);

				if ( ! empty( $page ) ) {
					$foo = get_the_post_thumbnail(
						$page[0]->ID,
						'full'
					);
					echo $foo;
				}

				printf(
					'<h3><a href="%1$s">%2$s</a></h3>',
					get_term_link( $term, 'series' ),
					$term->name
				);

				echo wpautop( wp_kses_post( $term->description ) );

				printf(
					'<a href="%1$s" class="view-more-link">%2$s</a>',
					$term->permalink,
					__( 'Explore project', 'sfpp' )
				);

				echo '</section>';
			}
		}

		foreach ( array( 'proj2', 'proj3', 'proj4', 'proj5', ) as $key ) {
			if ( isset( $instance[$key] ) && ! empty( $instance[$key] ) ) {
				$term = get_term( $instance[$key], 'series' );

				printf(
					'<section class="%1$s">',
					$key
				);

				printf(
					'<h3><a href="%1$s">%2$s</a></h3>',
					get_term_link( $term, 'series' ),
					$term->name
				);
				printf(
					'<a href="%1$s" class="view-more-link">%2$s</a>',
					get_term_link( $term, 'series' ),
					__( 'Explore project', 'sfpp' )
				);

				echo '</section>';
			}
		}

		if ( ! empty( $instance['linkurl'] ) && ! empty( $instance['linktext'] ) ) {
			echo '<a class="morelink btn btn-primary" href="' . esc_url( $instance['linkurl'] ) . '">' . esc_html( $instance['linktext'] ) . '</a>';
		}

		// close the widget
		echo wp_kses_post( $args['after_widget'] );
	}
}
