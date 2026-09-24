<?php
/**
 * DirtShack — bulk packing slips, two per A4 page (PDF Invoices & Packing Slips).
 *
 * Orders → select several → Bulk actions → PDF Packing Slip merges the slips into
 * one PDF. The plugin puts every slip on its own page, leaving half of each page
 * blank; this puts two slips on a page (top and bottom half) with a dashed cut
 * line between them, each keeping its own footer.
 *
 * An order with more items than fit in a half page gets a full page of its own,
 * so nothing spills into the next slip. Slips stay in the order they were
 * selected. A single packing slip (from the order screen) is unchanged.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Most item rows that fit in a half-page slip; longer orders get a full page. */
const DIRTSHACK_PACKING_SLIP_HALF_MAX_ITEMS = 3;

add_filter( 'wpo_wcpdf_merged_bulk_document_content', 'dirtshack_packing_slips_two_per_page', 10, 3 );
function dirtshack_packing_slips_two_per_page( $html, $html_content, $bulk ) {
	if ( ! is_object( $bulk ) || ! method_exists( $bulk, 'get_type' ) || 'packing-slip' !== $bulk->get_type() || count( $html_content ) < 2 ) {
		return $html;
	}

	$half = function ( $slip ) {
		return '<div class="ds-slip ds-slip-half">' . $slip . '</div>';
	};

	$pages   = [];
	$pending = null; // A half slip waiting for a partner on the same page.
	foreach ( $html_content as $slip ) {
		if ( substr_count( $slip, 'class="item-name"' ) > DIRTSHACK_PACKING_SLIP_HALF_MAX_ITEMS ) {
			if ( null !== $pending ) {
				$pages[] = $pending;
				$pending = null;
			}
			$pages[] = '<div class="ds-slip ds-slip-full">' . $slip . '</div>';
		} elseif ( null === $pending ) {
			$pending = $half( $slip );
		} else {
			$pages[] = $pending . '<div class="ds-cut-line"></div>' . $half( $slip );
			$pending = null;
		}
	}
	if ( null !== $pending ) {
		$pages[] = $pending;
	}

	return dirtshack_packing_slips_bulk_css() . implode( "\n<div style=\"page-break-before: always;\"></div>\n", $pages );
}

/**
 * Layout for merged slips only. Page margins shrink (each half carries its own
 * footer instead of the page-level one); a half is 13.5cm tall with its footer
 * pinned to its bottom edge.
 */
function dirtshack_packing_slips_bulk_css() {
	return '<style>
		@page { margin: 0.8cm 2cm 0.8cm 2cm; }
		.ds-slip { position: relative; }
		.ds-slip-half { height: 13.5cm; overflow: hidden; page-break-inside: avoid; }
		.ds-slip-half table.head { margin-bottom: 4mm; }
		.ds-slip-half td.header img { max-height: 2cm; }
		.ds-slip-half h1.document-type-label { margin: 2mm 0 3mm 0; }
		.ds-slip-half table.order-data-addresses { margin-bottom: 4mm; }
		.ds-slip-half div.bottom-spacer { height: 0; }
		.ds-slip-half #footer { position: absolute; bottom: 0; left: 0; right: 0; height: auto; padding-top: 1.5mm; }
		.ds-slip-full #footer { position: static; height: auto; margin-top: 10mm; padding-top: 2mm; }
		.ds-cut-line { height: 0; margin: 0.5cm 0; border-top: 0.3mm dashed #000; }
	</style>';
}
