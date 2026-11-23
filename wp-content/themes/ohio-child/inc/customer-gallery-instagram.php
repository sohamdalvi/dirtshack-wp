<?php
/**
 * DirtShack — Rider Stories (Instagram posts) on product pages.
 *
 * The posts are on customers' Instagram accounts, not the shop's, so they can't
 * be found automatically. Staff paste each post's link into Product data →
 * General → "Rider Stories", one per line. The product page then shows a
 * "Rider Stories" tab with a carousel of the posts. Each post's embed loads as
 * its card scrolls into view (assets/js/instagram-posts.js).
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Post-meta key holding the saved links, one per line. */
const DIRTSHACK_IG_META = '_ds_instagram_links';

// ─── Admin: the links field ──────────────────────────────────────────────────

add_action( 'woocommerce_product_options_general_product_data', 'dirtshack_ig_product_field' );
function dirtshack_ig_product_field() {
	global $product_object;

	$product = $product_object instanceof WC_Product ? $product_object : null;
	$value   = $product ? (string) $product->get_meta( DIRTSHACK_IG_META ) : '';

	echo '<div class="options_group">';
	woocommerce_wp_textarea_input(
		array(
			'id'          => DIRTSHACK_IG_META,
			'label'       => __( 'Rider Stories', 'ohio-child' ),
			'placeholder' => 'Instagram or YouTube link, e.g. https://www.youtube.com/watch?v=…',
			'value'       => $value,
			'rows'        => 3,
			'description' => dirtshack_ig_field_status( $value ),
		)
	);
	echo '</div>';
}

/**
 * Help text under the field, plus any line that isn't an Instagram post link.
 *
 * @param string $saved Saved links.
 * @return string HTML (escaped here).
 */
function dirtshack_ig_field_status( $saved ) {
	$parts   = array( esc_html__( 'Instagram post or reel links, or YouTube links, one per line. They show in the "Rider Stories" tab on the product page.', 'ohio-child' ) );
	$lines   = dirtshack_ig_split( $saved );
	$skipped = array();
	foreach ( $lines as $line ) {
		if ( ! dirtshack_ig_is_post_url( $line ) ) {
			$skipped[] = '<code>' . esc_html( $line ) . '</code>';
		}
	}
	$count = count( $lines ) - count( $skipped );
	if ( $count > 0 ) {
		/* translators: %d: number of posts */
		$parts[] = '<strong>' . esc_html( sprintf( _n( '%d post showing on the product page.', '%d posts showing on the product page.', $count, 'ohio-child' ), $count ) ) . '</strong>';
	}
	if ( $skipped ) {
		$parts[] = '<strong>' . esc_html__( 'Not an Instagram post link, so not shown:', 'ohio-child' ) . '</strong> ' . implode( ', ', $skipped );
	}
	return implode( '<br>', $parts );
}

add_action( 'woocommerce_process_product_meta', 'dirtshack_ig_save_product_field' );
function dirtshack_ig_save_product_field( $post_id ) {
	// woocommerce_process_product_meta already verifies the product-data nonce and
	// edit capability before firing.
	$raw   = isset( $_POST[ DIRTSHACK_IG_META ] ) ? wp_unslash( $_POST[ DIRTSHACK_IG_META ] ) : '';
	$links = array();
	foreach ( dirtshack_ig_split( $raw ) as $line ) {
		$canonical = dirtshack_ig_canonical( $line );
		if ( '' !== $canonical && ! in_array( $canonical, $links, true ) ) {
			$links[] = $canonical;
		}
	}
	$value = implode( "\n", $links );

	$product = wc_get_product( $post_id );
	if ( ! $product ) {
		return;
	}
	$changed = (string) $product->get_meta( DIRTSHACK_IG_META ) !== $value;
	if ( '' === $value ) {
		$product->delete_meta_data( DIRTSHACK_IG_META );
	} else {
		$product->update_meta_data( DIRTSHACK_IG_META, $value );
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

/** Non-empty trimmed lines. */
function dirtshack_ig_split( $text ) {
	$lines = array_map( 'trim', preg_split( '/\R/', (string) $text ) );
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * The main address of an Instagram post, reel or IGTV link, or '' if it isn't one.
 * Pasted variants are accepted and reduced to the post itself: with or without
 * "https://", with a tracking "?igsh=" part, or a link to one comment
 * (…/p/ID/c/COMMENT_ID/), which becomes https://www.instagram.com/p/ID/.
 *
 * @param string $url Candidate link.
 * @return string
 */
function dirtshack_ig_canonical( $url ) {
	$url = trim( (string) $url );
	if ( 1 === preg_match( '#^(?:https://)?(?:www\.)?instagram\.com/(p|reel|tv)/([A-Za-z0-9_-]+)(?:[/?\#].*)?$#i', $url, $m ) ) {
		return 'https://www.instagram.com/' . strtolower( $m[1] ) . '/' . $m[2] . '/';
	}
	// A YouTube video link (any form), reduced to watch?v=ID. Needs a YouTube host so a bare word isn't taken for an ID.
	if ( preg_match( '#youtu(?:\.be|be\.com)/#i', $url ) ) {
		$id = dirtshack_braap_youtube_id( $url );
		if ( $id ) {
			return 'https://www.youtube.com/watch?v=' . $id;
		}
	}
	return '';
}

/**
 * Whether a saved link is an Instagram post, reel or IGTV address.
 *
 * @param string $url Saved link.
 * @return bool
 */
function dirtshack_ig_is_instagram( $url ) {
	return false !== strpos( (string) $url, 'instagram.com/' );
}

/**
 * Whether a link is (or reduces to) an Instagram post, reel or IGTV address.
 *
 * @param string $url Candidate link.
 * @return bool
 */
function dirtshack_ig_is_post_url( $url ) {
	return '' !== dirtshack_ig_canonical( $url );
}

// ─── Assets ──────────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'dirtshack_ig_enqueue' );
function dirtshack_ig_enqueue() {
	if ( ! ( function_exists( 'is_product' ) && is_product() ) ) {
		return;
	}
	$rel  = 'assets/js/instagram-posts.js';
	$path = get_stylesheet_directory() . '/' . $rel;
	if ( ! file_exists( $path ) ) {
		return;
	}
	wp_enqueue_script(
		'dirtshack-instagram-posts',
		get_stylesheet_directory_uri() . '/' . $rel,
		array(),
		filemtime( $path ),
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
}

// ─── Product tab: Rider Stories ──────────────────────────────────────────────

/** Product tab key; Ohio prints it as data-ohio-tab="…" and #tab-title-…. */
const DIRTSHACK_RIDER_TAB = 'ds_rider_stories';

// Between Video guide (15) and More Info (25).
add_filter( 'woocommerce_product_tabs', 'dirtshack_rider_tab' );
function dirtshack_rider_tab( $tabs ) {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $tabs;
	}
	$links = dirtshack_ig_links( $product );
	if ( $links ) {
		$tabs[ DIRTSHACK_RIDER_TAB ] = array(
			/* translators: %d: number of rider stories */
			'title'    => sprintf( __( 'Rider Stories (%d)', 'ohio-child' ), count( $links ) ),
			'priority' => 20,
			'callback' => 'dirtshack_rider_tab_content',
		);
	}
	return $tabs;
}

/**
 * Valid Instagram post links saved on a product.
 *
 * @param WC_Product $product Product.
 * @return string[]
 */
function dirtshack_ig_links( $product ) {
	return array_values( array_filter( dirtshack_ig_split( $product->get_meta( DIRTSHACK_IG_META ) ), 'dirtshack_ig_is_post_url' ) );
}

function dirtshack_rider_tab_content() {
	global $product;
	$links = dirtshack_ig_links( $product );

	echo '<div class="ds-rs" data-ds-rider>';
	echo '<div class="ds-rs__track" role="list">';
	dirtshack_yt_facade_css();
	foreach ( $links as $url ) {
		if ( ! dirtshack_ig_is_instagram( $url ) ) {
			// YouTube: the same click-to-load facade as Video guide, with a thumbnail.
			$id     = dirtshack_braap_youtube_id( $url );
			$poster = sprintf( '<img src="%s" alt="" loading="lazy" width="480" height="360" />', esc_url( dirtshack_braap_thumb_url( $id ) ) );
			echo '<div class="ds-rs__slide" role="listitem">';
			echo dirtshack_yt_facade( $id, $poster, __( 'Play video', 'ohio-child' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in dirtshack_yt_facade().
			echo '<a class="ds-rs__link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Watch on YouTube', 'ohio-child' ) . '</a></div>';
			continue;
		}
		$label = preg_match( '#/(reel|tv)/#', $url ) ? __( 'Watch the reel on Instagram', 'ohio-child' ) : __( 'View the post on Instagram', 'ohio-child' );
		echo '<div class="ds-rs__slide" role="listitem"><div class="ds-rs__embed" data-permalink="' . esc_url( $url ) . '"></div>';
		echo '<a class="ds-rs__link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a></div>';
	}
	echo '</div>';
	echo '</div>';
	?>
	<style id="ds-rs-css">
	.ds-rs{position:relative}
	.ds-rs__track{display:flex;gap:1rem;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;overscroll-behavior-x:contain;padding:.25rem .25rem 1rem}
	.ds-rs__slide{flex:0 0 min(340px,85vw);scroll-snap-align:start;min-width:0}
	.ds-rs__embed{min-height:420px}
	.ds-rs__embed blockquote.instagram-media{margin:0 auto!important;max-width:100%!important;min-width:0!important;width:100%!important}
	.ds-rs__link{display:inline-block;margin-top:.4rem;font-size:.9rem;color:inherit;opacity:.75}
	</style>
	<?php
}

