<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FlicGal Migration Tool
 *
 * Provides a one-click migration from v2.2.14 (fa_gallery / [FAG]) to
 * v2.2.15 (flicgal_gallery / [FLICGAL]).
 *
 * @since 2.2.15
 */

/**
 * Add Migration submenu page under Flickr Album Gallery menu
 */
add_action( 'admin_menu', 'flicgal_add_migration_page' );
function flicgal_add_migration_page() {
	add_submenu_page(
		'edit.php?post_type=flicgal_gallery',
		__( 'Migration Tool', 'flickr-album-gallery' ),
		__( 'Migration Tool', 'flickr-album-gallery' ),
		'manage_options',
		'flicgal-migration',
		'flicgal_migration_page_render'
	);
}

/**
 * Get migration statistics
 *
 * @return array Counts for legacy galleries, shortcodes, and meta keys.
 */
function flicgal_get_migration_stats() {
	global $wpdb;

	// Count legacy fa_gallery posts
	$legacy_gallery_count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish','draft','pending','private')",
			'fa_gallery'
		)
	);

	// Count new flicgal_gallery posts
	$new_gallery_count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish','draft','pending','private')",
			'flicgal_gallery'
		)
	);

	// Count posts/pages containing old [FAG shortcode
	$old_shortcode_count = (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%[FAG %' AND post_status IN ('publish','draft','pending','private')"
	);

	// Count posts with old meta key fag_settings
	$old_meta_count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s",
			'fag_settings'
		)
	);

	// Count posts that already have new meta key flicgal_settings
	$new_meta_count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s",
			'flicgal_settings'
		)
	);

	return array(
		'legacy_galleries'  => $legacy_gallery_count,
		'new_galleries'     => $new_gallery_count,
		'old_shortcodes'    => $old_shortcode_count,
		'old_meta'          => $old_meta_count,
		'new_meta'          => $new_meta_count,
	);
}

/**
 * Handle migration form submission
 */
add_action( 'admin_init', 'flicgal_handle_migration' );
function flicgal_handle_migration() {
	if ( ! isset( $_POST['flicgal_migrate_action'] ) ) {
		return;
	}

	// Verify nonce
	if ( ! isset( $_POST['flicgal_migration_nonce'] ) || ! wp_verify_nonce( $_POST['flicgal_migration_nonce'], 'flicgal_run_migration' ) ) {
		wp_die( __( 'Security check failed.', 'flickr-album-gallery' ) );
	}

	// Check permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'flickr-album-gallery' ) );
	}

	global $wpdb;
	$results = array(
		'galleries_migrated'  => 0,
		'shortcodes_migrated' => 0,
		'meta_migrated'       => 0,
		'errors'              => array(),
	);

	$action = sanitize_text_field( $_POST['flicgal_migrate_action'] );

	// --- Step 1: Migrate Post Types (fa_gallery → flicgal_gallery) ---
	if ( $action === 'full' || $action === 'galleries' ) {
		$gallery_result = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_type = %s WHERE post_type = %s",
				'flicgal_gallery',
				'fa_gallery'
			)
		);
		if ( false === $gallery_result ) {
			$results['errors'][] = __( 'Failed to migrate gallery post types.', 'flickr-album-gallery' );
		} else {
			$results['galleries_migrated'] = (int) $gallery_result;
		}
	}

	// --- Step 2: Migrate Meta Keys (fag_settings → flicgal_settings) ---
	if ( $action === 'full' || $action === 'meta' ) {
		// Get all posts that have fag_settings but NOT flicgal_settings
		$posts_with_old_meta = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT pm.post_id, pm.meta_value 
				 FROM {$wpdb->postmeta} pm 
				 WHERE pm.meta_key = %s 
				 AND pm.post_id NOT IN (
				 	SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s
				 )",
				'fag_settings',
				'flicgal_settings'
			)
		);

		foreach ( $posts_with_old_meta as $row ) {
			$old_settings = maybe_unserialize( $row->meta_value );

			// Normalize inner keys from fag_ to flicgal_
			if ( is_array( $old_settings ) ) {
				$new_settings = array();
				foreach ( $old_settings as $key => $value ) {
					if ( is_array( $value ) ) {
						$new_inner = array();
						foreach ( $value as $inner_key => $inner_value ) {
							$new_key = str_replace( 'fag_', 'flicgal_', $inner_key );
							$new_inner[ $new_key ] = $inner_value;
						}
						$new_settings[ $key ] = $new_inner;
					} else {
						$new_settings[ $key ] = $value;
					}
				}
				update_post_meta( $row->post_id, 'flicgal_settings', $new_settings );
				$results['meta_migrated']++;
			}
		}
	}

	// --- Step 3: Migrate Shortcodes in Content ([FAG id=X] → [FLICGAL id=X]) ---
	if ( $action === 'full' || $action === 'shortcodes' ) {
		// Get all posts containing old shortcode
		$posts_with_old_shortcode = $wpdb->get_results(
			"SELECT ID, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%[FAG %' AND post_status IN ('publish','draft','pending','private')"
		);

		foreach ( $posts_with_old_shortcode as $post ) {
			$new_content = str_replace( '[FAG ', '[FLICGAL ', $post->post_content );
			// Also handle lowercase variants just in case
			$new_content = str_replace( '[fag ', '[FLICGAL ', $new_content );

			if ( $new_content !== $post->post_content ) {
				$wpdb->update(
					$wpdb->posts,
					array( 'post_content' => $new_content ),
					array( 'ID' => $post->ID ),
					array( '%s' ),
					array( '%d' )
				);
				$results['shortcodes_migrated']++;
			}
		}

		// Clear all post caches after content update
		if ( $results['shortcodes_migrated'] > 0 ) {
			wp_cache_flush();
		}
	}

	// Store results in transient for display
	set_transient( 'flicgal_migration_results', $results, 60 );

	// Redirect to migration page with success flag
	wp_safe_redirect( admin_url( 'edit.php?post_type=flicgal_gallery&page=flicgal-migration&migrated=1' ) );
	exit;
}

/**
 * Render the Migration Tool admin page
 */
function flicgal_migration_page_render() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to access this page.', 'flickr-album-gallery' ) );
	}

	$stats   = flicgal_get_migration_stats();
	$results = get_transient( 'flicgal_migration_results' );
	if ( $results ) {
		delete_transient( 'flicgal_migration_results' );
	}

	$needs_migration = ( $stats['legacy_galleries'] > 0 || $stats['old_shortcodes'] > 0 || $stats['old_meta'] > $stats['new_meta'] );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Flickr Album Gallery — Migration Tool', 'flickr-album-gallery' ); ?></h1>
		<p class="description"><?php esc_html_e( 'This tool migrates your galleries from v2.2.14 format to v2.2.15 format. Your existing galleries will continue to work even without migration, but migrating ensures full forward compatibility.', 'flickr-album-gallery' ); ?></p>

		<?php if ( $results ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><strong><?php esc_html_e( '✅ Migration completed successfully!', 'flickr-album-gallery' ); ?></strong></p>
				<ul style="list-style: disc; padding-left: 20px;">
					<?php if ( $results['galleries_migrated'] > 0 ) : ?>
						<li><?php echo esc_html( sprintf( __( '%d galleries migrated (fa_gallery → flicgal_gallery)', 'flickr-album-gallery' ), $results['galleries_migrated'] ) ); ?></li>
					<?php endif; ?>
					<?php if ( $results['meta_migrated'] > 0 ) : ?>
						<li><?php echo esc_html( sprintf( __( '%d gallery settings migrated (fag_settings → flicgal_settings)', 'flickr-album-gallery' ), $results['meta_migrated'] ) ); ?></li>
					<?php endif; ?>
					<?php if ( $results['shortcodes_migrated'] > 0 ) : ?>
						<li><?php echo esc_html( sprintf( __( '%d posts/pages updated ([FAG] → [FLICGAL])', 'flickr-album-gallery' ), $results['shortcodes_migrated'] ) ); ?></li>
					<?php endif; ?>
				</ul>
				<?php if ( ! empty( $results['errors'] ) ) : ?>
					<p style="color: red;"><strong><?php esc_html_e( 'Errors:', 'flickr-album-gallery' ); ?></strong></p>
					<ul style="list-style: disc; padding-left: 20px; color: red;">
						<?php foreach ( $results['errors'] as $error ) : ?>
							<li><?php echo esc_html( $error ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Status Dashboard -->
		<div class="flicgal-migration-dashboard" style="display: flex; gap: 20px; flex-wrap: wrap; margin: 20px 0;">
			<div class="flicgal-stat-card" style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid <?php echo $stats['legacy_galleries'] > 0 ? '#d63638' : '#00a32a'; ?>; padding: 15px 20px; min-width: 200px; border-radius: 0 4px 4px 0;">
				<h3 style="margin: 0 0 5px 0; font-size: 14px; color: #50575e;"><?php esc_html_e( 'Legacy Galleries (v2.2.14)', 'flickr-album-gallery' ); ?></h3>
				<p style="margin: 0; font-size: 32px; font-weight: 600; color: #1d2327;"><?php echo esc_html( $stats['legacy_galleries'] ); ?></p>
				<p style="margin: 5px 0 0; color: #787c82; font-size: 12px;"><?php esc_html_e( 'Post type: fa_gallery', 'flickr-album-gallery' ); ?></p>
			</div>
			<div class="flicgal-stat-card" style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #00a32a; padding: 15px 20px; min-width: 200px; border-radius: 0 4px 4px 0;">
				<h3 style="margin: 0 0 5px 0; font-size: 14px; color: #50575e;"><?php esc_html_e( 'New Galleries (v2.2.15)', 'flickr-album-gallery' ); ?></h3>
				<p style="margin: 0; font-size: 32px; font-weight: 600; color: #1d2327;"><?php echo esc_html( $stats['new_galleries'] ); ?></p>
				<p style="margin: 5px 0 0; color: #787c82; font-size: 12px;"><?php esc_html_e( 'Post type: flicgal_gallery', 'flickr-album-gallery' ); ?></p>
			</div>
			<div class="flicgal-stat-card" style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid <?php echo $stats['old_shortcodes'] > 0 ? '#dba617' : '#00a32a'; ?>; padding: 15px 20px; min-width: 200px; border-radius: 0 4px 4px 0;">
				<h3 style="margin: 0 0 5px 0; font-size: 14px; color: #50575e;"><?php esc_html_e( 'Old [FAG] Shortcodes', 'flickr-album-gallery' ); ?></h3>
				<p style="margin: 0; font-size: 32px; font-weight: 600; color: #1d2327;"><?php echo esc_html( $stats['old_shortcodes'] ); ?></p>
				<p style="margin: 5px 0 0; color: #787c82; font-size: 12px;"><?php esc_html_e( 'Pages/posts with [FAG id=X]', 'flickr-album-gallery' ); ?></p>
			</div>
			<div class="flicgal-stat-card" style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid <?php echo ( $stats['old_meta'] > $stats['new_meta'] ) ? '#dba617' : '#00a32a'; ?>; padding: 15px 20px; min-width: 200px; border-radius: 0 4px 4px 0;">
				<h3 style="margin: 0 0 5px 0; font-size: 14px; color: #50575e;"><?php esc_html_e( 'Legacy Meta Keys', 'flickr-album-gallery' ); ?></h3>
				<p style="margin: 0; font-size: 32px; font-weight: 600; color: #1d2327;"><?php echo esc_html( $stats['old_meta'] ); ?></p>
				<p style="margin: 5px 0 0; color: #787c82; font-size: 12px;"><?php esc_html_e( 'Posts with fag_settings key', 'flickr-album-gallery' ); ?></p>
			</div>
		</div>

		<?php if ( $needs_migration ) : ?>
			<!-- Migration Actions -->
			<div class="card" style="max-width: 800px; padding: 20px; margin-top: 10px;">
				<h2 style="margin-top: 0;"><?php esc_html_e( '🔄 Migration Required', 'flickr-album-gallery' ); ?></h2>
				<p><?php esc_html_e( 'Legacy data from v2.2.14 was detected. Your galleries still work due to automatic backward compatibility, but we recommend migrating to the new format for best performance and future compatibility.', 'flickr-album-gallery' ); ?></p>

				<div style="background: #f0f6fc; border: 1px solid #72aee6; border-radius: 4px; padding: 12px 16px; margin: 15px 0;">
					<strong>ℹ️ <?php esc_html_e( 'What does migration do?', 'flickr-album-gallery' ); ?></strong>
					<ol style="margin: 10px 0 0 0;">
						<li><?php esc_html_e( 'Converts gallery post type from "fa_gallery" to "flicgal_gallery"', 'flickr-album-gallery' ); ?></li>
						<li><?php esc_html_e( 'Copies gallery settings from old meta key "fag_settings" to new "flicgal_settings" (with key normalization)', 'flickr-album-gallery' ); ?></li>
						<li><?php esc_html_e( 'Updates shortcodes in all your pages/posts from [FAG id=X] to [FLICGAL id=X]', 'flickr-album-gallery' ); ?></li>
					</ol>
				</div>

				<div style="background: #fcf9e8; border: 1px solid #dba617; border-radius: 4px; padding: 12px 16px; margin: 15px 0;">
					<strong>⚠️ <?php esc_html_e( 'Important:', 'flickr-album-gallery' ); ?></strong>
					<?php esc_html_e( 'Please create a database backup before running the migration. This process modifies post types and content directly in the database.', 'flickr-album-gallery' ); ?>
				</div>

				<form method="post" action="" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to run the migration? Please ensure you have a database backup.', 'flickr-album-gallery' ) ); ?>');">
					<?php wp_nonce_field( 'flicgal_run_migration', 'flicgal_migration_nonce' ); ?>
					<input type="hidden" name="flicgal_migrate_action" value="full" />
					<p class="submit">
						<button type="submit" class="button button-primary button-hero">
							<?php esc_html_e( '🚀 Run Full Migration', 'flickr-album-gallery' ); ?>
						</button>
					</p>
				</form>

				<hr style="margin: 20px 0;" />

				<h3><?php esc_html_e( 'Or migrate individually:', 'flickr-album-gallery' ); ?></h3>
				<div style="display: flex; gap: 10px; flex-wrap: wrap;">
					<?php if ( $stats['legacy_galleries'] > 0 ) : ?>
					<form method="post" action="" style="display: inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Migrate gallery post types?', 'flickr-album-gallery' ) ); ?>');">
						<?php wp_nonce_field( 'flicgal_run_migration', 'flicgal_migration_nonce' ); ?>
						<input type="hidden" name="flicgal_migrate_action" value="galleries" />
						<button type="submit" class="button button-secondary">
							<?php echo esc_html( sprintf( __( 'Migrate %d Galleries', 'flickr-album-gallery' ), $stats['legacy_galleries'] ) ); ?>
						</button>
					</form>
					<?php endif; ?>

					<?php if ( $stats['old_meta'] > $stats['new_meta'] ) : ?>
					<form method="post" action="" style="display: inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Migrate meta keys?', 'flickr-album-gallery' ) ); ?>');">
						<?php wp_nonce_field( 'flicgal_run_migration', 'flicgal_migration_nonce' ); ?>
						<input type="hidden" name="flicgal_migrate_action" value="meta" />
						<button type="submit" class="button button-secondary">
							<?php echo esc_html( sprintf( __( 'Migrate %d Meta Keys', 'flickr-album-gallery' ), $stats['old_meta'] ) ); ?>
						</button>
					</form>
					<?php endif; ?>

					<?php if ( $stats['old_shortcodes'] > 0 ) : ?>
					<form method="post" action="" style="display: inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Update shortcodes in post content?', 'flickr-album-gallery' ) ); ?>');">
						<?php wp_nonce_field( 'flicgal_run_migration', 'flicgal_migration_nonce' ); ?>
						<input type="hidden" name="flicgal_migrate_action" value="shortcodes" />
						<button type="submit" class="button button-secondary">
							<?php echo esc_html( sprintf( __( 'Update %d Shortcodes', 'flickr-album-gallery' ), $stats['old_shortcodes'] ) ); ?>
						</button>
					</form>
					<?php endif; ?>
				</div>
			</div>

		<?php else : ?>
			<!-- All Good! -->
			<div class="notice notice-success" style="margin-top: 10px;">
				<p><strong><?php esc_html_e( '✅ No migration needed!', 'flickr-album-gallery' ); ?></strong> <?php esc_html_e( 'All your galleries are using the latest v2.2.15 format.', 'flickr-album-gallery' ); ?></p>
			</div>
		<?php endif; ?>

		<!-- Info Table -->
		<div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
			<h2 style="margin-top: 0;"><?php esc_html_e( '📋 Version Comparison', 'flickr-album-gallery' ); ?></h2>
			<table class="widefat striped" style="max-width: 700px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Feature', 'flickr-album-gallery' ); ?></th>
						<th><?php esc_html_e( 'v2.2.14 (Old)', 'flickr-album-gallery' ); ?></th>
						<th><?php esc_html_e( 'v2.2.15 (New)', 'flickr-album-gallery' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Post Type', 'flickr-album-gallery' ); ?></td>
						<td><code>fa_gallery</code></td>
						<td><code>flicgal_gallery</code></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Shortcode', 'flickr-album-gallery' ); ?></td>
						<td><code>[FAG id=X]</code></td>
						<td><code>[FLICGAL id=X]</code></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Meta Key', 'flickr-album-gallery' ); ?></td>
						<td><code>fag_settings</code></td>
						<td><code>flicgal_settings</code></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'CSS Framework', 'flickr-album-gallery' ); ?></td>
						<td><?php esc_html_e( 'Bootstrap', 'flickr-album-gallery' ); ?></td>
						<td><?php esc_html_e( 'Custom (lightweight)', 'flickr-album-gallery' ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
?>
