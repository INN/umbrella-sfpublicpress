<?php
/**
 * San Francisco Public Press Podcasts posts widget and associated functions
 */


/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'SFPublicPress_Podcasts_Widget' );
});
/**
 * The San Francisco Public Press Podcasts widget clss
 *
 * Based on the code-cleanup version of Largo Recent Posts from https://github.com/INN/umbrella-borderzine/pull/67/files
 *
 */
class SFPublicPress_Podcasts_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		$widget_ops = array(
			'classname' => 'sfpublicpress-podcasts',
			'description' => __( 'A way to list podcasts', 'sfpublicpress' ),
		);
		parent::__construct(
			'sfpublicpress-podcasts', // Base ID
			__( 'San Francisco Public Press Podcasts', 'sfpublicpress' ), // Name
			$widget_ops // Args
		);

	}

	/**
	 * Outputs the content of the recent posts widget.
	 *
	 * @param array $args widget arguments.
	 * @param array $instance saved values from databse.
	 * @global $post
	 * @global $shown_ids An array of post IDs already on the page, to avoid duplicating posts
	 * @global $wp_query Used to get posts on the page not in $shown_ids, to avoid duplicating posts
	 */
	public function widget( $args, $instance ) {

		global $post,
			$wp_query, // grab this to copy posts in the main column
			$shown_ids; // an array of post IDs already on a page so we can avoid duplicating posts;

		// Preserve global $post
		$preserve = $post;


		// Add the link to the title.
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$excerpt = isset( $instance['excerpt_display'] ) ? $instance['excerpt_display'] : 'num_sentences';

		$query_args = array (
			'post__not_in'   => get_option( 'sticky_posts' ),
			'posts_per_page' => isset( $instance['num_posts'] ) ? $instance['num_posts'] : 3,
			'post_status'    => 'publish',
			'tax_query'      => array(
				array(
					'taxonomy' => 'prominence',
					'field'    => 'slug',
					'terms'    => 'category-featured',
				),
			)
		);

		if ( isset( $instance['avoid_duplicates'] ) && 1 === $instance['avoid_duplicates'] ) {
			$query_args['post__not_in'] = $shown_ids;
		}
		if ( ! empty( $instance['cat'] ) ) {
			$query_args['cat'] = $instance['cat'];
		}

		/*
		 * here begins the widget output
		 */

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . wp_kses_post( $title ). $args['after_title'];
		}

		$posts = get_posts( $query_args );

		if ( count( $posts ) < $query_args['posts_per_page'] ) {
			$supplemental_query_args = $query_args;
			unset( $supplemental_query_args['tax_query'] ); // remove prominence
			$supplemental_query_args['posts_per_page'] = $query_args['posts_per_page'] - count( $posts );

			$supplemental_posts = get_posts( $supplemental_query_args );

			$posts = array_merge( $posts, $supplemental_posts );
		}

		if ( count( $posts ) > 0 ) {

			$output = '<ul>';

			global $post;
			$preserve = $post;

			foreach ( $posts as $p ) {
				setup_postdata( $p );
				$post = $p;
				$shown_ids[] = get_the_ID();

				// wrap the items in li's.
				$output .= sprintf(
					'<li class="%1$s" >',
					implode( ' ', get_post_class( '', get_the_ID() ) )
				);

				$context = array(
					'instance' => $instance,
					'thumb' => '',
					'podcast' => true,
					'excerpt' => $excerpt,
				);

				ob_start();
				largo_render_template( 'partials/widget', 'content', $context );
				$output .= ob_get_clean();

				// close the item
				$output .= '</li>';


				// cleanup
				wp_reset_postdata();

			} // end foreach

			$post = $preserve;


			// close the ul
			$output .= '</ul>';

			// print all of the items
			echo $output;

		} else {
			printf(
				'<p class="error"><strong>%1$s</strong></p>',
				sprintf(
					// translators: %s is the word this site uses for "posts", like "articles" or "stories". It's a plural noun.
					esc_html__( 'You don\'t have any recent %s', 'largo' ),
					of_get_option( 'posts_term_plural', 'Posts' )
				)
			);
		} // end more featured posts


		if ( ! empty( $instance['linkurl'] ) && ! empty( $instance['linktext'] ) ) {
			echo '<div class="more-container"><a class="morelink btn" href="' . esc_url( $instance['linkurl'] ) . '">' . esc_html( $instance['linktext'] ) . '</a></div>';
		}

		// close the widget
		echo wp_kses_post( $args['after_widget'] );

		// Restore global $post
		wp_reset_postdata();
		$post = $preserve;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['num_posts'] = intval( $new_instance['num_posts'] );
		$instance['avoid_duplicates'] = ! empty( $new_instance['avoid_duplicates'] ) ? 1 : 0;
		$instance['excerpt_display'] = sanitize_key( $new_instance['excerpt_display'] );
		$instance['hide_byline_date'] = true;
		$instance['cat'] = intval( $new_instance['cat'] );
		$instance['linktext'] = sanitize_text_field( $new_instance['linktext'] );
		$instance['linkurl'] = esc_url_raw( $new_instance['linkurl'] );
		return $instance;
	}

	public function form( $instance ) {
		$defaults = array(
			'title' => sprintf(
				// translators: %s is the word this site uses for "posts", like "articles" or "stories". It's a plural noun.
				__( 'Recent %1$s' , 'largo' ),
				of_get_option( 'posts_term_plural', 'Posts' )
			),
			'num_posts' => 3,
			'avoid_duplicates' => '',
			'excerpt_display' => 'num_sentences',
			'cat' => 0,
			'linktext' => '',
			'linkurl' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$duplicates = $instance['avoid_duplicates'] ? 'checked="checked"' : '';
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'largo' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:90%;" type="text" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'num_posts' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'largo' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'num_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'num_posts' ) ); ?>" value="<?php echo esc_attr( $instance['num_posts'] ); ?>" style="width:90%;" type="number" min="3" step="3"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'excerpt_display' ) ); ?>"><?php esc_html_e( 'Excerpt Display', 'largo' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'excerpt_display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'excerpt_display' ) ); ?>" class="widefat" style="width:90%;">
				<option <?php selected( $instance['excerpt_display'], 'custom_excerpt' ); ?> value="custom_excerpt"><?php esc_html_e( 'Use Custom Post Excerpt', 'largo' ); ?></option>
				<option <?php selected( $instance['excerpt_display'], 'none' ); ?> value="none"><?php esc_html_e( 'None', 'largo' ); ?></option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php echo $duplicates; ?> id="<?php echo esc_attr( $this->get_field_id( 'avoid_duplicates' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'avoid_duplicates' ) ); ?>" /> <label for="<?php echo esc_attr( $this->get_field_id( 'avoid_duplicates' ) ); ?>"><?php esc_html_e( 'Avoid showing podcasts here shown earlier on the same page?', 'sfpublicpress' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><?php esc_html_e( 'Limit to category: ', 'largo' ); ?>
				<?php
					wp_dropdown_categories(
						array(
							'name' => $this->get_field_name( 'cat' ),
							'show_option_all' => __( 'None (all categories)', 'largo' ),
							'hide_empty' => 0,
							'hierarchical' => 1,
							'selected' => $instance['cat'],
						)
					);
				?>
			</label>
		</p>

		<p>
			<?php
				esc_html_e( 'If any posts have the "Featured in Category" prominence term on them, those will be presented before other posts in the category.', 'sfpublicpress' );

				echo ' '; 

				// Because wp uses cat names in URLs
				$cat = get_category( $instance['cat'] );
				if ( is_a( $cat, 'WP_Term' ) ) {
					printf(
						'<a href="%1$s">%2$s</a>',
						'/wp-admin/edit.php?prominence=category-featured&category_name=' . $cat-> slug,
						sprintf(
							esc_html__( 'View featured podcasts in the "%1$s" category.', 'sfpublicpress' ),
							$cat->name
						)
					);
				} else {
					echo esc_html__( '(Choose a category and click "Save" to get a link to view featured podcasts in this category.)', 'sfpublicpress' );
				}
			?>
		</p>

		<p>
			<strong><?php esc_html_e( 'More Link', 'largo' ); ?></strong>
			<br />
			<small><?php esc_html_e( 'If you would like to add a more link at the bottom of the widget, add the link text and url here.', 'largo' ); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linktext' ) ); ?>"><?php esc_html_e( 'Link text:', 'largo' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linktext' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linktext' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['linktext'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linkurl' ) ); ?>"><?php esc_html_e( 'URL:', 'largo' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkurl' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkurl' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['linkurl'] ); ?>" />
		</p>

		<?php
	}
}