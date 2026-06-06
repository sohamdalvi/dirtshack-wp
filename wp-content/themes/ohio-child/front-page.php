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
 *   6. Latest Braap      — latest blog posts
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

/* ── Header: a sticky dark bar (not a floating overlay). Ohio positions the
      header absolutely over the hero; instead we use CSS position:sticky so the
      bar stays in normal flow — the hero sits *below* it (offset, no overlap) —
      and pins to the top of the viewport on scroll, with no layout jump. This
      overrides Ohio's absolute placement, its `top` offset and its JS `-sticky`
      fixed state. ── */
.home #masthead,
.home #masthead.header,
.home #masthead.-sticky {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 1000 !important;
}
/* Ancestors must not clip, or sticky won't pin */
.home .site,
.home .site-content,
.home #content { overflow: visible !important; }

/* Solid dark background + horizontal logo (matches Ohio's scrolled appearance) */
.home #masthead.header,
.home #masthead .header-wrap,
.home #masthead .header-wrap-inner,
.home #masthead .subheader {
    background: #111 !important;
}
.home #masthead .header-wrap { box-shadow: 0 2px 12px rgba(0,0,0,.4) !important; }

/* Shorter bar: Ohio sets an explicit 70px height — trim it to 54px and shrink the
   action icons/hamburger so they fit the slimmer bar. */
.home #masthead,
.home #masthead.header,
.home #masthead .header-wrap,
.home #masthead .header-wrap-inner {
    height: 54px !important;
    min-height: 54px !important;
}
.home #masthead .menu-optional .icon-button,
.home #masthead .mobile-hamburger,
.home #masthead .mobile-hamburger .hamburger,
.home #masthead .hamburger-button {
    height: 44px !important;
    width: 44px !important;
}

/* Logo: always show ONE horizontal (sticky) logo. Ohio ships four logo blocks —
   .logo + .logo-mobile (stacked, transparent-state) and .logo-sticky +
   .logo-sticky-mobile (horizontal, scrolled-state). Hide all but .logo-sticky so
   a single horizontal logo shows at every width / scroll state. */
.home #masthead .logo,
.home #masthead .logo-mobile,
.home #masthead .logo-sticky-mobile { display: none !important; }
.home #masthead .logo-sticky {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}
/* The sticky logo ships two scheme images (both the horizontal logo) — show only
   the dark-scheme one (correct on the dark bar) so the logo doesn't render twice,
   and cap its height so it stays a normal header logo (it's ~100px tall otherwise). */
.home #masthead .logo-sticky .dark-scheme-logo {
    display: block !important;
    max-height: 38px !important;
    width: auto !important;
}
.home #masthead .logo-sticky .main-logo.light-scheme-logo { display: none !important; }

/* Action icons in the top bar → white. Scoped to .menu-optional (the icon row)
   and the hamburger toggle ONLY — the hamburger slide-in menu lives in .nav /
   .slide-in-overlay and must keep its own (dark-on-light) colours. */
.home #masthead .menu-optional .icon-button,
.home #masthead .menu-optional .icon-button .icon,
.home #masthead .menu-optional .icon-button i,
.home #masthead .menu-optional > li > a { color: #fff !important; }
/* Hamburger → white. The glyph is an icon font (<i class="icon">), so colour it
   (NOT background — a white background turns it into a solid box). Span-based line
   variants still get a white background, harmlessly. */
.home #masthead .mobile-hamburger .hamburger i,
.home #masthead .mobile-hamburger .hamburger .icon,
.home #masthead .hamburger-button i,
.home #masthead .hamburger-button .icon { color: #fff !important; background-color: transparent !important; }
.home #masthead .mobile-hamburger .hamburger > span,
.home #masthead .hamburger-button > span { background-color: #fff !important; }

/* Inline nav links ("Shop", "Braap") sit in the dark bar on tablet/desktop →
   white. The SAME element becomes the mobile slide-in panel when opened (Ohio
   adds `.visible`); there the panel is light, so links revert to dark. This keys
   off the open-state class, not a width breakpoint, so it's robust either way. */
.home #masthead .slide-in-overlay .menu-link,
.home #masthead .nav-container .menu-link { color: #fff !important; }
.home #masthead .slide-in-overlay.visible .menu-link { color: #111 !important; }

/* Cart count badge stays brand green */
.home #masthead .cart-count,
.home #masthead .cart-button .count,
.home #masthead .header-cart-count { background: #C4E000 !important; color: #111 !important; }

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

/* ── 1. Hero ── */
#ds-home .ds-hero {
    position: relative !important;
    display: flex !important;
    align-items: flex-end !important;
    min-height: 45vh !important;
    padding: 0 !important;
    background-size: cover !important;
    background-position: center 35% !important;
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
    padding: 2.5rem var(--padx) 2.75rem !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    text-align: left !important;
}
#ds-home .ds-hero__title {
    margin: 0 0 1.1rem !important;
    font-size: clamp(1.7rem, 7vw, 3.2rem) !important;
    font-weight: 800 !important;
    letter-spacing: -.02em !important;
    line-height: 1.05 !important;
    text-transform: uppercase !important;
    color: #fff !important;
    text-shadow: 0 2px 14px rgba(0,0,0,.5) !important;
}
#ds-home .ds-accent { color: var(--g) !important; }
#ds-home .ds-hero__ctas {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: .75rem !important;
}

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
    text-decoration: none !important;
    color: var(--d) !important;
    transition: box-shadow .2s, transform .2s !important;
}
#ds-home .ds-product:hover { box-shadow: 0 8px 28px rgba(0,0,0,.10) !important; transform: translateY(-2px) !important; }
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
#ds-home .ds-product__body { padding: .85rem 1rem 1rem !important; flex: 1 !important; }
#ds-home .ds-product__name {
    font-size: .88rem !important;
    font-weight: 700 !important;
    margin: 0 0 .35rem !important;
    color: var(--d) !important;
    line-height: 1.3 !important;
}
#ds-home .ds-product__price { margin: 0 !important; font-size: .92rem !important; font-weight: 700 !important; color: var(--d) !important; }
#ds-home .ds-product__price * { color: var(--d) !important; }

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

/* ── Instagram feed (Smash Balloon) — let the plugin own its grid; just frame it ── */
#ds-home .ds-instagram #sb_instagram { margin: 0 auto !important; }

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
    #ds-home .ds-hero { min-height: 52vh !important; }
    #ds-home .ds-hero__content { padding: 4rem var(--padx) 4rem !important; }
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
            <h1 class="ds-hero__title">Fuelling the <span class="ds-accent">Dirt Biking</span><br>Culture in India</h1>
            <div class="ds-hero__ctas">
                <a class="ds-btn ds-btn--primary" href="<?php echo esc_url( $ds_shop_url ); ?>">Shop Parts</a>
                <a class="ds-btn ds-btn--ghost" href="<?php echo esc_url( $ds_market_url ); ?>">Used Bike Marketplace</a>
            </div>
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
                <a class="ds-product" href="<?php echo esc_url( get_permalink() ); ?>">
                    <span class="ds-product__img">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) );
                        else : ?>
                            <img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    </span>
                    <span class="ds-product__body">
                        <span class="ds-product__name"><?php the_title(); ?></span>
                        <span class="ds-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                    </span>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 3. MARKETPLACE CARD ── -->
    <section class="ds-section ds-section--grey">
        <div class="ds-wrap">
            <div class="ds-market">
                <div class="ds-market__copy">
                    <span class="ds-market__eyebrow">DirtShack Marketplace</span>
                    <h2 class="ds-market__title">Buy &amp; sell used dirt bikes</h2>
                    <p class="ds-market__sub">A dedicated marketplace for pre-owned bikes &amp; parts, built by riders. List yours free.</p>
                </div>
                <a class="ds-btn ds-btn--primary" href="<?php echo esc_url( $ds_market_url ); ?>">Visit the Marketplace &rarr;</a>
            </div>
        </div>
    </section>

    <!-- ── 4. INSTAGRAM FEED (Smash Balloon) ── -->
    <?php if ( shortcode_exists( 'instagram-feed' ) ) : ?>
    <section class="ds-section ds-instagram">
        <div class="ds-wrap">
            <div class="ds-section__head">
                <h2 class="ds-section__title">From the Trail</h2>
                <a class="ds-link" href="https://www.instagram.com/dirtshack.in/" target="_blank" rel="noopener">@dirtshack.in &rarr;</a>
            </div>
            <?php echo do_shortcode( '[instagram-feed]' ); ?>
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

    <!-- ── 6. LATEST BRAAP (blog) ── -->
    <?php
    $ds_posts = new WP_Query( array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 3,
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'no_found_rows'       => true,
    ) );

    if ( $ds_posts->have_posts() ) :
        $ds_blog_url = get_permalink( (int) get_option( 'page_for_posts' ) );
    ?>
    <section class="ds-section">
        <div class="ds-wrap">
            <div class="ds-section__head">
                <h2 class="ds-section__title">Latest from Braap</h2>
                <?php if ( $ds_blog_url ) : ?>
                    <a class="ds-link" href="<?php echo esc_url( $ds_blog_url ); ?>">All posts &rarr;</a>
                <?php endif; ?>
            </div>
            <div class="ds-blog-grid">
            <?php while ( $ds_posts->have_posts() ) : $ds_posts->the_post(); ?>
                <a class="ds-post" href="<?php echo esc_url( get_permalink() ); ?>">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <span class="ds-post__img"><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?></span>
                    <?php endif; ?>
                    <span class="ds-post__body">
                        <span class="ds-post__date"><?php echo esc_html( get_the_date() ); ?></span>
                        <span class="ds-post__title"><?php the_title(); ?></span>
                    </span>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
