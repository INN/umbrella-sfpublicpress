<?php
/**
 * Navigation
 *
 * This is the primary navigation for all pages, and is modified from Largo's partials/nav-main.php
 *
 * Differs in following ways:
 * - output on all pages
 * - removes .nav-shelf class
 *
 * @see inc/navigation.php
 * @since Largo 0.6.4
 * @package Largo
 * @link http://largo.readthedocs.io/users/themeoptions.html#navigation
 */
?>
<nav id="main-nav" class="navbar clearfix">
	<div class="navbar-inner">
		<div class="container">
			<?php
				/*
				 * Before Main Nav Shelf
				 *
				 * Use add_action( 'largo_before_main_nav_shelf', 'function_to_add');
				 *
				 * @link https://codex.wordpress.org/Function_Reference/add_action
				 * @since 0.5.5
				 */
				do_action( 'largo_before_main_nav_shelf' ); 
			?>

			<div class="">
				<h2 class="reveal-when-open">
					<?php esc_html_e( 'Menu', 'sfpublicpress' ); ?>
				</h2>
				<ul class="nav">
					<?php
						/*
						 * Before Main Nav List Items
						 *
						 * Use add_action( 'largo_before_main_nav_list_items', 'function_to_add');
						 *
						 * @link https://codex.wordpress.org/Function_Reference/add_action
						 * @since 0.5.5
						 */
						do_action( 'largo_before_main_nav_list_items' );

						/*
						 * Generate the Main Navigation shown mainly on homepages
						 *
						 * A Bootstrap Navbar is generated from a walker.
						 *
						 * @see inc/nav-menus.php
						 */
						$args = array(
							'theme_location' => 'main-nav',
							'depth' => 0,
							'container' => false,
							'items_wrap' => '%3$s',
							'menu_class' => 'nav',
							'walker' => new Bootstrap_Walker_Nav_Menu()
						);
						largo_nav_menu( $args );

						// $top_args = array(
						// 	'theme_location' => 'global-nav',
						// 	'depth'		 => 1,
						// 	'container' => false,
						// 	'items_wrap' => '%3$s',
						// 	'menu_class' => 'nav',
						// 	'walker' => new Bootstrap_Walker_Nav_Menu()
						// );
						// largo_nav_menu($top_args);

						/*
						 * After Main Nav List Items
						 *
						 * Use add_action( 'largo_after_main_nav_list_items', 'function_to_add');
						 *
						 * @link https://codex.wordpress.org/Function_Reference/add_action
						 * @since 0.5.5
						 */
						do_action( 'largo_after_main_nav_list_items' );
					?>

					<li class="toggle-nav-bar">
						<!-- "hamburger" button (3 bars) to trigger off-canvas navigation -->
						<a class="btn btn-navbar " title="<?php esc_attr_e('More', 'largo'); ?>">
							<div class="bars">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</div>
							<div class="menu-label">
								<?php esc_html_e( 'Menu', 'sfpublicpress' ); ?>
							</div>
						</a>
						<button class="close reveal-when-open" aria-label="<?php esc_attr_e( 'Close', 'sfpublicpress' ); ?>">
							<span class="dashicons dashicons-no-alt"></span>
						</button>
					</li>
				</ul>
				<ul class="mobile-global-nav nav">
					<?php
					$top_args = array(
						'theme_location' => 'global-nav',
						'depth'		 => 1,
						'container' => false,
						'items_wrap' => '%3$s',
						'menu_class' => 'nav',
						'walker' => new Bootstrap_Walker_Nav_Menu()
					);
					largo_nav_menu($top_args);
					?>
				</ul>

			</div>

			<?php 
				/*
				 * After Main Nav Shelf
				 *
				 * Use add_action( 'largo_after_main_nav_shelf', 'function_to_add');
				 *
				 * @link https://codex.wordpress.org/Function_Reference/add_action
				 * @since 0.5.5
				 */
				do_action( 'largo_after_main_nav_shelf' );
			?>

		</div>
	</div>
</nav>