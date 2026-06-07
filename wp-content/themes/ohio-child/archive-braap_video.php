<?php
/**
 * DirtShack — Braap Videos archive (and taxonomy listings).
 *
 * Used for the CPT archive at /braap/ and for the braap_type / braap_creator
 * taxonomy term pages. Renders a responsive facade grid (2 col mobile / 3 tablet
 * / 4 desktop, 16px radius) matching the homepage rules. Each card is a static
 * thumbnail + play overlay that LINKS to the single-video page — no live iframes
 * here (cacheable, fast on the shared VPS). No comment forms are rendered.
 *
 * @package ohio-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// The full-bleed "Braap" hero (dirtshack_current_hero() returns it for this
// archive / its taxonomies). Emitted directly because this custom template does
// not call Ohio's page_headline partial that the global hook keys off.
if ( function_exists( 'dirtshack_emit_hero_once' ) ) {
	dirtshack_emit_hero_once();
}

// Heading + intro adapt to the context (main archive vs a taxonomy term).
$ds_braap_heading = 'Braap';
$ds_braap_sub     = 'Original commentary on the best dirt-bike videos on the web.';
if ( is_tax() ) {
	$ds_term = get_queried_object();
	if ( $ds_term && ! is_wp_error( $ds_term ) ) {
		$ds_braap_heading = single_term_title( '', false );
		if ( get_queried_object()->taxonomy === DIRTSHACK_BRAAP_CREATOR ) {
			$ds_braap_heading = 'Videos by ' . $ds_term->name;
		}
		$ds_braap_sub = $ds_term->description ? $ds_term->description : '';
	}
}
?>

<div class="page-container top-offset bottom-offset">
	<section class="ds-braap">
		<div class="ds-braap__inner">
			<header class="ds-braap__head">
				<h1 class="ds-braap__title"><?php echo esc_html( $ds_braap_heading ); ?></h1>
				<?php if ( $ds_braap_sub ) : ?>
					<p class="ds-braap__sub"><?php echo esc_html( $ds_braap_sub ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="ds-braap-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						echo dirtshack_braap_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput — built from esc_* helpers
					endwhile;
					?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => '&larr; Newer',
						'next_text' => 'Older &rarr;',
					)
				);
				?>
			<?php else : ?>
				<p class="ds-braap-empty">No videos here yet — check back soon.</p>
			<?php endif; ?>
		</div>
	</section>
</div>

<?php
get_footer();
