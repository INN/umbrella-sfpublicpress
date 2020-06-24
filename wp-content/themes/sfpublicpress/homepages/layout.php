<?php
include_once get_template_directory() . '/homepages/homepage-class.php';

class SFPublicPress extends Homepage {
	var $name = 'SFPublicPress';
	var $type = 'sfpublicpress';
	var $description = 'The homepage for San Francisco Public Press.';
	var $rightRail = false;

	public function __construct( $options = array() ) {
		$defaults = array(
			'template' => get_stylesheet_directory() . '/homepages/template.php',
			'assets' => array(
				array(
					'homepage',
					get_stylesheet_directory_uri() . '/homepages/assets/css/homepage.css',
					array(),
					filemtime( get_stylesheet_directory() . '/homepages/assets/css/homepage.css' ),
				),
			),
			'prominenceTerms' => array(
				array(
					'name' => __('Homepage Featured', 'largo'),
					'description' => __('If you are using the Newspaper or Carousel optional homepage layout, add this label to posts to display them in the featured area on the homepage.', 'largo'),
					'slug' => 'homepage-featured'
				),
				array(
					'name' => __('Homepage Top Story', 'largo'),
					'description' => __('If you are using a "Big story" homepage layout, add this label to a post to make it the top story on the homepage', 'largo'),
					'slug' => 'top-story'
				),
			),
			'sidebars' => array(
				'Homepage Top Right (The top right area of the homepage, next to the top story)',
				'Homepage Secondary Featured (The area right under the top featured area)',
				'Homepage Bottom (The bottom area of the homepage, after the top and featured stories)',
			),
		);
		$options = array_merge( $defaults, $options );

		if( is_home() ) {
			add_action( 'pre_get_posts', array( 'SFPublicPress', 'sfpp_pre_get_posts__cats_home' ) );
		}

		$this->load( $options );
	}

	/**
	 * Exclude all posts from the `cats_home` category from this homepage
	 */
	public static function sfpp_pre_get_posts__cats_home( $query ) {
		if ( ! is_admin() & is_front_page() && is_home() ) {
			$exclude = of_get_option( 'cats_home' );

			if ( ! empty( $exclude ) ) {
				$query->set( 'category__not_in', $exclude );
			}
		}

		return $query;
	}
}

/**
 * Register this layout with Largo
 */
function sfpublicpress_homepage_layout() {
	register_homepage_layout( 'SFPublicPress' );
}
add_action( 'init', 'sfpublicpress_homepage_layout' );

