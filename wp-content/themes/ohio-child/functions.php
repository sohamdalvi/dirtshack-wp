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

    wp_enqueue_style(
        'dirtshack-font-archivo-black',
        'https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap',
        array(),
        null
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

    // Cache-bust the child style.css. Ohio enqueues it (handle 'ohio-style') with
    // a STATIC version (theme version 1.0.0), so Bluehost/CDN serve a stale copy
    // after edits. Re-stamp its version with the file's mtime so each deploy busts
    // the cache cleanly. (This runs after Ohio's enqueue at priority 10.)
    $css = get_stylesheet_directory() . '/style.css';
    if ( file_exists( $css ) && isset( wp_styles()->registered['ohio-style'] ) ) {
        wp_styles()->registered['ohio-style']->ver = (string) filemtime( $css );
    }
}

// ─── DirtShack ecosystem switcher (shared portal widget) ─────────────────────
//
// A self-contained, dependency-free portal switcher hosted centrally at
// dirtshack.in. It renders into a Shadow DOM (brings its own styles + fonts —
// we add NO CSS and it can't conflict with Ohio) and auto-detects the active
// portal by hostname (shop.dirtshack.in → highlights "Shop"). It must load from
// the hosted URL (not vendored) so new portals propagate here automatically.
//
//   1) Script — enqueued site-wide, deferred, no version query (let the host's
//      cache headers control freshness). WP 7.0 supports the array strategy arg.
//   2) Mount — a `[data-ecosystem-switcher]` div placed just left of the logo.
//      Ohio renders the logo via get_template_part('parts/elements/menu_logo')
//      from every header style, so hooking that template-part action drops the
//      mount immediately before the branding for ANY header style without
//      copying/patching an Ohio file (survives theme updates). Same technique as
//      dirtshack_emit_hero_once() on the page_headline part below.
add_action( 'wp_enqueue_scripts', 'dirtshack_ecosystem_switcher_script', 20 );
function dirtshack_ecosystem_switcher_script() {
    wp_enqueue_script(
        'dirtshack-ecosystem',
        'https://dirtshack.in/ecosystem/switcher.js',
        array(),
        null, // no version query — hosted cache headers control freshness
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}

add_action( 'get_template_part_parts/elements/menu_logo', 'dirtshack_ecosystem_switcher_mount' );
function dirtshack_ecosystem_switcher_mount() {
    static $done = false;
    if ( $done ) {
        return; // one mount per page, even if the logo part is loaded twice
    }
    $done = true;
    echo '<div data-ecosystem-switcher></div>';
}

// ─── Performance: trim render-blocking + per-request bloat ───────────────────
//
// A low-traffic WooCommerce store still pays for assets on every page view.
// Safe reductions (verified against live dirtshack.in, 2026-06-08):
//   - wp-emoji — legacy emoji detection script/style, unused.
//   - Unused Ohio "Linea" icon-font families — each is a render-blocking CSS
//     file + a webfont download. Keep basic/ecommerce/ionicons/fontawesome/
//     bootstrap (fontawesome powers the footer social bar); drop the rest.
//     If a glyph goes missing in Ohio's UI, re-add that family below.
//   - reCAPTCHA assets → only on My Account; Instagram assets → only on home.
// (We do NOT touch wc-cart-fragments — Ohio's add-to-cart depends on it; see note below.)
add_action( 'wp_enqueue_scripts', 'dirtshack_perf_dequeue', 99 );
function dirtshack_perf_dequeue() {
    if ( is_admin() ) {
        return;
    }

    // 3. Drop unused Linea icon-font packs (handles: icon-pack-<name>).
    //    These are <head> styles, so dequeue during wp_enqueue_scripts.
    $unused_icon_packs = array(
        'icon-pack-linea-arrows',
        'icon-pack-linea-music',
        'icon-pack-linea-weather',
        'icon-pack-linea-software',
        'icon-pack-linea-basic-elaboration',
    );
    foreach ( $unused_icon_packs as $handle ) {
        wp_dequeue_style( $handle );
    }

    // 4. reCAPTCHA-Woo: only login + register are protected (rcfwc_login/register = on;
    //    checkout/review/lost-password = off on live), and both forms live on the My Account
    //    page. The plugin enqueues the Google reCAPTCHA api.js + its own JS site-wide, so drop
    //    them everywhere except My Account. (wp-login.php is unaffected — it uses the separate
    //    login_enqueue_scripts hook, so admin-login reCAPTCHA still works.)
    if ( ! ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
        wp_dequeue_script( 'recaptcha' );   // https://www.google.com/recaptcha/api.js (external, render-affecting)
        wp_dequeue_script( 'rcfwc-js' );
    }

    // 5. Instagram feed (Smash Balloon) is rendered only in the homepage "From the Trail"
    //    section, but the plugin enqueues its CSS/JS on every page (incl. cart). Keep it on the
    //    front page only.
    if ( ! is_front_page() ) {
        wp_dequeue_script( 'sbi_scripts' );
        wp_dequeue_style( 'sbi_styles' );
    }
}

// NOTE: We deliberately DO NOT dequeue 'wc-cart-fragments'. Ohio's own
// woocommerce.min.js runs a CUSTOM AJAX add-to-cart (button class
// `data_button_ajax`) that references `wc_cart_fragments_params` — the JS object
// the wc-cart-fragments script localises. Dequeuing cart-fragments makes that
// object undefined, Ohio's handler throws, and the button's loading spinner
// never clears (infinite spinner; add-to-cart appears broken). So cart-fragments
// must load wherever an add-to-cart button can appear, i.e. site-wide. The
// per-page session cost is accepted — the cached HTML is unaffected (fragments
// run client-side after load), and WP Super Cache Expert + Cloudflare still serve
// the static page. (Removed the earlier dequeue on 2026-06-08 after it broke
// add-to-cart on shop/product pages.)

// ─── Cart page enhancements: rebuild qty +/- after re-render + auto-update ────
// Loaded only on the cart page; depends on jQuery. See assets/js/ds-cart.js.
add_action( 'wp_enqueue_scripts', 'dirtshack_enqueue_cart_js', 30 );
function dirtshack_enqueue_cart_js() {
    if ( ! ( function_exists( 'is_cart' ) && is_cart() ) ) {
        return;
    }
    $rel  = 'assets/js/ds-cart.js';
    $path = get_stylesheet_directory() . '/' . $rel;
    if ( ! file_exists( $path ) ) {
        return;
    }
    wp_enqueue_script(
        'dirtshack-cart',
        get_stylesheet_directory_uri() . '/' . $rel,
        array( 'jquery' ),
        (string) filemtime( $path ),
        true
    );
}

// ─── Fix Ohio's broken cart-page AJAX refresh (jQuery 3.7 incompatibility) ───
//
// Ohio's assets/js/woocommerce.min.js (handle 'ohio-woocommerce') refreshes the
// cart table + totals after add-to-cart / quantity change by calling, in effect:
//     $('.shop_table.cart').on('load', URL + ' .shop_table.cart:eq(0) > *', fn)
//     $('.cart_totals').on('load',     URL + ' .cart_totals:eq(0) > *',     fn)
// It MEANT jQuery's AJAX `.load(url, cb)` but wrote `.on('load', selector, cb)`.
// jQuery 3.7 then tries to parse the cart URL as a CSS selector and throws
// "Syntax error, unrecognized expression: https://…/cart/ .shop_table.cart:eq(0) > *",
// aborting the refresh → cart totals stay frozen until a full page reload.
// Fix = a child-theme copy of that file with `.on("load",o+…` → `.load(o+…`
// (2 spots), swapped in for the parent's handle so it survives Ohio updates.
// Ohio registers 'ohio-woocommerce' on wp_footer:10 (prints at :20), so we
// re-point its src at priority 11.
add_action( 'wp_footer', 'dirtshack_fix_ohio_cart_js', 11 );
function dirtshack_fix_ohio_cart_js() {
    if ( is_admin() ) {
        return;
    }
    $rel  = 'assets/js/ohio-woocommerce-jq3fix.js';
    $path = get_stylesheet_directory() . '/' . $rel;
    if ( ! file_exists( $path ) ) {
        return;
    }
    $scripts = wp_scripts();
    if ( isset( $scripts->registered['ohio-woocommerce'] ) ) {
        $scripts->registered['ohio-woocommerce']->src = get_stylesheet_directory_uri() . '/' . $rel;
        $scripts->registered['ohio-woocommerce']->ver = (string) filemtime( $path );
    }
}

// Disable the emoji detection loader site-wide (front end + admin).
add_action( 'init', 'dirtshack_disable_emojis' );
function dirtshack_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', function ( $plugins ) {
        return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
    } );
    add_filter( 'emoji_svg_url', '__return_false' );
}

// ─── Custom functions, hooks and filters below ───────────────────────────────
// Add all DirtShack-specific PHP customisations here instead of editing the
// parent theme. This file is safe from Ohio theme updates.

// "Braap" Videos custom post type (CPT + taxonomies + meta box + facade embeds).
require get_stylesheet_directory() . '/inc/braap-videos.php';

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
    // NOTE: Shop, single Product and My Account intentionally have NO hero
    // (disabled on request). dirtshack_no_banner_css() also hides Ohio's
    // page-headline on those pages so content sits cleanly under the sticky menu.
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
    // Braap Videos — the CPT archive at /braap/ and its taxonomy listings
    // (video type / creator) carry the "Braap" brand hero. (Single video pages
    // get NO hero — the lazy embed is the top of the page; see single-braap_video.php.)
    if ( ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'braap_video' ) )
        || ( function_exists( 'is_tax' ) && is_tax( array( 'braap_type', 'braap_creator' ) ) ) ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'braap' ),
            'tagline' => 'Twist the Throttle. <span class="ds-accent">Braap</span> On.',
        );
    }
    // The blog Posts page (now at /blog/) resolves as the blog index (is_home).
    // It keeps a hero, but the "Braap" brand has moved to the Videos CPT above,
    // so the blog gets its own generic banner.
    if ( is_home() && is_main_query() ) {
        return array(
            'image'   => dirtshack_hero_image_url( 'blog' ),
            'tagline' => 'The DirtShack <span class="ds-accent">Blog</span>',
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

// ─── No-banner pages (Shop, single Product, My Account) ───────────────────────
//
// These pages had a hero before; now they have none (removed from
// dirtshack_current_hero above). Without the hero, Ohio's default page-headline
// (its own title/image banner) would reappear — so hide it here and pull the
// content up flush under the sticky menu, with a little breathing room. Injected
// at wp_head 9999 so it lands after Ohio's inline dynamic CSS.
function dirtshack_is_no_banner_page() {
    return ( function_exists( 'is_shop' ) && is_shop() )
        || ( function_exists( 'is_product' ) && is_product() )
        || ( function_exists( 'is_account_page' ) && is_account_page() );
}
add_action( 'wp_head', 'dirtshack_no_banner_css', 9999 );
function dirtshack_no_banner_css() {
    if ( ! dirtshack_is_no_banner_page() ) {
        return;
    }
    ?>
<style id="ds-no-banner-css">
/* No hero / no Ohio page-headline banner on these pages */
.page-headline,
.subheader-holder,
.breadcrumb-holder { display: none !important; }
.page-container.top-offset { padding-top: 0 !important; }
/* A little breathing room below the sticky menu (header-cap is collapsed) */
.site-content, #content { padding-top: 1.25rem !important; }
/* Remove the social share (Facebook / X / Pinterest) from the product page */
.single-product .share-bar { display: none !important; }

/* Mobile product gallery: make it obvious there are more images. Ohio inits the
   gallery slider with navBtn:true on <=1180px, so the .clb-slider-nav-btn prev/next
   buttons exist in the DOM (when a product has >1 image) — but it hides the
   CONTAINER with display:none on touch. Force the container visible and place the
   two buttons as circular arrows on the left/right edges of the image. */
@media (max-width: 1180px) {
    /* positioning context for the side arrows */
    .single-product .woo-product-image-slider .clb-slider { position: relative !important; }

    /* un-hide the nav container (the key fix — display, not just opacity) */
    .single-product .woo-product-image-slider .clb-slider-nav-btn {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: none !important; /* container ignores taps; buttons re-enable */
    }

    /* prev / next as circular arrows pinned to the sides, centred over the image */
    .single-product .woo-product-image-slider .clb-slider-nav-btn .prev-btn,
    .single-product .woo-product-image-slider .clb-slider-nav-btn .next-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 6 !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 50% !important;
        background: rgba(17,17,17,.55) !important;
        color: #fff !important;
    }
    .single-product .woo-product-image-slider .clb-slider-nav-btn .prev-btn { left: 10px !important; right: auto !important; }
    .single-product .woo-product-image-slider .clb-slider-nav-btn .next-btn { right: 10px !important; left: auto !important; }

    /* white arrow glyphs */
    .single-product .woo-product-image-slider .clb-slider-nav-btn .prev-btn svg,
    .single-product .woo-product-image-slider .clb-slider-nav-btn .next-btn svg { fill: #fff !important; }
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

// Cart, Checkout and the blog index all render Ohio's `page_headline` partial
// right after the header. We hook that get_template_part call and emit the hero
// just before the (now hidden) headline — i.e. at the top of #content. The Braap
// Videos archive / taxonomy pages use the child theme's own templates, which
// call dirtshack_emit_hero_once() directly instead of going through this hook.
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

// Announcement bar removed on request — render hook disabled (function kept in
// case it's wanted back later; re-add this add_action to restore it).
// add_action( 'wp_body_open', 'dirtshack_cross_promo_strip' );
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
// Disabled with the announcement bar (no bar → no no-FOUC read needed).
// add_action( 'wp_head', 'dirtshack_xpromo_nofouc_script', 1 );
function dirtshack_xpromo_nofouc_script() {
    if ( dirtshack_xpromo_suppressed() ) {
        return;
    }
    ?>
<script>try{if(localStorage.getItem('dsXpromoDismissed')==='1'){document.documentElement.classList.add('ds-xpromo-off');}}catch(e){}</script>
    <?php
}

// Strip CSS — injected after Ohio's stylesheet so it always wins.
// Disabled with the announcement bar (no bar → no CSS needed).
// add_action( 'wp_head', 'dirtshack_cross_promo_css', 99 );
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


// ─── Footer: move "Follow Us" social links to the bottom ──────────────────────
//
// Ohio renders the social bar as a side rail in the header. We hide that rail in
// CSS (style.css) and re-render Ohio's own social_bar template part at the very
// bottom of the page. Reusing the template keeps the links sourced from Ohio's
// options (ACF `global_header_menu_social_links`) — nothing hardcoded — and is
// update-safe (no template override). The matching layout/visibility CSS lives in
// style.css under "Footer tweaks".
add_action( 'wp_footer', 'dirtshack_footer_social_bar', 20 );
function dirtshack_footer_social_bar() {
    if ( is_admin() ) {
        return;
    }
    // Brand icons (Font Awesome 'fa-brands', already loaded by Ohio) → the live
    // social URLs. Own markup (not Ohio's social_bar) so it doesn't collide with
    // the header side-rail and so we control the icon look.
    $links = array(
        array( 'url' => 'https://www.facebook.com/profile.php?id=61579063452683', 'icon' => 'fa-facebook-f', 'label' => 'Facebook' ),
        array( 'url' => 'https://www.instagram.com/dirtshack.in/',                'icon' => 'fa-instagram',  'label' => 'Instagram' ),
        array( 'url' => 'https://www.youtube.com/@dirtshackindia',                'icon' => 'fa-youtube',    'label' => 'YouTube' ),
    );
    echo '<div class="ds-footer-social"><div class="ds-footer-social__inner">';
    echo '<span class="ds-footer-social__label">Follow Us</span>';
    echo '<ul class="ds-footer-social__list">';
    foreach ( $links as $l ) {
        printf(
            '<li><a class="ds-footer-social__link" href="%s" target="_blank" rel="noopener" aria-label="%s"><i class="fa-brands %s" aria-hidden="true"></i></a></li>',
            esc_url( $l['url'] ),
            esc_attr( $l['label'] ),
            esc_attr( $l['icon'] )
        );
    }
    echo '</ul></div></div>';
}

// ─── Product gallery: dot indicators on mobile ───────────────────────────────
// Ohio initialises the gallery with dots:false on mobile and destroys the slider
// entirely on desktop — so destroy+reinit breaks the desktop layout. Instead we
// inject our own dot strip that mirrors Ohio's SVG dot style and listens to
// clb-slider.changed, which Ohio fires on every swipe. Desktop is unaffected.
add_action( 'wp_footer', 'dirtshack_gallery_dots', 25 );
function dirtshack_gallery_dots() {
    if ( ! is_product() ) {
        return;
    }
    ?>
<style id="ds-gallery-dots-css">
@media (max-width: 1180px) {
    .ds-gallery-dots {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 10px 0 4px !important;
    }
    .ds-gallery-dots svg circle { transition: stroke 0.2s, fill 0.2s !important; }
    .ds-gallery-dots .ds-dot-active svg circle { stroke: #c8e600 !important; fill: #c8e600 !important; }
}
@media (min-width: 1181px) { .ds-gallery-dots { display: none !important; } }
</style>
<script>
(function ($) {
    $(document).ready(function () {
        if ($(window).width() > 1180) return;

        setTimeout(function () {
            var gallery  = $('.woocommerce-product-gallery');
            var items    = gallery.find('.clb-slider-item:not(.cloned)');
            if (!gallery.length || items.length < 2) return;

            // Ohio's own dot SVG — same markup clb-slider uses internally
            var dotSVG = '<svg width="14" height="14" viewBox="0 0 22 22"><g stroke="#17161A" stroke-width="2" fill="none" transform="translate(11,11)"><circle cx="0" cy="0" r="7"></circle></g></svg>';
            var wrap = $('<div class="ds-gallery-dots"></div>');
            items.each(function (i) {
                wrap.append('<span class="' + (i === 0 ? 'ds-dot-active' : '') + '">' + dotSVG + '</span>');
            });
            gallery.after(wrap);

            gallery.on('clb-slider.changed clb-slider.init', function () {
                var idx = gallery.find('.clb-slider-item:not(.cloned)').index(
                    gallery.find('.clb-slider-item.active').first()
                );
                wrap.find('span').removeClass('ds-dot-active').eq(Math.max(idx, 0)).addClass('ds-dot-active');
            });
        }, 450);
    });
}(jQuery));
</script>
    <?php
}

// ─── Footer CSS (inline, wp_head 9999) ────────────────────────────────────────
//
// Lives here, not in style.css: the child style.css is enqueued with a static
// ?ver, so Bluehost/CDN cache an old copy and footer edits there don't reach live.
// Inline <style> ships with the (uncached) HTML and prints after Ohio's own inline
// CSS, so it wins. Hides the header social side-rail, the dark/light switcher, the
// newsletter column and the duplicate under-logo social; rebalances the remaining
// columns; and styles the bottom social strip.
add_action( 'wp_head', 'dirtshack_footer_css', 9999 );
function dirtshack_footer_css() {
    ?>
<style id="ds-footer-css">
/* ── Product page back button ── */
.ds-product-back-wrap {
    max-width: 1280px;
    margin: 0 auto;
    padding: 1.1rem 2rem 1.1rem;
}
.ds-product-back {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-family: 'Archivo Black', sans-serif;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #111;
    background: #c8e600;
    text-decoration: none;
    padding: 0.45rem 1rem 0.45rem 0.7rem;
    border-radius: 2px;
    line-height: 1;
    transition: transform 0.15s ease-out;
}
.ds-product-back:hover,
.ds-product-back:focus { color: #111 !important; background: #c8e600 !important; text-decoration: none !important; transform: translateX(-3px); transition: transform 0.15s ease-out; }
.ds-product-back svg { width: 1em; height: 1em; flex-shrink: 0; }

/* Hide the sticky next-product nav card on single product pages */
.sticky-nav[data-js="sticky-nav-product"] { display: none !important; }

/* Hide Ohio's header social side-rail (the vertical "Follow Us" on the page edge) */
.elements-bar { display: none !important; }

/* Hide the dark / light color switcher */
.color-switcher,
.color-switcher-mobile,
.color-switcher-toddler { display: none !important; }

/* Footer: hide the newsletter column + the duplicate under-logo social block */
#colophon .widgets-column:has(.widget_ohio_widget_subscribe) { display: none !important; }
#colophon .widget_ohio_widget_subscribe { display: none !important; } /* :has() fallback */
#colophon .widget_block:has(a[href*="instagram.com"]),
#colophon .widget_block:has(a[href*="facebook.com"]) { display: none !important; }

/* Rebalance the remaining footer columns to fill the row (3-up on desktop) */
@media (min-width: 992px) {
    #colophon .widgets .widgets-column {
        width: 33.3333% !important;
        max-width: 33.3333% !important;
        flex: 0 0 33.3333% !important;
    }
}

/* Tighten Ohio's tall footer widget row — it pads 72px top/bottom (49px on
   smaller screens), and our short columns left a big empty band above the
   scroll-top / copyright bar. Trim it close. */
#colophon .widgets { padding-top: 36px !important; padding-bottom: 18px !important; }

/* Footer logo: scale it up so the brand mark has more presence in the column. */
#colophon .widget_ohio_widget_logo .branding img,
#colophon .widget_ohio_widget_logo .logo img {
    height: auto !important;
    max-height: 96px !important;
    width: auto !important;
    max-width: 100% !important;
}

/* Bottom social strip — branded dark bar with brand icons */
.ds-footer-social {
    background: #111 !important;
    border-top: 2px solid #C4E000 !important;
    padding: 1rem clamp(1rem, 4vw, 3rem) !important;
}
.ds-footer-social__inner {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: .35rem 1.1rem !important;
    max-width: 1280px !important;
    margin: 0 auto !important;
}
.ds-footer-social__label {
    color: #C4E000 !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
    text-transform: uppercase !important;
    font-size: .72rem !important;
}
.ds-footer-social__list {
    display: flex !important;
    align-items: center !important;
    gap: 1.1rem !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}
.ds-footer-social__list > li { margin: 0 !important; padding: 0 !important; float: none !important; }
.ds-footer-social__link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #fff !important;
    font-size: 1.15rem !important;
    line-height: 1 !important;
    text-decoration: none !important;
    transition: color .18s !important;
}
.ds-footer-social__link:hover { color: #C4E000 !important; }
</style>
    <?php
}

// ─── Site-wide header: sticky dark bar (consistency across all pages) ─────────
//
// Ohio renders the header as a transparent overlay that floats over the hero and
// only turns into a solid dark bar once scrolled (its JS `-sticky` state). We make
// that solid dark bar the permanent appearance on EVERY page: a CSS position:sticky
// bar that stays in normal flow (so page content/heroes sit below it, no overlap)
// and pins to the top on scroll. Mirrors what the homepage used to do on its own —
// now global. (Homepage-only layout neutralisations stay in front-page.php.)
//
// Priority 9999 (not 99): this callback is registered in the child functions.php,
// which loads BEFORE the parent, so at priority 99 it would print *before* Ohio's
// own inline dynamic CSS and lose specificity ties. 9999 prints it last so it wins
// (same technique as dirtshack_fix_headline_bg_css above).
add_action( 'wp_head', 'dirtshack_header_css', 9999 );
function dirtshack_header_css() {
    ?>
<style id="ds-header-css">
/* Sticky in normal flow (overrides Ohio's absolute placement + JS -sticky fixed) */
.theme-ohio #masthead,
.theme-ohio #masthead.header,
.theme-ohio #masthead.-sticky {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 1000 !important;
}
/* Ancestors must not clip or sticky won't pin */
.theme-ohio .site,
.theme-ohio .site-content,
.theme-ohio #content { overflow: visible !important; }

/* Ohio inserts a .header-cap spacer to reserve room for its (formerly fixed)
   header. Our header is now sticky in-flow, so the cap is redundant and leaves a
   large empty gap below the menu on inner pages — collapse it. */
.theme-ohio .header-cap { display: none !important; }

/* Hide Ohio's top utility bar ("Welcome to Dirt Shack" / "Log In / Sign Up").
   It's the standalone .subheader strip above the header (not the header's own
   `subheader_included` modifier class), so this only removes that top bar. */
.theme-ohio .subheader { display: none !important; }

/* Solid dark background */
.theme-ohio #masthead.header,
.theme-ohio #masthead .header-wrap,
.theme-ohio #masthead .header-wrap-inner,
.theme-ohio #masthead .subheader { background: #111 !important; }
.theme-ohio #masthead .header-wrap { box-shadow: 0 2px 12px rgba(0,0,0,.4) !important; }

/* Align the header's inner content (switcher + logo on the left, nav + action
   icons/cart on the right) to the SAME 1280px content rail as the homepage
   sections, instead of running edge-to-edge of the browser. Mirrors how the
   front-page is built (.ds-section provides the clamp() gutter, .ds-wrap caps
   content at 1280px centred): the full-width dark .header-wrap takes the gutter,
   and .header-wrap-inner — the actual logo/nav row in this (header-3) layout —
   is capped + centred, so the switcher and cart line up with "Featured
   Products". (No .page-container exists in this header style.) */
.theme-ohio #masthead .header-wrap {
    padding-left: clamp(1rem, 5vw, 3rem) !important;
    padding-right: clamp(1rem, 5vw, 3rem) !important;
    box-sizing: border-box !important;
}
.theme-ohio #masthead .header-wrap-inner {
    width: 100% !important;
    max-width: 1280px !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

/* Shorter bar (Ohio sets 70px → 54px) + smaller action icons/hamburger */
.theme-ohio #masthead,
.theme-ohio #masthead.header,
.theme-ohio #masthead .header-wrap,
.theme-ohio #masthead .header-wrap-inner { height: 54px !important; min-height: 54px !important; }
.theme-ohio #masthead .menu-optional .icon-button,
.theme-ohio #masthead .mobile-hamburger,
.theme-ohio #masthead .mobile-hamburger .hamburger,
.theme-ohio #masthead .hamburger-button { height: 44px !important; width: 44px !important; }

/* One horizontal logo. Ohio ships 4 logo blocks — hide all but .logo-sticky and
   show only its dark-scheme image (both scheme imgs are the same Light-H.png, so
   showing both renders a doubled logo). Cap height so it stays a normal logo. */
.theme-ohio #masthead .logo,
.theme-ohio #masthead .logo-mobile,
.theme-ohio #masthead .logo-sticky-mobile { display: none !important; }
.theme-ohio #masthead .logo-sticky { display: block !important; opacity: 1 !important; visibility: visible !important; }
.theme-ohio #masthead .logo-sticky .dark-scheme-logo { display: block !important; }
.theme-ohio #masthead .logo-sticky .main-logo.light-scheme-logo { display: none !important; }
/* Lock the logo to ONE small height in both the top (non-sticky) and scrolled
   (.-sticky) states so it doesn't resize on scroll. Overrides Ohio's per-state
   logo height rules (it sets 100/70/50px at top and 45/30/20px when sticky).
   The wordmark is a wide 6.85:1 image. On mobile Ohio constrains .logo-sticky to
   a fixed ~107px width (the room left beside the action icons); forcing a fixed
   HEIGHT inside that clamped width (with object-fit:fill) crushed the aspect
   ratio. Size by aspect ratio instead — cap height at 30px AND width at 100% of
   the container, height:auto so the logo never distorts. Desktop: the container
   is wide, so max-height binds → 30px tall at full width. Mobile: max-width binds
   → it scales down proportionally to fit. object-fit:contain is a safety net. */
.theme-ohio #masthead .logo-sticky img,
.theme-ohio #masthead.-sticky .logo-sticky img {
    height: auto !important;
    min-height: 0 !important;
    max-height: 30px !important;
    width: auto !important;
    max-width: 100% !important;
    object-fit: contain !important;
}

/* Action icons + hamburger → white. The hamburger glyph is an icon font
   (<i class="icon">) → colour it, NOT background (white bg = solid box). */
.theme-ohio #masthead .menu-optional .icon-button,
.theme-ohio #masthead .menu-optional .icon-button .icon,
.theme-ohio #masthead .menu-optional .icon-button i,
.theme-ohio #masthead .menu-optional > li > a { color: #fff !important; }
.theme-ohio #masthead .mobile-hamburger .hamburger i,
.theme-ohio #masthead .mobile-hamburger .hamburger .icon,
.theme-ohio #masthead .hamburger-button i,
.theme-ohio #masthead .hamburger-button .icon { color: #fff !important; background-color: transparent !important; }
.theme-ohio #masthead .mobile-hamburger .hamburger > span,
.theme-ohio #masthead .hamburger-button > span { background-color: #fff !important; }

/* Inline nav links → white on the dark bar; revert to dark when the mobile
   slide-in panel is open (Ohio adds `.visible`) so they stay readable on the
   light panel. Keyed off the open-state class, not a width breakpoint. */
.theme-ohio #masthead .slide-in-overlay .menu-link,
.theme-ohio #masthead .nav-container .menu-link { color: #fff !important; }
.theme-ohio #masthead .slide-in-overlay.visible .menu-link { color: #111 !important; }

/* Cart count badge → brand green */
.theme-ohio #masthead .cart-count,
.theme-ohio #masthead .cart-button .count,
.theme-ohio #masthead .header-cart-count { background: #C4E000 !important; color: #111 !important; }

/* Ecosystem switcher → logo spacing. The switcher mounts as the sibling right
   before .branding (see dirtshack_ecosystem_switcher_mount). The switcher's
   trigger has a bordered box whose right edge sits flush against .branding at
   every width, so the wordmark ends up touching/overlapping that border. Add a
   small left margin to give the logo clear breathing room from the switcher.
   Adjacency selector → only the header logo that actually follows the switcher
   is affected (never the footer widget logo). */
.theme-ohio #masthead [data-ecosystem-switcher] + .branding { margin-left: 14px !important; }
</style>
    <?php
}

// ─── Accessibility: fill in missing image alt text ───────────────────────────
//
// Many product images (and other media) were uploaded without alt text, so they
// render `alt=""`. For a content image that conveys the product's appearance an
// empty alt is a miss (WCAG 1.1.1). This filter supplies a sensible fallback —
// the parent product/post title, else the attachment's own title — but ONLY when
// no alt has been set, so real, curated alt text is never overwritten. Applies
// everywhere WordPress builds an <img> (shop loop, product pages, homepage cards).
add_filter( 'wp_get_attachment_image_attributes', 'dirtshack_image_alt_fallback', 20, 2 );
function dirtshack_image_alt_fallback( $attr, $attachment ) {
    if ( ! empty( $attr['alt'] ) || ! ( $attachment instanceof WP_Post ) ) {
        return $attr;
    }
    $alt = $attachment->post_parent ? get_the_title( $attachment->post_parent ) : '';
    if ( '' === trim( (string) $alt ) ) {
        $alt = $attachment->post_title;
    }
    $alt = trim( wp_strip_all_tags( (string) $alt ) );
    if ( '' !== $alt ) {
        $attr['alt'] = $alt; // core escapes attributes on output
    }
    return $attr;
}

// ─── Performance: remove Ohio's full-screen preloader ────────────────────────
//
// Ohio gates the first paint behind a full-screen preloader overlay (a centred
// animated dot) and fades #page in from opacity:0 once window.load fires. On a
// shared host that delays meaningful content (and hurts LCP) for a purely
// decorative spinner. We:
//   1. strip the `global-page-animation` body class so #page never starts hidden
//      (its opacity:0 rule is scoped to that class), and
//   2. hide the `.page-preloader` overlay from the first paint.
// Both are file-based + cache-safe and don't depend on Ohio's JS running, so the
// real page always shows immediately. (The option itself lives in the DB, which
// our deploy never syncs — doing this in code makes it deterministic.)
add_filter( 'body_class', 'dirtshack_disable_page_fade' );
function dirtshack_disable_page_fade( $classes ) {
    return array_diff( $classes, array( 'global-page-animation' ) );
}

add_action( 'wp_head', 'dirtshack_disable_preloader_css', 9999 );
function dirtshack_disable_preloader_css() { ?>
<style id="ds-no-preloader">
.page-preloader { display: none !important; }
#page { opacity: 1 !important; margin-top: 0 !important; }
</style>
    <?php
}

/**
 * Checkout: drop the separate shipping address form.
 *
 * Removes the "Ship to a different address?" checkbox and the second set of
 * shipping fields. Shipping address is always the same as billing.
 */
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

/**
 * Copy billing address into shipping address on every order.
 *
 * Because the shipping form is hidden, WooCommerce never receives shipping
 * fields from the checkout POST and leaves _shipping_* meta blank. This hook
 * fills them so the order record, PDF invoices, and tax lookup all have a
 * populated shipping address.
 */
add_action( 'woocommerce_checkout_create_order', function ( $order, $data ) {
	$order->set_address( [
		'first_name' => $data['billing_first_name'] ?? '',
		'last_name'  => $data['billing_last_name']  ?? '',
		'company'    => $data['billing_company']     ?? '',
		'address_1'  => $data['billing_address_1']  ?? '',
		'address_2'  => $data['billing_address_2']  ?? '',
		'city'       => $data['billing_city']        ?? '',
		'state'      => $data['billing_state']       ?? '',
		'postcode'   => $data['billing_postcode']    ?? '',
		'country'    => $data['billing_country']     ?? '',
		'phone'      => $data['billing_phone']       ?? '',
		'email'      => $data['billing_email']       ?? '',
	], 'shipping' );
}, 10, 2 );

/**
 * Fix checkout tax not updating when billing state changes.
 *
 * woocommerce_default_customer_address=geolocation re-initialises the customer
 * session to base (MH) during update_order_review AJAX, clobbering the billing
 * location WooCommerce sets from posted fields. Explicitly re-applying the
 * posted billing location here (priority 10, before calculate_totals at 20)
 * ensures the correct state is used for CGST/SGST vs IGST resolution.
 */
add_action( 'woocommerce_checkout_update_order_review', function ( $posted_data ) {
	$data = [];
	parse_str( $posted_data, $data );

	$country  = $data['billing_country']  ?? '';
	$state    = $data['billing_state']    ?? '';
	$postcode = $data['billing_postcode'] ?? '';
	$city     = $data['billing_city']     ?? '';

	if ( $country ) {
		WC()->customer->set_billing_location( $country, $state, $postcode, $city );
		WC()->customer->save();
	}
}, 10 );
