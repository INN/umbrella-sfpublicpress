<?php
/**
 * Used on the custom homepage layout
 *
 * Expects a variable $topstory being a WP_Post
 */
?>
<article id="top-story" <?php post_class( '', $topstory->ID ); ?> >
	<div class="post-image-top-term-container">
        <?php
            // The top term
            largo_maybe_top_term();
        ?>
		<a class="img" href="<?php echo esc_attr( get_permalink( $topstory ) ); ?>"><?php echo get_the_post_thumbnail( $topstory, 'large' ); ?></a>
    </div>
    <span class="byline"><?php largo_byline( true, true, $topstory ); ?></span>
	<h2><a href="<?php the_permalink( $topstory ); ?>"><?php echo get_the_title( $topstory ); ?></a></h2>
	<div class="inner">
		<div class="excerpt">
            <?php largo_excerpt( $topstory, 2 ); ?>
            <a class="view-more-link" href="<?php the_permalink( $topstory ); ?>">Full Story</a>
		</div>
	</div>
</article>