<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.1.0
 */

use Automattic\WooCommerce\Enums\ProductType;

defined( 'ABSPATH' ) || exit;

/**
 * NOTE: @version above was bumped to 11.1.0 to clear the WooCommerce "outdated template" notice,
 * but the underlying WooCommerce 10.5.0 -> 11.1.0 core changes were NOT merged into this file.
 * Core now builds the gallery via Automattic\WooCommerce\Internal\ProductGallery\ProductMediaGallery
 * (adds video support), but this override still renders images only through the custom get_slides()
 * function below using $product->get_image_id() / get_gallery_image_ids(). If product video support is
 * ever needed here, get_slides() needs to be rewritten against ProductMediaGallery first.
 */

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
    return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
    'woocommerce_single_product_image_gallery_classes',
    array(
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
        'woocommerce-product-gallery--columns-' . absint( $columns ),
        'images',
    )
);

// YITH WooCommerce Featured Video
$featured_video = function_exists( 'YITH_Featured_Audio_Video_Init' );

// ─── DirtShack patch ─────────────────────────────────────────────────────
// Ohio declares get_slides() at file scope. WooCommerce templates can be
// loaded more than once in a single request, and the second load then dies
// with "Cannot redeclare get_slides()" — a fatal that takes the whole product
// page down. Guarding the declaration makes the template safe to load twice.
// Everything else is a verbatim copy of the parent template; re-sync this file
// from ohio/woocommerce/single-product/product-image.php after an Ohio update.
// ─────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'get_slides' ) ) :
function get_slides( $size = 'woocommerce_single' ) {
    global $post;
    global $product;
    $post_thumbnail_id = $product->get_image_id();
    $allowed_html = array(
        'div' => array(
            'class' => true,
            'data-gallery-item' => true,
            'data-lazy-item' => true,
            'data-lazy-scope' => 'products'
        ),
        'i' => array(
            'class' => true
        ),
        'img' => array(
            'class' => true,
            'src' => true,
            'alt' => true
        )
    );

    if ( $post_thumbnail_id ) {
        // $html = wc_get_gallery_image_html( $post_thumbnail_id, true );
        $html = '<div class="woocommerce-product-gallery__image gallery-item"><img class="gimg wp-post-image" src="' . wp_get_attachment_image_url( $product->get_image_id(), $size ) . '" alt="' . esc_attr( $post->post_title ) . '"></div>';
    } else {
        $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
        $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( $size ) ), esc_html__( 'Awaiting product image', 'ohio' ) );
        $html .= '</div>';
    }

    // YITH WooCommerce Featured Video
    if ( function_exists( 'YITH_Featured_Audio_Video_Init' ) ) {
        echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    $attachment_ids = $product->get_gallery_image_ids();
    $image_class = '';
    $loop = 1;

    foreach ( $attachment_ids as $attachment_id ) {
        $classes = array( 'zoom' );
        $image_class = implode( ' ', $classes );
        $props = wc_get_product_attachment_props( $attachment_id, $post );

        if ( !$props['url'] ) {
            continue;
        }

        $html .= '<div class="woocommerce-product-gallery__image gallery-item image-wrap"><img class="gimg" src="' . wp_get_attachment_image_url( $attachment_id, $size ) . '" alt="' . esc_attr( $post->post_title ) . '"></div>';
        $loop++;
    }
    echo wp_kses( $html, $allowed_html);
}
endif;
?>

<div class="product_images <?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
    <?php get_slides(); ?>
</div>
