<?php

// the thumbnail image (if we're using one)
if ( $podcast === true ) {
    printf(
        '<p class="mediaplay"><a href="%1$s">%2$s <span class="visuallyhidden">%3$s</span></a></p>',
        get_permalink(),
        '<span class="encircle">►</span>',
        __( 'Decorative play icon links to post: ', 'sfpublicpress') . get_the_title()
    );
} elseif ($thumb == 'small') {
	$img_location = ! empty( $instance['image_align'] ) ? $instance['image_align'] : 'left';
	$img_attr = array( 'class' => $img_location . '-align' );
	$img_attr['class'] .= " attachment-small";
	?>
		<div class="post-image-top-term-container">
            <?php
                // The top term
                if ( isset( $instance['show_top_term'] ) && $instance['show_top_term'] == 1 && largo_has_categories_or_tags() ) {
                    largo_maybe_top_term();
                }
            ?>
            <a href="<?php echo get_permalink(); ?>"><?php echo get_the_post_thumbnail( get_the_ID(), '60x60', $img_attr); ?></a>
        </div>
	<?php
} elseif ($thumb == 'medium') {
	$img_location = ! empty( $instance['image_align'] ) ? $instance['image_align'] : 'left';
	$img_attr = array('class' => $img_location . '-align');
	$img_attr['class'] .= " attachment-thumbnail";
    ?>
        <div class="post-image-top-term-container">
            <?php
                // The top term
                if ( isset( $instance['show_top_term'] ) && $instance['show_top_term'] == 1 && largo_has_categories_or_tags() ) {
                    largo_maybe_top_term();
                }
            ?>
            <a href="<?php echo get_permalink(); ?>"><?php echo get_the_post_thumbnail( get_the_ID(), 'post-thumbnail', $img_attr); ?></a>
        </div>
	<?php
} elseif ($thumb == 'large') {
	$img_attr = array();
	$img_attr['class'] = " attachment-two-third-full";
    ?>
        <div class="post-image-top-term-container">
            <?php
                // The top term
                if ( isset( $instance['show_top_term'] ) && $instance['show_top_term'] == 1 && largo_has_categories_or_tags() ) {
                    largo_maybe_top_term();
                }
            ?>
            <a href="<?php echo get_permalink(); ?>"><?php echo get_the_post_thumbnail( get_the_ID(), 'two-third-full', $img_attr); ?></a>
        </div>
	<?php
}
?>

<div class="inner-widget-content">
<?php
// the headline and optionally the post-type icon

// byline on posts
if ( isset( $instance['show_byline'] ) && $instance['show_byline'] == true && $podcast !== true ) {
	$hide_byline_date = ( ! empty( $instance['hide_byline_date'] ) ) ? $instance['hide_byline_date'] : true;
	?>
		<span class="byline"><?php echo largo_byline( false, $hide_byline_date, get_the_ID() ); ?></span>
	<?php
}

if( $podcast === true ) {
    if( largo_top_term( $options = array( 'echo' => FALSE ) ) ) {
        echo '<div class="podcast-top-term-date">' . largo_top_term( $options = array( 'echo' => FALSE ) ) . ' | ' . get_the_date( 'M d Y' ) . ' | ' . get_the_time() . '</div>';
    } else {
        echo '<div class="podcast-top-term-date">' . get_the_date( 'M d Y' ) . ' | ' . get_the_time() . '</div>';
    }
}

?>
<h5>
	<a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?>
	<?php
		if ( isset( $instance['show_icon'] ) && $instance['show_icon'] == true ) {
			post_type_icon();
		}
	?>
	</a>
</h5>

<?php

// the excerpt
if ( $excerpt == 'num_sentences' ) {
	$num_sentences = ( ! empty( $instance['num_sentences'] ) ) ? $instance['num_sentences'] : 2;
	?>
		<p><?php echo largo_trim_sentences( get_the_content(), $num_sentences ); ?></p>
	<?php } elseif ( $excerpt == 'custom_excerpt' ) { ?>
		<p><?php echo get_the_excerpt(); ?></p>
	<?php
}

?>

<a class="view-more-link" href="<?php the_permalink( $topstory ); ?>">Full Story</a>
</div>