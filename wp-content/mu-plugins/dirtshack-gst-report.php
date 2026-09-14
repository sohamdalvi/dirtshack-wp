<?php
/**
 * Plugin Name: DirtShack GST Report
 * Description: Generates monthly GST reports as CSV. Works in WP Admin UI and WP-CLI modes.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

define( 'DS_GST_STORE_STATE',    'MH' );
define( 'DS_GST_DEFAULT_RATE',   18.0 );
define( 'DS_GST_BATCH_SIZE',     50 );
define( 'DS_GST_ADMIN_SLUG',     'dirtshack-gst-report' );
define( 'DS_GST_NONCE_ACTION',   'ds_gst_report_download' );
define( 'DS_GST_CAPABILITY',     'manage_woocommerce' );

// ---------------------------------------------------------------------------
// Helpers (shared)
// ---------------------------------------------------------------------------

/**
 * Derive GST rate (%) from _tax_class product meta value.
 */
function ds_gst_rate_from_tax_class( string $tax_class ): float {
	if ( preg_match( '/^(\d+)-gst$/i', trim( $tax_class ), $m ) ) {
		return (float) $m[1];
	}
	return DS_GST_DEFAULT_RATE;
}

/**
 * Fetch HSN code from product attribute taxonomy.
 */
function ds_get_hsn_code( int $product_id ): string {
	if ( ! $product_id ) {
		return '';
	}
	$terms = wp_get_object_terms( $product_id, 'pa_hsn_code', [ 'fields' => 'names' ] );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}
	return (string) $terms[0];
}

/**
 * Format a WC_DateTime or null as d/m/Y H:i.
 */
function ds_fmt_wc_datetime( ?WC_DateTime $dt ): string {
	return $dt ? $dt->date( 'd/m/Y H:i' ) : '';
}

/**
 * Format _wcpdf_invoice_date meta value as d/m/Y.
 * Handles plain Unix timestamp (plugin <3.x) and serialized WC_DateTime (3.x+).
 */
function ds_fmt_invoice_date( $raw ): string {
	if ( empty( $raw ) ) {
		return '';
	}
	if ( is_string( $raw ) && strpos( $raw, 'WC_DateTime' ) !== false ) {
		$dt = @unserialize( $raw ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		if ( $dt instanceof WC_DateTime ) {
			return $dt->date( 'd/m/Y' );
		}
		// Fallback: extract timestamp from serialised string
		if ( preg_match( '/"timestamp";i:(\d+)/', $raw, $m ) ) {
			return wp_date( 'd/m/Y', (int) $m[1] );
		}
		return '';
	}
	if ( is_numeric( $raw ) ) {
		// wp_date() formats in the store timezone; date() would use UTC and show
		// invoices raised 00:00–05:30 IST as the previous day.
		return wp_date( 'd/m/Y', (int) $raw );
	}
	return '';
}

/**
 * Convert a Y-m-d date range to [start, end] Unix timestamps covering the whole
 * days in the store timezone.
 */
function ds_gst_range_timestamps( string $start_date, string $end_date ): array {
	$tz = wp_timezone();
	return [
		( new DateTimeImmutable( $start_date . ' 00:00:00', $tz ) )->getTimestamp(),
		( new DateTimeImmutable( $end_date . ' 23:59:59', $tz ) )->getTimestamp(),
	];
}

/**
 * Order numbers of completed orders, completed within the range, that have no
 * invoice date — these can never appear in any month's report.
 *
 * @return string[]
 */
function ds_gst_orders_missing_invoice( string $start_date, string $end_date ): array {
	$orders = wc_get_orders( [
		'status'         => [ 'wc-completed' ],
		'date_completed' => $start_date . '...' . $end_date,
		'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			[
				'key'     => '_wcpdf_invoice_date',
				'compare' => 'NOT EXISTS',
			],
		],
		'limit'          => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
	] );

	return array_map( static fn( $order ) => (string) $order->get_order_number(), $orders );
}

/**
 * Quote a single CSV field.
 */
function ds_csv_field( $value ): string {
	$str = ( $value === null || $value === false ) ? '' : (string) $value;
	return '"' . str_replace( '"', '""', $str ) . '"';
}

/**
 * Format a number to 2 decimal places.
 */
function ds_fmt_num( $n ): string {
	return number_format( (float) $n, 2, '.', '' );
}

/**
 * Log a message in CLI mode; silently skip in Admin mode.
 */
function ds_cli_log( string $message ): void {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
	}
}

// ---------------------------------------------------------------------------
// Core report function
// ---------------------------------------------------------------------------

/**
 * Generate GST CSV report for a date range.
 *
 * @param  string $start_date Y-m-d
 * @param  string $end_date   Y-m-d
 * @return string             Full CSV content including UTF-8 BOM
 * @throws Exception          On any processing error
 */
function ds_generate_gst_csv( string $start_date, string $end_date ): string {

	$headers = [
		'row_type',               // 0
		'order_number',           // 1
		'invoice_number',         // 2
		'invoice_date',           // 3
		'order_date',             // 4
		'paid_date',              // 5
		'billing_first_name',     // 6
		'billing_last_name',      // 7
		'billing_city',           // 8
		'billing_state',          // 9
		'GSTIN (Customer)',        // 10
		'order_subtotal',         // 11  invoice: WC subtotal; others: ""
		'order_total',            // 12  invoice: WC total;    others: ""
		'item_product_id',        // 13
		'item_name',              // 14
		'item_sku',               // 15
		'item_quantity',          // 16
		'item_subtotal',          // 17
		'item_subtotal_tax',      // 18
		'item_total',             // 19
		'item_total_tax',         // 20
		'HSN Code',               // 21  shipping rows: 996812
		'Place of Supply State Code', // 22
		'Taxable Value (Final Excl. Tax)', // 23  invoice: sum; item/shipping: individual
		'CGST Rate (%)',          // 24
		'CGST Amount',            // 25  invoice: sum; item/shipping: individual
		'SGST Rate (%)',          // 26
		'SGST Amount',            // 27  invoice: sum; item/shipping: individual
		'IGST Rate (%)',          // 28
		'IGST Amount',            // 29  invoice: sum; item/shipping: individual
	];

	// Blank template — all columns empty; filled by name via $col index map.
	$empty_row = array_fill( 0, count( $headers ), '' );

	// ------------------------------------------------------------------
	// Fetch orders in batches of DS_GST_BATCH_SIZE
	// ------------------------------------------------------------------

	// Orders belong to the month of their invoice date (_wcpdf_invoice_date, a Unix
	// timestamp), not the order created date. Range is whole days in store time.
	[ $start_ts, $end_ts ] = ds_gst_range_timestamps( $start_date, $end_date );

	$page       = 1;
	$all_orders = [];

	do {
		$batch = wc_get_orders( [
			'status'     => [ 'wc-completed' ],
			'meta_query' => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				[
					'key'     => '_wcpdf_invoice_date',
					'value'   => [ $start_ts, $end_ts ],
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC',
				],
			],
			'meta_key'   => '_wcpdf_invoice_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'    => 'meta_value_num',
			'order'      => 'ASC',
			'limit'      => DS_GST_BATCH_SIZE,
			'paged'      => $page,
		] );

		ds_cli_log( sprintf( 'Processing batch %d (%d orders)...', $page, count( $batch ) ) );

		$all_orders = array_merge( $all_orders, $batch );
		$page++;
	} while ( count( $batch ) === DS_GST_BATCH_SIZE );

	ds_cli_log( 'Total orders found: ' . count( $all_orders ) );

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		$missing = ds_gst_orders_missing_invoice( $start_date, $end_date );
		if ( $missing ) {
			WP_CLI::warning( sprintf(
				'%d completed order(s) in this period have no invoice date and are NOT in the report: %s',
				count( $missing ),
				implode( ', ', $missing )
			) );
		}
	}

	if ( empty( $all_orders ) ) {
		throw new Exception( 'No completed orders with an invoice date in this period.' );
	}

	ds_cli_log( 'Building CSV rows...' );

	// ------------------------------------------------------------------
	// Build rows — invoice → item(s) → shipping (3 row types per order)
	// ------------------------------------------------------------------

	// Column index map (matches $headers order above)
	$col = array_flip( $headers );

	$rows = [];

	foreach ( $all_orders as $order ) {
		/** @var WC_Order $order */

		$items = $order->get_items();
		if ( empty( $items ) ) {
			continue;
		}

		$order_number   = $order->get_order_number();
		$invoice_number = (string) $order->get_meta( '_wcpdf_invoice_number', true );
		$billing_state  = strtoupper( trim( $order->get_billing_state() ) );
		$is_intrastate  = ( $billing_state === DS_GST_STORE_STATE );

		// Running totals for the invoice row (summed from item + shipping rows)
		$total_taxable = 0.0;
		$total_cgst    = 0.0;
		$total_sgst    = 0.0;
		$total_igst    = 0.0;

		$child_rows = [];

		// ── Helper: build GST split for a taxable value + rate ────────
		$gst_split = function( float $taxable, float $rate ) use ( $is_intrastate ): array {
			if ( $is_intrastate ) {
				$half = $rate / 2;
				return [
					'cgst_rate'   => $half,
					'cgst_amount' => ( $half / 100 ) * $taxable,
					'sgst_rate'   => $half,
					'sgst_amount' => ( $half / 100 ) * $taxable,
					'igst_rate'   => 0.0,
					'igst_amount' => 0.0,
				];
			}
			return [
				'cgst_rate'   => 0.0,
				'cgst_amount' => 0.0,
				'sgst_rate'   => 0.0,
				'sgst_amount' => 0.0,
				'igst_rate'   => $rate,
				'igst_amount' => ( $rate / 100 ) * $taxable,
			];
		};

		// ── Item rows ─────────────────────────────────────────────────
		foreach ( $items as $item ) {
			/** @var WC_Order_Item_Product $item */

			$product_id    = (int) $item->get_product_id();
			$product       = $item->get_product();
			$tax_class     = $product ? (string) $product->get_tax_class() : '';
			$gst_rate      = ds_gst_rate_from_tax_class( $tax_class );
			$taxable_value = (float) $item->get_total();

			$gst = $gst_split( $taxable_value, $gst_rate );

			$total_taxable += $taxable_value;
			$total_cgst    += $gst['cgst_amount'];
			$total_sgst    += $gst['sgst_amount'];
			$total_igst    += $gst['igst_amount'];

			$row                                              = $empty_row;
			$row[ $col['row_type'] ]                          = 'item';
			$row[ $col['order_number'] ]                      = $order_number;
			$row[ $col['invoice_number'] ]                    = $invoice_number;
			$row[ $col['billing_state'] ]                     = $billing_state;
			$row[ $col['item_product_id'] ]                   = $product_id;
			$row[ $col['item_name'] ]                         = $item->get_name();
			$row[ $col['item_sku'] ]                          = $product ? $product->get_sku() : '';
			$row[ $col['item_quantity'] ]                     = $item->get_quantity();
			$row[ $col['item_subtotal'] ]                     = ds_fmt_num( $item->get_subtotal() );
			$row[ $col['item_subtotal_tax'] ]                 = ds_fmt_num( $item->get_subtotal_tax() );
			$row[ $col['item_total'] ]                        = ds_fmt_num( $taxable_value );
			$row[ $col['item_total_tax'] ]                    = ds_fmt_num( $item->get_total_tax() );
			$row[ $col['HSN Code'] ]                          = ds_get_hsn_code( $product_id );
			$row[ $col['Place of Supply State Code'] ]        = $billing_state;
			$row[ $col['Taxable Value (Final Excl. Tax)'] ]   = ds_fmt_num( $taxable_value );
			$row[ $col['CGST Rate (%)'] ]                     = ds_fmt_num( $gst['cgst_rate'] );
			$row[ $col['CGST Amount'] ]                       = ds_fmt_num( $gst['cgst_amount'] );
			$row[ $col['SGST Rate (%)'] ]                     = ds_fmt_num( $gst['sgst_rate'] );
			$row[ $col['SGST Amount'] ]                       = ds_fmt_num( $gst['sgst_amount'] );
			$row[ $col['IGST Rate (%)'] ]                     = ds_fmt_num( $gst['igst_rate'] );
			$row[ $col['IGST Amount'] ]                       = ds_fmt_num( $gst['igst_amount'] );

			$child_rows[] = $row;
		}

		// ── Shipping row (only if shipping amount > 0) ────────────────
		$shipping_taxable = (float) $order->get_shipping_total();

		if ( $shipping_taxable > 0 ) {
			$shipping_gst_rate = 18.0; // Standard GST rate for freight/courier services
			$gst               = $gst_split( $shipping_taxable, $shipping_gst_rate );

			$total_taxable += $shipping_taxable;
			$total_cgst    += $gst['cgst_amount'];
			$total_sgst    += $gst['sgst_amount'];
			$total_igst    += $gst['igst_amount'];

			$row                                              = $empty_row;
			$row[ $col['row_type'] ]                          = 'shipping';
			$row[ $col['order_number'] ]                      = $order_number;
			$row[ $col['invoice_number'] ]                    = $invoice_number;
			$row[ $col['billing_state'] ]                     = $billing_state;
			$row[ $col['item_name'] ]                         = 'Shipping';
			$row[ $col['item_quantity'] ]                     = 1;
			$row[ $col['item_subtotal'] ]                     = ds_fmt_num( $shipping_taxable );
			$row[ $col['item_subtotal_tax'] ]                 = ds_fmt_num( $order->get_shipping_tax() );
			$row[ $col['item_total'] ]                        = ds_fmt_num( $shipping_taxable );
			$row[ $col['item_total_tax'] ]                    = ds_fmt_num( $order->get_shipping_tax() );
			$row[ $col['HSN Code'] ]                          = '996812';
			$row[ $col['Place of Supply State Code'] ]        = $billing_state;
			$row[ $col['Taxable Value (Final Excl. Tax)'] ]   = ds_fmt_num( $shipping_taxable );
			$row[ $col['CGST Rate (%)'] ]                     = ds_fmt_num( $gst['cgst_rate'] );
			$row[ $col['CGST Amount'] ]                       = ds_fmt_num( $gst['cgst_amount'] );
			$row[ $col['SGST Rate (%)'] ]                     = ds_fmt_num( $gst['sgst_rate'] );
			$row[ $col['SGST Amount'] ]                       = ds_fmt_num( $gst['sgst_amount'] );
			$row[ $col['IGST Rate (%)'] ]                     = ds_fmt_num( $gst['igst_rate'] );
			$row[ $col['IGST Amount'] ]                       = ds_fmt_num( $gst['igst_amount'] );

			$child_rows[] = $row;
		}

		// ── Invoice row (output first, totals accumulated above) ──────
		$invoice                                         = $empty_row;
		$invoice[ $col['row_type'] ]                     = 'invoice';
		$invoice[ $col['order_number'] ]                 = $order_number;
		$invoice[ $col['invoice_number'] ]               = $invoice_number;
		$invoice[ $col['invoice_date'] ]                 = ds_fmt_invoice_date( $order->get_meta( '_wcpdf_invoice_date', true ) );
		$invoice[ $col['order_date'] ]                   = ds_fmt_wc_datetime( $order->get_date_created() );
		$invoice[ $col['paid_date'] ]                    = ds_fmt_wc_datetime( $order->get_date_paid() );
		$invoice[ $col['billing_first_name'] ]           = $order->get_billing_first_name();
		$invoice[ $col['billing_last_name'] ]            = $order->get_billing_last_name();
		$invoice[ $col['billing_city'] ]                 = $order->get_billing_city();
		$invoice[ $col['billing_state'] ]                = $billing_state;
		$invoice[ $col['GSTIN (Customer)'] ]             = (string) $order->get_meta( '_billing_gstin', true );
		$invoice[ $col['order_subtotal'] ]               = ds_fmt_num( $order->get_subtotal() );
		$invoice[ $col['order_total'] ]                  = ds_fmt_num( $order->get_total() );
		$invoice[ $col['Taxable Value (Final Excl. Tax)'] ] = ds_fmt_num( $total_taxable );
		$invoice[ $col['CGST Amount'] ]                  = ds_fmt_num( $total_cgst );
		$invoice[ $col['SGST Amount'] ]                  = ds_fmt_num( $total_sgst );
		$invoice[ $col['IGST Amount'] ]                  = ds_fmt_num( $total_igst );

		// Output: invoice first, then item/shipping child rows
		$rows[] = $invoice;
		foreach ( $child_rows as $child ) {
			$rows[] = $child;
		}
	}

	if ( empty( $rows ) ) {
		throw new Exception( 'Orders found but contained no line items.' );
	}

	// ------------------------------------------------------------------
	// Assemble CSV string
	// ------------------------------------------------------------------

	$lines   = [];
	$lines[] = implode( ',', array_map( 'ds_csv_field', $headers ) );

	foreach ( $rows as $row ) {
		$lines[] = implode( ',', array_map( 'ds_csv_field', $row ) );
	}

	// UTF-8 BOM + rows joined by CRLF for maximum Excel compatibility
	return "\xEF\xBB\xBF" . implode( "\r\n", $lines ) . "\r\n";
}

// ---------------------------------------------------------------------------
// MODE 1: WP Admin UI
// ---------------------------------------------------------------------------

if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	add_action( 'admin_menu', 'ds_gst_register_admin_menu' );
	add_action( 'admin_init', 'ds_gst_handle_download' );

	function ds_gst_register_admin_menu(): void {
		add_submenu_page(
			'woocommerce',
			'GST Report',
			'GST Report',
			DS_GST_CAPABILITY,
			DS_GST_ADMIN_SLUG,
			'ds_gst_render_admin_page'
		);
	}

	function ds_gst_render_admin_page(): void {
		if ( ! current_user_can( DS_GST_CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'dirtshack' ) );
		}

		$current_year = (int) date( 'Y' );
		$years        = [ $current_year, $current_year - 1, $current_year - 2 ];

		$months = [
			1  => 'January',   2  => 'February', 3  => 'March',
			4  => 'April',     5  => 'May',       6  => 'June',
			7  => 'July',      8  => 'August',    9  => 'September',
			10 => 'October',   11 => 'November',  12 => 'December',
		];

		// Display error notice if redirected back with an error
		$error_msg = '';
		if ( isset( $_GET['ds_gst_error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$error_msg = sanitize_text_field( wp_unslash( $_GET['ds_gst_error'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// Completed orders with no invoice date are excluded from every report;
		// flag any from the last 3 months so they get an invoice before filing.
		$missing_invoice = ds_gst_orders_missing_invoice(
			wp_date( 'Y-m-01', strtotime( '-2 months', strtotime( wp_date( 'Y-m-01' ) ) ) ),
			wp_date( 'Y-m-d' )
		);
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php if ( $error_msg ) : ?>
				<div class="notice notice-error">
					<p><?php echo esc_html( $error_msg ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $missing_invoice ) : ?>
				<div class="notice notice-warning">
					<p>
						<?php
						printf(
							'%d completed order(s) from the last 3 months have no invoice and will not appear in any GST report: %s',
							count( $missing_invoice ),
							esc_html( implode( ', ', $missing_invoice ) )
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<p>Select the month and year to download the GST report as a CSV file. Orders are included by <strong>invoice date</strong> (completed orders only).</p>

			<form method="post" action="">
				<?php wp_nonce_field( DS_GST_NONCE_ACTION ); ?>
				<input type="hidden" name="ds_gst_action" value="download" />

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="ds_gst_month">Month</label></th>
						<td>
							<select id="ds_gst_month" name="ds_gst_month">
								<?php foreach ( $months as $num => $label ) : ?>
									<option value="<?php echo esc_attr( $num ); ?>"
										<?php selected( $num, (int) date( 'n' ) ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="ds_gst_year">Year</label></th>
						<td>
							<select id="ds_gst_year" name="ds_gst_year">
								<?php foreach ( $years as $yr ) : ?>
									<option value="<?php echo esc_attr( $yr ); ?>">
										<?php echo esc_html( $yr ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" class="button button-primary" value="Download GST Report" />
				</p>
			</form>
		</div>
		<?php
	}

	function ds_gst_handle_download(): void {
		if (
			! isset( $_POST['ds_gst_action'] ) ||
			$_POST['ds_gst_action'] !== 'download'
		) {
			return;
		}

		// Nonce check
		if (
			! isset( $_POST['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), DS_GST_NONCE_ACTION )
		) {
			wp_die( 'Security check failed.', 'Error', [ 'response' => 403 ] );
		}

		// Capability check
		if ( ! current_user_can( DS_GST_CAPABILITY ) ) {
			wp_die( 'You do not have permission to perform this action.', 'Error', [ 'response' => 403 ] );
		}

		$month = isset( $_POST['ds_gst_month'] ) ? (int) $_POST['ds_gst_month'] : 0;
		$year  = isset( $_POST['ds_gst_year'] )  ? (int) $_POST['ds_gst_year']  : 0;

		if ( $month < 1 || $month > 12 || $year < 2000 || $year > 2100 ) {
			$redirect = add_query_arg(
				[ 'page' => DS_GST_ADMIN_SLUG, 'ds_gst_error' => urlencode( 'Invalid month or year selected.' ) ],
				admin_url( 'admin.php' )
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		$start_date = date( 'Y-m-01', mktime( 0, 0, 0, $month, 1, $year ) );
		$end_date   = date( 'Y-m-t',  mktime( 0, 0, 0, $month, 1, $year ) );
		$filename   = 'gst-report-' . date( 'Y-m', mktime( 0, 0, 0, $month, 1, $year ) ) . '.csv';

		try {
			$csv = ds_generate_gst_csv( $start_date, $end_date );
		} catch ( Exception $e ) {
			$redirect = add_query_arg(
				[ 'page' => DS_GST_ADMIN_SLUG, 'ds_gst_error' => urlencode( $e->getMessage() ) ],
				admin_url( 'admin.php' )
			);
			wp_safe_redirect( $redirect );
			exit;
		}

		// Stream CSV to browser
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $csv;
		exit;
	}
}

// ---------------------------------------------------------------------------
// MODE 2: WP-CLI
// ---------------------------------------------------------------------------

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	WP_CLI::add_command(
		'dirtshack gst-report',
		function ( array $args, array $assoc_args ) {

			// Parse and validate arguments
			$start_date = isset( $assoc_args['start'] ) ? trim( $assoc_args['start'] ) : '';
			$end_date   = isset( $assoc_args['end'] )   ? trim( $assoc_args['end'] )   : '';

			if ( ! $start_date || ! $end_date ) {
				WP_CLI::error(
					"Missing required arguments.\n" .
					"Usage: wp dirtshack gst-report --start=YYYY-MM-DD --end=YYYY-MM-DD [--output=filepath]"
				);
			}

			if (
				! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $start_date ) ||
				! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $end_date )
			) {
				WP_CLI::error( '--start and --end must be in YYYY-MM-DD format.' );
			}

			$default_output = getcwd() . '/gst-report-' . date( 'Y-m', strtotime( $start_date ) ) . '.csv';
			$output_path    = isset( $assoc_args['output'] ) ? trim( $assoc_args['output'] ) : $default_output;

			WP_CLI::log( "Fetching orders from {$start_date} to {$end_date}..." );

			try {
				$csv     = ds_generate_gst_csv( $start_date, $end_date );
				$written = file_put_contents( $output_path, $csv );

				if ( $written === false ) {
					WP_CLI::error( "Failed to write output file: {$output_path}" );
				}

				WP_CLI::success( "Report saved to: {$output_path}" );

			} catch ( Exception $e ) {
				WP_CLI::error( $e->getMessage() );
			}
		},
		[
			'shortdesc' => 'Generate a monthly GST report CSV.',
			'synopsis'  => [
				[
					'type'        => 'assoc',
					'name'        => 'start',
					'description' => 'Start date (YYYY-MM-DD).',
					'optional'    => false,
				],
				[
					'type'        => 'assoc',
					'name'        => 'end',
					'description' => 'End date (YYYY-MM-DD).',
					'optional'    => false,
				],
				[
					'type'        => 'assoc',
					'name'        => 'output',
					'description' => 'Output file path. Defaults to ./gst-report-YYYY-MM.csv.',
					'optional'    => true,
				],
			],
		]
	);
}
