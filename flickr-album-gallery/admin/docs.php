<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function flicgal_docs_page() {
	?>
	<div class="wrap">
		<div class="flicgal-admin-hero">
			<h1><?php esc_html_e( 'Documentation', 'flickr-album-gallery' ); ?> <span class="flicgal-version-badge">v<?php echo esc_html( FLICGAL_PLUGIN_VER ); ?></span></h1>
			<p><?php esc_html_e( 'Everything you need to set up and use Flickr Album Gallery on your WordPress website.', 'flickr-album-gallery' ); ?></p>
		</div>

		<div class="flicgal-docs-wrap">
			<div class="flicgal-docs-sidebar">
				<div class="sidebar-nav">
					<a href="#getting-started" class="active"><span class="dashicons dashicons-flag"></span>Getting Started</a>
					<a href="#api-key"><span class="dashicons dashicons-admin-network"></span>Flickr API Key</a>
					<a href="#create-gallery"><span class="dashicons dashicons-format-gallery"></span>Create Gallery</a>
					<a href="#shortcode"><span class="dashicons dashicons-shortcode"></span>Shortcode Usage</a>
					<a href="#layout"><span class="dashicons dashicons-grid-view"></span>Layout Options</a>
					<a href="#widget"><span class="dashicons dashicons-welcome-widgets-menus"></span>Widget</a>
					<a href="#migration"><span class="dashicons dashicons-update"></span>Migration Tool</a>
					<a href="#faq"><span class="dashicons dashicons-editor-help"></span>FAQ</a>
				</div>
			</div>

			<div class="flicgal-docs-content">
				<div class="flicgal-docs-section" id="getting-started">
					<h2>Getting Started</h2>
					<p>Follow these simple steps to display your Flickr albums on WordPress:</p>
					<div class="flicgal-step"><span class="flicgal-step-number">1</span><div class="flicgal-step-content"><strong>Get your Flickr API Key</strong><p>Apply for a free key at Flickr App Garden.</p></div></div>
					<div class="flicgal-step"><span class="flicgal-step-number">2</span><div class="flicgal-step-content"><strong>Create a Gallery</strong><p>Go to Flickr Album Gallery → Add New Album.</p></div></div>
					<div class="flicgal-step"><span class="flicgal-step-number">3</span><div class="flicgal-step-content"><strong>Enter API Key & Album ID</strong><p>Paste your API key and the Flickr Album ID.</p></div></div>
					<div class="flicgal-step"><span class="flicgal-step-number">4</span><div class="flicgal-step-content"><strong>Use the Shortcode</strong><p>Copy the shortcode and paste it into any page or post.</p></div></div>
				</div>

				<div class="flicgal-docs-section" id="api-key">
					<h2>How to Get Your Flickr API Key</h2>
					<ol>
						<li>Go to <a href="https://www.flickr.com/services/apps/create/" target="_blank">Flickr App Garden</a> and sign in.</li>
						<li>Click "Apply for a Non-Commercial Key".</li>
						<li>Fill in the application name and description.</li>
						<li>Your API Key will be displayed immediately. Copy the "Key" (not the "Secret").</li>
					</ol>
					<p><strong>Note:</strong> Your Flickr albums must be set to "Public" visibility.</p>
				</div>

				<div class="flicgal-docs-section" id="create-gallery">
					<h2>Creating a Gallery</h2>
					<ol>
						<li>Navigate to <strong>Flickr Album Gallery → Add New Album</strong>.</li>
						<li>Enter a title for your gallery.</li>
						<li>In Configure Settings, enter your <strong>Flickr API Key</strong>.</li>
						<li>Enter the <strong>Flickr Album ID</strong> (Photoset ID).</li>
						<li>Choose title visibility and column layout.</li>
						<li>Click <strong>Publish</strong>.</li>
					</ol>
					<h3>How to Find Your Album ID</h3>
					<p>Open your Flickr album in browser. The URL looks like:</p>
					<pre><code>https://www.flickr.com/photos/username/albums/72157720123456789</code></pre>
					<p>The number at the end is your Album ID.</p>
				</div>

				<div class="flicgal-docs-section" id="shortcode">
					<h2>Shortcode Usage</h2>
					<p>After creating a gallery, use the generated shortcode in any page or post:</p>
					<pre><code>[FLICGAL id=123]</code></pre>
					<p>Replace <code>123</code> with your gallery post ID. Find it in:</p>
					<ul>
						<li>The sidebar box on the gallery edit screen.</li>
						<li>The "Copy Shortcode" column in All Galleries list.</li>
					</ul>
					<h3>Legacy Shortcode Support</h3>
					<p>The old <code>[FAG]</code> shortcode from v2.2.14 is fully supported and works identically.</p>
				</div>

				<div class="flicgal-docs-section" id="layout">
					<h2>Layout Options</h2>
					<ul>
						<li><strong>2 Columns</strong> — Best for large photos, portfolios</li>
						<li><strong>3 Columns</strong> — Balanced layout for most galleries</li>
						<li><strong>4 Columns</strong> — Default, great for album overviews</li>
						<li><strong>6 Columns</strong> — Compact grid for many thumbnails</li>
					</ul>
					<p>All layouts are fully responsive and adapt to mobile screens.</p>
				</div>

				<div class="flicgal-docs-section" id="widget">
					<h2>Widget Usage</h2>
					<ol>
						<li>Go to <strong>Appearance → Widgets</strong>.</li>
						<li>Find the <strong>Flickr Album Gallery</strong> widget.</li>
						<li>Drag it to your desired widget area.</li>
						<li>Enter a title and select a gallery from the dropdown.</li>
						<li>Save the widget.</li>
					</ol>
				</div>

				<div class="flicgal-docs-section" id="migration">
					<h2>Migration Tool (v2.2.14 → v2.2.15)</h2>
					<p>Your existing galleries work automatically. For permanent migration:</p>
					<ol>
						<li>Go to <strong>Flickr Album Gallery → Migration Tool</strong>.</li>
						<li>Review the status dashboard.</li>
						<li>Take a database backup (recommended).</li>
						<li>Click <strong>"Run Full Migration"</strong>.</li>
					</ol>
					<p><strong>What gets migrated:</strong></p>
					<ul>
						<li>Post type: <code>fa_gallery</code> → <code>flicgal_gallery</code></li>
						<li>Shortcodes: <code>[FAG]</code> → <code>[FLICGAL]</code></li>
						<li>Meta keys: <code>fag_settings</code> → <code>flicgal_settings</code></li>
					</ul>
				</div>

				<div class="flicgal-docs-section" id="faq">
					<h2>Frequently Asked Questions</h2>
					<div class="flicgal-faq-item">
						<div class="flicgal-faq-question">Where do I get a Flickr API Key? <span class="dashicons dashicons-arrow-down-alt2"></span></div>
						<div class="flicgal-faq-answer">Visit <a href="https://www.flickr.com/services/apps/create/" target="_blank">Flickr App Garden</a> and apply for a non-commercial key. It is free and instant.</div>
					</div>
					<div class="flicgal-faq-item">
						<div class="flicgal-faq-question">My gallery is not showing photos. Why? <span class="dashicons dashicons-arrow-down-alt2"></span></div>
						<div class="flicgal-faq-answer">Make sure your Flickr album is set to "Public" visibility. Also verify your API Key and Album ID are correct.</div>
					</div>
					<div class="flicgal-faq-item">
						<div class="flicgal-faq-question">Can I display multiple galleries on one page? <span class="dashicons dashicons-arrow-down-alt2"></span></div>
						<div class="flicgal-faq-answer">Yes! Create separate galleries and place multiple shortcodes on the same page.</div>
					</div>
					<div class="flicgal-faq-item">
						<div class="flicgal-faq-question">I upgraded from v2.2.14 and galleries disappeared. What should I do? <span class="dashicons dashicons-arrow-down-alt2"></span></div>
						<div class="flicgal-faq-answer">Your galleries are safe. Go to Migration Tool and click "Run Full Migration". Your [FAG] shortcodes continue to work even without migration.</div>
					</div>
					<div class="flicgal-faq-item">
						<div class="flicgal-faq-question">Does the plugin support lightbox? <span class="dashicons dashicons-arrow-down-alt2"></span></div>
						<div class="flicgal-faq-answer">Yes, the plugin includes a built-in lightbox (blueimp Gallery) for viewing full-size images with navigation.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
