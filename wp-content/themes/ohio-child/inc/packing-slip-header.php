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
 * save printer ink.
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

/** No black fill on the items table header: black bold text with a rule below it (saves ink). */
add_filter( 'wpo_wcpdf_template_styles', 'dirtshack_packing_slip_styles', 10, 2 );
function dirtshack_packing_slip_styles( $css, $document ) {
	if ( dirtshack_is_packing_slip( $document ) ) {
		$css .= "\n.order-details thead th { color: black; background-color: transparent; border-top: 0; border-bottom: 1.5pt solid black; }\n";
	}
	return $css;
}
