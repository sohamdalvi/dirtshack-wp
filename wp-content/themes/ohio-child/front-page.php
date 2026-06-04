<?php
/**
 * DirtShack — Custom Homepage Template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Inject critical layout CSS into <head> — loads after Ohio's stylesheet so
// nothing can override it without JavaScript.
add_action( 'wp_head', function () { ?>
<style id="ds-home-css">
/* ── Reset Ohio interference on the homepage ── */
.home #content,
.home .site-content { overflow: visible !important; }
.home .subheader-holder,
.home .page-title-holder { display: none !important; }

/* ── Header: transparent over hero, dark when sticky/scrolled ── */

/* Keep transparent over hero — don't touch default state */

/* When Ohio makes the header sticky after scroll, force dark bg
   so the white part of the horizontal logo stays visible */
.home .header-sticky-holder,
.home .sticky-header,
.home .header-is-sticky,
.home [class*="header"][class*="sticky"],
.home .site-header.sticky,
.home .site-header.fixed,
.home .site-header.scrolled {
    background: #111 !important;
    background-color: #111 !important;
    box-shadow: 0 2px 12px rgba(0,0,0,.4) !important;
}
/* Nav & icon colour when sticky — keep white for contrast on dark bg */
.home .header-sticky-holder .main-menu > li > a,
.home .sticky-header .main-menu > li > a,
.home .header-is-sticky .main-menu > li > a,
.home [class*="header"][class*="sticky"] nav a {
    color: #fff !important;
}
/* Cart badge — yellow on dark */
.home .cart-count,
.home .header-cart-count {
    background: #c8e600 !important;
    color: #111 !important;
}

/* ── Hero ── */
.ds-hero {
    position: relative !important;
    display: flex !important;
    align-items: flex-end !important;
    min-height: 58vh !important;
    background-size: cover !important;
    background-position: center 35% !important;
    background-color: #111 !important;
    overflow: hidden !important;
    width: 100% !important;
    box-sizing: border-box !important;
    float: none !important;
    clear: both !important;
}
.ds-hero__overlay {
    position: absolute !important;
    top: 0 !important; right: 0 !important;
    bottom: 0 !important; left: 0 !important;
    background: linear-gradient(to left, rgba(0,0,0,.75) 0%, rgba(0,0,0,.15) 100%) !important;
    pointer-events: none !important;
}
.ds-hero__content {
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
.ds-hero__tagline {
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
.ds-accent { color: #c8e600 !important; }

/* ── Trust bar ── */
.ds-trust {
    display: block !important;
    width: 100% !important;
    background: #c8e600 !important;
    box-sizing: border-box !important;
}
.ds-trust__inner {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    gap: 1.5rem !important;
    max-width: 1280px !important;
    margin: 0 auto !important;
    padding: .9rem 5% !important;
    list-style: none !important;
    box-sizing: border-box !important;
}
.ds-trust__item {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: .5rem !important;
    flex: 1 !important;
    min-width: 0 !important;
    font-size: .78rem !important;
    color: #111 !important;
    line-height: 1.3 !important;
    float: none !important;
}
.ds-trust__item svg {
    flex-shrink: 0 !important;
    width: 18px !important;
    height: 18px !important;
    display: inline-block !important;
}
.ds-trust__item span { display: inline !important; }

/* ── Products section ── */
.ds-products {
    display: block !important;
    padding: 3.5rem 5% !important;
    background: #f4f4f4 !important;
    box-sizing: border-box !important;
}
.ds-products__head {
    display: flex !important;
    flex-direction: row !important;
    align-items: baseline !important;
    justify-content: space-between !important;
    max-width: 1280px !important;
    margin: 0 auto 1.5rem !important;
    gap: 1rem !important;
}
.ds-products__title {
    font-size: 1.6rem !important;
    font-weight: 800 !important;
    margin: 0 !important;
    color: #111 !important;
    float: none !important;
}
.ds-view-all {
    font-size: .8rem !important;
    font-weight: 700 !important;
    letter-spacing: .05em !important;
    text-transform: uppercase !important;
    color: #111 !important;
    text-decoration: none !important;
    border-bottom: 2px solid #c8e600 !important;
    white-space: nowrap !important;
}
.ds-products__grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 1.25rem !important;
    max-width: 1280px !important;
    margin: 0 auto !important;
    padding: 0 !important;
    list-style: none !important;
    float: none !important;
}

/* ── Product card ── */
.ds-product-card {
    display: flex !important;
    flex-direction: column !important;
    background: #fff !important;
    border: 1px solid #e5e5e5 !important;
    border-radius: 6px !important;
    overflow: hidden !important;
    text-decoration: none !important;
    color: #111 !important;
    transition: box-shadow .2s, transform .2s !important;
    float: none !important;
}
.ds-product-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.10) !important;
    transform: translateY(-2px) !important;
}
.ds-product-card__img {
    width: 100% !important;
    aspect-ratio: 1 / 1 !important;
    overflow: hidden !important;
    background: #f8f8f8 !important;
    display: block !important;
    flex-shrink: 0 !important;
}
.ds-product-card__img img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
}
.ds-product-card__body {
    padding: .85rem 1rem 1rem !important;
    flex: 1 !important;
}
.ds-product-card__name {
    font-size: .9rem !important;
    font-weight: 700 !important;
    margin: 0 0 .3rem !important;
    color: #111 !important;
    line-height: 1.3 !important;
}
.ds-product-card__price {
    margin: 0 !important;
    font-size: .95rem !important;
    font-weight: 600 !important;
    color: #111 !important;
}
.ds-product-card__price * { color: #111 !important; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .ds-products__grid { grid-template-columns: repeat(2, 1fr) !important; }
    .ds-trust__inner { flex-wrap: wrap !important; gap: .6rem !important; }
    .ds-trust__item { flex: 1 1 44% !important; }
}
@media (max-width: 600px) {
    .ds-hero { min-height: 45vh !important; }
    .ds-products { padding: 2rem 4% !important; }
    .ds-products__grid { grid-template-columns: repeat(2, 1fr) !important; gap: .75rem !important; }
}
</style>
<?php }, 99 ); // priority 99 = after Ohio's styles

get_header();
?>

<main id="ds-home" class="ds-home">

    <!-- ── HERO ── -->
    <section class="ds-hero" style="background-image:url('<?php echo esc_url( dirtshack_hero_image_url( 'home' ) ); ?>')">
        <div class="ds-hero__overlay"></div>
        <div class="ds-hero__content">
            <p class="ds-hero__tagline">Fueling the <span class="ds-accent">Dirt Biking</span> Culture in India</p>
        </div>
    </section>

    <!-- ── TRUST BAR ── -->
    <div class="ds-trust">
        <div class="ds-trust__inner">
            <div class="ds-trust__item">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                <span><strong>Quality Parts</strong> — tested on Indian trails</span>
            </div>
            <div class="ds-trust__item">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <span><strong>Pan-India Shipping</strong> — 2–7 business days</span>
            </div>
            <div class="ds-trust__item">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span><strong>Secure Checkout</strong> — Razorpay protected</span>
            </div>
            <div class="ds-trust__item">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span><strong>Rider Community</strong> — built by riders</span>
            </div>
        </div>
    </div>

    <!-- ── FEATURED PRODUCTS ── -->
    <?php
    $ds_products = new WP_Query( [
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );

    if ( $ds_products->have_posts() ) :
    ?>
    <section class="ds-products">
        <div class="ds-products__head">
            <h2 class="ds-products__title">Featured Products</h2>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="ds-view-all">View all &rarr;</a>
        </div>
        <div class="ds-products__grid">
        <?php while ( $ds_products->have_posts() ) : $ds_products->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) continue;
        ?>
            <a href="<?php echo esc_url( get_permalink() ); ?>" class="ds-product-card">
                <div class="ds-product-card__img">
                    <?php if ( has_post_thumbnail() ) :
                        the_post_thumbnail( 'woocommerce_thumbnail', [ 'loading' => 'lazy' ] );
                    else : ?>
                        <img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="ds-product-card__body">
                    <h3 class="ds-product-card__name"><?php the_title(); ?></h3>
                    <p class="ds-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
                </div>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
