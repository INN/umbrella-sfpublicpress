<div class="span3 widget-area" role="complementary">
	<?php if ( ! dynamic_sidebar( 'footer-1' ) )
		largo_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1  ) );
	?>
</div>

<div class="span6 widget-area" role="complementary">
	<?php dynamic_sidebar( 'footer-2' ); ?>
</div>

<div class="span3 widget-area" role="complementary">
	<?php if ( ! dynamic_sidebar( 'footer-3' ) ) {
		the_widget( 'WP_Widget_Search', array( 'title' => __('Search This Site', 'largo') ) );
		the_widget( 'WP_Widget_Archives', array( 'title' => __('Browse Archives', 'largo' ), 'dropdown' => 1 ) );
	} ?>
</div>

<div class="span12 widget-area" role="complementary">
    <div class="inner-bottom-widget-area">
        <?php if ( ! dynamic_sidebar( 'footer-4' ) ) { ?>
            <p><?php _e('Please add widgets to this content area in the WordPress admin area under appearance > widgets.', 'largo'); ?></p>
        <?php } ?>
    </div>
</div>