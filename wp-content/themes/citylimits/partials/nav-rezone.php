<nav id="main-nav" class="navbar clearfix">
	<div class="navbar-inner">
		<div class="container">
			<div class="nav-shelf">
				<?php
					largo_nav_menu( array(
						'theme_location' => 'zonein-menu',
						'depth' => 0,
						'container' => false,
						'items_wrap' => '%3$s',
						'menu_class' => 'nav',
						'walker' => new Bootstrap_Walker_Nav_Menu()
					) );
				?>
			</div>
		</div>
	</div>
</nav>
