<?php
/**
 * DirtShack — single-product content override (thin wrapper).
 *
 * Ohio's product layout (single-product/views/type_1.php) fires
 * `woocommerce_before_single_product` *inside* the right-hand summary column,
 * so the site-wide inner hero — which is full-bleed (100vw) — ends up nested in
 * that column and paints on top of the gallery and description.
 *
 * There is no Ohio/WooCommerce action that fires above the two-column grid, so
 * we override this template purely to emit the hero at the very top of the
 * product content (above the product wrapper), then immediately hand off to
 * Ohio's original template. The parent file is *included* live rather than
 * copied, so any future Ohio update to content-single-product.php still applies.
 *
 * @see ohio-child/functions.php — dirtshack_emit_hero_once() / hero CSS injection
 */

defined( 'ABSPATH' ) || exit;

// Render the page-top hero once, above the entire product layout.
if ( function_exists( 'dirtshack_emit_hero_once' ) ) {
	dirtshack_emit_hero_once();
}

// Back button — links to the shop (or browser history via JS fallback).
$back_url = wc_get_page_permalink( 'shop' );
?>
<div class="ds-product-back-wrap">
	<a class="ds-product-back" href="<?php echo esc_url( $back_url ); ?>" onclick="if(document.referrer&&document.referrer!==location.href){history.back();return false;}">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
		Back to Shop
	</a>
</div>
<?php

// Defer to Ohio's original template (kept live so theme updates still apply).
include get_template_directory() . '/woocommerce/content-single-product.php';
