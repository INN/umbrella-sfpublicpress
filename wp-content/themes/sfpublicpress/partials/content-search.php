<?php
/*
 * The template for displaying the search partial.
 *
 * @package Largo
 */
$values = get_post_custom( $post->ID );
$entry_classes = 'entry-content';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

	<div class="<?php echo $entry_classes; ?>">

		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( array( 'before' => __( 'Permalink to', 'largo' ) . ' ' ) )?>" rel="bookmark"><?php the_title(); ?></a>
        </h2>
        
        <div class="byline">
            <div class="byline-date"><?php echo get_the_date( 'm.d.Y', get_the_ID() ); ?></div>
            <span class="sep"> | </span>
            <h5 class="byline"><?php largo_byline( true, true, get_the_ID() ); ?></h5>
        </div>

		<?php largo_excerpt( $post, 1, null, null, true, false ); ?>

	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
