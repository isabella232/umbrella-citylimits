<?php
// This map depends on the header js in page-neighborhoods.php

?>
<section class="map">
	<h2>The Neighborhoods</h2>
	<div class="row-fluid">
		<div class="span8">
			<p class="instruction">Click on a neighborhood to get news, documents, opinions and videos about that community.</p>
			<div id="map-container">
				<div id="googft-mapCanvas"></div>
				<div id="map-tooltip"></div>
				<div id="map-key">
					<h3>Key</h3>
					<div class="circle blue"><span>Proposal Anticipated</span></div>
					<div class="circle yellow"><span>Proposal in Approval Process</span></div>
					<div class="circle green"><span>Proposal Approved</span></div>
					<div class="circle red"><span>Proposal Defeated or Withdrawn</span></div>
				</div>
			</div>
		</div>
		<div class="span4 plan-status">
			<h2>Rezoning Status</h2>
			<?php
			$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhoods', 'hide_empty' => false ) );
			$count = 0;
			?>
			<div class="row-fluid">
				<?php foreach ( $neighborhoods as $neighborhood ) : ?>
					<?php
					$status = get_term_meta( $neighborhood->term_id, 'neighborhood-status', true );
					switch ( $status ) {
						case 'red':
							$status_label = 'Proposal Defeated or Withdrawn';
							break;
						case 'yellow':
							$status_label = 'Proposal in Approval Process';
							break;
						case 'green':
							$status_label = 'Proposal Approved';
							break;
						case 'blue':
							$status_label = 'Proposal Anticipated';
							break;
						default:
							$status_label = '';
							break;
					}
					$status_latlon = get_term_meta( $neighborhood->term_id, 'neighborhood-latlon', true );
					?>

					<!-- <?php if ( isset( $title ) ) : ?>
						<h1 class="page-title"><?php echo $title; ?></h1>
					<?php endif; ?> -->

					<div class="zone-w-status" data-status="<?php echo $status_label; ?>" data-color="<?php echo $status; ?>" data-latlon="<?php echo $status_latlon; ?>"><h5><a href="<?php echo get_term_link($neighborhood); ?>" title="<?php echo $status_label; ?>"><div class="circle <?php echo $status; ?>"></div><?php echo $neighborhood->name; ?></a></h5></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>


