/**
 * DirtShack cart-page enhancements (loaded only on /cart/).
 *
 * Fixes two gaps in Ohio's cart behaviour:
 *  1. Ohio builds the qty +/- buttons (.quantity-nav) with JS on page load, but
 *     when the cart table is re-rendered (Ohio's add-to-cart .load() refresh, or
 *     WooCommerce's "Update cart" AJAX which replaces the .woocommerce div) the
 *     freshly-loaded server HTML has none, and Ohio never rebuilds them -> the
 *     +/- signs vanish. We rebuild them after every cart re-render.
 *  2. Changing the quantity only changes the number; nothing recalculates the
 *     totals. We auto-trigger WooCommerce's "Update cart" (debounced) on change.
 *
 * Care is taken NOT to double-bind Ohio's initial buttons: we only build +/-
 * for .quantity elements that don't already have a .quantity-nav, and bind the
 * click handlers directly to the buttons we create. The auto-update listener is
 * delegated, so it also benefits Ohio's original buttons (which trigger 'change').
 */
(function ($) {
	'use strict';

	var QNAV =
		'<div class="quantity-nav">' +
			'<div tabindex="0" class="quantity-button button -flat quantity-down">' +
				'<svg width="14" height="2" viewBox="0 0 14 2" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H0V0H14V2Z"/></svg>' +
			'</div>' +
			'<div tabindex="0" class="quantity-button button -flat quantity-up">' +
				'<svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path d="M14 8H8V14H6V8H0V6H6V0H8V6H14V8Z"/></svg>' +
			'</div>' +
		'</div>';

	function step($input, dir) {
		var val = parseFloat($input.val());
		if (isNaN(val)) { val = 0; }
		var min = parseFloat($input.attr('min'));
		if (isNaN(min)) { min = 0; }
		var maxAttr = $input.attr('max');
		var max = (maxAttr === '' || maxAttr == null) ? Infinity : parseFloat(maxAttr);
		if (dir > 0) { if (val < max) { val++; } }
		else { if (val > min) { val--; } }
		$input.val(val).trigger('change');
	}

	// Rebuild +/- on any .quantity in the cart form that lost them.
	function rebuildQtyNav() {
		$('.woocommerce-cart-form .quantity').each(function () {
			var $q = $(this);
			if ($q.find('.quantity-nav').length) { return; } // Ohio's (or ours) already present
			var $input = $q.find('input[type="number"], input[type="text"]').first();
			if (!$input.length) { return; }
			var $nav = $(QNAV).insertAfter($input);
			$nav.find('.quantity-up').on('click', function () { step($input, 1); });
			$nav.find('.quantity-down').on('click', function () { step($input, -1); });
		});
	}

	$(function () {
		if (!$('.woocommerce-cart-form').length) { return; }

		// Auto-update the cart (debounced) whenever a quantity changes — works for
		// Ohio's original +/-, the ones we rebuild, and manual typing.
		var timer = null;
		$(document.body).on('change input', '.woocommerce-cart-form input.qty', function () {
			clearTimeout(timer);
			timer = setTimeout(function () {
				var $btn = $('.woocommerce-cart-form :input[name="update_cart"]');
				if ($btn.length) { $btn.prop('disabled', false).trigger('click'); }
			}, 600);
		});

		// Rebuild +/- after every cart re-render.
		$(document.body).on(
			'cart_page_refreshed updated_wc_div updated_cart_totals wc_fragments_refreshed wc_cart_emptied',
			function () { rebuildQtyNav(); }
		);
	});
})(jQuery);
