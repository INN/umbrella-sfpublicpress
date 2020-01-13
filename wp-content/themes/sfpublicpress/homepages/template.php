<?php
	global $shown_ids;

    $topstory = largo_home_single_top();
	$shown_ids[] = $topstory->ID;

    $featured_stories = largo_home_featured_stories( 3 );
?>
<div class="widget-area clearfix">
    <div class="widget-area-left">
        <?php
            largo_render_template( 'partials/home', 'top', array( 'topstory' => $topstory ) );
        ?>
    </div>
    <div class="widget-area-right">
        <?php
            dynamic_sidebar( 'Homepage Top Right' );
        ?>
    </div>
</div>

<div class="secondary-featured-widget-area clearfix">
    <div class="widget-area">
        <?php
            dynamic_sidebar( 'Homepage Secondary Featured' );
        ?>
    </div>
</div>

<div class="bottom-widget-area clearfix">
    <div class="widget-area">
        <?php
            dynamic_sidebar( 'Homepage Bottom' );
        ?>
    </div>
</div>