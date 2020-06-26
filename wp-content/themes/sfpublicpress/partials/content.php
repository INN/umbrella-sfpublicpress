<?php
/*
 * The default template for displaying content
 *
 * @package Largo
 */
$args = array (
	// post-specific, should probably not be filtered but may be useful
	'post_id' => $post->ID,
	'hero_class' => largo_hero_class( $post->ID, FALSE ),

	// only used to determine the existence of a youtube_url
	'values' => get_post_custom( $post->ID ),

	// this should be filtered in the event of a term-specific archive
	'featured' => has_term( 'homepage-featured', 'prominence' ),

	// $show_thumbnail does not control whether or not the thumbnail is displayed;
	// it controls whether or not the thumbnail is displayed normally.
	'show_thumbnail' => TRUE,
	'show_byline' => TRUE,
	'show_excerpt' => TRUE,
	'in_series' => FALSE,
);

$args = apply_filters( 'largo_content_partial_arguments', $args, get_queried_object() );

extract( $args );

$entry_classes = 'entry-content';

$show_top_tag = largo_has_categories_or_tags();

if ( $featured ) {
	$entry_classes .= ' span10 with-hero';
	$show_thumbnail = FALSE;
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

    <?php
		// Special treatment for posts that are in the Homepage Featured prominence taxonomy term and have thumbnails
		if ( $featured && ( has_post_thumbnail() || $values['youtube_url'] ) ) { ?>
			<header>
				<div class="hero span12 <?php echo $hero_class; ?>">
				<?php
					if( has_post_thumbnail() ){
						echo( '<a href="' . get_permalink() . '" title="' . the_title_attribute( array( 'before' => __( 'Permalink to', 'largo' ) . ' ', 'echo' => false )) . '" rel="bookmark">' );
						the_post_thumbnail( 'full' );
						echo( '</a>' );
					}
				?>
				</div>
			</header>
		<?php } // end Homepage Featured thumbnail block

		echo '<div class="' . $entry_classes . '">';

		if ( largo_has_categories_or_tags() ) {
			largo_maybe_top_term( array( 'post' => get_the_ID() ) );
		}

		if ( $show_thumbnail ) {
			echo '<div class="has-thumbnail '.$hero_class.'"><a href="' . get_permalink() . '">' . get_the_post_thumbnail() . '</a></div>';
		}
	?>

		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( array( 'before' => __( 'Permalink to', 'largo' ) . ' ' ) )?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>

		<?php
            if ( $show_byline ) { ?>
                <div class="byline">
                    <div class="byline-date"><?php echo get_the_date( 'm.d.Y', get_the_ID() ); ?></div>
                    <span class="sep"> | </span>
                    <h5 class="byline"><?php largo_byline( true, true, get_the_ID() ); ?></h5>
                </div>
			<?php }
		?>

		<?php
			if ( $show_excerpt ) {
				largo_excerpt();
			}
		?>

		</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
