<?php
/**
 * DirtShack — create a customer account from a guest order (admin only).
 *
 * A guest checkout leaves the customer's details only on the order, so WooCommerce's
 * Add order screen can't find them. This adds:
 *
 * - Order page → Order actions → "Create customer account from this order"
 * - Orders list → Bulk actions → "Create customer accounts"
 *
 * Each creates a customer with the order's name, email, phone, billing and shipping
 * address, then links every guest order with that billing email to it. Next time,
 * Add order → Customer finds them and "Load billing / shipping address" fills the
 * order in.
 *
 * If an account with that email already exists, the orders are linked to it and only
 * its empty billing / shipping address is filled in; nothing is overwritten. No email
 * is sent to the customer; they can set a password with "Lost your password?".
 * Orders without a billing email are skipped.
 *
 * Works with both the legacy posts order table and HPOS.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

const DIRTSHACK_CREATE_CUSTOMER_ACTION = 'dirtshack_create_customer';

/**
 * Create (or find) the customer account for a guest order and link its guest orders.
 *
 * Creates the WP user directly rather than through wc_create_new_customer(), which
 * would send the "new account" email and run checkout registration checks
 * (reCAPTCHA / OTP) that can't pass from the admin.
 *
 * @return array{status: string, user_id?: int, linked?: int} status is one of
 *         created, existing, already_linked, no_email, error.
 */
function dirtshack_create_customer_from_order( WC_Order $order ) {
	if ( $order->get_customer_id() ) {
		return [ 'status' => 'already_linked', 'user_id' => $order->get_customer_id() ];
	}

	$email = $order->get_billing_email();
	if ( ! $email || ! is_email( $email ) ) {
		return [ 'status' => 'no_email' ];
	}

	$user_id = email_exists( $email );
	$status  = $user_id ? 'existing' : 'created';

	if ( ! $user_id ) {
		$name    = [ 'first_name' => $order->get_billing_first_name(), 'last_name' => $order->get_billing_last_name() ];
		$user_id = wp_insert_user(
			$name + [
				'user_login' => wc_create_new_customer_username( $email, $name ),
				'user_pass'  => wp_generate_password(),
				'user_email' => $email,
				'role'       => 'customer',
			]
		);
		if ( is_wp_error( $user_id ) ) {
			/* translators: %s: error message */
			$order->add_order_note( sprintf( __( 'Could not create a customer account: %s', 'ohio-child' ), $user_id->get_error_message() ), false, true );
			return [ 'status' => 'error' ];
		}
	}

	// Fill the account's billing / shipping from the order, but never overwrite an address it already has.
	$customer = new WC_Customer( $user_id );
	foreach ( [ 'billing', 'shipping' ] as $type ) {
		if ( 'existing' === $status && $customer->{"get_{$type}_address_1"}() ) {
			continue;
		}
		foreach ( $order->get_address( $type ) as $key => $value ) {
			if ( '' !== $value && is_callable( [ $customer, "set_{$type}_{$key}" ] ) ) {
				$customer->{"set_{$type}_{$key}"}( $value );
			}
		}
	}
	$customer->save();

	$linked = wc_update_new_customer_past_orders( $user_id );

	$order->add_order_note(
		sprintf(
			'created' === $status
				/* translators: 1: email, 2: number of orders */
				? __( 'Customer account created for %1$s; %2$d guest order(s) with this email linked to it.', 'ohio-child' )
				/* translators: 1: email, 2: number of orders */
				: __( 'Linked to the existing customer account %1$s; %2$d guest order(s) with this email linked to it.', 'ohio-child' ),
			$email,
			$linked
		),
		false,
		true
	);

	return [ 'status' => $status, 'user_id' => $user_id, 'linked' => $linked ];
}

/** Order page → Order actions, shown only on guest orders that have a billing email. */
add_filter( 'woocommerce_order_actions', 'dirtshack_create_customer_order_action', 10, 2 );
function dirtshack_create_customer_order_action( $actions, $order = null ) {
	if ( $order instanceof WC_Order && ! $order->get_customer_id() && $order->get_billing_email() ) {
		$actions[ DIRTSHACK_CREATE_CUSTOMER_ACTION ] = __( 'Create customer account from this order', 'ohio-child' );
	}
	return $actions;
}

add_action( 'woocommerce_order_action_' . DIRTSHACK_CREATE_CUSTOMER_ACTION, 'dirtshack_create_customer_from_order' );

/** Orders list → Bulk actions (legacy screen and HPOS screen). */
add_filter( 'bulk_actions-edit-shop_order', 'dirtshack_create_customer_bulk_action', 20 );
add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'dirtshack_create_customer_bulk_action', 20 );
function dirtshack_create_customer_bulk_action( $actions ) {
	$actions[ DIRTSHACK_CREATE_CUSTOMER_ACTION ] = __( 'Create customer accounts', 'ohio-child' );
	return $actions;
}

add_filter( 'handle_bulk_actions-edit-shop_order', 'dirtshack_handle_create_customer_bulk_action', 10, 3 );
add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', 'dirtshack_handle_create_customer_bulk_action', 10, 3 );
function dirtshack_handle_create_customer_bulk_action( $redirect_to, $action, $ids ) {
	if ( DIRTSHACK_CREATE_CUSTOMER_ACTION !== $action ) {
		return $redirect_to;
	}

	// Newest first, so a customer with several orders gets their latest address.
	$ids = array_map( 'absint', (array) $ids );
	rsort( $ids );

	$counts = [ 'created' => 0, 'existing' => 0, 'already_linked' => 0, 'no_email' => 0, 'error' => 0 ];
	foreach ( $ids as $id ) {
		$order = wc_get_order( $id );
		if ( $order instanceof WC_Order ) {
			$result = dirtshack_create_customer_from_order( $order );
			$counts[ $result['status'] ]++;
		}
	}

	$redirect_to = remove_query_arg( array_map( fn( $k ) => "ds_customers_$k", array_keys( $counts ) ), $redirect_to );
	foreach ( array_filter( $counts ) as $key => $count ) {
		$redirect_to = add_query_arg( "ds_customers_$key", $count, $redirect_to );
	}
	return $redirect_to;
}

/** Result of the bulk action, on the Orders list. */
add_action( 'admin_notices', 'dirtshack_create_customer_bulk_notice' );
function dirtshack_create_customer_bulk_notice() {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, [ 'edit-shop_order', 'woocommerce_page_wc-orders' ], true ) ) {
		return;
	}

	$messages = [
		/* translators: %d: number of customers */
		'created'        => __( '%d customer account(s) created.', 'ohio-child' ),
		/* translators: %d: number of orders */
		'existing'       => __( '%d order(s) linked to an existing customer account with the same email.', 'ohio-child' ),
		/* translators: %d: number of orders */
		'already_linked' => __( '%d order(s) were already linked to a customer.', 'ohio-child' ),
		/* translators: %d: number of orders */
		'no_email'       => __( '%d order(s) skipped: no billing email.', 'ohio-child' ),
		/* translators: %d: number of orders */
		'error'          => __( '%d order(s) failed; see the order notes.', 'ohio-child' ),
	];

	$lines = [];
	foreach ( $messages as $key => $message ) {
		$count = isset( $_GET[ "ds_customers_$key" ] ) ? absint( $_GET[ "ds_customers_$key" ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $count ) {
			$lines[] = sprintf( $message, $count );
		}
	}

	if ( $lines ) {
		printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( implode( ' ', $lines ) ) );
	}
}
