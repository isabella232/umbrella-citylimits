<?php

/**
 * Register the widget
 */
add_action( 'widgets_init', function() {
	register_widget( 'citylimits_special_projects_featured_content_widget' );
});

/**
 * Largo Recent Posts
 */
class citylimits_special_projects_featured_content_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$widget_ops = array(
			'classname' => 'citylimits-special-projects-featured-content',
			'description' => __( 'A flexible widget to display recent posts on series landing pages with a "load more" posts button', 'citylimits' )
		);
		parent::__construct(
			'citylimits-special-projects-featured-content-widget', // Base ID
			__( 'City Limits Special Projects Featured Content', 'citylimits' ), // Name
			$widget_ops // Args
		);

	}

	/**
	 * Outputs the content of the recent posts widget.
	 *
	 * @param array $args widget arguments.
	 * @param array $instance saved values from databse.
	 * @global $post
	 * @global $shown_ids An array of post IDs already on the page, to avoid duplicating posts
	 * @global $wp_query Used to get posts on the page not in $shown_ids, to avoid duplicating posts
	 */
	function widget( $args, $instance ) {

		global $post,
			$wp_query, // grab this to copy posts in the main column
			$shown_ids; // an array of post IDs already on a page so we can avoid duplicating posts;

		// Preserve global $post
		$preserve = $post;

		if ( isset( $wp_query->query_vars['term'] )
		&& isset( $wp_query->query_vars['taxonomy'] )
		&& 'series' == $wp_query->query_vars['taxonomy'] ) {

			var_dump($query_vars['term']);
			$term = get_term_by( 'name', 'election-watch-2018', 'series');
			var_dump($term);

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			echo $args['before_widget'];

			echo '<div class="citylimits-special-projects-featured-content-inner-container">';

			if ( $title ) echo $args['before_title'] . $title . $args['after_title'];

			$thumb = isset( $instance['thumbnail_display'] ) ? $instance['thumbnail_display'] : 'small';
			$excerpt = isset( $instance['excerpt_display'] ) ? $instance['excerpt_display'] : 'num_sentences';

			$series = $wp_query->query_vars['term'];

			// default query args: by date, descending
			$args = array(
				'p' 				=> '',
				'post_type' 		=> 'post',
				'taxonomy' 			=> 'series',
				'term' 				=> $series,
				'order' 			=> 'DESC',
				'posts_per_page' 	=> 8,
				'post__not_in'   => get_option( 'sticky_posts' ),
				'is_series_featured_content_widget'   => true,
			);

			//stores original 'paged' value in 'pageholder'
			global $cftl_previous;
			if ( isset($cftl_previous['pageholder']) && $cftl_previous['pageholder'] > 1 ) {
				$args['paged'] = $cftl_previous['pageholder'];
				global $paged;
				$paged = $args['paged'];
			}

			//change args as needed
			//these unusual WP_Query args are handled by filters defined in cftl-series-order.php
			switch ( $opt['post_order'] ) {
				case 'ASC':
					$args['orderby'] = 'ASC';
					break;
				case 'custom':
					$args['orderby'] = 'series_custom';
					break;
				case 'featured, DESC':
				case 'featured, ASC':
					$args['orderby'] = $opt['post_order'];
					break;
			}

			if ( isset( $instance['avoid_duplicates'] ) && 1 === $instance['avoid_duplicates'] ) {
				$args['post__not_in'] = $shown_ids;
			}

			echo '<ul>';

			$series_query = new WP_Query($args);
			$counter = 1;

			if ( $series_query->have_posts() ) {
			$output = '';

				while ( $series_query->have_posts() ) : $series_query->the_post(); $shown_ids[] = get_the_ID();

					// wrap the items in li's.
					$output .= '<li><div class="inner"><div class="post-inner">';

					$context = array(
						'instance' => $instance,
						'thumb' => $thumb,
						'excerpt' => $excerpt
					);

					ob_start();
					largo_render_template( 'partials/widget', 'content', $context );
					$output .= ob_get_clean();

					// close the item
					$output .= '</div></div></li>';

				endwhile;

				// print all of the items
				echo $output;
			} else {
				printf( __( '<p class="error"><strong>You don\'t have any recent %s.</strong></p>', 'largo' ), strtolower( $posts_term ) );
			} // end more featured posts

			wp_reset_postdata();

			// Enqueue the LMP data
			$posts_term = of_get_option('posts_term_plural');

			largo_render_template('partials/load-more-posts', array(
				'nav_id' => 'nav-below',
				'the_query' => $series_query,
				'posts_term' => ($posts_term) ? $posts_term : 'Posts'
			));

			// close the ul
			echo '</ul>';

			echo '</div>';
			
			echo $args['after_widget'];

			// Restore global $post
			wp_reset_postdata();
			$post = $preserve;

		}

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['avoid_duplicates'] = ! empty( $new_instance['avoid_duplicates'] ) ? 1 : 0;
		$instance['thumbnail_display'] = 'large';
		$instance['image_align'] = sanitize_key( $new_instance['image_align'] );
		$instance['excerpt_display'] = sanitize_key( $new_instance['excerpt_display'] );
		$instance['num_sentences'] = intval( $new_instance['num_sentences'] );
		$instance['show_byline'] = ! empty($new_instance['show_byline']);
		$instance['hide_byline_date'] = ! empty($new_instance['hide_byline_date']);
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __( 'Recent ' . of_get_option( 'posts_term_plural', 'Posts' ), 'largo' ),
			'avoid_duplicates' => '',
			'thumbnail_display' => 'large',
			'excerpt_display' => 'num_sentences',
			'num_sentences' => 2,
			'show_byline' => '',
			'hide_byline_date' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$duplicates = $instance['avoid_duplicates'] ? 'checked="checked"' : '';
		$showbyline = $instance['show_byline'] ? 'checked="checked"' : '';
		$hidebylinedate = $instance['hide_byline_date'] ? 'checked="checked"' : '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'largo' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php echo $duplicates; ?> id="<?php echo $this->get_field_id( 'avoid_duplicates' ); ?>" name="<?php echo $this->get_field_name( 'avoid_duplicates' ); ?>" /> <label for="<?php echo $this->get_field_id( 'avoid_duplicates' ); ?>"><?php _e( 'Avoid Duplicate Posts?', 'largo' ); ?></label>
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
			<input id="<?php echo $this->get_field_id( 'num_sentences' ); ?>" name="<?php echo $this->get_field_name( 'num_sentences' ); ?>" value="<?php echo (int) $instance['num_sentences']; ?>" style="width:90%;" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php echo $showbyline; ?> id="<?php echo $this->get_field_id( 'show_byline' ); ?>" name="<?php echo $this->get_field_name( 'show_byline' ); ?>" /> <label for="<?php echo $this->get_field_id( 'show_byline' ); ?>"><?php _e( 'Show byline on posts?', 'largo' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php echo $hidebylinedate; ?> id="<?php echo $this->get_field_id( 'hide_byline_date' ); ?>" name="<?php echo $this->get_field_name( 'hide_byline_date' ); ?>" /> <label for="<?php echo $this->get_field_id( 'hide_byline_date' ); ?>"><?php _e( 'Hide the publish date in the byline?', 'largo' ); ?></label>
		</p>

	<?php
	}
}
