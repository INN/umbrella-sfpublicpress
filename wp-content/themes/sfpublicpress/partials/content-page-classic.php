<?php
/**
 * The template for displaying content in the single.php template
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'hnews item' ); ?> itemscope itemtype="https://schema.org/Article">

	<?php do_action( 'largo_before_post_header' ); ?>

	<header>

		<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>
		<?php if ( $subtitle = get_post_meta( $post->ID, 'subtitle', true ) )
			echo '<h2 class="subtitle">' . $subtitle . '</h2>';
		?>

		<?php largo_post_metadata( $post->ID ); ?>

	</header><!-- / entry header -->

	<?php
		do_action( 'largo_after_post_header' );

		largo_hero( null,'' );

		do_action( 'largo_after_hero' );
	?>
    
    <div class="page-content-area span8">
        <div class="entry-content clearfix" itemprop="articleBody">
            <?php do_action('largo_before_page_content'); ?>

            <?php the_content(); ?>
            
            <?php do_action('largo_after_page_content'); ?>
        </div><!-- .entry-content -->

        <footer class="post-meta bottom-meta">
    </div>
    <div class="sidebar-content-area span4">
        <?php get_sidebar(); ?>
    </div>

	</footer><!-- /.post-meta -->

	<?php do_action( 'largo_after_post_footer' ); ?>

</article><!-- #post-<?php the_ID(); ?> -->
