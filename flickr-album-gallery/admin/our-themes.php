<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function flicgal_our_themes_page() {
	$themes = array(
		array( 'name' => 'Marin', 'slug' => 'marin', 'installs' => '500+' ),
		array( 'name' => 'Avantex', 'slug' => 'avantex', 'installs' => '300+' ),
		array( 'name' => 'Building Construction', 'slug' => 'building-construction', 'installs' => '200+' ),
		array( 'name' => 'Avantex Construction', 'slug' => 'avantex-construction', 'installs' => '200+' ),
		array( 'name' => 'Architect Designs', 'slug' => 'architect-designs', 'installs' => '100+' ),
		array( 'name' => 'Avantex Automobile', 'slug' => 'avantex-automobile', 'installs' => '100+' ),
		array( 'name' => 'Avantex Education', 'slug' => 'avantex-education', 'installs' => '100+' ),
		array( 'name' => 'Avantex Medical', 'slug' => 'avantex-medical', 'installs' => '100+' ),
		array( 'name' => 'BusinessExpo', 'slug' => 'businessexpo', 'installs' => '100+' ),
		array( 'name' => 'Crypto AirDrop', 'slug' => 'crypto-airdrop', 'installs' => '100+' ),
		array( 'name' => 'Crypto Compare', 'slug' => 'crypto-compare', 'installs' => '100+' ),
		array( 'name' => 'Crypto Token', 'slug' => 'crypto-token', 'installs' => '100+' ),
		array( 'name' => 'Medical Health', 'slug' => 'medical-health', 'installs' => '90+' ),
		array( 'name' => 'Leather House', 'slug' => 'leather-house', 'installs' => '60+' ),
		array( 'name' => 'Avantex Yoga', 'slug' => 'avantex-yoga', 'installs' => '60+' ),
		array( 'name' => 'Crypto Mining', 'slug' => 'crypto-mining', 'installs' => '50+' ),
		array( 'name' => 'Modern House', 'slug' => 'modern-house', 'installs' => '50+' ),
		array( 'name' => 'Meme Token', 'slug' => 'meme-token', 'installs' => '40+' ),
		array( 'name' => 'Paws and Care', 'slug' => 'paws-and-care', 'installs' => '30+' ),
	);
	?>
	<div class="wrap">
		<div class="flicgal-admin-hero">
			<h1>Our Themes</h1>
			<p>Beautiful, starter WordPress themes by WP Frank — designed for business, crypto, medical, and more.</p>
			<div class="flicgal-hero-stats">
				<div class="flicgal-hero-stat"><span class="stat-number"><?php echo count( $themes ); ?></span><span class="stat-label">Free Themes</span></div>
				<div class="flicgal-hero-stat"><span class="stat-number">WordPress.org</span><span class="stat-label">Published On</span></div>
				<div class="flicgal-hero-stat"><span class="stat-number">100%</span><span class="stat-label">GPL Licensed</span></div>
			</div>
		</div>

		<div class="flicgal-card-grid">
			<?php foreach ( $themes as $theme ) : ?>
				<div class="flicgal-theme-card">
					<div class="card-banner">
						<img src="<?php echo esc_url( 'https://ts.w.org/wp-content/themes/' . $theme['slug'] . '/screenshot.png?w=772&h=250' ); ?>" alt="<?php echo esc_attr( $theme['name'] ); ?>" onerror="this.src='<?php echo esc_url( FLICGAL_PLUGIN_URL . 'img/flickr.jpg' ); ?>';">
					</div>
					<h3><?php echo esc_html( $theme['name'] ); ?></h3>
					<span class="card-installs"><span class="dashicons dashicons-admin-site-alt3"></span> <?php echo esc_html( $theme['installs'] ); ?> active installs</span>
					<div class="flicgal-card-actions" style="margin-top: 16px;">
						<a href="<?php echo esc_url( 'https://wordpress.org/themes/' . $theme['slug'] . '/' ); ?>" target="_blank" class="flicgal-btn flicgal-btn-outline">Details</a>
						<a href="<?php echo esc_url( admin_url( 'theme-install.php?search=' . urlencode( $theme['name'] ) ) ); ?>" class="flicgal-btn flicgal-btn-primary">Install Now</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
