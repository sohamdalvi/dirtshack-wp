<?php
/**
 * DirtShack — customer GSTIN on orders (admin-entered, B2B invoices).
 *
 * Adds a "GSTIN" field to the Billing section of the order edit screen. It is
 * stored as order meta _billing_gstin — the key the GST report
 * (mu-plugins/dirtshack-gst-report.php) already reads — and printed under the
 * billing address on the PDF invoice, as a GST invoice to a registered buyer
 * must carry their GSTIN.
 *
 * If a checkout field is added later, it should write the same _billing_gstin
 * key so this admin field, the invoice and the report keep working unchanged.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

const DIRTSHACK_GSTIN_META = '_billing_gstin';

/**
 * WooCommerce India state code => GST state code(s) (first 2 digits of a GSTIN).
 * Andhra Pradesh keeps its pre-2014 code 28 as a legacy alternative; Daman & Diu
 * (25) merged into Dadra & Nagar Haveli (26) in 2020.
 */
const DIRTSHACK_GST_STATE_CODES = [
	'JK' => [ '01' ], 'HP' => [ '02' ], 'PB' => [ '03' ], 'CH' => [ '04' ],
	'UK' => [ '05' ], 'HR' => [ '06' ], 'DL' => [ '07' ], 'RJ' => [ '08' ],
	'UP' => [ '09' ], 'BR' => [ '10' ], 'SK' => [ '11' ], 'AR' => [ '12' ],
	'NL' => [ '13' ], 'MN' => [ '14' ], 'MZ' => [ '15' ], 'TR' => [ '16' ],
	'ML' => [ '17' ], 'AS' => [ '18' ], 'WB' => [ '19' ], 'JH' => [ '20' ],
	'OR' => [ '21' ], 'CT' => [ '22' ], 'MP' => [ '23' ], 'GJ' => [ '24' ],
	'DD' => [ '26', '25' ], 'DN' => [ '26' ], 'MH' => [ '27' ], 'KA' => [ '29' ],
	'GA' => [ '30' ], 'LD' => [ '31' ], 'KL' => [ '32' ], 'TN' => [ '33' ],
	'PY' => [ '34' ], 'AN' => [ '35' ], 'TS' => [ '36' ], 'AP' => [ '37', '28' ],
	'LA' => [ '38' ],
];

/** Uppercase and strip spaces/dashes, so "27 aapfu-0939f1zv" is stored cleanly. */
function dirtshack_normalize_gstin( $gstin ) {
	return strtoupper( preg_replace( '/[\s\-]+/', '', (string) $gstin ) );
}

/**
 * Problems with a (normalized) GSTIN for the given billing state, as
 * human-readable strings. Empty array = looks valid.
 *
 * @return string[]
 */
function dirtshack_gstin_problems( $gstin, $billing_country, $billing_state ) {
	if ( '' === $gstin ) {
		return [];
	}

	if ( ! preg_match( '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gstin ) ) {
		return [ 'GSTIN format is invalid — it should be 15 characters, e.g. 27AAPFU0939F1ZV.' ];
	}

	$problems = [];

	// Check digit: base-36 Luhn over the first 14 characters.
	$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$sum   = 0;
	for ( $i = 0; $i < 14; $i++ ) {
		$product = strpos( $chars, $gstin[ $i ] ) * ( $i % 2 ? 2 : 1 );
		$sum    += intdiv( $product, 36 ) + $product % 36;
	}
	if ( $chars[ ( 36 - $sum % 36 ) % 36 ] !== $gstin[14] ) {
		$problems[] = 'GSTIN check digit does not match — there is probably a typo.';
	}

	if ( 'IN' !== $billing_country ) {
		$problems[] = 'Billing country is not India.';
	} elseif ( isset( DIRTSHACK_GST_STATE_CODES[ $billing_state ] )
		&& ! in_array( substr( $gstin, 0, 2 ), DIRTSHACK_GST_STATE_CODES[ $billing_state ], true ) ) {
		$problems[] = sprintf(
			'GSTIN state code %s does not match the billing state (%s expects %s).',
			substr( $gstin, 0, 2 ),
			$billing_state,
			implode( ' or ', DIRTSHACK_GST_STATE_CODES[ $billing_state ] )
		);
	}

	return $problems;
}

// ─── 1. Admin field ──────────────────────────────────────────────────────────
// WooCommerce renders billing fields in the order meta box and saves any field
// without a setter to "_billing_{key}" meta, i.e. _billing_gstin.

add_filter( 'woocommerce_admin_billing_fields', 'dirtshack_admin_billing_gstin_field' );
function dirtshack_admin_billing_gstin_field( $fields ) {
	$fields['gstin'] = [
		'label'         => 'GSTIN',
		'show'          => true,
		'wrapper_class' => 'form-field-wide',
		'placeholder'   => '27AAPFU0939F1ZV',
		'description'   => 'Customer GST number, for B2B invoices. Add it before the order is completed / the month is filed.',
	];
	return $fields;
}

// WooCommerce saves the meta box at priority 40; normalize the value after it.
add_action( 'woocommerce_process_shop_order_meta', 'dirtshack_normalize_saved_gstin', 50 );
function dirtshack_normalize_saved_gstin( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$raw        = (string) $order->get_meta( DIRTSHACK_GSTIN_META );
	$normalized = dirtshack_normalize_gstin( $raw );

	if ( '' === $normalized ) {
		if ( '' !== $raw || $order->meta_exists( DIRTSHACK_GSTIN_META ) ) {
			$order->delete_meta_data( DIRTSHACK_GSTIN_META );
			$order->save();
		}
	} elseif ( $normalized !== $raw ) {
		$order->update_meta_data( DIRTSHACK_GSTIN_META, $normalized );
		$order->save();
	}
}

// ─── 2. Validation warning on the order screen ───────────────────────────────

add_action( 'woocommerce_admin_order_data_after_billing_address', 'dirtshack_admin_gstin_warnings' );
function dirtshack_admin_gstin_warnings( $order ) {
	$problems = dirtshack_gstin_problems(
		(string) $order->get_meta( DIRTSHACK_GSTIN_META ),
		$order->get_billing_country(),
		strtoupper( trim( $order->get_billing_state() ) )
	);

	foreach ( $problems as $problem ) {
		printf(
			'<p style="color:#b32d2e;"><strong>GSTIN warning:</strong> %s</p>',
			esc_html( $problem )
		);
	}
}

// ─── 3. PDF invoice (PDF Invoices & Packing Slips) ───────────────────────────

add_action( 'wpo_wcpdf_after_billing_address', 'dirtshack_pdf_invoice_gstin', 10, 2 );
function dirtshack_pdf_invoice_gstin( $document_type, $order ) {
	if ( ! in_array( $document_type, [ 'invoice', 'credit-note' ], true ) || ! $order instanceof WC_Order ) {
		return;
	}

	$gstin = (string) $order->get_meta( DIRTSHACK_GSTIN_META );
	if ( '' === $gstin ) {
		return;
	}

	printf( '<div class="billing-gstin">GSTIN: %s</div>', esc_html( $gstin ) );
}
