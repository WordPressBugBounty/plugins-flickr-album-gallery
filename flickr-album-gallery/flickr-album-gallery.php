<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Plugin Name: Flickr Album Gallery
 * Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
 * Description: Flickr Album Gallery is on JS API plugin to display all public Flickr albums on your WordPress website.
 * Version:     2.2.15
 * Author:      FARAZFRANK
 * Author URI:  https://wpfrank.com/
 * Text Domain: flickr-album-gallery
 * Domain Path: /languages
 * License:     GPL2

Flickr Album Gallery is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Flickr Album Gallery is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Flickr Album Gallery. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

/**
 * Constant Variable
 */
define( 'FLICGAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FLICGAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLICGAL_PLUGIN_VER', '2.2.15' );

// load JS script
function flicgal_load_scripts() {
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'flicgal_load_scripts' );

/**
 * Flickr album gallery Plugin Class
 */
class FlicGal_Main {

	public function __construct() {
		// CPT must be registered on both admin and frontend for shortcode queries to work.
		add_action( 'init', array( &$this, 'flicgal_register_cpt' ), 1 );
		add_action( 'init', array( &$this, 'flicgal_register_legacy_cpt' ), 1 );

		if ( is_admin() ) {
			add_action( 'plugins_loaded', array( &$this, 'flicgal_translate_plugin' ), 1 );
			add_action( 'add_meta_boxes', array( &$this, 'flicgal_add_meta_boxes' ) );
			add_action( 'admin_init', array( &$this, 'flicgal_add_meta_boxes' ), 1 );
			add_action( 'save_post', array( &$this, 'flicgal_save_meta_box' ), 9, 1 );
			add_action( 'admin_menu', array( &$this, 'flicgal_register_submenu_pages' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'flicgal_admin_enqueue' ) );
			add_filter( 'post_row_actions', array( &$this, 'flicgal_row_actions' ), 10, 2 );
			add_action( 'admin_action_flicgal_duplicate_gallery', array( &$this, 'flicgal_duplicate_gallery' ) );
		}
	}

	/**
	 * Translate Plugin
	 */
	public function flicgal_translate_plugin() {
		load_plugin_textdomain( 'flickr-album-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	// 2 - Register Flickr Album Custom Post Type
	public function flicgal_register_cpt() {
		$labels = array(
			'name'               => __( 'Flickr Album Gallery', 'flickr-album-gallery' ),
			'singular_name'      => __( 'Flickr Album Gallery', 'flickr-album-gallery' ),
			'add_new'            => __( 'Add New Album', 'flickr-album-gallery' ),
			'add_new_item'       => __( 'Add New Album', 'flickr-album-gallery' ),
			'edit_item'          => __( 'Edit Flickr Album', 'flickr-album-gallery' ),
			'new_item'           => __( 'New Flickr Album', 'flickr-album-gallery' ),
			'view_item'          => __( 'View Album Gallery', 'flickr-album-gallery' ),
			'search_items'       => __( 'Search Album Galleries', 'flickr-album-gallery' ),
			'not_found'          => __( 'No Album Galleries Found', 'flickr-album-gallery' ),
			'not_found_in_trash' => __( 'No Album Galleries Found in Trash', 'flickr-album-gallery' ),
			'parent_item_colon'  => __( 'Parent Album Gallery:', 'flickr-album-gallery' ),
			'all_items'          => __( 'All Album Galleries', 'flickr-album-gallery' ),
			'menu_name'          => __( 'Flickr Album Gallery', 'flickr-album-gallery' ),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-format-gallery',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
		);

		register_post_type( 'flicgal_gallery', $args );
		add_filter( 'manage_edit-flicgal_gallery_columns', array( &$this, 'flicgal_gallery_columns' ) );
		add_action( 'manage_flicgal_gallery_posts_custom_column', array( &$this, 'flicgal_gallery_manage_columns' ), 10, 2 );
	}

	/**
	 * Register Legacy CPT (v2.2.14 backward compatibility)
	 *
	 * Registers the old 'fa_gallery' post type as hidden so that existing
	 * galleries created in v2.2.14 remain visible and queryable.
	 * The admin UI shows only the new 'flicgal_gallery' post type.
	 */
	public function flicgal_register_legacy_cpt() {
		$legacy_args = array(
			'labels'              => array(
				'name'          => __( 'Flickr Albums (Legacy)', 'flickr-album-gallery' ),
				'singular_name' => __( 'Flickr Album (Legacy)', 'flickr-album-gallery' ),
				'all_items'     => __( 'Legacy Albums (v2.2.14)', 'flickr-album-gallery' ),
				'menu_name'     => __( 'Legacy Albums', 'flickr-album-gallery' ),
			),
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'fa_gallery', $legacy_args );
	}

	function flicgal_gallery_columns( $columns ) {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'title'             => __( 'Title' ),
			'flicgal-shortcode' => __( 'Shortcode', 'flickr-album-gallery' ),
			'date'              => __( 'Date' ),
		);
		return $columns;
	}

	function flicgal_gallery_manage_columns( $columns, $post_id ) {
		switch ( $columns ) {
			case 'flicgal-shortcode':
				$shortcode = '[FLICGAL id=' . $post_id . ']';
				?>
				<div class="flicgal-list-actions">
					<span class="flicgal-list-shortcode">
						<code><?php echo esc_html( $shortcode ); ?></code>
						<button type="button" class="flicgal-list-copy-btn" onclick="flicgalCopyShortcode('<?php echo esc_js( $shortcode ); ?>', this)">
							<?php esc_html_e( 'Copy Shortcode', 'flickr-album-gallery' ); ?>
						</button>
					</span>
					<?php
					$duplicate_url = wp_nonce_url(
						admin_url( 'admin.php?action=flicgal_duplicate_gallery&post=' . $post_id ),
						'flicgal_duplicate_' . $post_id
					);
					?>
					<a href="<?php echo esc_url( $duplicate_url ); ?>" class="flicgal-list-duplicate-btn">
						<?php esc_html_e( 'Duplicate Gallery', 'flickr-album-gallery' ); ?>
					</a>
				</div>
				<?php
				break;
			default:
				break;
		}
	}


	// 3 - Meta Box Creator
	public function flicgal_add_meta_boxes() {
		add_meta_box( __( 'Configure Settings', 'flickr-album-gallery' ), __( 'Configure Settings', 'flickr-album-gallery' ), array( &$this, 'flicgal_meta_box_form_function' ), 'flicgal_gallery', 'normal', 'low' );
		add_meta_box( __( 'Flickr Album Gallery Shortcode', 'flickr-album-gallery' ), __( 'Flickr Album Gallery Shortcode', 'flickr-album-gallery' ), array( &$this, 'flicgal_shortcode_meta_box_form_function' ), 'flicgal_gallery', 'side', 'low' );
		add_meta_box( __( 'Rate Us', 'flickr-album-gallery' ), __( 'Rate Us', 'flickr-album-gallery' ), array( &$this, 'flicgal_rate_us_meta_box_function' ), 'flicgal_gallery', 'side', 'low' );
	}

	/**
	 * Rate Us Meta Box
	 */
	public function flicgal_rate_us_meta_box_function() {
		?>
		<div class="flicgal-rate-us-box">
			<p><?php esc_html_e( 'Please Review & Rate Us On WordPress', 'flickr-album-gallery' ); ?></p>
			<div class="flicgal-stars">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</div>
			<a href="https://wordpress.org/plugins/flickr-album-gallery/#reviews" target="_blank" class="flicgal-btn flicgal-btn-primary">
				<?php esc_html_e( 'RATE US', 'flickr-album-gallery' ); ?>
			</a>
		</div>
		<?php
	}


	/**
	 * Shortcode Meta Box
	 */
	public function flicgal_shortcode_meta_box_form_function() {
		$shortcode = '[FLICGAL id=' . get_the_ID() . ']';
		?>
		<p><?php esc_html_e( 'Use below shortcode in any Page/Post to publish your Flickr Album Gallery', 'flickr-album-gallery' ); ?></p>
		<div class="flicgal-shortcode-copy-wrap">
			<input readonly="readonly" type="text" value="<?php echo esc_attr( $shortcode ); ?>">
			<button type="button" class="flicgal-copy-btn" onclick="flicgalCopyShortcode('<?php echo esc_js( $shortcode ); ?>', this)">
				<?php esc_html_e( 'Copy Shortcode', 'flickr-album-gallery' ); ?>
			</button>
		</div>
		<?php
	}


	/**
	 * Gallery API Key & Album ID Form
	 */
	public function flicgal_meta_box_form_function( $post ) {
		$flicgal_settings = $this->flicgal_get_settings( $post->ID );

		if ( isset( $flicgal_settings[0]['flicgal_api_key'] ) && $flicgal_settings[0]['flicgal_album_id'] ) {
			$flicgal_api_key    = $flicgal_settings[0]['flicgal_api_key'];
			$flicgal_album_id   = $flicgal_settings[0]['flicgal_album_id'];
			$flicgal_show_title = isset( $flicgal_settings[0]['flicgal_show_title'] ) ? $flicgal_settings[0]['flicgal_show_title'] : '';
			$flicgal_col_layout = isset( $flicgal_settings[0]['flicgal_col_layout'] ) ? $flicgal_settings[0]['flicgal_col_layout'] : '';
			$flicgal_image_limit = isset( $flicgal_settings[0]['flicgal_image_limit'] ) ? $flicgal_settings[0]['flicgal_image_limit'] : 200;
		}

		/**
		 * Default Settings
		 */
		if ( ! isset( $flicgal_api_key ) ) {
			$flicgal_api_key = '';
		}

		if ( ! isset( $flicgal_album_id ) ) {
			$flicgal_album_id = '';
		}

		if ( ! isset( $flicgal_show_title ) ) {
			$flicgal_show_title = 'yes';
		}

		if ( ! isset( $flicgal_col_layout ) ) {
			$flicgal_col_layout = 'flicgal-col-4';
		}

		if ( ! isset( $flicgal_image_limit ) ) {
			$flicgal_image_limit = 200;
		}

		// Add nonce field for security
		wp_nonce_field( 'flicgal_save_settings', 'flicgal_settings_nonce' );
		?>
		<div class="flicgal-settings-wrap">
			<ul class="flicgal-tabs-nav">
				<li class="active" data-tab="flicgal-api-settings"><span class="dashicons dashicons-admin-network"></span> <?php esc_html_e( 'Flickr API', 'flickr-album-gallery' ); ?></li>
				<li data-tab="flicgal-layout-settings"><span class="dashicons dashicons-layout"></span> <?php esc_html_e( 'Layout', 'flickr-album-gallery' ); ?></li>
				<li data-tab="flicgal-pro-upgrade" class="flicgal-pro-tab"><span class="dashicons dashicons-star-filled"></span> <?php esc_html_e( 'Upgrade to Pro', 'flickr-album-gallery' ); ?></li>
			</ul>

			<div class="flicgal-tabs-content">
				<div class="flicgal-tab-pane active" id="flicgal-api-settings">
					<div class="flicgal-settings-section">
						<h3><?php esc_html_e( 'Flickr API Settings', 'flickr-album-gallery' ); ?></h3>
						<div class="flicgal-field-row">
							<label for="flicgal-api-key"><?php esc_html_e( 'Flickr API Key', 'flickr-album-gallery' ); ?></label>
							<div class="flicgal-field-input">
								<input required type="text" name="flicgal-api-key" id="flicgal-api-key" value="<?php echo esc_attr( $flicgal_api_key ); ?>" placeholder="e.g. 1234567890abcdef1234567890abcdef">
								<p class="description"><?php printf( __( 'Get your API key from <a href="%s" target="_blank">Flickr App Garden</a>', 'flickr-album-gallery' ), 'https://www.flickr.com/services/apps/create/apply/' ); ?></p>
							</div>
						</div>
						<div class="flicgal-field-row">
							<label for="flicgal-album-id"><?php esc_html_e( 'Flickr Album ID', 'flickr-album-gallery' ); ?></label>
							<div class="flicgal-field-input">
								<input required type="text" name="flicgal-album-id" id="flicgal-album-id" value="<?php echo esc_attr( $flicgal_album_id ); ?>" placeholder="e.g. 72157626359051649">
								<p class="description"><?php esc_html_e( 'Enter the ID of the Flickr album you want to display.', 'flickr-album-gallery' ); ?></p>
							</div>
						</div>
					</div>
				</div>

				<div class="flicgal-tab-pane" id="flicgal-layout-settings">
					<div class="flicgal-settings-section">
						<h3><?php esc_html_e( 'Gallery Layout', 'flickr-album-gallery' ); ?></h3>
						<div class="flicgal-field-row">
							<label><?php esc_html_e( 'Show Gallery Title', 'flickr-album-gallery' ); ?></label>
							<div class="flicgal-field-input">
								<label class="flicgal-radio-toggle">
									<input type="radio" name="flicgal-show-title" value="yes" <?php checked( $flicgal_show_title, 'yes' ); ?>>
									<span><?php esc_html_e( 'Yes', 'flickr-album-gallery' ); ?></span>
								</label>
								<label class="flicgal-radio-toggle">
									<input type="radio" name="flicgal-show-title" value="no" <?php checked( $flicgal_show_title, 'no' ); ?>>
									<span><?php esc_html_e( 'No', 'flickr-album-gallery' ); ?></span>
								</label>
							</div>
						</div>
						<div class="flicgal-field-row">
							<label for="flicgal-col-layout"><?php esc_html_e( 'Column Layout', 'flickr-album-gallery' ); ?></label>
							<div class="flicgal-field-input">
								<select name="flicgal-col-layout" id="flicgal-col-layout">
									<option value="flicgal-col-3" <?php selected( in_array( $flicgal_col_layout, array( 'flicgal-col-3', 'col-md-4' ) ) ? 'flicgal-col-3' : $flicgal_col_layout, 'flicgal-col-3' ); ?>><?php esc_html_e( 'Three Columns', 'flickr-album-gallery' ); ?></option>
									<option value="flicgal-col-4" <?php selected( in_array( $flicgal_col_layout, array( 'flicgal-col-4', 'col-md-3' ) ) ? 'flicgal-col-4' : $flicgal_col_layout, 'flicgal-col-4' ); ?>><?php esc_html_e( 'Four Columns', 'flickr-album-gallery' ); ?></option>
								</select>
							</div>
						</div>
						<div class="flicgal-field-row">
							<label for="flicgal-image-limit"><?php esc_html_e( 'Image Display Limit', 'flickr-album-gallery' ); ?></label>
							<div class="flicgal-field-input">
								<div class="flicgal-range-wrapper">
									<input type="range" name="flicgal-image-limit" id="flicgal-image-limit" min="1" max="200" step="1" value="<?php echo esc_attr( $flicgal_image_limit ); ?>" class="flicgal-range-slider">
									<span class="flicgal-range-value"><?php echo esc_html( $flicgal_image_limit ); ?></span>
								</div>
								<p class="description"><?php esc_html_e( 'Select the maximum number of images to fetch and display (1 to 200).', 'flickr-album-gallery' ); ?></p>
							</div>
						</div>
					</div>
				</div>

				<div class="flicgal-tab-pane" id="flicgal-pro-upgrade">
					<div class="flicgal-pro-section">
						<h3><?php esc_html_e( 'Upgrade to Flickr Album Gallery Pro', 'flickr-album-gallery' ); ?></h3>
						<p><?php esc_html_e( 'Unlock advanced features and premium support by upgrading to our Pro version.', 'flickr-album-gallery' ); ?></p>
						
						<table class="flicgal-comparison-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Features', 'flickr-album-gallery' ); ?></th>
									<th><?php esc_html_e( 'Free', 'flickr-album-gallery' ); ?></th>
									<th><?php esc_html_e( 'Pro', 'flickr-album-gallery' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php esc_html_e( 'Flickr Albums Display', 'flickr-album-gallery' ); ?></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Responsive Layout', 'flickr-album-gallery' ); ?></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Extended Column Layouts (2, 5, 6)', 'flickr-album-gallery' ); ?></td>
									<td><span class="dashicons dashicons-no-alt flicgal-cross"></span></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Video Support', 'flickr-album-gallery' ); ?></td>
									<td><span class="dashicons dashicons-no-alt flicgal-cross"></span></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Pagination', 'flickr-album-gallery' ); ?></td>
									<td><span class="dashicons dashicons-no-alt flicgal-cross"></span></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Premium Support', 'flickr-album-gallery' ); ?></td>
									<td><span class="dashicons dashicons-no-alt flicgal-cross"></span></td>
									<td><span class="dashicons dashicons-yes-alt flicgal-check"></span></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Image Display Limit', 'flickr-album-gallery' ); ?></td>
									<td><strong>200</strong></td>
									<td><strong>500</strong></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Lightbox Preview Styles', 'flickr-album-gallery' ); ?></td>
									<td><strong>1</strong></td>
									<td><strong>8</strong></td>
								</tr>
								<tr>
									<td><?php esc_html_e( 'Premium Hover Animations', 'flickr-album-gallery' ); ?></td>
									<td><strong><?php esc_html_e( 'None', 'flickr-album-gallery' ); ?></strong></td>
									<td><strong><?php esc_html_e( 'Multiple', 'flickr-album-gallery' ); ?></strong></td>
								</tr>
							</tbody>
						</table>

						<div class="flicgal-pro-cta">
							<a href="https://wpfrank.com/wordpress-plugins/flickr-album-gallery-pro/" target="_blank" class="flicgal-btn flicgal-btn-primary flicgal-btn-lg">
								<?php esc_html_e( 'Upgrade to Pro Now', 'flickr-album-gallery' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get Gallery Settings (with migration)
	 */
	public function flicgal_get_settings( $post_id ) {
		$settings = get_post_meta( $post_id, 'flicgal_settings', true );

		if ( empty( $settings ) ) {
			// Fallback to old meta key
			$settings = get_post_meta( $post_id, 'fag_settings', true );

			if ( ! empty( $settings ) ) {
				// Migrate to new key
				update_post_meta( $post_id, 'flicgal_settings', $settings );
			}
		}

		// Normalize keys from fag_ to flicgal_
		if ( is_array( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_inner = array();
					foreach ( $value as $inner_key => $inner_value ) {
						$new_inner_key = str_replace( 'fag_', 'flicgal_', $inner_key );
						$new_inner[ $new_inner_key ] = $inner_value;
					}
					$settings[ $key ] = $new_inner;
				}
			}
		}

		return $settings;
	}

	/**
	 * FlicGal Save
	 */
	public function flicgal_save_meta_box( $PostID ) {
		// Verify nonce
		if ( ! isset( $_POST['flicgal_settings_nonce'] ) || ! wp_verify_nonce( $_POST['flicgal_settings_nonce'], 'flicgal_save_settings' ) ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', $PostID ) ) {
			return;
		}

		// Check for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['flicgal-api-key'] ) && isset( $_POST['flicgal-album-id'] ) ) {

			$flicgal_api_key     = sanitize_text_field( wp_unslash( $_POST['flicgal-api-key'] ) );
			$flicgal_album_id    = sanitize_text_field( wp_unslash( $_POST['flicgal-album-id'] ) );
			$flicgal_show_title  = sanitize_text_field( wp_unslash( $_POST['flicgal-show-title'] ) );
			$flicgal_col_layout  = sanitize_text_field( wp_unslash( $_POST['flicgal-col-layout'] ) );
			$flicgal_image_limit = isset( $_POST['flicgal-image-limit'] ) ? absint( $_POST['flicgal-image-limit'] ) : 200;

			$flicgal_array[]     = array(
				'flicgal_api_key'        => $flicgal_api_key,
				'flicgal_album_id'       => $flicgal_album_id,
				'flicgal_show_title'     => $flicgal_show_title,
				'flicgal_col_layout'     => $flicgal_col_layout,
				'flicgal_image_limit'    => $flicgal_image_limit,
				'flicgal_plugin_version' => FLICGAL_PLUGIN_VER,
			);
			update_post_meta( $PostID, 'flicgal_settings', $flicgal_array );
		}
	}
	/**
	 * Register admin submenu pages.
	 */
	public function flicgal_register_submenu_pages() {
		add_submenu_page(
			'edit.php?post_type=flicgal_gallery',
			__( 'Documentation', 'flickr-album-gallery' ),
			__( 'Documentation', 'flickr-album-gallery' ),
			'manage_options',
			'flicgal-docs',
			'flicgal_docs_page'
		);
		add_submenu_page(
			'edit.php?post_type=flicgal_gallery',
			__( 'Our Plugins', 'flickr-album-gallery' ),
			__( 'Our Plugins', 'flickr-album-gallery' ),
			'manage_options',
			'flicgal-our-plugins',
			'flicgal_our_plugins_page'
		);
		add_submenu_page(
			'edit.php?post_type=flicgal_gallery',
			__( 'Our Themes', 'flickr-album-gallery' ),
			__( 'Our Themes', 'flickr-album-gallery' ),
			'manage_options',
			'flicgal-our-themes',
			'flicgal_our_themes_page'
		);
	}

	/**
	 * Enqueue admin CSS and JS.
	 */
	public function flicgal_admin_enqueue( $hook ) {
		$screen = get_current_screen();
		if ( $screen && ( 'flicgal_gallery' === $screen->post_type || strpos( $hook, 'flicgal' ) !== false ) ) {
			wp_enqueue_style( 'flicgal-admin-css', FLICGAL_PLUGIN_URL . 'css/flicgal-admin.css', array(), FLICGAL_PLUGIN_VER );
			wp_enqueue_script( 'flicgal-admin-js', FLICGAL_PLUGIN_URL . 'js/flicgal-admin.js', array(), FLICGAL_PLUGIN_VER, true );
		}
	}

	/**
	 * Add Duplicate link to row actions.
	 */
	public function flicgal_row_actions( $actions, $post ) {
		if ( 'flicgal_gallery' === $post->post_type && current_user_can( 'edit_posts' ) ) {
			$duplicate_url = wp_nonce_url(
				admin_url( 'admin.php?action=flicgal_duplicate_gallery&post=' . $post->ID ),
				'flicgal_duplicate_' . $post->ID
			);
			$actions['duplicate'] = '<a href="' . esc_url( $duplicate_url ) . '" title="' . esc_attr__( 'Duplicate this gallery', 'flickr-album-gallery' ) . '">' . esc_html__( 'Duplicate', 'flickr-album-gallery' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Handle gallery duplication.
	 */
	public function flicgal_duplicate_gallery() {
		if ( ! isset( $_GET['post'] ) || ! isset( $_GET['_wpnonce'] ) ) {
			wp_die( esc_html__( 'Invalid request.', 'flickr-album-gallery' ) );
		}

		$post_id = absint( $_GET['post'] );

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'flicgal_duplicate_' . $post_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'flickr-album-gallery' ) );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'flickr-album-gallery' ) );
		}

		$original = get_post( $post_id );
		if ( ! $original || 'flicgal_gallery' !== $original->post_type ) {
			wp_die( esc_html__( 'Gallery not found.', 'flickr-album-gallery' ) );
		}

		$new_post_id = wp_insert_post( array(
			'post_title'  => $original->post_title . ' (Copy)',
			'post_type'   => 'flicgal_gallery',
			'post_status' => 'draft',
			'post_author' => get_current_user_id(),
		) );

		if ( $new_post_id && ! is_wp_error( $new_post_id ) ) {
			// Copy gallery settings meta.
			$settings = get_post_meta( $post_id, 'flicgal_settings', true );
			if ( ! empty( $settings ) ) {
				update_post_meta( $new_post_id, 'flicgal_settings', $settings );
			}
			wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=flicgal_gallery' ) );
		exit;
	}
}//end class

global $flicgal_main;
$flicgal_main = new FlicGal_Main();

// Flickr Album gallery Shortcode [FLICGAL] + legacy [FAG] alias
require_once 'shortcode.php';
require_once 'widget.php';

// Admin-only includes
if ( is_admin() ) {
	require_once 'migration.php';
	require_once FLICGAL_PLUGIN_DIR . 'admin/docs.php';
	require_once FLICGAL_PLUGIN_DIR . 'admin/our-plugins.php';
	require_once FLICGAL_PLUGIN_DIR . 'admin/our-themes.php';
}
?>