<?php
/**
 * DirtShack — "Made to Order" product indicator.
 *
 * Some products are manufactured on demand (built only after an order is placed)
 * and therefore ship later than stocked items. This module lets an admin flag a
 * product as made-to-order and then surfaces a consistent, on-brand badge
 * everywhere the product appears — WooCommerce shop/archive loops, the homepage
 * "Featured Products" cards (front-page.php), and the single product page — so
 * customers understand the fulfilment timeline before buying.
 *
 * Self-contained (native WooCommerce product-data hook + post meta, no ACF) so it
 * travels with the theme code and is safe from Ohio theme updates, matching the
 * Braap CPT approach. All front-end CSS is injected inline via wp_head 9999 (the
 * child style.css ships with a static ?ver and can be edge-cached stale on live —
 * inline-late is the reliable override, see ARCHITECTURE / functions.php notes).
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Post-meta key holding the made-to-order flag ('yes' when on). */
const DIRTSHACK_MTO_META = '_ds_made_to_order';

/** Customer-facing copy. Kept as constants so the messaging is edited in one place. */
const DIRTSHACK_MTO_LABEL   = 'Made to Order';
const DIRTSHACK_MTO_TIMELINE = 'Ships within 7 working days';

// ─── Admin: flag a product as made-to-order ──────────────────────────────────
// A single checkbox in the Product data → Inventory tab (where stock/availability
// lives). Native Woo hooks keep it inside the standard product-data UI.

add_action( 'woocommerce_product_options_inventory_product_data', 'dirtshack_mto_product_field' );
function dirtshack_mto_product_field() {
	echo '<div class="options_group">';
	woocommerce_wp_checkbox(
		array(
			'id'          => DIRTSHACK_MTO_META,
			'label'       => __( 'Made to Order', 'ohio-child' ),
			'description' => __( 'Manufactured on demand — shows a "Made to Order · Ships within 7 working days" badge on the shop, homepage and product page.', 'ohio-child' ),
		)
	);
	echo '</div>';
}

add_action( 'woocommerce_process_product_meta', 'dirtshack_mto_save_product_field' );
function dirtshack_mto_save_product_field( $post_id ) {
	// woocommerce_process_product_meta already verifies the product-data nonce and
	// edit capability before firing, so we only normalise the checkbox value here.
	$is_mto = isset( $_POST[ DIRTSHACK_MTO_META ] ) ? 'yes' : '';

	$product = wc_get_product( $post_id );
	if ( $product ) {
		$product->update_meta_data( DIRTSHACK_MTO_META, $is_mto );
		$product->save();
	} else {
		update_post_meta( $post_id, DIRTSHACK_MTO_META, $is_mto );
	}
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Is the given product flagged made-to-order?
 *
 * @param WC_Product|int|null $product Product object, ID, or null for the global.
 * @return bool
 */
function dirtshack_is_made_to_order( $product = null ) {
	if ( null === $product ) {
		$product = $GLOBALS['product'] ?? null;
	}
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}
	if ( ! $product instanceof WC_Product ) {
		return false;
	}

	return 'yes' === $product->get_meta( DIRTSHACK_MTO_META );
}

/**
 * Full badge — label + timeline in a framed block. Used on the single product
 * page, where the fulfilment timeline is spelled out before purchase.
 *
 * @return string Escaped HTML.
 */
function dirtshack_made_to_order_badge() {
	// Small clock glyph reinforces "time to ship" without relying on an icon font.
	$icon = '<svg class="ds-mto__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

	return sprintf(
		'<div class="ds-mto ds-mto--full" role="note">%1$s<span class="ds-mto__text"><strong class="ds-mto__label">%2$s</strong><span class="ds-mto__timeline">%3$s</span></span></div>',
		$icon,
		esc_html( DIRTSHACK_MTO_LABEL ),
		esc_html( DIRTSHACK_MTO_TIMELINE )
	);
}

/**
 * Corner ribbon — the compact neon tag used on catalog listings (shop loop
 * thumbnails and the homepage featured cards) and the single-product gallery.
 * Visual only (aria-hidden); pair with dirtshack_made_to_order_sr_label() where
 * the surrounding markup doesn't otherwise convey it to assistive tech.
 *
 * @return string Escaped HTML.
 */
function dirtshack_made_to_order_ribbon() {
	return sprintf(
		'<span class="ds-mto-ribbon" aria-hidden="true">%s</span>',
		esc_html( DIRTSHACK_MTO_LABEL )
	);
}

/** Screen-reader-only label so listing ribbons remain accessible. */
function dirtshack_made_to_order_sr_label() {
	return sprintf(
		'<span class="ds-mto-sr">%s</span>',
		esc_html( DIRTSHACK_MTO_LABEL )
	);
}

// ─── Front-end: shop / archive loop ──────────────────────────────────────────
// On listings a neon corner ribbon on the thumbnail is enough (per brief) — no
// price-line chip. The ribbon itself is a CSS ::after on .product-item-thumbnail
// (decorative), so we also emit a visually-hidden label through the one loop hook
// Ohio reliably fires (woocommerce_after_shop_loop_item_title, inside .woo-price)
// to keep the indicator accessible to screen readers.

add_action( 'woocommerce_after_shop_loop_item_title', 'dirtshack_mto_loop_sr_label', 15 );
function dirtshack_mto_loop_sr_label() {
	if ( dirtshack_is_made_to_order() ) {
		echo dirtshack_made_to_order_sr_label(); // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped.
	}
}

// Tag the loop <li> so the thumbnail can carry the CSS corner ribbon.
add_filter( 'woocommerce_post_class', 'dirtshack_mto_loop_class', 10, 2 );
function dirtshack_mto_loop_class( $classes, $product ) {
	if ( dirtshack_is_made_to_order( $product ) ) {
		$classes[] = 'ds-mto-item';
	}
	return $classes;
}

// ─── Front-end: single product page ──────────────────────────────────────────
// Full badge in the summary column, between the short description (20) and the
// add-to-cart form (30) so it sets expectations right before purchase.

add_action( 'woocommerce_single_product_summary', 'dirtshack_mto_single_badge', 25 );
function dirtshack_mto_single_badge() {
	if ( dirtshack_is_made_to_order() ) {
		echo dirtshack_made_to_order_badge(); // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped.
	}
}

// Corner ribbon over the product gallery (fires in the image column, after the
// sale flash/images). Decorative; mirrors the sale-flash convention.
add_action( 'woocommerce_before_single_product_summary', 'dirtshack_mto_single_ribbon', 25 );
function dirtshack_mto_single_ribbon() {
	if ( dirtshack_is_made_to_order() ) {
		echo dirtshack_made_to_order_ribbon(); // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped.
	}
}

// ─── Styles ──────────────────────────────────────────────────────────────────
// Inline at wp_head 9999 so it beats Ohio's inline dynamic CSS and is never
// served stale (see the CSS-caching gotcha). Scoped to the surfaces that show a
// product: shop/archive, single product, and the homepage featured grid.

add_action( 'wp_head', 'dirtshack_mto_css', 9999 );
function dirtshack_mto_css() {
	if ( ! ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_shop() || is_product_taxonomy() || is_product() || is_cart() ) ) && ! is_front_page() ) {
		return;
	}
	?>
	<style id="ds-made-to-order-css">
	/* Brand tokens with hardcoded fallbacks (style.css :root can be cached stale). */
	.ds-mto{--ds-mto-green:var(--ds-yellow,#C4E000);--ds-mto-dark:var(--ds-dark,#111);
		display:inline-flex;align-items:center;gap:.4em;box-sizing:border-box}
	.ds-mto *{box-sizing:border-box}
	.ds-mto__icon{width:1.15em;height:1.15em;flex:0 0 auto;display:block}
	.ds-mto__text{display:flex;flex-direction:column;line-height:1.2;text-align:left}
	.ds-mto__label{font-weight:800;letter-spacing:.01em}
	.ds-mto__timeline{font-weight:600;opacity:.85}

	/* Screen-reader-only label (keeps the visual-only listing ribbon accessible). */
	.ds-mto-sr{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;
		overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

	/* Full badge — single product summary. Framed block with neon accent. */
	.ds-mto--full{width:100%;margin:.9rem 0 1.1rem;padding:.85rem 1rem;gap:.65em;
		border:1px solid rgba(17,17,17,.12);border-left:4px solid var(--ds-mto-green);
		border-radius:var(--ds-radius,16px);background:rgba(196,224,0,.08);
		color:var(--ds-mto-dark)}
	.ds-mto--full .ds-mto__icon{width:1.5em;height:1.5em;color:var(--ds-mto-dark)}
	.ds-mto--full .ds-mto__label{font-size:1.02rem}
	.ds-mto--full .ds-mto__timeline{font-size:.9rem;opacity:.8}

	/* Decorative corner ribbon on loop thumbnails. Ohio's .product-item-thumbnail
	   is the positioned image wrapper. */
	.ds-mto-item .product-item-thumbnail{position:relative}
	.ds-mto-item .product-item-thumbnail::after{content:"<?php echo esc_js( DIRTSHACK_MTO_LABEL ); ?>";
		position:absolute;top:10px;left:10px;z-index:3;
		padding:.28rem .55rem;border-radius:8px;
		background:var(--ds-yellow,#C4E000);color:var(--ds-dark,#111);
		font-size:.62rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;
		line-height:1;pointer-events:none;box-shadow:0 2px 8px rgba(0,0,0,.18)}

	/* Corner ribbon — shared by the single-product gallery and the homepage
	   featured cards (real element, so it carries its own accessible text). */
	.single-product .woo-product-image,
	#ds-home .ds-product__img{position:relative}
	.ds-mto-ribbon{position:absolute;top:14px;left:14px;z-index:5;
		padding:.4rem .7rem;border-radius:10px;
		background:var(--ds-yellow,#C4E000);color:var(--ds-dark,#111);
		font-size:.72rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;
		line-height:1;box-shadow:0 3px 12px rgba(0,0,0,.2)}
	#ds-home .ds-product__img .ds-mto-ribbon{top:8px;left:8px;font-size:.6rem;padding:.28rem .5rem;border-radius:8px}
	</style>
	<?php
}
