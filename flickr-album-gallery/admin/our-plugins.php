<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function flicgal_our_plugins_page() {
	$plugins = array(
		array( 'name' => 'Portfolio Filter Gallery', 'slug' => 'portfolio-filter-gallery', 'installs' => '20,000+', 'icon' => 'dashicons-portfolio', 'desc' => 'Create stunning filterable portfolio galleries with categories and smooth animations.' ),
		array( 'name' => 'Ultimate Responsive Image Slider', 'slug' => 'ultimate-responsive-image-slider', 'installs' => '30,000+', 'icon' => 'dashicons-images-alt2', 'desc' => 'Beautiful responsive image slider with touch support and multiple transition effects.' ),
		array( 'name' => 'Coming Soon Maintenance Mode', 'slug' => 'coming-soon-maintenance-mode', 'installs' => '7,000+', 'icon' => 'dashicons-clock', 'desc' => 'Display a coming soon or maintenance mode page while your site is under construction.' ),
		array( 'name' => 'Blog Filter Post Filtering', 'slug' => 'blog-filter', 'installs' => '7,000+', 'icon' => 'dashicons-filter', 'desc' => 'Filter and display blog posts by category with beautiful grid layouts.' ),
		array( 'name' => 'AWPLife Weather Effects', 'slug' => 'weather-effect', 'installs' => '5,000+', 'icon' => 'dashicons-cloud', 'desc' => 'Add beautiful weather effects like snow, rain, and fireworks to your website.' ),
		array( 'name' => 'Image Gallery', 'slug' => 'new-image-gallery', 'installs' => '4,000+', 'icon' => 'dashicons-format-gallery', 'desc' => 'Create beautiful image galleries with lightbox and multiple layout options.' ),
		array( 'name' => 'Album Gallery', 'slug' => 'new-album-gallery', 'installs' => '4,000+', 'icon' => 'dashicons-images-alt', 'desc' => 'Organize photos into albums with cover images and grid layouts.' ),
		array( 'name' => 'Album Gallery For Flickr', 'slug' => 'flickr-album-gallery', 'installs' => '4,000+', 'icon' => 'dashicons-camera', 'desc' => 'Display Flickr albums beautifully on your WordPress website via API.' ),
		array( 'name' => 'Pricing Table', 'slug' => 'abc-pricing-table', 'installs' => '4,000+', 'icon' => 'dashicons-money-alt', 'desc' => 'Create responsive and beautiful pricing tables for your products and services.' ),
		array( 'name' => 'Social Media Icon Widget', 'slug' => 'new-social-media-widget', 'installs' => '4,000+', 'icon' => 'dashicons-share', 'desc' => 'Display social media icons with a simple widget. Supports all major platforms.' ),
		array( 'name' => 'Filter Gallery', 'slug' => 'filter-gallery', 'installs' => '3,000+', 'icon' => 'dashicons-grid-view', 'desc' => 'Create filterable image galleries with categories and responsive grid layouts.' ),
		array( 'name' => 'Slider Factory', 'slug' => 'slider-factory', 'installs' => '3,000+', 'icon' => 'dashicons-slides', 'desc' => 'Create beautiful sliders with multiple layouts, animations, and touch support.' ),
		array( 'name' => 'Animated Live Wall Gallery', 'slug' => 'animated-live-wall', 'installs' => '2,000+', 'icon' => 'dashicons-layout', 'desc' => 'Create animated live wall galleries with unique photo display effects.' ),
		array( 'name' => 'Modal Popup Box', 'slug' => 'modal-popup-box', 'installs' => '2,000+', 'icon' => 'dashicons-external', 'desc' => 'Create beautiful popup boxes for promotions, notices, and content display.' ),
		array( 'name' => 'Profile Box Shortcode And Widget', 'slug' => 'facebook-likebox-widget-and-shortcode', 'installs' => '2,000+', 'icon' => 'dashicons-admin-users', 'desc' => 'Display social media profile boxes with like buttons and follow widgets.' ),
		array( 'name' => 'Responsive Slider Gallery', 'slug' => 'responsive-slider-gallery', 'installs' => '2,000+', 'icon' => 'dashicons-format-image', 'desc' => 'Create responsive slider galleries with thumbnails and lightbox.' ),
		array( 'name' => 'Responsive Slideshow', 'slug' => 'slider-responsive-slideshow', 'installs' => '2,000+', 'icon' => 'dashicons-playlist-video', 'desc' => 'Create beautiful responsive slideshows with multiple transition effects.' ),
		array( 'name' => 'Slider for Photos Images Videos', 'slug' => 'media-slider', 'installs' => '2,000+', 'icon' => 'dashicons-video-alt3', 'desc' => 'Create sliders with photos, images, and videos in a responsive layout.' ),
		array( 'name' => 'Social Media Feed Gallery', 'slug' => 'wp-instagram-feed-awplife', 'installs' => '2,000+', 'icon' => 'dashicons-instagram', 'desc' => 'Display your social media feeds in a beautiful gallery layout.' ),
		array( 'name' => 'Album Photostream Flickr Gallery', 'slug' => 'wp-flickr-gallery', 'installs' => '1,000+', 'icon' => 'dashicons-camera-alt', 'desc' => 'Display Flickr photostream and albums with beautiful gallery layouts.' ),
		array( 'name' => 'Contact Form Widget', 'slug' => 'new-contact-form-widget', 'installs' => '1,000+', 'icon' => 'dashicons-email-alt', 'desc' => 'Simple and lightweight contact form widget for WordPress.' ),
		array( 'name' => 'Grid Gallery for Images', 'slug' => 'new-grid-gallery', 'installs' => '1,000+', 'icon' => 'dashicons-screenoptions', 'desc' => 'Create responsive grid galleries with multiple column layouts.' ),
		array( 'name' => 'Login Page Customizer', 'slug' => 'customizer-login-page', 'installs' => '1,000+', 'icon' => 'dashicons-lock', 'desc' => 'Customize your WordPress login page with backgrounds, logos, and styles.' ),
		array( 'name' => 'Photo Gallery for Images', 'slug' => 'new-photo-gallery', 'installs' => '1,000+', 'icon' => 'dashicons-format-gallery', 'desc' => 'Create beautiful photo galleries with lightbox support.' ),
		array( 'name' => 'Team Member Showcase', 'slug' => 'team-builder-member-showcase', 'installs' => '1,000+', 'icon' => 'dashicons-groups', 'desc' => 'Showcase your team members with photos, roles, and social links.' ),
		array( 'name' => 'Testimonial Customer Feedback', 'slug' => 'testimonial-maker', 'installs' => '1,000+', 'icon' => 'dashicons-format-quote', 'desc' => 'Display customer testimonials and reviews in beautiful layouts.' ),
		array( 'name' => 'Video Gallery YouTube Vimeo', 'slug' => 'new-video-gallery', 'installs' => '1,000+', 'icon' => 'dashicons-video-alt2', 'desc' => 'Create video galleries from YouTube and Vimeo with grid layouts.' ),
		array( 'name' => 'Event Monster', 'slug' => 'event-monster', 'installs' => '700+', 'icon' => 'dashicons-calendar-alt', 'desc' => 'Manage events with ticket booking, schedules, and registration.' ),
		array( 'name' => 'Lead Generation Form', 'slug' => 'lead-generation-form', 'installs' => '600+', 'icon' => 'dashicons-megaphone', 'desc' => 'Create lead capture forms with email notifications and submissions tracking.' ),
	);

	$total_installs = '130,000+';
	?>
	<div class="wrap">
		<div class="flicgal-admin-hero">
			<h1>Our Plugins</h1>
			<p>Starter WordPress plugins crafted by WP Frank — trusted by thousands of websites worldwide.</p>
			<div class="flicgal-hero-stats">
				<div class="flicgal-hero-stat"><span class="stat-number"><?php echo esc_html( $total_installs ); ?></span><span class="stat-label">Total Installs</span></div>
				<div class="flicgal-hero-stat"><span class="stat-number"><?php echo count( $plugins ); ?></span><span class="stat-label">Free Plugins</span></div>
				<div class="flicgal-hero-stat"><span class="stat-number">WordPress.org</span><span class="stat-label">Published On</span></div>
			</div>
		</div>

		<div class="flicgal-card-grid">
			<?php foreach ( $plugins as $plugin ) : ?>
				<div class="flicgal-plugin-card">
					<div class="card-banner">
						<img src="<?php echo esc_url( 'https://ps.w.org/' . $plugin['slug'] . '/assets/banner-772x250.jpg' ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>" onerror="if(this.src.indexOf('.jpg') != -1){ this.src=this.src.replace('.jpg', '.png'); } else { this.src='<?php echo esc_url( FLICGAL_PLUGIN_URL . 'img/flickr.jpg' ); ?>'; }">
					</div>
					<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
					<span class="card-installs"><span class="dashicons dashicons-admin-site-alt3"></span> <?php echo esc_html( $plugin['installs'] ); ?> active installs</span>
					<p class="card-desc"><?php echo esc_html( $plugin['desc'] ); ?></p>
					<div class="flicgal-card-actions">
						<a href="<?php echo esc_url( 'https://wordpress.org/plugins/' . $plugin['slug'] . '/' ); ?>" target="_blank" class="flicgal-btn flicgal-btn-outline">Details</a>
						<a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=' . urlencode( $plugin['name'] ) ) ); ?>" class="flicgal-btn flicgal-btn-primary">Install Now</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
