<?php
/**
 * DirtShack — packing slip shop header (PDF Invoices & Packing Slips).
 *
 * The packing slip goes in the parcel, so it shows only the brand and GSTIN
 * instead of the registered business name and full address:
 *
 *   DirtShack Pune
 *   GSTN: 27AIQPD6061J1ZZ
 *
 * The customer's shipping address sits top-right next to the logo, and this
 * shop block moves below it on the left (pdf-templates/packing-slip.php).
 *
 * The Product / Quantity table header is printed without the black fill, to
 * save printer ink. Variation attributes (Size: XL, Colour: …) are not listed
 * under the items; any other item meta still is.
 *
 * The plugin's shop name / address settings and the invoice template are left
 * unchanged, so the invoice still prints the full legal name and address.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

const DIRTSHACK_PACKING_SLIP_SHOP_NAME = 'DirtShack Pune';
const DIRTSHACK_PACKING_SLIP_ADDRESS   = 'GSTN: 27AIQPD6061J1ZZ';

function dirtshack_is_packing_slip( $document ) {
	return is_object( $document ) && method_exists( $document, 'get_type' ) && 'packing-slip' === $document->get_type();
}

add_filter( 'wpo_wcpdf_shop_name_settings_text', 'dirtshack_packing_slip_shop_name', 10, 2 );
function dirtshack_packing_slip_shop_name( $name, $document ) {
	return dirtshack_is_packing_slip( $document ) ? DIRTSHACK_PACKING_SLIP_SHOP_NAME : $name;
}

add_filter( 'wpo_wcpdf_shop_address', 'dirtshack_packing_slip_shop_address', 10, 2 );
function dirtshack_packing_slip_shop_address( $address, $document ) {
	return dirtshack_is_packing_slip( $document ) ? DIRTSHACK_PACKING_SLIP_ADDRESS : $address;
}

/** Use the child theme's packing slip layout; every other document keeps the plugin template. */
add_filter( 'wpo_wcpdf_template_file', 'dirtshack_packing_slip_template', 10, 2 );
function dirtshack_packing_slip_template( $file_path, $type ) {
	$custom = get_stylesheet_directory() . '/pdf-templates/packing-slip.php';
	if ( 'packing-slip' === $type && 'packing-slip.php' === basename( $file_path ) && file_exists( $custom ) ) {
		return $custom;
	}
	return $file_path;
}

/**
 * No black fill on the items table header: black bold text with a rule below it (saves ink).
 * Text is larger than the plugin's 9pt default so the slip reads easily in the parcel.
 */
add_filter( 'wpo_wcpdf_template_styles', 'dirtshack_packing_slip_styles', 10, 2 );
function dirtshack_packing_slip_styles( $css, $document ) {
	if ( dirtshack_is_packing_slip( $document ) ) {
		$css .= "\n.order-details thead th { color: black; background-color: transparent; border-top: 0; border-bottom: 1.5pt solid black; }\n";
		$css .= "body, h3, h4 { font-size: 11pt; }\n";
		$css .= "td.shipping-address { width: 50%; font-size: 12pt; }\n";
		$css .= ".item-meta { font-size: 9pt; line-height: 9pt; }\n";
	}
	return $css;
}

/**
 * Drop variation attribute lines (Size: XL, Colour: …) from packing slip items.
 * WooCommerce lists them under the item only when they aren't already in the
 * product name, so only some products showed them. Other item meta is kept.
 */
add_filter( 'wpo_wcpdf_order_item_data', 'dirtshack_packing_slip_hide_variant_meta', 10, 3 );
function dirtshack_packing_slip_hide_variant_meta( $data, $order, $type ) {
	if ( 'packing-slip' !== $type || empty( $data['variation_id'] ) || empty( $data['meta'] ) || ! $data['item'] instanceof WC_Order_Item_Product ) {
		return $data;
	}

	$product    = $data['item']->get_product();
	$attributes = $product && $product->is_type( 'variation' ) ? array_keys( $product->get_variation_attributes( false ) ) : [];

	$lines = [];
	foreach ( $data['item']->get_formatted_meta_data() as $meta ) {
		if ( in_array( $meta->key, $attributes, true ) || 0 === strpos( $meta->key, 'pa_' ) ) {
			continue;
		}
		$lines[] = '<strong class="wc-item-meta-label">' . wp_kses_post( $meta->display_key ) . ':</strong> ' . wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
	}
	$data['meta'] = $lines ? '<ul class="wc-item-meta"><li>' . implode( '</li><li>', $lines ) . '</li></ul>' : '';

	return $data;
}
