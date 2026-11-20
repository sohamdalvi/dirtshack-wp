<?php
/**
 * DirtShack — LUT note on export invoices (PDF Invoices & Packing Slips).
 *
 * Export supplies (shipping country other than India) are made under a Letter of
 * Undertaking (LUT), so GST is not charged. The invoice must carry the LUT
 * reference. Printed only on the invoice document, and only when the shipping
 * country (falls back to billing if empty) is set and is not India.
 *
 * Update DIRTSHACK_LUT_TEXT when the LUT is renewed for a new period.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

const DIRTSHACK_LUT_LINE_1 = 'LUT Declaration: Supply meant for export under Letter of Undertaking (LUT) without payment of integrated tax.';
const DIRTSHACK_LUT_LINE_2 = 'LUT ARN: AD2711260128249 | Dated: 16-11-2026';

add_action( 'wpo_wcpdf_after_order_details', 'dirtshack_export_lut_note', 10, 2 );
function dirtshack_export_lut_note( $document_type, $order ) {
	if ( 'invoice' !== $document_type || ! $order instanceof WC_Order ) {
		return;
	}

	$country = strtoupper( trim( (string) $order->get_shipping_country() ) );
	if ( '' === $country ) {
		$country = strtoupper( trim( (string) $order->get_billing_country() ) );
	}

	// No country on the order, or India: domestic supply, no LUT note.
	if ( '' === $country || 'IN' === $country ) {
		return;
	}

	// Light grey box below the Total row, smaller muted text, two lines.
	printf(
		'<div class="export-lut-note" style="margin-top:5mm;padding:2.5mm 3mm;background:#F3F4F6;border-top:0.3mm solid #E5E7EB;color:#4B5563;font-size:8.5pt;line-height:1.4;">'
		. '<div>%s</div><div><strong>%s</strong></div></div>',
		esc_html( DIRTSHACK_LUT_LINE_1 ),
		esc_html( DIRTSHACK_LUT_LINE_2 )
	);
}
