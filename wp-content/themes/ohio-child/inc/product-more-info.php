<?php
/**
 * DirtShack — "More Info" product tab, fed only by the More Info field.
 *
 * Product data → General gets a rich-text "More Info" editor (bold, lists, tables,
 * links, images). Whatever is saved there shows in a "More Info" tab on the
 * product page, after Video guide and before Reviews. Nothing else feeds the tab:
 * the content comes only from this field.
 *
 * The default WooCommerce "Additional information" tab is removed, so weight,
 * dimensions and attribute rows (Compatibility, HSN code, etc.) are no longer
 * printed on the page. Those values stay on the product: shipping and the GST
 * report still read them.
 *
 * The tab is only added when the field has content, so products without More Info
 * get no empty tab.
 *
 * Saving changed content clears that product page from WP Super Cache and, when the
 * API token is set up, Cloudflare (same as the Video guide field).
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Post-meta key holding the More Info HTML (kses-filtered on save). */
const DIRTSHACK_MINFO_META = '_ds_more_info';

/** Product tab key; Ohio prints it as data-ohio-tab="…" and #tab-title-…. */
const DIRTSHACK_MINFO_TAB = 'ds_more_info';

// ─── Admin: the rich-text field ──────────────────────────────────────────────

add_action( 'woocommerce_product_options_general_product_data', 'dirtshack_minfo_product_field' );
function dirtshack_minfo_product_field() {
	global $product_object;

	$value = $product_object instanceof WC_Product ? (string) $product_object->get_meta( DIRTSHACK_MINFO_META ) : '';

	echo '<div class="options_group ds-more-info-field">';
	echo '<p class="form-field"><label for="ds_more_info_editor">' . esc_html__( 'More Info', 'ohio-child' ) . '</label></p>';
	wp_editor(
		$value,
		'ds_more_info_editor',
		array(
			'textarea_name' => DIRTSHACK_MINFO_META,
			'textarea_rows' => 10,
			'media_buttons' => false,
			'teeny'         => false,
			'quicktags'     => true,
		)
	);
	echo '<p class="description" style="margin:.5em 12px 0">' . esc_html__( 'Shown in the "More Info" tab on the product page. Use for fitment, compatibility, notes and similar details. Leave empty to hide the tab.', 'ohio-child' ) . '</p>';
	echo '</div>';
}

add_action( 'woocommerce_process_product_meta', 'dirtshack_minfo_save_product_field' );
function dirtshack_minfo_save_product_field( $post_id ) {
	// woocommerce_process_product_meta already verifies the product-data nonce and
	// edit capability before firing.
	$raw  = isset( $_POST[ DIRTSHACK_MINFO_META ] ) ? wp_unslash( $_POST[ DIRTSHACK_MINFO_META ] ) : '';
	$html = trim( wp_kses_post( $raw ) );

	$product = wc_get_product( $post_id );
	if ( ! $product ) {
		return;
	}
	$changed = (string) $product->get_meta( DIRTSHACK_MINFO_META ) !== $html;
	if ( '' === $html ) {
		$product->delete_meta_data( DIRTSHACK_MINFO_META );
	} else {
		$product->update_meta_data( DIRTSHACK_MINFO_META, $html );
	}
	$product->save();

	if ( $changed && 'publish' === get_post_status( $post_id ) ) {
		if ( function_exists( 'wpsc_delete_post_cache' ) ) {
			wpsc_delete_post_cache( $post_id );
		}
		if ( function_exists( 'dirtshack_cloudflare_purge_request' ) ) {
			dirtshack_cloudflare_purge_request( array( 'files' => array( get_permalink( $post_id ) ) ) );
		}
	}
}

// ─── Front-end: product tabs ─────────────────────────────────────────────────

// Hide WooCommerce's Additional information tab. Runs late so it sees every tab.
add_filter( 'woocommerce_product_tabs', 'dirtshack_minfo_remove_additional_tab', 99 );
function dirtshack_minfo_remove_additional_tab( $tabs ) {
	unset( $tabs['additional_information'] );
	return $tabs;
}

// Hide the Reviews tab while the product has no approved reviews. Shown again as
// soon as one is approved. Customers can't start a review from the tab while it's
// hidden.
add_filter( 'woocommerce_product_tabs', 'dirtshack_minfo_hide_empty_reviews', 99 );
function dirtshack_minfo_hide_empty_reviews( $tabs ) {
	global $product;
	if ( $product instanceof WC_Product && 0 === (int) $product->get_review_count() ) {
		unset( $tabs['reviews'] );
	}
	return $tabs;
}

// "More Info" tab between Video guide (15) and Reviews (30).
add_filter( 'woocommerce_product_tabs', 'dirtshack_minfo_tab' );
function dirtshack_minfo_tab( $tabs ) {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $tabs;
	}
	if ( '' !== trim( (string) $product->get_meta( DIRTSHACK_MINFO_META ) ) ) {
		$tabs[ DIRTSHACK_MINFO_TAB ] = array(
			'title'    => __( 'More Info', 'ohio-child' ),
			'priority' => 25,
			'callback' => 'dirtshack_minfo_tab_content',
		);
	}
	return $tabs;
}

function dirtshack_minfo_tab_content() {
	global $product;
	$html = wp_kses_post( (string) $product->get_meta( DIRTSHACK_MINFO_META ) );

	echo '<div class="ds-more-info-content">';
	// Output is wp_kses_post-filtered on save and again here; panel classes stripped so the tabs can't shift.
	echo dirtshack_tabs_strip_panels( wpautop( $html ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- kses-filtered above.
	echo '</div>';
}
