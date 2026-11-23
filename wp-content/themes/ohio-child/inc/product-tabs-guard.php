<?php
/**
 * DirtShack — keep stray tab markup in product descriptions from breaking the tabs.
 *
 * Ohio's tab script (ohio/assets/js/source/main.js, initTabs) pairs the tab buttons
 * with the panels by position: it counts every `.tabs-content-item` inside the tab
 * box. If a product description contains a pasted copy of a tab panel (an element
 * with id="tab-…" and the tabs-content-item class), that copy is counted as an
 * extra panel, so every tab after it shows the wrong panel. That happened on
 * rear-lift-strap-v2 (the video guide showed under Additional information).
 *
 * This removes the tabs-content-item class from such elements when a product
 * description is printed. The content itself is left alone, so the text still
 * shows inside the Description tab. The real fix is to delete the stray block in
 * the product's Description; this stops the same shift if it comes back.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'the_content', 'dirtshack_tabs_guard_description', 9 );

/**
 * Strip tabs-content-item from pasted tab panels inside a product description.
 *
 * @param string $content Post content.
 * @return string
 */
function dirtshack_tabs_guard_description( $content ) {
	if ( ! function_exists( 'is_product' ) || ! is_product() || ! in_the_loop() ) {
		return $content;
	}
	return dirtshack_tabs_strip_panels( $content );
}

/**
 * Remove tabs-content-item from any id="tab-…" element in a string of HTML.
 * Used for product descriptions and for the More Info tab content.
 *
 * @param string $content HTML.
 * @return string
 */
function dirtshack_tabs_strip_panels( $content ) {
	if ( false === strpos( $content, 'tabs-content-item' ) ) {
		return $content;
	}

	return preg_replace_callback(
		'/<[a-z][a-z0-9]*\b[^>]*>/i',
		static function ( $m ) {
			$tag = $m[0];
			// Only tags that look like a tab panel: an id="tab-…" and the panel class.
			if ( ! preg_match( '/\sid\s*=\s*["\']tab-/i', $tag ) || ! preg_match( '/\sclass\s*=\s*["\'][^"\']*\btabs-content-item\b/i', $tag ) ) {
				return $tag;
			}
			return preg_replace_callback(
				'/(\sclass\s*=\s*)(["\'])([^"\']*)\2/i',
				static function ( $c ) {
					$classes = preg_replace( '/\btabs-content-item\b/', '', $c[3] );
					$classes = trim( preg_replace( '/\s+/', ' ', $classes ) );
					return $c[1] . $c[2] . $classes . $c[2];
				},
				$tag,
				1
			);
		},
		$content
	);
}
