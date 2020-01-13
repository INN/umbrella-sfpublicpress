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
		parent::__construct( 'sfpp-projects-widget', esc_html__( 'SFPP Projects List', 'sfpp', $widget_ops ) );
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
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj1'],
						)
					);
				?>
				<small><?php esc_html_e( 'This series will be displayed in the widget with the featured image from its series landing page, if any.', 'sfpp' ); ?></small>
			</p>

			<p>Projects after the first project are displayed as the project name.</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj2' ) ); ?>"><?php esc_html_e( 'Second project:', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
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
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj3'],
						)
					);
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj4' ) ); ?>"><?php esc_html_e( 'Fourth project:', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj4'],
						)
					);
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'proj5' ) ); ?>"><?php esc_html_e( 'Fifth project.', 'sfpp' ); ?></label>
				<?php
					wp_dropdown_categories(
						array(
							'taxonomy' => 'series',
							'orderby' => 'name',
							'class' => 'postform widefat',
							'selected' => $instance['proj5'],
						)
					);
				?>
			</p>

		<?php
	}

	/**
	 * Save the widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		foreach ( array( 'proj1', 'proj2', 'proj3', 'proj4', 'proj5', ) as $key ) {
			if ( ! isset( $new_instance[$key] ) || empty( $new_instance[$key] ) ) {
				$instance[$key] = null;
			} else {
				$instance[$key] = sanitize_key( $new_instance[$key] );
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

		if ( ! empty( $instance['linkurl'] ) && ! empty( $instance['linktext'] ) ) {
			echo '<p class="morelink btn btn-primary"><a href="' . esc_url( $instance['linkurl'] ) . '">' . esc_html( $instance['linktext'] ) . '</a></p>';
		}

		// close the widget
		echo wp_kses_post( $args['after_widget'] );
	}
}
