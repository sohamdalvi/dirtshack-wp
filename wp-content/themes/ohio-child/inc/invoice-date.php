<?php
/**
 * DirtShack — invoice date = order completion date.
 *
 * PDF Invoices & Packing Slips creates the invoice (number + date) the first time
 * it is needed — typically when the PDF is attached to the New/Processing order
 * email — so an order placed on the 5th and completed on the 8th gets an invoice
 * dated the 5th. We invoice on fulfilment, so when an order moves to Completed we
 * re-date the invoice to the completion date (creating the invoice then if it
 * doesn't exist yet). The invoice number is left untouched.
 *
 * Runs at priority 5 on woocommerce_order_status_completed, ahead of WooCommerce's
 * transactional email (priority 10), so the PDF attached to the "Completed order"
 * email already carries the new date. The date is written through the plugin's
 * document API, which updates _wcpdf_invoice_date — the same meta the GST report
 * (mu-plugins/dirtshack-gst-report.php) reads.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Order meta flag set once the invoice has been re-dated, so re-completing an
 *  order later (e.g. completed → processing → completed) never moves the date of
 *  an invoice that may already have been filed. */
const DIRTSHACK_INVOICE_DATED_META = '_ds_invoice_dated_on_completion';

add_action( 'woocommerce_order_status_completed', 'dirtshack_invoice_date_on_completion', 5, 2 );
function dirtshack_invoice_date_on_completion( $order_id, $order = null ) {
	if ( ! function_exists( 'wcpdf_get_invoice' ) ) {
		return;
	}

	$order = $order instanceof WC_Order ? $order : wc_get_order( $order_id );
	if ( ! $order || $order->get_meta( DIRTSHACK_INVOICE_DATED_META ) ) {
		return;
	}

	// true = create the invoice now if it doesn't exist yet.
	$invoice = wcpdf_get_invoice( $order, true );
	if ( ! $invoice ) {
		return;
	}

	$completed = $order->get_date_completed();
	$invoice->set_date( $completed ? $completed : new WC_DateTime() );
	$invoice->save();

	$order->update_meta_data( DIRTSHACK_INVOICE_DATED_META, 'yes' );
	$order->save_meta_data();
}
