<?php
/*
 * Sticky Navigation Menu
 *
 * Applied on all pages after a user scrolls past the Main Navigation or affixed
 * to the top of most pages that aren't the home page.
 *
 * This is copied from Largo's partials/nav-sticky.php to override the nav image uploaded to Theme Options and replace it with an SVG logo.
 * @package Largo
 * @link http://largo.readthedocs.io/users/themeoptions.html#navigation
 */

$site_name = ( of_get_option( 'nav_alt_site_name', false ) ) ? of_get_option( 'nav_alt_site_name' ) : get_bloginfo('name'); ?>
 <div class="sticky-nav-wrapper nocontent">
	<div class="sticky-nav-holder">

	<?php
    /*
     * Before Sticky Nav Container
     *
     * Use add_action( 'largo_before_sticky_nav_container', 'function_to_add');
     *
     * @link https://codex.wordpress.org/Function_Reference/add_action
     */
    do_action( 'largo_before_sticky_nav_container' ); ?>

		<div class="sticky-nav-container">
			<nav id="sticky-nav" class="sticky-navbar navbar clearfix">
				<div class="container">
					<ul id="mobile-sticky-nav">
						<?php
							/* Build Main Navigation using Boostrap_Walker_Nav_Menu() */
							$args = array(
								'theme_location' => 'mobile-sticky-menu',
								'depth'		 => 0,
								'container'	 => false,
								'items_wrap' => '%3$s',
								'menu_class' => 'nav',
								'walker'	 => new Bootstrap_Walker_Nav_Menu()
							);
							largo_nav_menu($args);
						?>
					</ul>
					<div class="nav-right">
						<ul id="header-extras">
							<li>
								<!-- "hamburger" button (3 bars) to trigger off-canvas navigation -->
								<a class="btn btn-navbar toggle-nav-bar" title="<?php esc_attr_e('More', 'largo'); ?>">
									<div class="bars">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</div>
									<span>Menu</span>
								</a>
							</li>
						</ul>
					</div>

					<!-- BEGIN DESKTOP MENU -->
					<div class="nav-shelf">
						<div class="close-menu toggle-nav-bar">
							<span class="dashicons dashicons-no-alt" aria-label="close menu"></span>
						</div>
						<div class="expanded-nav-menu">
							<ul class="nav">
							<?php
								/**
								 * Don't display the search in the header if we're on the search page
								 *
								 * @link https://github.com/INN/Largo/pull/1167
								 * @since 0.5.5
								 */
								if ( ! is_search() ) {
								?>
								<li id="sticky-nav-search">
									<form class="form-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
										<div class="input-append">
											<span class="text-input-wrapper">
												<input type="text" placeholder="<?php esc_attr_e('Search', 'largo'); ?>"
													class="input-medium appendedInputButton search-query" value="" name="s" />
											</span>
											<button type="submit" class="search-submit btn"><i class="icon-search" title="<?php esc_attr_e('Search', 'largo'); ?>" role="button"></i></button>
										</div>
									</form>
								</li>
								<?php }

								/* Build Main Navigation using Boostrap_Walker_Nav_Menu() */
								$args = array(
									'theme_location' => 'main-nav',
									'depth'		 => 0,
									'container'	 => false,
									'items_wrap' => '%3$s',
									'menu_class' => 'nav',
									'walker'	 => new Bootstrap_Walker_Nav_Menu()
								);
								largo_nav_menu($args);

								?>
							</ul>
							<ul class="languages-nav">
							<?php

								$args = array(
									'theme_location' => 'languages-menu',
									'depth' => 0,
									'container' => true,
									'items_wrap' => '%3$s',
									'menu_class' => 'languages-nav',
									'walker' => new Bootstrap_Walker_Nav_Menu()
								);
								largo_nav_menu( $args );

							?>
							</ul>
							<div class="special-projects">
								<ul id="special-projects-secondary-menu">
								<?php
									$args = array(
										'theme_location' => 'special-projects-secondary-menu',
										'depth' => 0,
										'container' => true,
										'items_wrap' => '%3$s',
										'menu_class' => 'languages-nav',
										'walker' => new Bootstrap_Walker_Nav_Menu()
									);
									largo_nav_menu( $args );
								?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</nav>
		</div>
	</div>
</div>
