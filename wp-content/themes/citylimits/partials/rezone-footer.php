</div> <!--main-->
</div> <!--page-->

<div id="rezone-footer">
	<div>
		<h2>About This Project</h2>
		
		<section class="rezone-overview row-fluid">
			<div class="span12">
				<?php $series_id = 989786;
					  echo apply_filters('the_content', get_post_field('post_content', $series_id)); 
				?>
			</div>
		</section>

		<div class="row-fluid">
			<div class="span12 plan-status">
				<?php
				$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhoods', 'hide_empty' => false ) );
				$count = 0;
				?>
				<div class="row-fluid">	
					<?php foreach ( $neighborhoods as $neighborhood ) : ?>			
						<?php $status = get_term_meta( $neighborhood->term_id, 'neighborhood-status', true ); ?>
						<div class="zone-w-status"><h5><a href="<?php echo get_term_link($neighborhood); ?>"><div class="circle <?php echo $status; ?>"></div><?php echo $neighborhood->name; ?></a></h5></div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
<!-- </div></div> --> <!-- these divs are closed in the footer -->
