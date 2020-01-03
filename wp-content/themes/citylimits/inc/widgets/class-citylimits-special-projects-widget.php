<?php

/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'citylimits_special_projects_widget' );
});

/*
 * List all of the terms in a custom taxonomy
 */
class citylimits_special_projects_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' 	=> 'citylimits-special-projects',
			'description' 	=> __('Display special projects', 'citylimits')
		);
		parent::__construct( 'citylimits-special-projects-widget', __('City Limits Special Projects', 'citylimits'), $widget_ops);
	}

	function widget( $args, $instance ) {
		global $post;
		// Preserve global $post
		$preserve = $post;

		// instance: num, series (id), title

		//get the posts
		$series_1_posts = citylimits_get_series_posts( $instance['series_1'], $instance['num'], 'featured, DESC' );
		$series_2_posts = citylimits_get_series_posts( $instance['series_2'], $instance['num'], 'featured, DESC' );
		$series_3_posts = citylimits_get_series_posts( $instance['series_3'], $instance['num'], 'featured, DESC' );

		$series_arr = [$series_1_posts, $series_2_posts, $series_3_posts];

		if ( empty( $series_1_posts ) && empty( $series_2_posts ) && empty( $series_3_posts ) ) return; //output nothing if no posts found

		$widget_title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		echo '<div class="citylimits-special-projects-container">';

		if ( ! empty( $widget_title ) ) echo $args['before_title'] . $widget_title . $args['after_title'];

		$series_counter = 0;

		echo '<div class="citylimits-special-projects-inner-container">';

		foreach( $series_arr as $series ) {

			$series_counter++;

			if( $series->have_posts() ) {
				
				echo '<div class="citylimits-special-project">';

				$thumbnail = '';
				$series_id = $instance['series_'.$series_counter];
				$title_link = get_term_link( (int) $instance['series_'.$series_counter], 'series' );
				$term = get_term( $instance['series_'.$series_counter], 'series' );
				$title = apply_filters( 'widget_title', $term->name, $instance, $this->id_base );
				$excerpt = isset( $instance['excerpt_display'] ) ? $instance['excerpt_display'] : 'num_sentences';
				
				// get the term meta post so we can grab the featured image
				$term_meta_post = largo_get_term_meta_post( 'series', $series_id );

				if( has_post_thumbnail( $term_meta_post ) ){

					$thumbnail = get_the_post_thumbnail_url( $term_meta_post, 'rect_thumb_half' );

					echo '<a class="citylimits-special-project-image" href="'.$title_link.'"><img class="citylimits-special-project-img" src="'.$thumbnail.'"><span class="citylimits-special-projects-image-border"></span></a>';

				}

				echo '<a class="citylimits-special-project-title" href="'.$title_link.'">'.$title.'</a>';

				echo '<ul>';

				while ( $series->have_posts() ) {

					$context = array(
						'instance' => $instance,
						'thumb' => false,
						'excerpt' => $excerpt,
						'podcast' => false,
					);

					$series->the_post();
					echo '<li>';
					largo_render_template( 'partials/widget', 'content', $context );
					echo '</li>';

				}

				echo '</ul></div>';

			}

		}

		echo '</div></div>';

		echo $args['after_widget'];

		// Restore global $post
		wp_reset_postdata();
		$post = $preserve;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['num'] = 3;
		$instance['excerpt_display'] = sanitize_key( $new_instance['excerpt_display'] );
		$instance['num_sentences'] = intval( $new_instance['num_sentences'] );
		$instance['series_1'] = sanitize_key( $new_instance['series_1'] );
		$instance['series_2'] = sanitize_key( $new_instance['series_2'] );
		$instance['series_3'] = sanitize_key( $new_instance['series_3'] );
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		// to control: which series, # of posts
		// @todo enhance with more control over thumbnail, icon, etc
		$instance = wp_parse_args( (array) $instance, array(
			'title' => 'Series',
			'num' => 3,
			'show_top_term' => 1,
			'excerpt_display' => 'num_sentences',
			'num_sentences' => 1,
			'thumbnail_display' => 'small',
			'image_align' => 'left',
			'show_byline' => 0,
			'series_1' => 'null',
			'series_2' => 'null',
			'series_3' => 'null')
		);
		$title = esc_attr( $instance['title'] );
		$num = $instance['num'];
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'largo' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('series_1'); ?>"><?php _e( 'Series 1', 'citylimits'); ?>:</label><br/>
			<select style="max-width: 100%;" name="<?php echo $this->get_field_name('series_1'); ?>" id="<?php echo $this->get_field_id('series_1'); ?>">
			<?php
			$terms = get_terms( 'series' );
			foreach ( $terms as $term ) {
				echo '<option value="', $term->term_id, '"', selected($instance['series_1'], $term->term_id, FALSE), '>', $term->name, '</option>';
			} ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('series_2'); ?>"><?php _e( 'Series 2', 'citylimits'); ?>:</label><br/>
			<select style="max-width: 100%;" name="<?php echo $this->get_field_name('series_2'); ?>" id="<?php echo $this->get_field_id('series_2'); ?>">
			<?php
			$terms = get_terms( 'series' );
			foreach ( $terms as $term ) {
				echo '<option value="', $term->term_id, '"', selected($instance['series_2'], $term->term_id, FALSE), '>', $term->name, '</option>';
			} ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('series_3'); ?>"><?php _e( 'Series 3', 'citylimits'); ?>:</label><br/>
			<select style="max-width: 100%;" name="<?php echo $this->get_field_name('series_3'); ?>" id="<?php echo $this->get_field_id('series_3'); ?>">
			<?php
			$terms = get_terms( 'series' );
			foreach ( $terms as $term ) {
				echo '<option value="', $term->term_id, '"', selected($instance['series_3'], $term->term_id, FALSE), '>', $term->name, '</option>';
			} ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_display' ); ?>"><?php _e( 'Excerpt Display', 'largo' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'excerpt_display' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_display' ); ?>" class="widefat" style="width:90%;">
				<option <?php selected( $instance['excerpt_display'], 'num_sentences' ); ?> value="num_sentences"><?php _e( 'Use # of Sentences', 'largo' ); ?></option>
				<option <?php selected( $instance['excerpt_display'], 'custom_excerpt' ); ?> value="custom_excerpt"><?php _e( 'Use Custom Post Excerpt', 'largo' ); ?></option>
				<option <?php selected( $instance['excerpt_display'], 'none' ); ?> value="none"><?php _e( 'None', 'largo' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_sentences' ); ?>"><?php _e( 'Excerpt Length (# of Sentences):', 'largo' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'num_sentences' ); ?>" type="number" min="1" name="<?php echo $this->get_field_name( 'num_sentences' ); ?>" value="<?php echo (int) $instance['num_sentences']; ?>" style="width:90%;" />
		</p>

	<?php
	}

}