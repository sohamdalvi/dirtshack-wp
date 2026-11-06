<?php
/**
 * DirtShack — Customer Gallery.
 *
 * Photos of DirtShack parts on customers' bikes. They are posted in the
 * "Customer Gallery" album on the DirtShack Facebook Page, with the product's
 * slug in the caption. A Cloudflare Worker (workers/customer-gallery in the
 * repo) copies them into Cloudflare R2 every hour and writes one JSON list per
 * product, so nothing is stored on, or processed by, this server. This module
 * only shows them:
 *
 *   - Product pages: a "Customer Gallery" thumbnail strip between the main
 *     image block and the description tabs; it scrolls sideways and each photo
 *     opens full screen, where visitors can page through all of them. It stays
 *     hidden until the visitor's browser has loaded <gallery>/products/<slug>.json
 *     and found photos (assets/js/customer-gallery.js). The page HTML is the
 *     same with or without photos, so new photos never need a page-cache purge.
 *   - [customer_gallery] shortcode: every photo, filterable by product. Put it
 *     on a page with the slug "customer-gallery" and product pages link to it.
 *   - Products → Customer Gallery (admin): last sync, errors, captions without a
 *     product slug, the slug of every product, and "Sync now".
 *
 * Off until wp-config.php has:
 *
 *   define( 'DIRTSHACK_GALLERY_URL', 'https://gallery.dirtshack.in' );
 *
 * and, for the "Sync now" button (from the Worker's setup):
 *
 *   define( 'DIRTSHACK_GALLERY_SYNC_URL', 'https://dirtshack-customer-gallery.<subdomain>.workers.dev/sync' );
 *   define( 'DIRTSHACK_GALLERY_SYNC_SECRET', '<the Worker's SYNC_SECRET>' );
 *
 * @package ohio-child
 */

defined( 'ABSPATH' ) || exit;

/** Admin screen: Products → Customer Gallery. */
const DIRTSHACK_GALLERY_ADMIN_PAGE = 'ds-customer-gallery';

/** Slug of the page holding [customer_gallery]; product pages link to it when it exists. */
const DIRTSHACK_GALLERY_PAGE_SLUG = 'customer-gallery';

// ─── Settings ────────────────────────────────────────────────────────────────

/**
 * Address of the Customer Gallery files without a trailing slash, or '' while
 * the feature is off. Plain http is accepted only on a local/development site.
 *
 * @return string
 */
function dirtshack_gallery_url() {
	if ( ! defined( 'DIRTSHACK_GALLERY_URL' ) ) {
		return '';
	}
	$url = untrailingslashit( trim( (string) DIRTSHACK_GALLERY_URL ) );
	if ( 0 === strpos( $url, 'https://' ) ) {
		return $url;
	}
	if ( 0 === strpos( $url, 'http://' ) && in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
		return $url;
	}
	return '';
}

/** Whether "Sync now" is set up. */
function dirtshack_gallery_can_sync() {
	return defined( 'DIRTSHACK_GALLERY_SYNC_URL' ) && defined( 'DIRTSHACK_GALLERY_SYNC_SECRET' )
		&& 0 === strpos( (string) DIRTSHACK_GALLERY_SYNC_URL, 'https://' ) && '' !== (string) DIRTSHACK_GALLERY_SYNC_SECRET;
}

/** Address of the published Customer Gallery page, or ''. */
function dirtshack_gallery_page_url() {
	static $url = null;
	if ( null === $url ) {
		$page = get_page_by_path( DIRTSHACK_GALLERY_PAGE_SLUG );
		$url  = ( $page && 'publish' === $page->post_status ) ? (string) get_permalink( $page ) : '';
	}
	return $url;
}

/** The product a single-product page is about (not a related/upsell one), or null. */
function dirtshack_gallery_main_product() {
	global $product;
	if ( ! ( function_exists( 'is_product' ) && is_product() ) || ! $product instanceof WC_Product ) {
		return null;
	}
	return $product->get_id() === get_queried_object_id() ? $product : null;
}

/** True on pages that show gallery photos: product pages, and pages with the shortcode. */
function dirtshack_gallery_on_this_page() {
	if ( '' === dirtshack_gallery_url() || is_admin() ) {
		return false;
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		return true;
	}
	$post = get_queried_object();
	return is_singular() && $post instanceof WP_Post && has_shortcode( $post->post_content, 'customer_gallery' );
}

// ─── Product page ────────────────────────────────────────────────────────────

/*
 * Ohio's product layouts print the image + summary block, then load the tabs
 * with wc_get_template_part( 'single-product/tabs/tabs' ). That function's
 * filter runs just before the tabs are printed, so echoing there puts the strip
 * between the main image block and the description on every layout. If a layout
 * ever skips the tabs, the start of single-product/up-sells.php (which
 * woocommerce_upsell_display() always loads) is the fallback.
 */
add_filter( 'wc_get_template_part', 'dirtshack_gallery_before_tabs', 10, 2 );
function dirtshack_gallery_before_tabs( $template, $slug ) {
	if ( 'single-product/tabs/tabs' === $slug ) {
		dirtshack_gallery_product_section();
	}
	return $template;
}

add_action( 'woocommerce_before_template_part', 'dirtshack_gallery_before_upsells' );
function dirtshack_gallery_before_upsells( $template_name ) {
	if ( 'single-product/up-sells.php' === $template_name ) {
		dirtshack_gallery_product_section();
	}
}

/** The product page's thumbnail strip; prints once, hidden until photos load. */
function dirtshack_gallery_product_section() {
	static $done = false;
	if ( $done || '' === dirtshack_gallery_url() ) {
		return;
	}
	$product = dirtshack_gallery_main_product();
	if ( ! $product ) {
		return;
	}
	$done = true;

	$wrap  = ( class_exists( 'OhioOptions' ) && ! OhioOptions::get( 'page_add_wrapper', true ) ) ? ' -full-w' : '';
	$page  = dirtshack_gallery_page_url();
	$arrow = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="%s" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>';
	?>
	<div class="page-container ds-cgallery-wrap<?php echo esc_attr( $wrap ); ?>" hidden>
		<section id="customer-gallery" class="ds-cgallery ds-cgallery--product" aria-labelledby="ds-cgallery-title"
			data-ds-cgallery="product"
			data-source="<?php echo esc_url( dirtshack_gallery_url() ); ?>"
			data-product="<?php echo esc_attr( $product->get_slug() ); ?>"
			data-name="<?php echo esc_attr( $product->get_name() ); ?>">
			<div class="ds-cgallery__head">
				<h2 id="ds-cgallery-title" class="ds-cgallery__title"><?php esc_html_e( 'Customer Gallery', 'ohio-child' ); ?> <span class="ds-cgallery__count"></span></h2>
				<?php if ( $page ) : ?>
					<a class="ds-cgallery__all" href="<?php echo esc_url( $page ); ?>"><?php esc_html_e( 'See all customer photos', 'ohio-child' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="ds-cgallery__strip">
				<button type="button" class="ds-cgallery__nav ds-cgallery__nav--prev" aria-label="<?php esc_attr_e( 'Scroll photos left', 'ohio-child' ); ?>" hidden><?php printf( $arrow, 'M15 5l-7 7 7 7' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed markup. ?></button>
				<ul class="ds-cgallery__track" role="list"></ul>
				<button type="button" class="ds-cgallery__nav ds-cgallery__nav--next" aria-label="<?php esc_attr_e( 'Scroll photos right', 'ohio-child' ); ?>" hidden><?php printf( $arrow, 'M9 5l7 7-7 7' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed markup. ?></button>
			</div>
		</section>
	</div>
	<?php
}

// ─── Gallery page: [customer_gallery] ────────────────────────────────────────

/*
 * [customer_gallery]                     every photo, newest first
 * [customer_gallery product="bar-ends"]  start filtered to one product
 * Visitors can filter by product; ?product=<slug> in the address does the same.
 */
add_shortcode( 'customer_gallery', 'dirtshack_gallery_shortcode' );
function dirtshack_gallery_shortcode( $atts ) {
	if ( '' === dirtshack_gallery_url() ) {
		return current_user_can( 'manage_woocommerce' )
			? '<p><em>' . esc_html__( 'The Customer Gallery is off: add DIRTSHACK_GALLERY_URL to wp-config.php (see Products → Customer Gallery). Only shop managers see this note.', 'ohio-child' ) . '</em></p>'
			: '';
	}
	$atts = shortcode_atts( array( 'product' => '' ), $atts, 'customer_gallery' );

	dirtshack_gallery_enqueue_script();
	ob_start();
	dirtshack_gallery_css(); // Prints once; normally already done in <head>.
	?>
	<div class="ds-cgallery ds-cgallery--page" aria-busy="true"
		data-ds-cgallery="all"
		data-source="<?php echo esc_url( dirtshack_gallery_url() ); ?>"
		data-shop="<?php echo esc_url( home_url( '/' ) ); ?>"
		data-product="<?php echo esc_attr( sanitize_title( $atts['product'] ) ); ?>">
		<div class="ds-cgallery__filters" role="group" aria-label="<?php esc_attr_e( 'Show photos of', 'ohio-child' ); ?>" hidden></div>
		<p class="ds-cgallery__status" role="status"><?php esc_html_e( 'Loading photos…', 'ohio-child' ); ?></p>
		<ul class="ds-cgallery__grid" role="list"></ul>
		<div class="ds-cgallery__foot">
			<button type="button" class="ds-cgallery__more" hidden><?php esc_html_e( 'Show more', 'ohio-child' ); ?></button>
		</div>
		<noscript><p><?php esc_html_e( 'Turn on JavaScript to see the Customer Gallery.', 'ohio-child' ); ?></p></noscript>
	</div>
	<?php
	return ob_get_clean();
}

// ─── Assets ──────────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'dirtshack_gallery_enqueue' );
function dirtshack_gallery_enqueue() {
	if ( dirtshack_gallery_on_this_page() ) {
		dirtshack_gallery_enqueue_script();
	}
}

/** Deferred, footer, versioned by file time like ds.js so caches bust on deploy. */
function dirtshack_gallery_enqueue_script() {
	$rel  = 'assets/js/customer-gallery.js';
	$path = get_stylesheet_directory() . '/' . $rel;
	if ( ! file_exists( $path ) ) {
		return;
	}
	wp_enqueue_script(
		'dirtshack-customer-gallery',
		get_stylesheet_directory_uri() . '/' . $rel,
		array(),
		filemtime( $path ),
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
}

// Open the connection to the gallery host early; the JSON is fetched in CORS
// mode, hence crossorigin.
add_filter( 'wp_resource_hints', 'dirtshack_gallery_preconnect', 10, 2 );
function dirtshack_gallery_preconnect( $urls, $relation ) {
	if ( 'preconnect' === $relation && dirtshack_gallery_on_this_page() ) {
		$urls[] = array( 'href' => dirtshack_gallery_url(), 'crossorigin' => 'anonymous' );
	}
	return $urls;
}

// Inline at wp_head 9999 like the other modules (style.css can be served stale).
add_action( 'wp_head', 'dirtshack_gallery_head_css', 9999 );
function dirtshack_gallery_head_css() {
	if ( dirtshack_gallery_on_this_page() ) {
		dirtshack_gallery_css();
	}
}

function dirtshack_gallery_css() {
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;
	?>
	<style id="ds-customer-gallery-css">
	.ds-cgallery-wrap[hidden],.ds-cgallery [hidden],.ds-cglb [hidden]{display:none!important}
	.ds-cgallery-wrap{margin-top:2.5rem;margin-bottom:1rem}
	.ds-cgallery{scroll-margin-top:110px}
	.ds-cgallery--product .ds-cgallery__head{align-items:center;margin-bottom:.85rem}
	.ds-cgallery--product .ds-cgallery__title{font-size:clamp(1.15rem,1rem + .6vw,1.5rem);line-height:1.2;text-transform:uppercase;letter-spacing:.02em}
	.ds-cgallery__count{font-size:.7em;font-weight:400;text-transform:none;letter-spacing:normal;opacity:.6}
	.ds-cgallery__strip{display:flex;align-items:center;gap:.5rem;padding:.75rem;border-radius:10px;background:#efefef}
	.ds-cgallery__track{list-style:none;flex:1 1 auto;min-width:0;margin:0;padding:2px;display:flex;gap:.6rem;overflow-x:auto;overscroll-behavior-x:contain;scroll-snap-type:x proximity;scroll-behavior:smooth;scrollbar-width:none;-webkit-overflow-scrolling:touch}
	.ds-cgallery__track::-webkit-scrollbar{display:none}
	.ds-cgallery__track .ds-cgallery__item{flex:0 0 auto;width:150px;scroll-snap-align:start}
	@media (max-width:599px){.ds-cgallery__strip{padding:.5rem}.ds-cgallery__track .ds-cgallery__item{width:112px}.ds-cgallery__nav{display:none!important}}
	.ds-cgallery__nav{appearance:none;flex:0 0 auto;display:flex;align-items:center;justify-content:center;width:40px;height:40px;margin:0;padding:0;border:1.5px solid var(--ds-dark,#111);border-radius:50%;background:#fff;color:var(--ds-dark,#111);cursor:pointer;box-shadow:none;min-width:0;line-height:1;transition:background .2s,color .2s,opacity .2s}
	.ds-cgallery__nav svg{width:20px;height:20px;display:block}
	.ds-cgallery__nav:hover:not(:disabled),.ds-cgallery__nav:focus-visible{background:var(--ds-dark,#111);color:var(--ds-yellow,#C4E000);outline:none}
	.ds-cgallery__nav:disabled{opacity:.3;cursor:default}
	.ds-cgallery__head{display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:.25rem 1.5rem;margin:0 0 1.25rem}
	.ds-cgallery__title{margin:0}
	.ds-cgallery__lede{margin:0;opacity:.7}
	.ds-cgallery__grid{list-style:none;margin:0;padding:0;display:grid;gap:.5rem;grid-template-columns:repeat(2,minmax(0,1fr))}
	@media (min-width:600px){.ds-cgallery__grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem}}
	@media (min-width:1000px){.ds-cgallery__grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
	.ds-cgallery__item{margin:0;padding:0;min-width:0}
	.ds-cgallery__item::before{content:none}
	.ds-cgallery__thumb{display:block;position:relative;aspect-ratio:1/1;overflow:hidden;border-radius:8px;background:#ececec}
	.ds-cgallery__thumb img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .35s ease}
	.ds-cgallery__thumb:hover img{transform:scale(1.04)}
	.ds-cgallery__thumb:focus-visible{outline:3px solid var(--ds-yellow,#C4E000);outline-offset:3px}
	.ds-cgallery__product{display:block;margin-top:.4rem;font-size:.85rem;line-height:1.35;font-weight:600;color:var(--ds-dark,#111);text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
	.ds-cgallery__product:hover{text-decoration:underline}
	.ds-cgallery__foot{display:flex;flex-wrap:wrap;align-items:center;gap:1rem 1.5rem;margin-top:1.25rem}
	.ds-cgallery__more{appearance:none;margin:0;padding:.65em 1.4em;border:2px solid var(--ds-dark,#111);border-radius:999px;background:transparent;color:var(--ds-dark,#111);font:inherit;font-weight:700;line-height:1.2;text-transform:none;letter-spacing:normal;cursor:pointer;box-shadow:none;height:auto;min-width:0}
	.ds-cgallery__more:hover{background:var(--ds-dark,#111);color:var(--ds-yellow,#C4E000)}
	.ds-cgallery__all{font-weight:700;color:var(--ds-dark,#111);text-decoration:none;border-bottom:2px solid var(--ds-yellow,#C4E000)}
	.ds-cgallery__filters{display:flex;flex-wrap:wrap;gap:.5rem;margin:0 0 1.5rem}
	.ds-cgallery__chip{appearance:none;margin:0;padding:.45em 1em;border:1.5px solid var(--ds-dark,#111);border-radius:999px;background:#fff;color:var(--ds-dark,#111);font:inherit;font-size:.9rem;line-height:1.2;text-transform:none;letter-spacing:normal;cursor:pointer;box-shadow:none;height:auto;min-width:0}
	.ds-cgallery__chip[aria-pressed="true"]{background:var(--ds-dark,#111);color:var(--ds-yellow,#C4E000)}
	.ds-cgallery__chip small{font-size:.8em;opacity:.7;margin-left:.3em}
	.ds-cgallery__status{margin:0 0 1rem;opacity:.7}
	html.ds-cglb-open{overflow:hidden}
	.ds-cglb{box-sizing:border-box;width:100%;max-width:100%;height:100%;max-height:100%;margin:0;padding:3.5rem 1rem 1rem;border:0;background:transparent;color:#fff;overflow:auto;overscroll-behavior:contain}
	.ds-cglb::backdrop{background:rgba(0,0,0,.9)}
	.ds-cglb__figure{margin:0 auto;max-width:1200px;min-height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center}
	.ds-cglb__img{display:block;width:auto;height:auto;max-width:100%;max-height:calc(100vh - 11rem);border-radius:6px;background:#222}
	.ds-cglb__text{max-width:720px;margin:1rem 0 0;font-size:1rem;line-height:1.5;text-align:center;white-space:pre-line}
	.ds-cglb__meta{display:flex;flex-wrap:wrap;justify-content:center;gap:.4rem 1.25rem;margin:.75rem 0 0;font-size:.9rem;text-align:center}
	.ds-cglb__meta a{color:var(--ds-yellow,#C4E000);text-decoration:none;border-bottom:1px solid currentColor}
	.ds-cglb__count{opacity:.7}
	@media (min-width:600px){.ds-cglb{padding:3.5rem 4.5rem 1.5rem}}
	.ds-cglb__btn{appearance:none;position:fixed;z-index:1;display:flex;align-items:center;justify-content:center;width:44px;height:44px;margin:0;padding:0;border:1px solid rgba(255,255,255,.4);border-radius:50%;background:rgba(0,0,0,.6);color:#fff;cursor:pointer;box-shadow:none;min-width:0}
	.ds-cglb__btn:hover,.ds-cglb__btn:focus-visible{background:var(--ds-yellow,#C4E000);color:#111;outline:none}
	.ds-cglb__btn svg{width:22px;height:22px;display:block}
	.ds-cglb__close{top:.75rem;right:.75rem}
	.ds-cglb__prev{left:.75rem;top:50%;transform:translateY(-50%)}
	.ds-cglb__next{right:.75rem;top:50%;transform:translateY(-50%)}
	@media (max-width:599px){.ds-cglb__prev,.ds-cglb__next{top:auto;bottom:.75rem;transform:none}.ds-cglb{padding-bottom:4.5rem}}
	</style>
	<?php
}

// ─── Admin: Products → Customer Gallery ──────────────────────────────────────

add_action( 'admin_menu', 'dirtshack_gallery_admin_menu' );
function dirtshack_gallery_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=product',
		__( 'Customer Gallery', 'ohio-child' ),
		__( 'Customer Gallery', 'ohio-child' ),
		'manage_woocommerce',
		DIRTSHACK_GALLERY_ADMIN_PAGE,
		'dirtshack_gallery_admin_page'
	);
}

function dirtshack_gallery_admin_url() {
	return admin_url( 'edit.php?post_type=product&page=' . DIRTSHACK_GALLERY_ADMIN_PAGE );
}

/**
 * Fetch one of the gallery's JSON files, skipping any cached copy.
 *
 * @param string $path e.g. "status.json".
 * @return array|null|WP_Error Decoded JSON, null if the file doesn't exist yet.
 */
function dirtshack_gallery_fetch_json( $path ) {
	$response = wp_remote_get(
		add_query_arg( 't', time(), dirtshack_gallery_url() . '/' . $path ),
		array( 'timeout' => 8, 'headers' => array( 'Accept' => 'application/json' ) )
	);
	if ( is_wp_error( $response ) ) {
		return $response;
	}
	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( 404 === $code ) {
		return null;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( 200 !== $code || ! is_array( $data ) ) {
		/* translators: %d: HTTP status code */
		return new WP_Error( 'ds_gallery_fetch', sprintf( __( 'unexpected reply (HTTP %d)', 'ohio-child' ), $code ) );
	}
	return $data;
}

/** "6 Nov 2026, 3:17 pm (12 minutes ago)" for an ISO time, or "never". */
function dirtshack_gallery_when( $iso ) {
	$ts = $iso ? strtotime( (string) $iso ) : false;
	if ( ! $ts ) {
		return __( 'never', 'ohio-child' );
	}
	/* translators: 1: date and time, 2: e.g. "12 minutes" */
	return sprintf( __( '%1$s (%2$s ago)', 'ohio-child' ), wp_date( 'j M Y, g:i a', $ts ), human_time_diff( $ts ) );
}

add_action( 'admin_post_ds_gallery_sync', 'dirtshack_gallery_sync_now' );
function dirtshack_gallery_sync_now() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'ohio-child' ), 403 );
	}
	check_admin_referer( 'ds_gallery_sync' );

	$notice = array( 'ok' => false, 'message' => __( '"Sync now" is not set up.', 'ohio-child' ) );
	if ( dirtshack_gallery_can_sync() ) {
		$recheck  = ! empty( $_POST['recheck'] ); // phpcs:ignore WordPress.Security.NonceVerification -- checked above.
		$response = wp_remote_post(
			$recheck ? add_query_arg( 'recheck', '1', DIRTSHACK_GALLERY_SYNC_URL ) : DIRTSHACK_GALLERY_SYNC_URL,
			array(
				'timeout' => 25,
				'headers' => array( 'Authorization' => 'Bearer ' . DIRTSHACK_GALLERY_SYNC_SECRET ),
			)
		);
		if ( is_wp_error( $response ) ) {
			$notice['message'] = sprintf( __( 'Could not reach the sync Worker: %s', 'ohio-child' ), $response->get_error_message() );
		} else {
			$code   = (int) wp_remote_retrieve_response_code( $response );
			$status = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( 200 === $code && is_array( $status ) && ! empty( $status['ok'] ) ) {
				$counts = isset( $status['counts'] ) && is_array( $status['counts'] ) ? $status['counts'] : array();
				$notice = array(
					'ok'      => true,
					/* translators: 1: photos on the site, 2: photos without a product */
					'message' => sprintf(
						__( 'Synced. %1$d photos on the site, %2$d not linked to a product.', 'ohio-child' ),
						(int) ( $counts['published'] ?? 0 ),
						(int) ( $counts['unmatched'] ?? 0 )
					),
				);
			} elseif ( is_array( $status ) && ! empty( $status['error'] ) ) {
				$notice['message'] = sprintf( __( 'Sync finished with an error: %s', 'ohio-child' ), (string) $status['error'] );
			} else {
				$notice['message'] = sprintf( __( 'The sync Worker answered HTTP %d.', 'ohio-child' ), $code );
			}
		}
	}
	set_transient( 'ds_gallery_notice_' . get_current_user_id(), $notice, 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( dirtshack_gallery_admin_url() );
	exit;
}

function dirtshack_gallery_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	echo '<div class="wrap ds-cg-admin"><h1>' . esc_html__( 'Customer Gallery', 'ohio-child' ) . '</h1>';

	$notice_key = 'ds_gallery_notice_' . get_current_user_id();
	$notice     = get_transient( $notice_key );
	if ( is_array( $notice ) ) {
		delete_transient( $notice_key );
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			$notice['ok'] ? 'success' : 'error',
			esc_html( $notice['message'] )
		);
	}

	echo '<p class="ds-cg-lede">' . esc_html__( 'Post a photo in the "Customer Gallery" album on the DirtShack Facebook Page with the product\'s slug in the caption, for example gear-shaft-saver-klx230. Within the hour it shows on that product\'s page. To take a photo down, delete it from the album or remove the slug from its caption.', 'ohio-child' ) . '</p>';

	if ( '' === dirtshack_gallery_url() ) {
		echo '<div class="notice notice-warning inline"><p><strong>' . esc_html__( 'The Customer Gallery is off.', 'ohio-child' ) . '</strong> ' . esc_html__( 'Set up the Worker first (workers/customer-gallery/README.md in the repo), then add this to wp-config.php:', 'ohio-child' ) . '</p>';
		echo "<pre class=\"ds-cg-code\">define( 'DIRTSHACK_GALLERY_URL', 'https://gallery.dirtshack.in' );\n\n// Optional, for \"Sync now\":\ndefine( 'DIRTSHACK_GALLERY_SYNC_URL', 'https://dirtshack-customer-gallery.&lt;subdomain&gt;.workers.dev/sync' );\ndefine( 'DIRTSHACK_GALLERY_SYNC_SECRET', '&lt;the Worker's SYNC_SECRET&gt;' );</pre></div>";
		dirtshack_gallery_admin_slugs( null );
		dirtshack_gallery_admin_css();
		echo '</div>';
		return;
	}

	$status = dirtshack_gallery_fetch_json( 'status.json' );
	$all    = dirtshack_gallery_fetch_json( 'all.json' );

	echo '<h2>' . esc_html__( 'Last sync', 'ohio-child' ) . '</h2>';
	if ( is_wp_error( $status ) ) {
		/* translators: 1: address, 2: error */
		echo '<div class="notice notice-error inline"><p>' . esc_html( sprintf( __( 'Could not read %1$s: %2$s', 'ohio-child' ), dirtshack_gallery_url() . '/status.json', $status->get_error_message() ) ) . '</p></div>';
	} elseif ( null === $status ) {
		echo '<p>' . esc_html__( 'No sync has run yet. The Worker runs every hour, or use "Sync now".', 'ohio-child' ) . '</p>';
	} else {
		$counts = isset( $status['counts'] ) && is_array( $status['counts'] ) ? $status['counts'] : array();
		echo '<table class="widefat striped ds-cg-facts"><tbody>';
		printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html__( 'Last run', 'ohio-child' ), esc_html( dirtshack_gallery_when( $status['lastRun'] ?? '' ) ) );
		printf(
			'<tr><th>%s</th><td>%s</td></tr>',
			esc_html__( 'Result', 'ohio-child' ),
			! empty( $status['ok'] )
				? '<span class="ds-cg-ok">' . esc_html__( 'OK', 'ohio-child' ) . '</span>'
				: '<span class="ds-cg-bad">' . esc_html( (string) ( $status['error'] ?? __( 'Failed', 'ohio-child' ) ) ) . '</span>'
		);
		if ( empty( $status['ok'] ) ) {
			printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html__( 'Last successful run', 'ohio-child' ), esc_html( dirtshack_gallery_when( $status['lastSuccess'] ?? '' ) ) );
		}
		printf( '<tr><th>%s</th><td>%d</td></tr>', esc_html__( 'Photos on the site', 'ohio-child' ), (int) ( $counts['published'] ?? 0 ) );
		printf( '<tr><th>%s</th><td>%d</td></tr>', esc_html__( 'Not linked to a product', 'ohio-child' ), (int) ( $counts['unmatched'] ?? 0 ) );
		printf( '<tr><th>%s</th><td>%d</td></tr>', esc_html__( 'Waiting to be copied', 'ohio-child' ), (int) ( $counts['waiting'] ?? 0 ) );
		echo '</tbody></table>';

		if ( ! empty( $status['warnings'] ) && is_array( $status['warnings'] ) ) {
			echo '<ul class="ds-cg-warnings">';
			foreach ( $status['warnings'] as $warning ) {
				echo '<li>' . esc_html( (string) $warning ) . '</li>';
			}
			echo '</ul>';
		}
	}

	if ( dirtshack_gallery_can_sync() ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="ds-cg-sync">';
		echo '<input type="hidden" name="action" value="ds_gallery_sync">';
		wp_nonce_field( 'ds_gallery_sync' );
		submit_button( __( 'Sync now', 'ohio-child' ), 'primary', 'submit', false );
		echo ' <label><input type="checkbox" name="recheck" value="1"> ' . esc_html__( 'Also recheck every photo against the shop', 'ohio-child' ) . '</label>';
		echo '</form>';
	} else {
		echo '<p class="description">' . esc_html__( 'Add DIRTSHACK_GALLERY_SYNC_URL and DIRTSHACK_GALLERY_SYNC_SECRET to wp-config.php to get a "Sync now" button here.', 'ohio-child' ) . '</p>';
	}

	if ( is_array( $status ) && ! empty( $status['unmatched'] ) && is_array( $status['unmatched'] ) ) {
		echo '<h2>' . esc_html__( 'Not linked to a product', 'ohio-child' ) . '</h2>';
		echo '<p>' . esc_html__( 'These album photos have no product slug in their caption (or a misspelt one), so they are not on the site. Edit the caption on Facebook; the next sync picks it up.', 'ohio-child' ) . '</p>';
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Caption', 'ohio-child' ) . '</th><th>' . esc_html__( 'Posted', 'ohio-child' ) . '</th><th></th></tr></thead><tbody>';
		foreach ( $status['unmatched'] as $photo ) {
			$caption = trim( (string) ( $photo['caption'] ?? '' ) );
			$fb      = (string) ( $photo['fb'] ?? '' );
			$ts      = ! empty( $photo['date'] ) ? strtotime( (string) $photo['date'] ) : false;
			printf(
				'<tr><td>%1$s</td><td>%2$s</td><td>%3$s</td></tr>',
				'' === $caption ? '<em>' . esc_html__( '(no caption)', 'ohio-child' ) . '</em>' : esc_html( wp_trim_words( $caption, 30 ) ),
				$ts ? esc_html( wp_date( 'j M Y', $ts ) ) : '',
				dirtshack_gallery_is_facebook_url( $fb ) ? '<a href="' . esc_url( $fb ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open on Facebook', 'ohio-child' ) . '</a>' : ''
			);
		}
		echo '</tbody></table>';
	}

	if ( is_array( $status ) && ! empty( $status['waiting'] ) && is_array( $status['waiting'] ) ) {
		echo '<h2>' . esc_html__( 'Waiting to be copied', 'ohio-child' ) . '</h2>';
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Photo', 'ohio-child' ) . '</th><th>' . esc_html__( 'Tries', 'ohio-child' ) . '</th><th>' . esc_html__( 'Last error', 'ohio-child' ) . '</th></tr></thead><tbody>';
		foreach ( $status['waiting'] as $photo ) {
			$fb = (string) ( $photo['fb'] ?? '' );
			printf(
				'<tr><td>%1$s</td><td>%2$d</td><td>%3$s</td></tr>',
				dirtshack_gallery_is_facebook_url( $fb ) ? '<a href="' . esc_url( $fb ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open on Facebook', 'ohio-child' ) . '</a>' : esc_html( (string) ( $photo['id'] ?? '' ) ),
				(int) ( $photo['failures'] ?? 0 ),
				esc_html( (string) ( $photo['error'] ?? '' ) )
			);
		}
		echo '</tbody></table>';
	}

	dirtshack_gallery_admin_slugs( is_array( $all ) ? $all : null );

	$page = dirtshack_gallery_page_url();
	echo '<h2>' . esc_html__( 'Gallery page', 'ohio-child' ) . '</h2><p>';
	if ( $page ) {
		/* translators: %s: page address */
		echo wp_kses( sprintf( __( 'All photos are shown at <a href="%s">%s</a>, and product pages link there.', 'ohio-child' ), esc_url( $page ), esc_html( $page ) ), array( 'a' => array( 'href' => true ) ) );
	} else {
		echo esc_html__( 'To show every photo on one page, create a page with the slug "customer-gallery" and put [customer_gallery] in it. Product pages then link to it.', 'ohio-child' );
	}
	echo '</p>';

	dirtshack_gallery_admin_css();
	echo '</div>';
}

/**
 * The slug to type in captions for each published product, with its photo count.
 *
 * @param array|null $all Decoded all.json, if it could be read.
 */
function dirtshack_gallery_admin_slugs( $all ) {
	$counts = array();
	if ( $all && isset( $all['photos'] ) && is_array( $all['photos'] ) ) {
		foreach ( $all['photos'] as $photo ) {
			foreach ( (array) ( $photo['products'] ?? array() ) as $slug ) {
				$counts[ $slug ] = ( $counts[ $slug ] ?? 0 ) + 1;
			}
		}
	}
	$products = wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => -1,
			'orderby' => 'title',
			'order'   => 'ASC',
		)
	);

	echo '<h2>' . esc_html__( 'Product slugs for captions', 'ohio-child' ) . '</h2>';
	echo '<table class="widefat striped ds-cg-slugs"><thead><tr><th>' . esc_html__( 'Product', 'ohio-child' ) . '</th><th>' . esc_html__( 'Slug to put in the caption', 'ohio-child' ) . '</th><th>' . esc_html__( 'Photos', 'ohio-child' ) . '</th></tr></thead><tbody>';
	foreach ( $products as $product ) {
		$slug = $product->get_slug();
		printf(
			'<tr><td><a href="%1$s">%2$s</a></td><td><code>%3$s</code> <button type="button" class="button button-small ds-cg-copy" data-copy="%3$s">%4$s</button></td><td>%5$s</td></tr>',
			esc_url( (string) get_edit_post_link( $product->get_id() ) ),
			esc_html( $product->get_name() ),
			esc_attr( $slug ),
			esc_html__( 'Copy', 'ohio-child' ),
			null === $all ? '–' : (int) ( $counts[ $slug ] ?? 0 )
		);
	}
	echo '</tbody></table>';
	?>
	<script>
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '.ds-cg-copy' );
		if ( ! btn || ! navigator.clipboard ) {
			return;
		}
		navigator.clipboard.writeText( btn.getAttribute( 'data-copy' ) ).then( function () {
			var label = btn.textContent;
			btn.textContent = <?php echo wp_json_encode( __( 'Copied', 'ohio-child' ) ); ?>;
			setTimeout( function () { btn.textContent = label; }, 1500 );
		} );
	} );
	</script>
	<?php
}

function dirtshack_gallery_is_facebook_url( $url ) {
	return (bool) preg_match( '#^https://(www\.|m\.)?facebook\.com/#', (string) $url );
}

function dirtshack_gallery_admin_css() {
	?>
	<style>
	.ds-cg-admin .ds-cg-lede{max-width:760px;font-size:14px}
	.ds-cg-admin .ds-cg-facts{max-width:760px}
	.ds-cg-admin .ds-cg-facts th{width:220px;font-weight:600}
	.ds-cg-admin .ds-cg-ok{color:#008a20;font-weight:600}
	.ds-cg-admin .ds-cg-bad{color:#b32d2e;font-weight:600}
	.ds-cg-admin .ds-cg-warnings{list-style:disc;margin:1em 0 0 1.5em}
	.ds-cg-admin .ds-cg-sync{margin:1.25em 0}
	.ds-cg-admin .ds-cg-sync label{margin-left:.75em}
	.ds-cg-admin .ds-cg-code{background:#f6f7f7;padding:12px;overflow:auto;max-width:900px}
	.ds-cg-admin .ds-cg-slugs{max-width:900px}
	.ds-cg-admin .ds-cg-slugs code{font-size:13px}
	.ds-cg-admin h2{margin-top:2em}
	</style>
	<?php
}
