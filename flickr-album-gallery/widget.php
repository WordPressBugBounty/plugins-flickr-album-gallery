<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Flickr Album Gallery Widget
 */
class FlicGal_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'flicgal_gallery_widget', // Base ID
			'Flickr Album Gallery', // Name
			array(
				'description' => 'Display Flickr album galleries into widget areas',
				'flickr-album-gallery',
			) // Args
		);
	}

	public function widget( $args, $instance ) {
		$Title = apply_filters( 'flickr_widget_title', $instance['Title'] );
		echo $args['before_widget'];
		$FID = apply_filters( 'flickr_widget_shortcode', $instance['Shortcode'] );
		if ( is_numeric( $FID ) ) {
			if ( ! empty( $instance['Title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['Title'] ) . $args['after_title'];
			}
			echo do_shortcode( '[FLICGAL id=' . esc_html( $FID ) . ']' );
		} else {
			echo esc_html( '<p>Sorry! No Flickr Album Gallery Shortcode Found.</p>' );
		}
		echo $args['after_widget'];
		wp_reset_postdata();
	}

	public function form( $instance ) {

		if ( isset( $instance['Title'] ) ) {
			$Title = $instance['Title'];
		} else {
			$Title = 'Flickr Album Gallery';
		}

		if ( isset( $instance['Shortcode'] ) ) {
			$Shortcode = $instance['Shortcode'];
		} else {
			$Shortcode = 'Select Any Flickr Album Gallery';
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'Title' ) ); ?>"><?php esc_html_e( 'Widget Title' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'Title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'Title' ) ); ?>" type="text" value="<?php echo esc_attr( $Title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'Shortcode' ) ); ?>"><?php esc_html_e( 'Select Any', 'flickr-album-gallery' ); ?> (Required)</label>
			<?php
			// Get All Flickr Shortcode Custom Post Type (both new + legacy for v2.2.14 compat)
			$flicgal_new_count    = wp_count_posts( 'flicgal_gallery' )->publish;
			$flicgal_legacy_count = wp_count_posts( 'fa_gallery' )->publish;
			$FLICKR_All_Posts     = $flicgal_new_count + $flicgal_legacy_count;
			global $flicgal_all_flickr;
			$flicgal_all_flickr_args = array(
				'post_type'      => array( 'flicgal_gallery', 'fa_gallery' ),
				'orderby'        => 'ASC',
				'posts_per_page' => $FLICKR_All_Posts,
			);
			$flicgal_all_flickr = new WP_Query( $flicgal_all_flickr_args );
			?>
			<select id="<?php echo esc_attr( $this->get_field_id( 'Shortcode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'Shortcode' ) ); ?>" style="width: 100%;">
				<option value="Select Any Settings" 
				<?php
				if ( $Shortcode == 'Select Any Settings' ) {
					echo esc_attr( 'selected="selected"' ); } ?>
				>Select Any Settings</option>
				<?php
				if ( $flicgal_all_flickr->have_posts() ) {
					?>
					<?php
					while ( $flicgal_all_flickr->have_posts() ) :
						$flicgal_all_flickr->the_post();
						$PostId    = get_the_ID();
						$PostTitle = get_the_title( $PostId );
						?>
				<option value="<?php echo esc_attr( $PostId ); ?>" 
										  <?php
											if ( $Shortcode == $PostId ) {
												echo esc_attr( 'selected="selected"' ); } ?>
				>
						<?php
						if ( $PostTitle ) {
							echo esc_html( $PostTitle );
						} else {
							esc_html_e( 'No Title', 'flickr-album-gallery' );
						}
						?>
</option>
				<?php endwhile; ?>
					<?php
				} else {
					echo esc_html( '<option>Sorry! No Flickr Album Gallery Shortcode Found.</option>' );
				}
				?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['Title']     = ( ! empty( $new_instance['Title'] ) ) ? strip_tags( $new_instance['Title'] ) : '';
		$instance['Shortcode'] = ( ! empty( $new_instance['Shortcode'] ) ) ? strip_tags( $new_instance['Shortcode'] ) : 'Select Any Flickr Album Gallery';
		return $instance;
	}
} // end of class Flickr Album Gallery Shortcode Widget Class

// Register Flickr Album Gallery Shortcode Widget
add_action( 'widgets_init', 'flicgal_register_widget' );
function flicgal_register_widget() {
	register_widget( 'FlicGal_Widget' );
}
?>
