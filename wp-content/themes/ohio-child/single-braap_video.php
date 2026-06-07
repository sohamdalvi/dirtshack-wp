<?php
/**
 * DirtShack — single Braap video.
 *
 * Layout (top → bottom):
 *   1. Click-to-load lazy embed (facade → youtube-nocookie iframe on click)
 *   2. Title + taxonomy tags + creator
 *   3. Original written commentary (the post body — the page's primary content)
 *   4. Native WordPress comments (enabled on single video pages ONLY)
 *
 * No big hero here — the embed is the top of the page. We don't render Ohio's
 * page_headline partial, so no default banner appears.
 *
 * @package ohio-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$ds_post_id = get_the_ID();
	$ds_player  = dirtshack_braap_player( $ds_post_id );
	$ds_type    = dirtshack_braap_type_tag( $ds_post_id );
	$ds_creators = get_the_terms( $ds_post_id, DIRTSHACK_BRAAP_CREATOR );
	?>

	<div class="page-container top-offset bottom-offset">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'ds-braap-single' ); ?>>

			<?php if ( $ds_player ) : ?>
				<?php echo $ds_player; // phpcs:ignore WordPress.Security.EscapeOutput — built from esc_* in helper ?>
			<?php elseif ( has_post_thumbnail() ) : ?>
				<div class="ds-ytfacade" style="cursor:default;">
					<span class="ds-ytfacade__thumb"><?php the_post_thumbnail( 'large' ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $ds_type ) : ?>
				<div class="ds-braap-single__tags"><?php echo $ds_type; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>

			<h1 class="ds-braap-single__title"><?php the_title(); ?></h1>

			<p class="ds-braap-single__meta"><?php echo esc_html( get_the_date() ); ?></p>

			<div class="ds-braap-single__commentary">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ohio-child' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php if ( ! empty( $ds_creators ) && ! is_wp_error( $ds_creators ) ) : ?>
				<p class="ds-braap-creator">
					Creator:
					<?php
					$ds_links = array();
					foreach ( $ds_creators as $ds_c ) {
						$ds_l = get_term_link( $ds_c );
						$ds_links[] = is_wp_error( $ds_l )
							? esc_html( $ds_c->name )
							: '<a href="' . esc_url( $ds_l ) . '">' . esc_html( $ds_c->name ) . '</a>';
					}
					echo wp_kses_post( implode( ', ', $ds_links ) );
					?>
				</p>
			<?php endif; ?>

		</article>

		<?php
		// Native comments — single video pages only.
		if ( comments_open() || get_comments_number() ) {
			?>
			<div class="ds-braap-single ds-braap-single--comments">
				<?php comments_template(); ?>
			</div>
			<?php
		}
		?>
	</div>

	<?php
endwhile;

get_footer();
