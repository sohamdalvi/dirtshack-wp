<?php
/**
 * Ohio Child Theme — functions.php
 *
 * WordPress loads this file before the parent theme's functions.php,
 * so all parent hooks/filters still fire. We only add what is specific
 * to DirtShack customisations here.
 */

// ─── Enqueue parent + child stylesheets ──────────────────────────────────────
//
// Ohio's enqueue.php calls:
//   wp_enqueue_style( 'ohio-style', get_stylesheet_uri() )
// When this child theme is active, get_stylesheet_uri() returns the CHILD's
// style.css, so the parent's main CSS would never load on its own.
// We enqueue it here at priority 5 — before Ohio's default wp_enqueue_scripts
// fires — so the parent CSS always prints first, followed by the child's CSS.
//
add_action( 'wp_enqueue_scripts', 'ohio_child_enqueue_styles', 5 );
function ohio_child_enqueue_styles() {
    // Load the parent theme's full stylesheet (18 000+ lines of Ohio CSS).
    wp_enqueue_style(
        'ohio-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'ohio' )->get( 'Version' )
    );

    // The child's style.css is already enqueued by Ohio's enqueue.php as
    // 'ohio-style' (because get_stylesheet_uri() now points here).
    // We just need to declare the parent as a dependency so WordPress
    // guarantees the correct load order.
    add_filter( 'style_loader_tag', 'ohio_child_add_parent_dep', 10, 2 );
}

/**
 * Make 'ohio-style' (child CSS) depend on 'ohio-parent-style'.
 * This runs once — only for the ohio-style handle — then removes itself.
 */
function ohio_child_add_parent_dep( $tag, $handle ) {
    if ( $handle === 'ohio-style' ) {
        global $wp_styles;
        if ( isset( $wp_styles->registered['ohio-style'] ) ) {
            $deps = $wp_styles->registered['ohio-style']->deps;
            if ( ! in_array( 'ohio-parent-style', $deps, true ) ) {
                $wp_styles->registered['ohio-style']->deps[] = 'ohio-parent-style';
            }
        }
        remove_filter( 'style_loader_tag', 'ohio_child_add_parent_dep', 10 );
    }
    return $tag;
}

// ─── Enqueue child JS (cache-safe client behaviours) ─────────────────────────
//
// One small, deferred, site-wide script (announcement-bar dismissal). The
// version string is the file's modification time so a CDN/browser cache busts
// cleanly the moment the file changes — and never on a per-request basis.
add_action( 'wp_enqueue_scripts', 'dirtshack_enqueue_scripts', 20 );
function dirtshack_enqueue_scripts() {
    $rel  = 'assets/js/ds.js';
    $path = get_stylesheet_directory() . '/' . $rel;
    if ( ! file_exists( $path ) ) {
        return;
    }
    wp_enqueue_script(
        'dirtshack-js',
        get_stylesheet_directory_uri() . '/' . $rel,
        array(),
        filemtime( $path ),
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}

// ─── Custom functions, hooks and filters below ───────────────────────────────
// Add all DirtShack-specific PHP customisations here instead of editing the
// parent theme. This file is safe from Ohio theme updates.

// ─── Redirect legacy /shop/ → /woo-shop/ (the real WooCommerce shop) ──────────
//
// The original "Shop" page (slug "shop", ID 10) is an empty leftover. The active
// WooCommerce shop page is "woo-shop" (woocommerce_shop_page_id), which renders
// the product catalog. Some nav-menu items and old links still point at /shop/,
// landing visitors on a blank page. 301-redirect any hit on the old slug to the
// real shop so every entry point resolves correctly.
add_action( 'template_redirect', 'dirtshack_redirect_legacy_shop' );
function dirtshack_redirect_legacy_shop() {
    if ( is_page( 'shop' ) ) {
        wp_safe_redirect( home_url( '/woo-shop/' ), 301 );
        exit;
    }
}

// ─── Hero banners (Home, Shop, Product, Cart, My Account, Braap) ──────────────
//
// The custom homepage opens with a full-bleed hero image; we give the other
// top-level WooCommerce / blog landings the same treatment so the whole site
// shares one visual language.
//
// Each page loads its own image by a predictable filename from
//   ohio-child/assets/heroes/<pagename>.jpg
// (home.jpg, shop.jpg, product.jpg, cart.jpg, myaccount.jpg, braap.jpg) so the
// images are easy to find and swap. If a named file is missing we fall back to
// the original homepage image so nothing ever renders broken.

/**
 * Resolve a hero image URL by its short name (e.g. 'shop' → shop.jpg).
 * Falls back to the legacy homepage image when the named file isn't present yet.
 */
function dirtshack_hero_image_url( $name ) {
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    // Prefer WebP (much smaller), fall back to JPG/PNG named file.
    foreach ( array( 'webp', 'jpg' ) as $ext ) {
        $rel = 'assets/heroes/' . $name . '.' . $ext;
        if ( file_exists( $dir . '/' . $rel ) ) {
            return $uri . '/' . $rel;
        }
    }
    return content_url( 'uploads/2026/hero.jpg' ); // safety fallback
}

/**
 * Hero settings for the current request, or false when no hero applies.
 * Image + tagline are chosen per page; tagline may contain a `.ds-accent` span.
 */
function dirtshack_current_hero() {
    // Single product view — check before is_shop (a product is not the shop).
    if ( function_exists( 'is_product' ) && is_product() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'product' ),
            'tagline' => 'Built to <span class="ds-accent">Ride</span> Hard',
        );
    }
    if ( function_exists( 'is_shop' ) && is_shop() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'shop' ),
            'tagline' => 'Gear & Parts Built for the <span class="ds-accent">Trail</span>',
        );
    }
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'cart' ),
            'tagline' => 'Your <span class="ds-accent">Cart</span>',
        );
    }
    // Checkout — a standard WooCommerce page (renders page_headline like cart),
    // so the get_template_part hero hook fires here too. Checked before
    // is_account_page since order-received lives under the checkout endpoint.
    if ( function_exists( 'is_checkout' ) && is_checkout() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'checkout' ),
            'tagline' => 'Secure <span class="ds-accent">Checkout</span>',
        );
    }
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'myaccount' ),
            'tagline' => 'My <span class="ds-accent">Account</span>',
        );
    }
    // The "Braap" page is set as the blog Posts page, so it resolves as the
    // blog index (is_home), not a normal page (is_page).
    if ( is_home() && is_main_query() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'braap' ),
            'tagline' => 'Twist the Throttle. <span class="ds-accent">Braap</span> On.',
        );
    }
    return false;
}

/**
 * Output the hero markup. Full-bleed is handled in CSS so it breaks out of
 * whatever container Ohio wraps the content in.
 */
function dirtshack_render_hero( $tagline_html, $image_url ) {
    ?>
    <section class="ds-hero ds-hero--inner" style="background-image:url('<?php echo esc_url( $image_url ); ?>')">
        <div class="ds-hero__overlay"></div>
        <div class="ds-hero__content">
            <p class="ds-hero__tagline"><?php echo wp_kses_post( $tagline_html ); ?></p>
        </div>
    </section>
    <?php
}

// Inject the hero CSS into <head> at priority 99 — after Ohio's stylesheet — so
// it cannot be overridden (same technique as front-page.php). Only prints on the
// pages that actually show a hero.
add_action( 'wp_head', 'dirtshack_inner_hero_css', 99 );
function dirtshack_inner_hero_css() {
    if ( ! dirtshack_current_hero() ) {
        return;
    }
    ?>
<style id="ds-inner-hero-css">
/* Hide Ohio's default page-title bar + breadcrumbs so the hero is the only banner */
.page-headline,
.breadcrumb-holder,
.subheader-holder { display: none !important; }
/* Pull the content flush under the header (Ohio adds a top offset otherwise) */
.page-container.top-offset { padding-top: 0 !important; }

/* ── Hero (mirrors the homepage hero) ── */
.ds-hero--inner {
    position: relative !important;
    display: flex !important;
    align-items: flex-end !important;
    min-height: 48vh !important;
    background-size: cover !important;
    background-position: center 35% !important;
    background-color: #111 !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
    /* full-bleed: break out of any constrained container to viewport width */
    width: 100vw !important;
    max-width: 100vw !important;
    margin-left: 50% !important;
    transform: translateX(-50%) !important;
    float: none !important;
    clear: both !important;
}
.ds-hero--inner .ds-hero__overlay {
    position: absolute !important;
    top: 0 !important; right: 0 !important;
    bottom: 0 !important; left: 0 !important;
    background: linear-gradient(to left, rgba(0,0,0,.75) 0%, rgba(0,0,0,.15) 100%) !important;
    pointer-events: none !important;
}
.ds-hero--inner .ds-hero__content {
    position: relative !important;
    z-index: 2 !important;
    width: 100% !important;
    max-width: 1280px !important;
    margin: 0 auto !important;
    padding: 3rem 5% 3.5rem !important;
    text-align: right !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-end !important;
}
.ds-hero--inner .ds-hero__tagline {
    margin: 0 !important;
    padding: 0 !important;
    font-size: clamp(1rem, 2.2vw, 1.4rem) !important;
    font-weight: 700 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    color: #fff !important;
    text-shadow: 0 1px 8px rgba(0,0,0,.6) !important;
    line-height: 1.4 !important;
    background: none !important;
}
.ds-hero--inner .ds-accent { color: #c8e600 !important; }

/* Breathing room between the hero and the content below it (Braap + Shop pages) */
.ds-hero--inner { margin-bottom: 3rem !important; }

@media (max-width: 600px) {
    .ds-hero--inner { min-height: 38vh !important; margin-bottom: 1.75rem !important; }
}
</style>
    <?php
}

// ─── Fix tiled header background images ───────────────────────────────────────
//
// Ohio's dynamic CSS renders the post/page header background (.page-headline
// .bg-image) with `background-size:auto` and `background-repeat:repeat`, which
// tiles featured images across the header banner instead of filling it (most
// visible on single blog posts whose featured image is a YouTube thumbnail).
// Force cover + no-repeat. Ohio's rule isn't !important, so an !important rule at
// equal specificity wins regardless of source order; we still print it late
// (priority 9999) to sit after the theme's dynamic CSS.
//
// On individual blog posts we also swap the header background to the shared Blog
// hero image (assets/heroes/blog.jpg) so every article gets one consistent
// branded banner instead of its own featured image, while Ohio's headline still
// renders the post title + meta on top.
add_action( 'wp_head', 'dirtshack_fix_headline_bg_css', 9999 );
function dirtshack_fix_headline_bg_css() {
    ?>
<style id="ds-headline-bg-fix">
.page-headline .bg-image {
    background-size: cover !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
}
<?php if ( is_singular( 'post' ) ) : ?>
.page-headline .bg-image {
    background-image: url('<?php echo esc_url( dirtshack_hero_image_url( 'blog' ) ); ?>') !important;
}
<?php endif; ?>
</style>
    <?php
}

/**
 * Emit the hero once per request. Shared by the injection hooks below; the
 * static guard prevents a double-render if more than one hook fires.
 */
function dirtshack_emit_hero_once() {
    static $done = false;
    if ( $done ) {
        return;
    }
    $hero = dirtshack_current_hero();
    if ( $hero ) {
        $done = true;
        dirtshack_render_hero( $hero['tagline'], $hero['image'] );
    }
}

// Shop, Cart, My Account and Braap (blog) all render Ohio's `page_headline`
// partial right after the header. We hook that get_template_part call and emit
// the hero just before the (now hidden) headline — i.e. at the top of #content.
add_action( 'get_template_part_parts/elements/page_headline', 'dirtshack_emit_hero_once' );

// Single product view does NOT use page_headline, and Ohio's product layout
// exposes no PHP action above its two-column grid:
//   - `woocommerce_before_single_product` fires *inside* the right summary
//     column (ohio/woocommerce/single-product/views/type_1.php), so a full-bleed
//     hero rendered there overlaps the gallery + description.
//   - breadcrumbs / sticky-product load via wc_get_template_part(), which does
//     not fire the WP `get_template_part_*` action, so they can't be hooked.
// Instead we emit the hero from a thin child-theme override of WooCommerce's
// content-single-product.php (see ohio-child/woocommerce/content-single-product.php),
// which places it above <div class="product"> — the top of the product content.

// ─── Cross-promo strip: Store → Marketplace ──────────────────────────────────
//
// A slim, dismissible bar above the header introducing Market.DirtShack.in.
// Rendered at wp_body_open (top of <body>, above Ohio's header) and its CSS is
// injected at wp_head priority 99 — same technique as the hero — so Ohio cannot
// override it. Dismissal is remembered client-side via localStorage (no cookie
// round-trip, cache-safe on Bluehost). Never shown during cart/checkout so it
// can't interrupt an active purchase flow.

if ( ! defined( 'DIRTSHACK_MARKETPLACE_URL' ) ) {
    define( 'DIRTSHACK_MARKETPLACE_URL', 'https://market.dirtshack.in/' );
}

/**
 * True on pages where the strip must never appear (cart / checkout).
 */
function dirtshack_xpromo_suppressed() {
    return ( function_exists( 'is_cart' ) && is_cart() )
        || ( function_exists( 'is_checkout' ) && is_checkout() );
}

add_action( 'wp_body_open', 'dirtshack_cross_promo_strip' );
function dirtshack_cross_promo_strip() {
    if ( dirtshack_xpromo_suppressed() ) {
        return;
    }
    $url = add_query_arg(
        array(
            'utm_source'   => 'dirtshack_store',
            'utm_medium'   => 'topbar',
            'utm_campaign' => 'cross_promo',
        ),
        DIRTSHACK_MARKETPLACE_URL
    );
    ?>
    <div class="ds-xpromo" id="ds-xpromo" role="region" aria-label="DirtShack Marketplace">
        <div class="ds-xpromo__inner">
            <span class="ds-xpromo__icon" aria-hidden="true">&#127949;</span>
            <span class="ds-xpromo__text">
                <strong class="ds-xpromo__head">Buy. Sell. Ride.</strong>
                <span class="ds-xpromo__sub">Used bikes &amp; parts on the DirtShack Marketplace &mdash; list yours free.</span>
            </span>
            <a class="ds-xpromo__cta" href="<?php echo esc_url( $url ); ?>">
                Browse the Marketplace&nbsp;&rarr;
            </a>
            <button type="button" class="ds-xpromo__close" aria-label="<?php esc_attr_e( 'Dismiss', 'ohio-child' ); ?>">&times;</button>
        </div>
    </div>
    <?php
}

// No-flash dismissal read. The page HTML is page-cached and always *includes*
// the bar, so we can't render it hidden server-side per visitor. This tiny
// synchronous <head> script runs before first paint, reads localStorage and sets
// `ds-xpromo-off` on <html> so the CSS below hides the bar with zero layout shift
// (no FOUC, no CLS). It is byte-identical for every visitor, so it stays fully
// cacheable. The click-to-dismiss handler lives in assets/js/ds.js.
add_action( 'wp_head', 'dirtshack_xpromo_nofouc_script', 1 );
function dirtshack_xpromo_nofouc_script() {
    if ( dirtshack_xpromo_suppressed() ) {
        return;
    }
    ?>
<script>try{if(localStorage.getItem('dsXpromoDismissed')==='1'){document.documentElement.classList.add('ds-xpromo-off');}}catch(e){}</script>
    <?php
}

// Strip CSS — injected after Ohio's stylesheet so it always wins.
add_action( 'wp_head', 'dirtshack_cross_promo_css', 99 );
function dirtshack_cross_promo_css() {
    if ( dirtshack_xpromo_suppressed() ) {
        return;
    }
    ?>
<style id="ds-xpromo-css">
/* Dismissed (class set by the no-FOUC head script / ds.js) */
html.ds-xpromo-off .ds-xpromo { display: none !important; }

.ds-xpromo {
    background: var(--ds-dark, #111) !important;
    color: #fff !important;
    font-size: .82rem !important;
    line-height: 1.3 !important;
    border-bottom: 2px solid var(--ds-yellow, #C4E000) !important;
}
.ds-xpromo__inner {
    max-width: var(--ds-max, 1280px) !important;
    margin: 0 auto !important;
    padding: .55rem var(--ds-pad, 1.5rem) !important;
    display: flex !important;
    align-items: center !important;
    gap: .9rem !important;
}
.ds-xpromo__icon { font-size: 1.15rem !important; flex: 0 0 auto !important; }
.ds-xpromo__text {
    display: flex !important;
    align-items: baseline !important;
    gap: .6rem !important;
    flex: 1 1 auto !important;
    min-width: 0 !important;
}
.ds-xpromo__head {
    color: var(--ds-yellow, #c8e600) !important;
    font-weight: 800 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}
.ds-xpromo__sub {
    color: #e8e8e8 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}
.ds-xpromo__cta {
    flex: 0 0 auto !important;
    background: var(--ds-yellow, #c8e600) !important;
    color: var(--ds-dark, #111) !important;
    font-weight: 800 !important;
    letter-spacing: .05em !important;
    text-transform: uppercase !important;
    text-decoration: none !important;
    padding: .5rem 1.1rem !important;
    border-radius: var(--ds-radius, 3px) !important;
    font-size: .76rem !important;
    white-space: nowrap !important;
    transition: opacity .18s !important;
}
.ds-xpromo__cta:hover { opacity: .82 !important; }
.ds-xpromo__close {
    flex: 0 0 auto !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    background: transparent !important;
    border: 0 !important;
    color: #bbb !important;
    font-size: 1.25rem !important;
    line-height: 1 !important;
    padding: .15rem .35rem !important;
    margin: 0 !important;
    cursor: pointer !important;
    transition: color .18s !important;
}
.ds-xpromo__close:hover { color: #fff !important; }

/* ── Mobile: stack copy, keep it one tidy block ── */
@media (max-width: 768px) {
    .ds-xpromo__inner { flex-wrap: wrap !important; gap: .5rem .7rem !important; padding: .55rem 1rem !important; position: relative !important; padding-right: 2.2rem !important; }
    .ds-xpromo__text { flex-direction: column !important; align-items: flex-start !important; gap: .1rem !important; flex: 1 1 60% !important; }
    .ds-xpromo__sub { white-space: normal !important; font-size: .74rem !important; }
    .ds-xpromo__cta { flex: 1 1 100% !important; text-align: center !important; padding: .6rem 1rem !important; }
    .ds-xpromo__close { position: absolute !important; top: .35rem !important; right: .5rem !important; }
}
</style>
    <?php
}

