<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_shortcode( 'FLICGAL', 'flicgal_shortcode' );
add_shortcode( 'FAG', 'flicgal_shortcode' ); // v2.2.14 backward compatibility alias
function flicgal_shortcode( $Id ) {
    ob_start();
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'imagesloaded' );
    wp_enqueue_script( 'flicgal-blueimp-gallery-js', plugins_url( 'js/blueimp-gallery.js', __FILE__ ), array( 'jquery' ), FLICGAL_PLUGIN_VER, true );
    wp_enqueue_script( 'flicgal-jquery-blueimp-gallery-js', plugins_url( 'js/jquery.blueimp-gallery.js', __FILE__ ), array( 'flicgal-blueimp-gallery-js' ), FLICGAL_PLUGIN_VER, true );
    wp_enqueue_script( 'flicgal-frontend-js', plugins_url( 'js/flicgal-frontend.js', __FILE__ ), array( 'jquery' ), FLICGAL_PLUGIN_VER, true );

    // CSS
    wp_enqueue_style( 'flicgal-blueimp-gallery-css', FLICGAL_PLUGIN_URL . 'css/blueimp-gallery.css' );
    wp_enqueue_style( 'flicgal-site-css', FLICGAL_PLUGIN_URL . 'css/flicgal-shortcode-style.css' );

    if ( isset( $Id['id'] ) ) {
        global $flicgal_main;
        $flickr_album_post_id = sanitize_text_field($Id['id']);
        // Query BOTH new and legacy post types for backward compatibility with v2.2.14
        $AllGalleries = array(
            'p'          => $Id['id'],
            'post_type'  => array( 'flicgal_gallery', 'fa_gallery' ),
            'orderby'    => 'ASC',
            'post_status' => 'publish',
        );
        $loop = new WP_Query( $AllGalleries );

        while ( $loop->have_posts() ) :
            $loop->the_post();
            $ID = get_the_ID();

            $flicgal_settings = $flicgal_main->flicgal_get_settings( $ID );

            if ( is_array( $flicgal_settings ) ) {
                foreach ( $flicgal_settings as $flicgal_album ) {
                    $flicgal_api_key    = $flicgal_album['flicgal_api_key'];
                    $flicgal_album_id   = $flicgal_album['flicgal_album_id'];
                    $flicgal_show_title = isset( $flicgal_album['flicgal_show_title'] ) ? $flicgal_album['flicgal_show_title'] : '';
                    $flicgal_col_layout = isset( $flicgal_album['flicgal_col_layout'] ) ? $flicgal_album['flicgal_col_layout'] : 'flicgal-col-4';
                    $flicgal_image_limit = isset( $flicgal_album['flicgal_image_limit'] ) ? $flicgal_album['flicgal_image_limit'] : 200;
                    // Inline CSS
                    $custom_css = "
                        .flicgal-gallery-wrapper .flickr-img-responsive {
                            width:100% !important;
                            height:auto !important;
                            display:block !important;
                        }
                        .flicgal-gallery-wrapper .flicgal-loading-img img {
                            max-width: 45px !important;
                            max-height: 45px !important;
                            box-shadow: none !important;
                        }
                        .flicgal-gallery-wrapper .flicgal-gallery-container {
                            padding:15px !important;
                        }
                        .flicgal-gallery-wrapper .play-pause {
                            display: none !important;
                        }
                        .flicgal-gallery-wrapper.gallery-" . esc_html( $ID ) . " {
                            overflow:hidden !important;
                            clear: both !important;
                        }
                        .flicgal-gallery-wrapper .flicgal-fnf {
                            background-color: #a92929 !important;
                            border-radius: 5px !important;
                            color: #fff !important;
                            font-family: initial !important;
                            text-align: center !important;
                            padding:12px !important;
                        }
                    ";
                    wp_add_inline_style( 'flicgal-site-css', $custom_css );

                    // Inline JS
                    $custom_js = "
                    jQuery(function() {
                        jQuery('.gallery-" . esc_js( $ID ) . "').flicgal_jquery_plugin({
                            apiKey: '" . esc_js( $flicgal_api_key ) . "',
                            photosetId: '" . esc_js( $flicgal_album_id ) . "',
                            colLayout: '" . esc_js( $flicgal_col_layout ) . "',
                            galleryId: '" . esc_js( $ID ) . "',
                            imageLimit: '" . esc_js( $flicgal_image_limit ) . "'
                        });
                    });
                    ";
                    wp_add_inline_script( 'flicgal-frontend-js', $custom_js );

                    ?>
                    <div class="flicgal-gallery-wrapper gallery-<?php echo esc_attr( $ID ); ?>">
                        <?php if ( $flicgal_show_title == 'yes' ) { ?>
                            <h3 class="flicgal-title"><?php echo esc_html( get_the_title( $ID ) ); ?></h3>
                        <?php } ?>
                        <div class="flicgal-gallery-container-wrapper">
                            <div class="flicgal-spinner-wrapper">
                                <div class="flicgal-loading-img"><img src="<?php echo esc_url( FLICGAL_PLUGIN_URL . 'img/loading.gif' ); ?>" /></div>
                            </div>
                            <div class="flicgal-gallery-container"></div>
                        </div>
                        <!-- Blueimp gallery -->
                        <div id="blueimp-gallery-<?php echo esc_attr( $ID ); ?>" class="blueimp-gallery blueimp-gallery-controls">
                            <div class="slides"></div>
                            <h3 class="title"></h3>
                            <a class="prev">‹</a>
                            <a class="next">›</a>
                            <a class="close">×</a>
                            <a class="play-pause"></a>
                            <ol class="indicator"></ol>
                        </div>
                    </div>
                    <?php
                } // end of foreach
            } // end of is_array
            ?>

            <?php
            $blueimp_js = "
            jQuery(function() {
                jQuery.extend(blueimp.Gallery.prototype.options, {
                    useBootstrapModal: false,
                    hidePageScrollbars: false,
                    container: \"#blueimp-gallery-" . esc_js($ID) . "\",
                });
            });
            ";
            wp_add_inline_script( 'flicgal-jquery-blueimp-gallery-js', $blueimp_js );
        endwhile;
    } else {
        $flicgal_allowed_shortcode_msg = array(
            'div' => array(
                'align' => array(),
                'class' => array(),
            ),
        );
        echo wp_kses( "<div align='center' class='flicgal-alert flicgal-alert-danger'>" . __( 'Sorry! Invalid Flickr Album Shortcode Embedded', 'flickr-album-gallery' ) . '</div>', $flicgal_allowed_shortcode_msg );
    }
    wp_reset_postdata();
    return ob_get_clean();
}
?>