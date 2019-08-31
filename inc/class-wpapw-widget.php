<?php

/**
 * The main widget class.
 *
 * @since      1.0.0
 * @package    Wpapw
 */

function wpapw_widget() {
	register_widget( 'wpapw_widget' );
}
add_action( 'widgets_init', 'wpapw_widget' );

Class wpapw_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'wpapw_widget',
			__( 'Advanced Posts', 'wpapw' ),
			array(
				'classname' => 'wpapw-posts',
				'description' => esc_html__( 'Displays your latest or trending posts.', 'wpapw' )
			)
		);
	}
	
	public function widget( $args, $instance ) {
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$sort = empty( $instance['sort'] ) ? 'latest' : $instance['sort'];
		$number = empty( $instance['number'] ) ? 5 : $instance['number'];
		$layout = empty( $instance['layout'] ) ? 'small' : $instance['layout'];
		$cat = empty( $instance['cat'] ) ? '' : $instance['cat'];
		$count = empty( $instance['count'] ) ? '' : $instance['count'];
		$time = $sort == 'trending' ? $time = '7 day ago' : $instance['time'];
		
		echo $args['before_widget'];
		 
		do_action( 'wpapw_before_widget', $instance );
		 
		if ( $sort == 'trending' || $sort == 'popular' ) {
			if ( $sort == 'trending' ) $time = '7 day ago';
			$query = array(
				'category__in' 				=> $cat,
				'posts_per_page' 			=> $number,
				'post_type'   				=> 'post',
				'post_status' 				=> 'publish',
				'orderby'      				=> 'meta_value_num',
				'meta_key'     				=> 'post_views_count',
				'date_query' => array(
					array(
					  'column' => 'post_date_gmt',
					  'after'  => $time,
					)
				),
			);
		} else {
			$query = array(
				'category__in' 				=> $cat,
				'posts_per_page' 			=> $number,
				'post_type'   				=> 'post',
				'post_status' 				=> 'publish',
				'orderby'      				=> 'date',
			);
		}
		$posts = new WP_Query($query);
		
		if ($posts->have_posts()) { ?>

			<div class="wpapw__widget">

				<?php if ( $title ) echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title']; ?>

				<div class="wpapw__posts <?php if ($layout == 'large') : ?>--large<?php endif; ?>">

					<?php while( $posts->have_posts() ) : $posts->the_post(); ?>
						<div class="wpapw__post">
							<?php if ($layout == 'large') { ?>
							
									<?php if (has_post_thumbnail()) { ?>
										<div class="wpapw__img">
											<a href="<?php echo esc_url(get_permalink()); ?>" data-bg-wpapw="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'large' ); ?>"></a>
										</div>
									<?php } ?>

									<div class="wpapw__desc <?php if ($count == 'on') { ?>--count<?php } ?>">
										<h3 class="wpapw__count"><?php echo esc_html($posts->current_post +1); ?></h3>
										<div>
											<span class="wpapw__cat"><?php the_category( ', ' ); ?></span>
											<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
										</div>
									</div>
								
							<?php } else { ?>
							
								<?php if ($count == 'on') { ?>
									<p class="wpapw__count"><?php echo esc_html($posts->current_post +1); ?></p>
								<?php } ?>
								<?php if( has_post_thumbnail()) { ?>
									<div class="wpapw__img">
										<a href="<?php echo esc_url(get_permalink()); ?>">
											<?php the_post_thumbnail('thumbnail'); ?>
										</a>
									</div>
								<?php } ?>
								<div class="wpapw__desc">
									<span class="wpapw__cat"><?php the_category( ', ' ); ?></span>
									<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
								</div>
								
							<?php } ?>
						</div>	
					<?php endwhile; ?>

					<?php wp_reset_postdata(); ?>

				</div>

			</div>

		<?php 
		} else {
			printf(
				'%1$s <a href="%2$s">%3$s</a>',
				esc_html__( 'You have no posts to show for selected options. Try changing them in', 'wpapw' ),
				esc_url( admin_url( 'customize.php?autofocus[panel]=widgets' ) ),
				esc_html__( 'widgets panel.', 'wpapw' )
			);
		}
		 
		do_action( 'wpiw_after_widget', $instance );
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => __( 'Latest Stories', 'wpapw' ),
			'sort' => 'latest',
			'number' => 5,
			'layout' => 'small',
			'cat'   => '',
			'count' => '',
			'time' => '999 month ago'
		) );
		$title = $instance['title'];
		$sort = $instance['sort'];
		$number = absint( $instance['number'] );
		$layout = $instance['layout'];
		$count = $instance['count'];
		$cat = $instance['cat'];
		$time = $instance['time'];
		?>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><strong><?php esc_html_e( 'Title', 'wpapw' ); ?>:</strong> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>"><strong><?php esc_html_e( 'Sort by', 'wpapw' ); ?>:</strong> </label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort' ) ); ?>" class="widefat">
				<option value="latest" <?php selected( 'latest', $sort ); ?>><?php esc_html_e( 'Latest', 'wpapw' ); ?></option>
				<option value="trending" <?php selected( 'trending', $sort ); ?>><?php esc_html_e( 'Trending', 'wpapw' ); ?></option>
				<option value="popular" <?php selected( 'popular', $sort ); ?>><?php esc_html_e( 'Popular', 'wpapw' ); ?></option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><strong><?php esc_html_e( 'Number of Posts', 'wpapw' ); ?>:</strong> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><strong> <?php esc_html_e( 'Layout', 'wpapw' ); ?>:</strong></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>" class="widefat">
				<option value="small" <?php selected( 'small', $layout ); ?>><?php esc_html_e( 'List', 'wpapw' ); ?></option>
				<option value="large" <?php selected( 'large', $layout ); ?>><?php esc_html_e( 'Large', 'wpapw' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><strong><?php esc_html_e( 'Category', 'wpapw' ); ?>:</strong></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cat' ) ); ?>" class="widefat">
                <option value=""><?php esc_html_e( 'All', 'wpapw'); ?></option>
                <?php foreach(get_categories('parent=0&hide_empty=0') as $term) { ?>
                <option <?php selected( $instance['cat'], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                <?php } ?>      
            </select>
        </p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><strong><?php esc_html_e('Show Counter', 'wpapw'); ?></strong></label>
			<label class="codemade-switch__switch">
				<input class="widefat codemade-switch__input" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="checkbox" <?php checked($instance['count'], 'on'); ?>/>
				<span class="codemade-switch__slider"></span>
			</label>
		</p>
		<?php if ($sort == 'popular') { ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>"><strong><?php esc_html_e( 'Time Range', 'wpapw' ); ?>:</strong></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'time' ) ); ?>" class="widefat">
					<option value="999 month ago" <?php selected( '999 month ago', $time ); ?>><?php esc_html_e( 'Any Time', 'wpapw' ); ?></option>
					<option value="24 hour ago" <?php selected( '24 hour ago', $time ); ?>><?php esc_html_e( 'Last 24 Hours', 'wpapw' ); ?></option>
					<option value="7 day ago" <?php selected( '7 day ago', $time ); ?>><?php esc_html_e( 'Last 7 Days', 'wpapw' ); ?></option>
					<option value="1 month ago" <?php selected( '1 month ago', $time ); ?>><?php esc_html_e( 'Last 30 Days', 'wpapw' ); ?></option>
					<option value="2 month ago" <?php selected( '2 month ago', $time ); ?>><?php esc_html_e( 'Last 2 Months', 'wpapw' ); ?></option>
					<option value="3 month ago" <?php selected( '3 month ago', $time ); ?>><?php esc_html_e( 'Last 3 Months', 'wpapw' ); ?></option>
					<option value="6 month ago" <?php selected( '6 month ago', $time ); ?>><?php esc_html_e( 'Last 6 Months', 'wpapw' ); ?></option>
					<option value="12 month ago" <?php selected( '12 month ago', $time ); ?>><?php esc_html_e( 'Last Year', 'wpapw' ); ?></option>
				</select>
			</p>
		<?php }
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['sort'] = ( ( 'latest' === $new_instance['sort'] || 'trending' === $new_instance['sort'] || 'popular' === $new_instance['sort'] ) ? $new_instance['sort'] : 'latest' );
		$instance['number'] = ! absint( $new_instance['number'] ) ? 5 : $new_instance['number'];
		$instance['layout'] = ( ( 'small' === $new_instance['layout'] || 'large' === $new_instance['layout'] ) ? $new_instance['layout'] : 'small' );
		$instance['count'] = $new_instance['count'];
		$instance['cat'] = $new_instance['cat'];
		$instance['time'] = $new_instance['time'];
		return $instance;
	}

}
