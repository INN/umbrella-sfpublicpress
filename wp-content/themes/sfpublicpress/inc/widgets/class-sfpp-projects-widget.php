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
}
