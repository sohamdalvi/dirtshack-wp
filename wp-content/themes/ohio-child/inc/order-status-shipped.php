<?php
/**
 * DirtShack — "Shipped" order status.
 *
 * Adds a wc-shipped status that sits between Processing and Completed:
 * Processing → Shipped (dispatched) → Completed (delivered / fulfilled).
 *
 * Shipped is only a waypoint. The invoice is still dated when the order moves to
 * Completed (inc/invoice-date.php), and the GST report still counts only Completed
 * orders, so moving an order to Shipped changes nothing for accounting.
 *
 * Works with both the legacy posts order table and HPOS. The bulk "Change status
 * to shipped" action needs no handler of its own: WooCommerce applies any
 * mark_{status} action for a registered status.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

const DIRTSHACK_STATUS_SHIPPED = 'wc-shipped';

/** Register the status the same way WooCommerce registers its own. */
add_filter( 'woocommerce_register_shop_order_post_statuses', 'dirtshack_register_shipped_status' );
function dirtshack_register_shipped_status( $statuses ) {
	$statuses[ DIRTSHACK_STATUS_SHIPPED ] = [
		'label'                     => _x( 'Shipped', 'Order status', 'ohio-child' ),
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		/* translators: %s: number of orders */
		'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'ohio-child' ),
	];
	return $statuses;
}

/** Add it to the status dropdown, right after Processing. */
add_filter( 'wc_order_statuses', 'dirtshack_add_shipped_to_order_statuses' );
function dirtshack_add_shipped_to_order_statuses( $statuses ) {
	$out = [];
	foreach ( $statuses as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'wc-processing' === $key ) {
			$out[ DIRTSHACK_STATUS_SHIPPED ] = _x( 'Shipped', 'Order status', 'ohio-child' );
		}
	}
	return $out;
}

/** A shipped order has been paid for (sales reports, "paid" checks, download access). */
add_filter( 'woocommerce_order_is_paid_statuses', 'dirtshack_shipped_is_paid' );
function dirtshack_shipped_is_paid( $statuses ) {
	$statuses[] = 'shipped';
	return $statuses;
}

add_filter( 'woocommerce_reports_order_statuses', 'dirtshack_shipped_in_reports' );
function dirtshack_shipped_in_reports( $statuses ) {
	if ( is_array( $statuses ) && in_array( 'processing', $statuses, true ) ) {
		$statuses[] = 'shipped';
	}
	return $statuses;
}

/** Bulk action on the Orders list (legacy screen and HPOS screen). */
add_filter( 'bulk_actions-edit-shop_order', 'dirtshack_shipped_bulk_action', 20 );
add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'dirtshack_shipped_bulk_action', 20 );
function dirtshack_shipped_bulk_action( $actions ) {
	$out = [];
	foreach ( $actions as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'mark_processing' === $key ) {
			$out['mark_shipped'] = __( 'Change status to shipped', 'ohio-child' );
		}
	}
	if ( ! isset( $out['mark_shipped'] ) ) {
		$out['mark_shipped'] = __( 'Change status to shipped', 'ohio-child' );
	}
	return $out;
}

/**
 * Row action buttons on the Orders list: "Shipped" on Processing orders, and keep
 * "Complete" available once an order is Shipped.
 */
add_filter( 'woocommerce_admin_order_actions', 'dirtshack_shipped_row_actions', 10, 2 );
function dirtshack_shipped_row_actions( $actions, $order ) {
	$mark_url = function ( $status ) use ( $order ) {
		return wp_nonce_url(
			admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $status . '&order_id=' . $order->get_id() ),
			'woocommerce-mark-order-status'
		);
	};

	if ( $order->has_status( 'processing' ) ) {
		$actions = [
			'shipped' => [
				'url'    => $mark_url( 'shipped' ),
				'name'   => __( 'Shipped', 'ohio-child' ),
				'action' => 'shipped',
			],
		] + $actions;
	}

	if ( $order->has_status( 'shipped' ) && ! isset( $actions['complete'] ) ) {
		$actions['complete'] = [
			'url'    => $mark_url( 'completed' ),
			'name'   => __( 'Complete', 'woocommerce' ),
			'action' => 'complete',
		];
	}

	return $actions;
}

/** Badge colour on the Orders list and the row-action button icon. */
add_action( 'admin_head', 'dirtshack_shipped_admin_css' );
function dirtshack_shipped_admin_css() {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, [ 'edit-shop_order', 'woocommerce_page_wc-orders' ], true ) ) {
		return;
	}
	?>
	<style>
		.order-status.status-shipped { background: #c8e0f4; color: #1d4f7a; }
		.wc-action-button-shipped::after { font-family: dashicons !important; content: "\f344" !important; }
	</style>
	<?php
}
