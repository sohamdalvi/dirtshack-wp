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
 * The plugin's shop name / address settings are left unchanged, so the invoice
 * still prints the full legal name and address.
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
