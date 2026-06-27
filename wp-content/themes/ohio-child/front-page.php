<?php
/**
 * DirtShack — Custom Homepage Template (mobile-first rebuild)
 *
 * Section order (mobile-first; scales up responsively to desktop):
 *   0. Announcement bar  — rendered site-wide at wp_body_open (see functions.php)
 *   0. Sticky header     — Ohio header via get_header()
 *   1. Hero ~45vh        — one image, dark overlay, headline + two CTAs
 *   2. Shop by Category  — native WooCommerce product categories
 *   3. Marketplace card  — single CTA out to market.dirtshack.in
 *   4. Featured Products — WooCommerce "featured" flag (falls back to latest)
 *   5. Why DirtShack     — four icon blocks
 *   0. Footer            — Ohio footer via get_footer()
 *
 * Cache notes: every section renders from deterministic, page-cacheable queries.
 * No nonces, sessions, timestamps, randomised ordering or per-visitor output is
 * baked into the HTML — anything per-visitor (cart count, dismissed bar) runs
 * client-side so the cached markup is byte-identical for every request.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Inject the homepage CSS into <head> at priority 99 — i.e. *after* Ohio's
// stylesheet and its inline dynamic CSS — so our layout always wins without
// JavaScript. Inline CSS is static and identical for every visitor, so it stays
// fully page-cacheable. Mobile-first: base rules target mobile, min-width media
// queries scale up to tablet (≥768px) and desktop (≥1025px).
add_action( 'wp_head', 'dirtshack_home_css', 99 );
function dirtshack_home_css() { ?>
<style id="ds-home-css">
/* ── Neutralise Ohio interference on the homepage ── */
.home #content,
.home .site-content { overflow: visible !important; }
.home .page-headline,
.home .subheader-holder,
.home .page-title-holder,
.home .breadcrumb-holder { display: none !important; }
.home .page-container.top-offset { padding-top: 0 !important; }

/* ── Header: the sticky dark bar is now applied SITE-WIDE from functions.php
   (dirtshack_header_css), so the homepage-only header rules that used to live
   here have moved there. ── */

/* ── Homepage design tokens (scoped fallbacks so the page is self-contained) ── */
#ds-home {
    --g: #C4E000;          /* neon green   */
    --d: #111;             /* near-black   */
    --grey: #f4f4f4;
    --bd: #e5e5e5;
    --r: 16px;             /* corner radius */
    --max: 1280px;
    --padx: clamp(1rem, 5vw, 3rem);
    background: #fff !important;
}

/* ── Shared section primitives ── */
#ds-home .ds-section {
    display: block !important;
    padding: 40px var(--padx) !important;   /* 40px mobile … */
    box-sizing: border-box !important;
}
#ds-home .ds-section--grey { background: var(--grey) !important; }
#ds-home .ds-section--dark { background: var(--d) !important; }
#ds-home .ds-wrap {
    max-width: var(--max) !important;
    margin: 0 auto !important;
    width: 100% !important;
}
#ds-home .ds-section__head {
    display: flex !important;
    align-items: baseline !important;
    justify-content: space-between !important;
    gap: 1rem !important;
    margin: 0 0 1.5rem !important;
}
#ds-home .ds-section__title {
    font-size: clamp(1.4rem, 4vw, 1.9rem) !important;
    font-weight: 800 !important;
    letter-spacing: -.02em !important;
    margin: 0 !important;
    color: var(--d) !important;
    line-height: 1.15 !important;
}
#ds-home .ds-section--dark .ds-section__title { color: #fff !important; }
#ds-home .ds-link {
    font-size: .78rem !important;
    font-weight: 700 !important;
    letter-spacing: .05em !important;
    text-transform: uppercase !important;
    color: var(--d) !important;
    text-decoration: none !important;
    border-bottom: 2px solid var(--g) !important;
    white-space: nowrap !important;
    padding-bottom: 1px !important;
}

/* ── Buttons (neon-green, rounded) ── */
#ds-home .ds-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: .4rem !important;
    padding: .85rem 1.6rem !important;
    border-radius: 999px !important;
    font-size: .82rem !important;
    font-weight: 800 !important;
    letter-spacing: .05em !important;
    text-transform: uppercase !important;
    text-decoration: none !important;
    line-height: 1 !important;
    border: 2px solid transparent !important;
    cursor: pointer !important;
    transition: opacity .18s, background .18s, color .18s !important;
}
#ds-home .ds-btn--primary { background: var(--g) !important; color: var(--d) !important; }
#ds-home .ds-btn--primary:hover { opacity: .85 !important; }
#ds-home .ds-btn--ghost { background: rgba(0,0,0,.25) !important; color: #fff !important; border-color: #fff !important; }
#ds-home .ds-btn--ghost:hover { background: #fff !important; color: var(--d) !important; }

/* ── Responsive product/category grid: 2 col mobile ── */
#ds-home .ds-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1rem !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}
/* Featured products: the query renders 8; show 4 on mobile, 6 on tablet, 8 on
   desktop by hiding the extras per breakpoint. Cache-safe — the HTML is identical
   for every visitor, only the CSS adapts (you can't vary the count server-side
   behind a full-page cache). Keeps each tier to clean rows (2×2 / 2×3 / 2×4). */
@media (max-width: 767px) {
    #ds-home .ds-grid .ds-product:nth-child(n+5) { display: none !important; }
}
@media (min-width: 768px) and (max-width: 1024px) {
    #ds-home .ds-grid .ds-product:nth-child(n+7) { display: none !important; }
}

/* ── 1. Hero ── */
#ds-home .ds-hero {
    position: relative !important;
    display: flex !important;
    align-items: flex-end !important;
    min-height: clamp(220px, 32vh, 340px) !important;
    padding: 0 !important;
    background-size: cover !important;
    background-position: center 70% !important;
    background-color: var(--d) !important;
    overflow: hidden !important;
}
#ds-home .ds-hero__overlay {
    position: absolute !important;
    inset: 0 !important;
    background: linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.45) 45%, rgba(0,0,0,.10) 100%) !important;
    pointer-events: none !important;
}
#ds-home .ds-hero__content {
    position: relative !important;
    z-index: 2 !important;
    width: 100% !important;
    max-width: var(--max) !important;
    margin: 0 auto !important;
    padding: 2.5rem var(--padx) 1rem !important;   /* small bottom pad: title sits on the bottom edge */
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    text-align: left !important;
}
#ds-home .ds-hero__title {
    margin: 0 !important;
    font-size: clamp(1.05rem, 2.1vw, 1.6rem) !important;
    font-family: "Archivo Black", sans-serif !important;
    font-weight: 800 !important;
    letter-spacing: -.01em !important;
    line-height: 1.18 !important;
    text-transform: uppercase !important;
    color: #fff !important;
    text-shadow: 0 2px 14px rgba(0,0,0,.5) !important;
}
#ds-home .ds-accent { color: var(--g) !important; }

/* Marketplace badge — top-right of the hero, styled as a clearly-EXTERNAL link
   (translucent dark pill + neon-green border + ↗ outbound arrow, opens new tab). */
#ds-home .ds-hero__market {
    position: absolute !important;
    top: .9rem !important;
    right: clamp(1rem, 4vw, 2rem) !important;
    z-index: 3 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: .4rem !important;
    max-width: calc(100% - 2rem) !important;
    padding: .5rem .9rem !important;
    border-radius: 999px !important;
    background: rgba(0,0,0,.55) !important;
    border: 1.5px solid var(--g) !important;
    color: #fff !important;
    font-size: .68rem !important;
    font-weight: 800 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    text-decoration: none !important;
    line-height: 1.1 !important;
    -webkit-backdrop-filter: blur(4px) !important;
    backdrop-filter: blur(4px) !important;
    transition: background .18s, color .18s !important;
}
#ds-home .ds-hero__market:hover { background: var(--g) !important; color: var(--d) !important; }
#ds-home .ds-hero__market .ds-ext { color: var(--g) !important; font-weight: 900 !important; font-size: 1.05em !important; }
#ds-home .ds-hero__market:hover .ds-ext { color: var(--d) !important; }

/* ── Marketplace card ── */
#ds-home .ds-market {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 1rem !important;
    border-radius: var(--r) !important;
    padding: 2rem 1.5rem !important;
    background:
        linear-gradient(120deg, rgba(17,17,17,.92) 0%, rgba(17,17,17,.65) 100%),
        var(--d) !important;
    border: 2px solid var(--g) !important;
}
#ds-home .ds-market__eyebrow {
    font-size: .72rem !important;
    font-weight: 800 !important;
    letter-spacing: .15em !important;
    text-transform: uppercase !important;
    color: var(--g) !important;
}
#ds-home .ds-market__title {
    margin: 0 !important;
    font-size: clamp(1.3rem, 5vw, 1.75rem) !important;
    font-weight: 800 !important;
    line-height: 1.15 !important;
    color: #fff !important;
}
#ds-home .ds-market__sub {
    margin: 0 !important;
    font-size: .9rem !important;
    line-height: 1.5 !important;
    color: #d6d6d6 !important;
    max-width: 46ch !important;
}

/* ── 4. Product card ── */
#ds-home .ds-product {
    display: flex !important;
    flex-direction: column !important;
    background: #fff !important;
    border: 1px solid var(--bd) !important;
    border-radius: var(--r) !important;
    overflow: hidden !important;
    color: var(--d) !important;
    transition: box-shadow .2s, transform .2s !important;
}
#ds-home .ds-product:hover { box-shadow: 0 8px 28px rgba(0,0,0,.10) !important; transform: translateY(-2px) !important; }
/* Image + name are the clickable link; the add-to-cart button sits outside it
   (nested <a> is invalid HTML). flex:1 lets the link grow so the foot pins low. */
#ds-home .ds-product__link {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    text-decoration: none !important;
    color: var(--d) !important;
}
#ds-home .ds-product__img {
    width: 100% !important;
    aspect-ratio: 1 / 1 !important;
    overflow: hidden !important;
    background: #f8f8f8 !important;
    display: block !important;
}
#ds-home .ds-product__img img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
    transition: transform .35s !important;
}
#ds-home .ds-product:hover .ds-product__img img { transform: scale(1.05) !important; }
#ds-home .ds-product__body { padding: .85rem 1rem .5rem !important; }
#ds-home .ds-product__name {
    font-size: .88rem !important;
    font-weight: 700 !important;
    margin: 0 !important;
    color: var(--d) !important;
    line-height: 1.3 !important;
}
#ds-home .ds-product__foot {
    display: flex !important;
    flex-direction: column !important;
    gap: .6rem !important;
    padding: 0 1rem 1rem !important;
}
#ds-home .ds-product__price { margin: 0 !important; font-size: .92rem !important; font-weight: 700 !important; color: var(--d) !important; }
#ds-home .ds-product__price * { color: var(--d) !important; }
/* Add-to-cart: full-width neon pill (a comfortable ≥40px tap target). Overrides
   Ohio's stock button styling. "added"/loading states keep the brand colour. */
#ds-home .ds-product__foot .button,
#ds-home .ds-product__cart {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
    padding: .65rem 1rem !important;
    min-height: 40px !important;
    border: 0 !important;
    border-radius: 999px !important;
    background: var(--g) !important;
    color: var(--d) !important;
    font-size: .76rem !important;
    font-weight: 800 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    text-align: center !important;
    text-decoration: none !important;
    line-height: 1.25 !important;
    cursor: pointer !important;
    transition: opacity .18s !important;
}
#ds-home .ds-product__foot .button:hover,
#ds-home .ds-product__cart:hover { opacity: .85 !important; color: var(--d) !important; }
#ds-home .ds-product__foot .added_to_cart {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
    text-align: center !important;
    font-size: .72rem !important;
    font-weight: 700 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    color: var(--d) !important;
    text-decoration: underline !important;
    text-underline-offset: 3px !important;
}

/* ── 5. Why DirtShack — 2 col mobile ── */
#ds-home .ds-why-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1rem !important;
}
#ds-home .ds-why {
    display: flex !important;
    flex-direction: column !important;
    gap: .3rem !important;
    padding: .8rem .9rem !important;
    background: #1a1a1a !important;
    border: 1px solid #2a2a2a !important;
    border-radius: var(--r) !important;
}
#ds-home .ds-why__icon {
    width: 24px !important; height: 24px !important;
    color: var(--g) !important;
}
#ds-home .ds-why__icon svg { width: 100% !important; height: 100% !important; display: block !important; }
#ds-home .ds-why__title { margin: 0 !important; font-size: .86rem !important; font-weight: 800 !important; color: #fff !important; line-height: 1.2 !important; }
#ds-home .ds-why__text { margin: 0 !important; font-size: .76rem !important; line-height: 1.35 !important; color: #b9b9b9 !important; }

/* Compact "ribbon": trim the section's vertical padding + heading gap */
#ds-home .ds-why-section { padding-top: 26px !important; padding-bottom: 26px !important; }
#ds-home .ds-why-section .ds-section__head { margin-bottom: 1rem !important; }

/* ── Instagram feed (Smash Balloon) — restyle the plugin chrome to match the brand
   (black / white / neon green) instead of its default grey + Instagram-blue look.
   These elements are injected by the plugin's JS, but the CSS still applies. ── */
#ds-home .ds-instagram #sb_instagram { margin: 0 auto !important; }

/* Hide the plugin's profile header/bio — we already show the @dirtshack.in link
   above the feed, so the avatar + bio block is redundant. */
#ds-home .ds-instagram #sb_instagram .sbi_header,
#ds-home .ds-instagram #sb_instagram .sb_instagram_header { display: none !important; }

/* Rounded photos to match the rest of the cards */
#ds-home .ds-instagram #sb_instagram #sbi_images .sbi_item,
#ds-home .ds-instagram #sb_instagram #sbi_images .sbi_photo_wrap,
#ds-home .ds-instagram #sb_instagram #sbi_images .sbi_photo { border-radius: 10px !important; overflow: hidden !important; }

/* Keep the tiles square, but a bit smaller with a clear gap between them.
   .sbi_photo is forced to a 1:1 box (height:auto + aspect-ratio beat the inline
   pixel height the plugin's JS sets). Widening the .sbi_item padding opens up the
   gutter — which both separates the tiles and shrinks each square (border-box, so
   padding eats into the fixed column width). */
#ds-home .ds-instagram #sb_instagram #sbi_images .sbi_photo {
    aspect-ratio: 1 / 1 !important;
    height: auto !important;
    min-height: 0 !important;
    padding-bottom: 0 !important;
}
#ds-home .ds-instagram #sb_instagram #sbi_images .sbi_photo_wrap { height: auto !important; }
#ds-home .ds-instagram #sb_instagram #sbi_images .sbi_item {
    height: auto !important;
    padding: 12px !important;
}

/* "Load More" → dark pill (secondary) */
#ds-home .ds-instagram #sb_instagram #sbi_load .sbi_load_btn,
#ds-home .ds-instagram #sb_instagram .sbi_load_btn {
    background: #1a1a1a !important;
    color: #fff !important;
    border: 1px solid #2a2a2a !important;
    border-radius: 999px !important;
    font-weight: 800 !important;
    letter-spacing: .05em !important;
    text-transform: uppercase !important;
    transition: background .18s, opacity .18s !important;
}
#ds-home .ds-instagram #sb_instagram .sbi_load_btn:hover { background: #000 !important; }

/* "Follow on Instagram" → neon-green pill (primary, matches the brand CTAs) */
#ds-home .ds-instagram #sb_instagram .sbi_follow_btn a,
#ds-home .ds-instagram #sb_instagram a.sbi_follow_btn {
    background: #C4E000 !important;
    color: #111 !important;
    border: 0 !important;
    border-radius: 999px !important;
    font-weight: 800 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    transition: opacity .18s !important;
}
#ds-home .ds-instagram #sb_instagram .sbi_follow_btn a:hover,
#ds-home .ds-instagram #sb_instagram a.sbi_follow_btn:hover { opacity: .85 !important; }
#ds-home .ds-instagram #sb_instagram .sbi_follow_btn a .fa-instagram,
#ds-home .ds-instagram #sb_instagram .sbi_follow_btn a svg,
#ds-home .ds-instagram #sb_instagram .sbi_follow_btn svg { color: #111 !important; fill: #111 !important; }

/* ── 6. Blog cards — 1 col mobile ── */
#ds-home .ds-blog-grid {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1.25rem !important;
}
#ds-home .ds-post {
    display: flex !important;
    flex-direction: column !important;
    background: #fff !important;
    border: 1px solid var(--bd) !important;
    border-radius: var(--r) !important;
    overflow: hidden !important;
    text-decoration: none !important;
    color: var(--d) !important;
    transition: box-shadow .2s, transform .2s !important;
}
#ds-home .ds-post:hover { box-shadow: 0 8px 28px rgba(0,0,0,.10) !important; transform: translateY(-2px) !important; }
#ds-home .ds-post__img { width: 100% !important; aspect-ratio: 16 / 9 !important; overflow: hidden !important; background: #eee !important; display: block !important; }
#ds-home .ds-post__img img { width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important; }
#ds-home .ds-post__body { padding: 1rem 1.1rem 1.2rem !important; }
#ds-home .ds-post__date { font-size: .72rem !important; font-weight: 700 !important; letter-spacing: .06em !important; text-transform: uppercase !important; color: #888 !important; }
#ds-home .ds-post__title { margin: .35rem 0 0 !important; font-size: 1.02rem !important; font-weight: 800 !important; line-height: 1.25 !important; color: var(--d) !important; }

/* ── Tablet (≥768px): 3-col grids ── */
@media (min-width: 768px) {
    #ds-home .ds-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 1.25rem !important; }
    #ds-home .ds-why-grid { grid-template-columns: repeat(4, 1fr) !important; }
    #ds-home .ds-blog-grid { grid-template-columns: repeat(3, 1fr) !important; }
    #ds-home .ds-market { flex-direction: row !important; align-items: center !important; justify-content: space-between !important; padding: 2.25rem 2.5rem !important; }
    #ds-home .ds-market__copy { display: flex !important; flex-direction: column !important; gap: .5rem !important; }
}

/* ── Desktop (≥1025px): 4-col product/category grids, 80px section padding ── */
@media (min-width: 1025px) {
    #ds-home .ds-section { padding: 80px var(--padx) !important; }
    #ds-home .ds-why-section { padding-top: 44px !important; padding-bottom: 44px !important; } /* keep ribbon compact */
    #ds-home .ds-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 1.5rem !important; }
    #ds-home .ds-hero { min-height: clamp(220px, 32vh, 340px) !important; }
    #ds-home .ds-hero__content { padding: 3rem var(--padx) 1.5rem !important; }
}
</style>
<?php }

get_header();

/* Helper: marketplace URL with cross-promo UTM tags (constant from functions.php). */
$ds_market_url = defined( 'DIRTSHACK_MARKETPLACE_URL' )
    ? add_query_arg(
        array( 'utm_source' => 'dirtshack_store', 'utm_medium' => 'homepage', 'utm_campaign' => 'marketplace' ),
        DIRTSHACK_MARKETPLACE_URL
    )
    : 'https://market.dirtshack.in/';

/* Helper: the WooCommerce shop URL ("Shop Parts"). */
$ds_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $ds_shop_url ) {
    $ds_shop_url = get_post_type_archive_link( 'product' );
}
?>

<main id="ds-home" class="ds-home">

    <!-- ── 1. HERO ── -->
    <section class="ds-hero" style="background-image:url('<?php echo esc_url( dirtshack_hero_image_url( 'home' ) ); ?>')">
        <div class="ds-hero__overlay"></div>
        <div class="ds-hero__content">
            <h1 class="ds-hero__title">Engineered to Global Standards.<br>Built for <span class="ds-accent">Indian Riders.</span></h1>
        </div>
    </section>

    <!-- ── 2. FEATURED PRODUCTS ── -->
    <?php
    // Pull products flagged "featured" in WooCommerce (content stays editable via
    // the featured flag — nothing hardcoded). Deterministic order. If the store
    // has no featured products yet, fall back to the latest published products so
    // the section never renders empty.
    $ds_featured_ids = function_exists( 'wc_get_featured_product_ids' ) ? wc_get_featured_product_ids() : array();

    $ds_args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'posts_per_page'      => 8,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    );
    if ( ! empty( $ds_featured_ids ) ) {
        $ds_args['post__in'] = $ds_featured_ids;
        $ds_args['orderby']  = 'post__in'; // stable, follows featured order
    } else {
        $ds_args['orderby'] = 'date';
        $ds_args['order']   = 'DESC';
    }
    // Only saleable products in the catalog (mirrors WooCommerce visibility).
    $ds_args['tax_query'] = array( array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => array( 'exclude-from-catalog' ),
        'operator' => 'NOT IN',
    ) );

    $ds_products = new WP_Query( $ds_args );

    if ( $ds_products->have_posts() ) :
    ?>
    <section class="ds-section ds-section--grey">
        <div class="ds-wrap">
            <div class="ds-section__head">
                <h2 class="ds-section__title">Featured Products</h2>
                <a class="ds-link" href="<?php echo esc_url( $ds_shop_url ); ?>">View all &rarr;</a>
            </div>
            <div class="ds-grid">
            <?php while ( $ds_products->have_posts() ) : $ds_products->the_post();
                $product = wc_get_product( get_the_ID() );
                if ( ! $product ) { continue; }
            ?>
                <div class="ds-product">
                    <a class="ds-product__link" href="<?php echo esc_url( get_permalink() ); ?>">
                        <span class="ds-product__img">
                            <?php if ( has_post_thumbnail() ) :
                                the_post_thumbnail( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) );
                            else : ?>
                                <img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                            <?php endif; ?>
                        </span>
                        <span class="ds-product__body">
                            <span class="ds-product__name"><?php the_title(); ?></span>
                        </span>
                    </a>
                    <div class="ds-product__foot">
                        <span class="ds-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                        <?php
                        // WooCommerce's own loop button: respects product type (Add to
                        // cart / Select options / Read more), adds a real ?add-to-cart
                        // href so it works without JS, and is upgraded to AJAX by the
                        // site cart script. Kept OUTSIDE the card link (a nested <a>
                        // would be invalid HTML and shatter the grid layout).
                        woocommerce_template_loop_add_to_cart( array( 'class' => 'button ds-product__cart' ) );
                        ?>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 5. WHY DIRTSHACK (compact ribbon) ── -->
    <section class="ds-section ds-section--dark ds-why-section">
        <div class="ds-wrap">
            <div class="ds-section__head">
                <h2 class="ds-section__title">Why DirtShack</h2>
            </div>
            <div class="ds-why-grid">
                <div class="ds-why">
                    <span class="ds-why__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <h3 class="ds-why__title">Quality Parts</h3>
                    <p class="ds-why__text">Tested on Indian trails — gear that holds up where you ride.</p>
                </div>
                <div class="ds-why">
                    <span class="ds-why__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
                    <h3 class="ds-why__title">Pan-India Shipping</h3>
                    <p class="ds-why__text">Delivered to your door in 2–7 business days, nationwide.</p>
                </div>
                <div class="ds-why">
                    <span class="ds-why__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                    <h3 class="ds-why__title">Secure Checkout</h3>
                    <p class="ds-why__text">Razorpay-protected payments — UPI, cards &amp; netbanking.</p>
                </div>
                <div class="ds-why">
                    <span class="ds-why__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                    <h3 class="ds-why__title">Rider Community</h3>
                    <p class="ds-why__text">Built by riders, for riders — support that speaks dirt.</p>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
