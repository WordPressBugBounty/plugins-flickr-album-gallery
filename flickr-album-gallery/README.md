# Flickr Album Gallery

Display Flickr albums on WordPress with lightbox preview and shortcode integration. Refactored for standard repository compliance.

[![WordPress Plugin](https://img.shields.io/wordpress/plugin/v/flickr-album-gallery.svg)](https://wordpress.org/plugins/flickr-album-gallery/)
[![WordPress Compatible](https://img.shields.io/wordpress/v/flickr-album-gallery.svg)](https://wordpress.org/plugins/flickr-album-gallery/)

## Description

Flickr Album Gallery helps you showcase your Flickr photo collections directly on your WordPress website. Whether you're a photographer sharing your portfolio, a blogger documenting your travels, or a business highlighting your work, this plugin provides a straightforward way to embed and display Flickr albums.

### Third-Party Service Disclosure

This plugin utilizes the **Flickr API** to fetch and display images from your Flickr account. By using this plugin, you are interacting with Flickr's services.
* **Service URL:** [https://www.flickr.com/services/api/](https://www.flickr.com/services/api/)
* **Terms of Service:** [https://www.flickr.com/help/terms](https://www.flickr.com/help/terms)
* **Privacy Policy:** [https://www.flickr.com/help/privacy](https://www.flickr.com/help/privacy)

## Key Features

*   **Easy Integration**: Embed Flickr albums using shortcode `[FLICGAL id=123]`.
*   **Responsive Layouts**: Choose from 2, 3, 4, or 6 column grids.
*   **Lightbox Preview**: Beautiful built-in lightbox for full-size image viewing.
*   **Masonry Support**: Clean masonry grid display.
*   **SEO Friendly**: Includes title tags and alt text for images.
*   **No Limits**: No artificial limits on the number of photos per album.
*   **Lightweight**: Zero third-party framework dependencies (Bootstrap removed).

## Installation

1.  Go to your WordPress dashboard and navigate to **Plugins > Add New**.
2.  Search for **"Flickr Album Gallery"** and click **Install Now**.
3.  **Activate** the plugin.
4.  Navigate to **Flickr Album Gallery > Add New Album**.
5.  Enter your Flickr API Key and Album ID.
6.  Publish and use the shortcode `[FLICGAL id=123]` on any page or post.

## Frequently Asked Questions

### How do I get my Flickr API Key?
Visit the [Flickr API Guide](https://wpfrank.com/how-to-get-flickr-album-id/) for instructions.

### How do I find my Flickr Album ID?
The Album ID is the number in your Flickr album URL after `/albums/`.

### Is it compatible with Page Builders?
Yes, it works perfectly with Elementor, Gutenberg, Divi, and others.

## Changelog

### 2.2.16
*   **Security**: Conducted comprehensive security audit; hardened data saving with strict nonce verification and capability checks.
*   **UI**: Resolved lightbox control distortions and enforced consistent layout across themes.
*   **Standards**: Full compliance with WordPress.org 2026 repository guidelines.
*   **Assets**: Improved asset versioning for better cache management.

### 2.2.15
*   **Refactoring**: Full codebase refactor for standard compliance.
*   **Framework Removal**: Replaced Bootstrap with a custom lightweight Flexbox grid.
*   **Cleanup**: Removed all promotional content and third-party upsells.
*   **Namespace**: Migrated from `fag_` to `flicgal_` for better isolation.

## License

This project is licensed under the GPLv2 or later.
