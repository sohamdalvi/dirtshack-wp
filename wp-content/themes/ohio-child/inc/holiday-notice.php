<?php
/**
 * DirtShack — holiday / shipping-delay notice.
 *
 * WooCommerce → Settings → Holiday notice: a message (e.g. "Diwali break: orders
 * placed 11–15 Nov will ship from 16 Nov."), the dates it shows, and where:
 *
 * - a bar at the top of the home page, or of every page
 * - product pages, above Add to cart
 * - cart and checkout
 * - the order-received page and the Processing / On hold order emails
 *
 * The top bar and product pages are page-cached (WP Super Cache), so there the
 * notice is printed whenever it is set up and not yet over, carrying its start/end
 * timestamps; a tiny inline script shows or hides it by the visitor's clock. That
 * way it switches on and off on its dates without waiting for the cache to expire.
 * Cart, checkout, the order-received page and emails are never cached, so they
 * check the dates on the server. Saving the settings clears the page cache.
 *
 * Dates are whole days in the store's timezone (Settings → General), inclusive.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

const DIRTSHACK_HOLIDAY_TAB = 'dirtshack_holiday';

/**
 * The configured notice, or null when it is off or has no message.
 *
 * @return array{message: string, from: int, until: int}|null Unix timestamps; from 0 / until PHP_INT_MAX when open-ended.
 */
function dirtshack_holiday_notice() {
	$message = trim( (string) get_option( 'dirtshack_holiday_message', '' ) );
	if ( 'yes' !== get_option( 'dirtshack_holiday_enabled', 'no' ) || '' === $message ) {
		return null;
	}

	$day = function ( $option ) {
		$date = DateTimeImmutable::createFromFormat( '!Y-m-d', (string) get_option( $option, '' ), wp_timezone() );
		return $date ?: null;
	};
	$start = $day( 'dirtshack_holiday_start' );
	$end   = $day( 'dirtshack_holiday_end' );

	return [
		'message' => $message,
		'from'    => $start ? $start->getTimestamp() : 0,
		'until'   => $end ? $end->modify( '+1 day' )->getTimestamp() - 1 : PHP_INT_MAX,
	];
}

function dirtshack_holiday_notice_is_active( $notice ) {
	return $notice && $notice['from'] <= time() && time() <= $notice['until'];
}

/** Placement checkboxes default to on until the settings are first saved. */
function dirtshack_holiday_shows_on( $place ) {
	return 'yes' === get_option( "dirtshack_holiday_on_{$place}", 'yes' );
}

/**
 * Notice markup for a placement (bar, product, cart, checkout, thankyou). On cached
 * pages it is printed until the end date, hidden if not currently active, and an
 * inline script re-checks the dates by the visitor's clock. On uncached pages it
 * is printed only while active.
 */
function dirtshack_holiday_notice_html( $place, $cached ) {
	$notice = dirtshack_holiday_notice();
	if ( ! $notice || time() > $notice['until'] || ( ! $cached && ! dirtshack_holiday_notice_is_active( $notice ) ) ) {
		return '';
	}

	$id   = 'ds-holiday-' . $place;
	$html = sprintf(
		'<div id="%s" class="ds-holiday %s" role="note" data-from="%d" data-until="%d"%s><div class="ds-holiday__text">%s</div></div>',
		esc_attr( $id ),
		'bar' === $place ? 'ds-holiday--bar' : 'ds-holiday--box',
		$notice['from'],
		$notice['until'],
		dirtshack_holiday_notice_is_active( $notice ) ? '' : ' hidden',
		wp_kses_post( nl2br( $notice['message'] ) )
	);
	if ( $cached ) {
		// Looked up by id, so it still works if an optimiser moves inline scripts.
		$html .= "<script>(function(e){if(!e)return;var t=Date.now()/1e3;e.hidden=!(t>=+e.dataset.from&&t<=+e.dataset.until)})(document.getElementById('{$id}'))</script>";
	}
	return $html;
}

// ─── Storefront placements ───────────────────────────────────────────────────

add_action( 'wp_body_open', 'dirtshack_holiday_bar' );
function dirtshack_holiday_bar() {
	$where = get_option( 'dirtshack_holiday_bar', 'home' );
	$is_cart_or_checkout = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() );
	if ( 'all' === $where ? $is_cart_or_checkout : ! ( 'home' === $where && is_front_page() ) ) {
		return;
	}
	echo dirtshack_holiday_notice_html( 'bar', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_action( 'woocommerce_before_add_to_cart_form', 'dirtshack_holiday_product' );
function dirtshack_holiday_product() {
	if ( dirtshack_holiday_shows_on( 'product' ) ) {
		echo dirtshack_holiday_notice_html( 'product', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_action( 'woocommerce_before_cart', 'dirtshack_holiday_cart' );
function dirtshack_holiday_cart() {
	if ( dirtshack_holiday_shows_on( 'cart' ) ) {
		echo dirtshack_holiday_notice_html( 'cart', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_action( 'woocommerce_before_checkout_form', 'dirtshack_holiday_checkout', 5 );
function dirtshack_holiday_checkout() {
	if ( dirtshack_holiday_shows_on( 'checkout' ) ) {
		echo dirtshack_holiday_notice_html( 'checkout', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/** Order-received page: Ohio's template fires this inside a .vc_row, so wrap it in a full-width column. */
add_action( 'woocommerce_before_thankyou', 'dirtshack_holiday_thankyou' );
function dirtshack_holiday_thankyou( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! dirtshack_holiday_shows_on( 'thankyou' ) || ! $order || $order->has_status( 'failed' ) ) {
		return;
	}
	$html = dirtshack_holiday_notice_html( 'thankyou', false );
	if ( $html ) {
		echo '<div class="vc_col-sm-12">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/** Order confirmation emails to the customer (Processing / On hold). */
add_action( 'woocommerce_email_before_order_table', 'dirtshack_holiday_email', 5, 4 );
function dirtshack_holiday_email( $order, $sent_to_admin, $plain_text, $email = null ) {
	$notice = dirtshack_holiday_notice();
	if (
		$sent_to_admin || ! dirtshack_holiday_shows_on( 'email' ) || ! dirtshack_holiday_notice_is_active( $notice )
		|| ! $email || ! in_array( $email->id, [ 'customer_processing_order', 'customer_on_hold_order' ], true )
	) {
		return;
	}

	if ( $plain_text ) {
		echo wp_strip_all_tags( $notice['message'] ) . "\n\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	printf(
		'<p style="margin:0 0 20px;padding:12px 16px;background:#f6fadf;border-left:4px solid #C4E000;color:#111;">%s</p>',
		wp_kses_post( nl2br( $notice['message'] ) )
	);
}

/** Styles, only while a notice is set up and not over (they're cached with the page). */
add_action( 'wp_head', 'dirtshack_holiday_css', 99 );
function dirtshack_holiday_css() {
	$notice = dirtshack_holiday_notice();
	if ( ! $notice || time() > $notice['until'] ) {
		return;
	}
	?>
<style id="ds-holiday-css">
.ds-holiday[hidden] { display: none !important; }
.ds-holiday a { text-decoration: underline !important; }
.ds-holiday--bar {
    background: var(--ds-dark, #111) !important;
    color: #fff !important;
    border-bottom: 2px solid var(--ds-yellow, #C4E000) !important;
    font-size: .85rem !important;
    line-height: 1.4 !important;
}
.ds-holiday--bar .ds-holiday__text {
    max-width: var(--ds-max, 1280px) !important;
    margin: 0 auto !important;
    padding: .6rem var(--ds-pad, 1.5rem) !important;
    text-align: center !important;
}
.ds-holiday--bar a { color: var(--ds-yellow, #C4E000) !important; }
.ds-holiday--box {
    background: #f6fadf !important;
    color: var(--ds-dark, #111) !important;
    border-left: 4px solid var(--ds-yellow, #C4E000) !important;
    border-radius: 6px !important;
    padding: .85rem 1.1rem !important;
    margin: 0 0 1.5rem !important;
    font-size: .95rem !important;
    line-height: 1.45 !important;
}
.ds-holiday--box a { color: inherit !important; }
@media (max-width: 768px) {
    .ds-holiday--bar .ds-holiday__text { padding: .55rem 1rem !important; font-size: .8rem !important; }
}
</style>
	<?php
}

// ─── Settings: WooCommerce → Settings → Holiday notice ───────────────────────

add_filter( 'woocommerce_settings_tabs_array', 'dirtshack_holiday_settings_tab', 50 );
function dirtshack_holiday_settings_tab( $tabs ) {
	$tabs[ DIRTSHACK_HOLIDAY_TAB ] = __( 'Holiday notice', 'ohio-child' );
	return $tabs;
}

add_action( 'woocommerce_settings_' . DIRTSHACK_HOLIDAY_TAB, 'dirtshack_holiday_settings_output' );
function dirtshack_holiday_settings_output() {
	woocommerce_admin_fields( dirtshack_holiday_settings() );
}

add_action( 'woocommerce_update_options_' . DIRTSHACK_HOLIDAY_TAB, 'dirtshack_holiday_settings_save' );
function dirtshack_holiday_settings_save() {
	woocommerce_update_options( dirtshack_holiday_settings() );

	$start = get_option( 'dirtshack_holiday_start' );
	$end   = get_option( 'dirtshack_holiday_end' );
	if ( $start && $end && $end < $start ) {
		WC_Admin_Settings::add_error( __( '"Show until" is before "Show from", so the notice will never show.', 'ohio-child' ) );
	}

	// Cached pages carry the old notice (or none) until the cache is cleared.
	if ( function_exists( 'wp_cache_clear_cache' ) ) {
		wp_cache_clear_cache();
		WC_Admin_Settings::add_message( __( 'Page cache cleared so the change shows on the site right away.', 'ohio-child' ) );
	}
}

/** One-line status shown at the top of the settings tab. */
function dirtshack_holiday_status() {
	$notice = dirtshack_holiday_notice();
	$date   = fn( $ts ) => wp_date( get_option( 'date_format' ), $ts );

	if ( ! $notice ) {
		return __( 'Status: off.', 'ohio-child' );
	}
	if ( time() > $notice['until'] ) {
		/* translators: %s: date */
		return sprintf( __( 'Status: ended on %s.', 'ohio-child' ), $date( $notice['until'] ) );
	}
	if ( time() < $notice['from'] ) {
		/* translators: %s: date */
		return sprintf( __( 'Status: scheduled, starts showing on %s.', 'ohio-child' ), $date( $notice['from'] ) );
	}
	return PHP_INT_MAX === $notice['until']
		? __( 'Status: showing now, until you turn it off.', 'ohio-child' )
		/* translators: %s: date */
		: sprintf( __( 'Status: showing now, until the end of %s.', 'ohio-child' ), $date( $notice['until'] ) );
}

function dirtshack_holiday_settings() {
	return [
		[
			'title' => __( 'Holiday notice', 'ohio-child' ),
			'type'  => 'title',
			'id'    => 'dirtshack_holiday',
			'desc'  => __( 'Tell shoppers about shipping delays during holidays or other breaks. Shoppers see it before they pay (product page, cart, checkout) and again after (order-received page and confirmation email).', 'ohio-child' )
				. '<br><strong>' . esc_html( dirtshack_holiday_status() ) . '</strong>',
		],
		[
			'title'   => __( 'Enable', 'ohio-child' ),
			'desc'    => __( 'Show the notice between the dates below', 'ohio-child' ),
			'id'      => 'dirtshack_holiday_enabled',
			'type'    => 'checkbox',
			'default' => 'no',
		],
		[
			'title'       => __( 'Message', 'ohio-child' ),
			'desc'        => __( 'One or two short lines. Say when orders will ship, e.g. "Diwali break: orders placed 11–15 Nov will ship from 16 Nov." Links are allowed.', 'ohio-child' ),
			'id'          => 'dirtshack_holiday_message',
			'type'        => 'textarea',
			'placeholder' => 'Diwali break: orders placed 11–15 Nov will ship from 16 Nov.',
			'css'         => 'width: 100%; max-width: 600px; height: 70px;',
		],
		[
			'title' => __( 'Show from', 'ohio-child' ),
			'desc'  => __( 'First day it shows. Start a few days before the break, since orders placed just before it are delayed too. Empty: starts now.', 'ohio-child' ),
			'id'    => 'dirtshack_holiday_start',
			'type'  => 'date',
		],
		[
			'title' => __( 'Show until', 'ohio-child' ),
			'desc'  => __( 'Last day it shows; it switches off by itself after this day. Empty: shows until you untick Enable.', 'ohio-child' ),
			'id'    => 'dirtshack_holiday_end',
			'type'  => 'date',
		],
		[
			'title'   => __( 'Top bar', 'ohio-child' ),
			'id'      => 'dirtshack_holiday_bar',
			'type'    => 'select',
			'default' => 'home',
			'options' => [
				'home' => __( 'Home page only', 'ohio-child' ),
				'all'  => __( 'Every page (cart and checkout show the box instead)', 'ohio-child' ),
				'off'  => __( 'Off', 'ohio-child' ),
			],
		],
		[
			'title'         => __( 'Also show on', 'ohio-child' ),
			'desc'          => __( 'Product pages, above Add to cart', 'ohio-child' ),
			'id'            => 'dirtshack_holiday_on_product',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'start',
		],
		[
			'desc'          => __( 'Cart', 'ohio-child' ),
			'id'            => 'dirtshack_holiday_on_cart',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		],
		[
			'desc'          => __( 'Checkout', 'ohio-child' ),
			'id'            => 'dirtshack_holiday_on_checkout',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		],
		[
			'desc'          => __( 'Order-received page', 'ohio-child' ),
			'id'            => 'dirtshack_holiday_on_thankyou',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		],
		[
			'desc'          => __( 'Order confirmation email (Processing and On hold)', 'ohio-child' ),
			'id'            => 'dirtshack_holiday_on_email',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'end',
		],
		[
			'type' => 'sectionend',
			'id'   => 'dirtshack_holiday',
		],
	];
}
