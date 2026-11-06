<?php
/**
 * DirtShack — product video guides (YouTube).
 *
 * Products can have long YouTube videos: product details, fitting and install
 * guides. Paste the links into Product data → General → "Video guide" (one per
 * line) and the product page gets:
 *
 *   - a "Video guide" tab next to Description, each video shown as the same
 *     click-to-load facade the Braap pages use (poster + play button; the
 *     youtube-nocookie iframe only loads on click, see assets/js/ds.js), and
 *   - a "Watch the video guide" link above Add to Cart that opens that tab.
 *
 * Not in the image gallery on purpose: these are long guides rather than a quick
 * look at the product, and WooCommerce's own gallery videos (beta) only take
 * uploaded video files, not YouTube links.
 *
 * Saving changed links clears that product page from WP Super Cache and, when
 * the API token is set up, Cloudflare (dirtshack_cloudflare_purge_request in
 * holiday-notice.php), so the tab shows right away instead of within 4 hours.
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Post-meta key holding the YouTube links, one per line, as typed. */
const DIRTSHACK_PVIDEO_META = '_ds_video_guide';

/** Product tab key; Ohio prints it as data-ohio-tab="…" and #tab-title-…. */
const DIRTSHACK_PVIDEO_TAB = 'ds_video';

// ─── Admin: the links field ──────────────────────────────────────────────────
// A textarea in Product data → General, which every product type shows.

add_action( 'woocommerce_product_options_general_product_data', 'dirtshack_pvideo_product_field' );
function dirtshack_pvideo_product_field() {
	global $product_object;

	echo '<div class="options_group">';
	woocommerce_wp_textarea_input(
		array(
			'id'          => DIRTSHACK_PVIDEO_META,
			'label'       => __( 'Video guide', 'ohio-child' ),
			'placeholder' => 'https://www.youtube.com/watch?v=…',
			'rows'        => 3,
			'description' => dirtshack_pvideo_field_status( $product_object instanceof WC_Product ? $product_object : null ),
		)
	);
	echo '</div>';
}

/**
 * Help text under the field, plus what the saved links turned into: how many
 * videos show, and any line that isn't a YouTube link (those are skipped).
 *
 * @param WC_Product|null $product Product being edited.
 * @return string HTML (escaped here; WooCommerce runs it through wp_kses_post).
 */
function dirtshack_pvideo_field_status( $product ) {
	$parts = array( esc_html__( 'YouTube links, one per line: product details, fitting or install guides. They show in a "Video guide" tab on the product page.', 'ohio-child' ) );

	if ( $product ) {
		$skipped = array();
		foreach ( dirtshack_pvideo_lines( $product ) as $line ) {
			if ( ! dirtshack_braap_youtube_id( $line ) ) {
				$skipped[] = '<code>' . esc_html( $line ) . '</code>';
			}
		}
		$count = count( dirtshack_pvideo_ids( $product ) );
		if ( $count ) {
			/* translators: %d: number of videos */
			$parts[] = '<strong>' . esc_html( sprintf( _n( '%d video showing on the product page.', '%d videos showing on the product page.', $count, 'ohio-child' ), $count ) ) . '</strong>';
		}
		if ( $skipped ) {
			$parts[] = '<strong>' . esc_html__( 'Not a YouTube link, so not shown:', 'ohio-child' ) . '</strong> ' . implode( ', ', $skipped );
		}
	}

	return implode( '<br>', $parts );
}

add_action( 'woocommerce_process_product_meta', 'dirtshack_pvideo_save_product_field' );
function dirtshack_pvideo_save_product_field( $post_id ) {
	// woocommerce_process_product_meta already verifies the product-data nonce and
	// edit capability before firing.
	$links = isset( $_POST[ DIRTSHACK_PVIDEO_META ] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST[ DIRTSHACK_PVIDEO_META ] ) ) ) : '';

	$product = wc_get_product( $post_id );
	if ( ! $product ) {
		return;
	}
	$changed = (string) $product->get_meta( DIRTSHACK_PVIDEO_META ) !== $links;
	if ( '' === $links ) {
		$product->delete_meta_data( DIRTSHACK_PVIDEO_META );
	} else {
		$product->update_meta_data( DIRTSHACK_PVIDEO_META, $links );
	}
	$product->save();

	// The cached product page doesn't have the new tab yet. Clear the server copy
	// first so Cloudflare fetches the new page, not the old cached one.
	if ( $changed && 'publish' === get_post_status( $post_id ) ) {
		if ( function_exists( 'wpsc_delete_post_cache' ) ) {
			wpsc_delete_post_cache( $post_id );
		}
		if ( function_exists( 'dirtshack_cloudflare_purge_request' ) ) {
			dirtshack_cloudflare_purge_request( array( 'files' => array( get_permalink( $post_id ) ) ) );
		}
	}
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * The non-empty lines of the saved field.
 *
 * @param WC_Product $product Product.
 * @return string[]
 */
function dirtshack_pvideo_lines( $product ) {
	$lines = array_map( 'trim', preg_split( '/\R/', (string) $product->get_meta( DIRTSHACK_PVIDEO_META ) ) );
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * YouTube video IDs for a product, in the order typed, without repeats.
 * Uses the Braap URL parser, so watch, youtu.be, Shorts and embed links all work.
 *
 * @param WC_Product $product Product.
 * @return string[]
 */
function dirtshack_pvideo_ids( $product ) {
	$ids = array();
	foreach ( dirtshack_pvideo_lines( $product ) as $line ) {
		$id = dirtshack_braap_youtube_id( $line );
		if ( $id ) {
			$ids[] = $id;
		}
	}
	return array_values( array_unique( $ids ) );
}

// ─── Front-end: product page ─────────────────────────────────────────────────

// Tab between Description (10) and Additional information (20) / Reviews (30).
add_filter( 'woocommerce_product_tabs', 'dirtshack_pvideo_tab' );
function dirtshack_pvideo_tab( $tabs ) {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $tabs;
	}
	$count = count( dirtshack_pvideo_ids( $product ) );
	if ( $count ) {
		$tabs[ DIRTSHACK_PVIDEO_TAB ] = array(
			'title'    => _n( 'Video guide', 'Video guides', $count, 'ohio-child' ),
			'priority' => 15,
			'callback' => 'dirtshack_pvideo_tab_content',
		);
	}
	return $tabs;
}

function dirtshack_pvideo_tab_content() {
	global $product;
	$ids  = dirtshack_pvideo_ids( $product );
	$name = $product->get_name();

	echo '<div class="ds-pvideos">';
	foreach ( $ids as $i => $vid ) {
		$poster = sprintf( '<img src="%s" alt="" loading="lazy" width="480" height="360" />', esc_url( dirtshack_braap_thumb_url( $vid ) ) );
		$label  = count( $ids ) > 1 ? sprintf( 'Play video %1$d: %2$s', $i + 1, $name ) : sprintf( 'Play video: %s', $name );
		echo dirtshack_yt_facade( $vid, $poster, $label ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in dirtshack_yt_facade().
	}
	echo '</div>';
}

// "Watch the video guide" right above Add to Cart (30), after the Made to Order
// badge (25). Without JS it's a plain jump to the tab title; ds.js also opens
// the tab.
add_action( 'woocommerce_single_product_summary', 'dirtshack_pvideo_jump_link', 27 );
function dirtshack_pvideo_jump_link() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$count = count( dirtshack_pvideo_ids( $product ) );
	if ( ! $count ) {
		return;
	}
	printf(
		'<a class="ds-video-jump" href="#tab-title-%1$s"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="11" fill="#111"/><path d="M10 8v8l6-4z" fill="#C4E000"/></svg><span>%2$s</span></a>',
		esc_attr( DIRTSHACK_PVIDEO_TAB ),
		esc_html( _n( 'Watch the video guide', 'Watch the video guides', $count, 'ohio-child' ) )
	);
}

// ─── Styles ──────────────────────────────────────────────────────────────────
// Inline at wp_head 9999 like the other modules (style.css can be served stale),
// and only on product pages that have a video.

add_action( 'wp_head', 'dirtshack_pvideo_css', 9999 );
function dirtshack_pvideo_css() {
	if ( ! ( function_exists( 'is_product' ) && is_product() ) ) {
		return;
	}
	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product || ! dirtshack_pvideo_ids( $product ) ) {
		return;
	}
	?>
	<style id="ds-product-video-css">
	.ds-pvideos{display:grid;gap:1.5rem;max-width:960px}
	.ds-video-jump{display:inline-flex;align-items:center;gap:.55em;margin:0 0 1.1rem;
		font-weight:700;color:var(--ds-dark,#111);text-decoration:none}
	.ds-video-jump svg{width:1.6em;height:1.6em;flex:0 0 auto;display:block;transition:transform .2s}
	.ds-video-jump span{border-bottom:2px solid var(--ds-yellow,#C4E000)}
	.ds-video-jump:hover svg{transform:scale(1.1)}
	/* Room for the sticky header when the link scrolls the tabs into view. */
	.single-product-tabs{scroll-margin-top:110px}
	</style>
	<?php
	dirtshack_yt_facade_css();
}
