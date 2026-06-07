<?php
/**
 * DirtShack — "Braap" Videos custom post type.
 *
 * A video library that is SEPARATE from the blog: each entry is an original
 * written commentary (the post body) about a YouTube video. The video's URL
 * lives in post meta and the embed is rendered from that meta — never pasted
 * into the editor body — so the body stays purely the author's words.
 *
 * Performance is the headline constraint (shared Bluehost VPS): we NEVER render
 * live YouTube iframes in the archive grid or the homepage Braap section. Those
 * surfaces show a static, lazy-loaded thumbnail "facade" (a play overlay on the
 * poster image) and link to the single-video page. Only the single-video page
 * loads an iframe, and even there it is click-to-load (a facade that injects a
 * youtube-nocookie iframe on interaction — see assets/js/ds.js). All markup is
 * deterministic and page-cacheable; the only client-side bit is the click.
 *
 * Everything here is self-contained in the child theme (no ACF field config in
 * the DB), so it travels with the deployed theme code.
 *
 * @package ohio-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DIRTSHACK_BRAAP_CPT       = 'braap_video';
const DIRTSHACK_BRAAP_TYPE_TAX  = 'braap_type';      // hierarchical: motocross / enduro / technique / trails
const DIRTSHACK_BRAAP_CREATOR   = 'braap_creator';   // non-hierarchical: per-creator listing
const DIRTSHACK_BRAAP_META      = '_braap_youtube_url';

// ─── Register the CPT + taxonomies ───────────────────────────────────────────

add_action( 'init', 'dirtshack_register_braap_cpt' );
function dirtshack_register_braap_cpt() {

	register_post_type(
		DIRTSHACK_BRAAP_CPT,
		array(
			'labels'       => array(
				'name'                  => 'Braap Videos',
				'singular_name'         => 'Video',
				'menu_name'             => 'Braap',
				'add_new'               => 'Add Video',
				'add_new_item'          => 'Add New Video',
				'edit_item'             => 'Edit Video',
				'new_item'              => 'New Video',
				'view_item'             => 'View Video',
				'view_items'            => 'View Videos',
				'search_items'          => 'Search Videos',
				'not_found'             => 'No videos found',
				'not_found_in_trash'    => 'No videos found in Trash',
				'all_items'             => 'All Videos',
				'archives'              => 'Video Archives',
				'featured_image'        => 'Poster Image',
				'set_featured_image'    => 'Set poster image',
				'remove_featured_image' => 'Remove poster image',
				'use_featured_image'    => 'Use as poster image',
			),
			'public'       => true,
			'has_archive'  => 'braap',                       // archive at /braap/
			'rewrite'      => array(
				'slug'       => 'braap',
				'with_front' => false,
			),
			'menu_icon'    => 'dashicons-video-alt3',
			'menu_position'=> 5,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt' ),
			'show_in_rest' => true,                          // block editor for the commentary body
			'taxonomies'   => array( DIRTSHACK_BRAAP_TYPE_TAX, DIRTSHACK_BRAAP_CREATOR ),
		)
	);

	// Video type — hierarchical (motocross / enduro / technique / trails …).
	register_taxonomy(
		DIRTSHACK_BRAAP_TYPE_TAX,
		DIRTSHACK_BRAAP_CPT,
		array(
			'labels'            => array(
				'name'          => 'Video Types',
				'singular_name' => 'Video Type',
				'menu_name'     => 'Types',
				'all_items'     => 'All Types',
				'edit_item'     => 'Edit Type',
				'add_new_item'  => 'Add New Type',
				'search_items'  => 'Search Types',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'braap-type',
				'with_front' => false,
			),
		)
	);

	// Creator — non-hierarchical so a creator's videos can be listed together.
	register_taxonomy(
		DIRTSHACK_BRAAP_CREATOR,
		DIRTSHACK_BRAAP_CPT,
		array(
			'labels'            => array(
				'name'          => 'Creators',
				'singular_name' => 'Creator',
				'menu_name'     => 'Creators',
				'all_items'     => 'All Creators',
				'edit_item'     => 'Edit Creator',
				'add_new_item'  => 'Add New Creator',
				'search_items'  => 'Search Creators',
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'braap-creator',
				'with_front' => false,
			),
		)
	);

	// YouTube URL meta — exposed to REST so the block editor / future tooling can
	// read it; sanitised as a URL; only editors of the post may write it.
	register_post_meta(
		DIRTSHACK_BRAAP_CPT,
		DIRTSHACK_BRAAP_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}

// ─── Admin: YouTube URL meta box ─────────────────────────────────────────────

add_action( 'add_meta_boxes', 'dirtshack_braap_add_meta_box' );
function dirtshack_braap_add_meta_box() {
	add_meta_box(
		'dirtshack_braap_video',
		'YouTube Video',
		'dirtshack_braap_meta_box_cb',
		DIRTSHACK_BRAAP_CPT,
		'side',
		'high'
	);
}

function dirtshack_braap_meta_box_cb( $post ) {
	wp_nonce_field( 'dirtshack_braap_save', 'dirtshack_braap_nonce' );
	$url = get_post_meta( $post->ID, DIRTSHACK_BRAAP_META, true );
	$id  = dirtshack_braap_youtube_id( $url );
	?>
	<p>
		<label for="dirtshack_braap_youtube_url"><strong>YouTube URL</strong></label>
		<input
			type="url"
			id="dirtshack_braap_youtube_url"
			name="dirtshack_braap_youtube_url"
			value="<?php echo esc_attr( $url ); ?>"
			placeholder="https://www.youtube.com/watch?v=…"
			style="width:100%;"
		/>
	</p>
	<p class="description">
		Paste the full YouTube link (watch, youtu.be, Shorts or embed). The embed is
		rendered from this URL — don't paste it into the body.
	</p>
	<?php if ( $id ) : ?>
		<p style="margin-top:8px;">
			<img src="<?php echo esc_url( dirtshack_braap_thumb_url( $id ) ); ?>" alt="" style="width:100%;height:auto;border-radius:6px;" />
			<span class="description">Detected video ID: <code><?php echo esc_html( $id ); ?></code></span>
		</p>
	<?php elseif ( $url ) : ?>
		<p style="color:#b32d2e;"><strong>Couldn't read a video ID from that URL.</strong> Double-check the link.</p>
	<?php endif; ?>
	<?php
}

add_action( 'save_post_' . DIRTSHACK_BRAAP_CPT, 'dirtshack_braap_save_meta', 10, 2 );
function dirtshack_braap_save_meta( $post_id, $post ) {
	if ( ! isset( $_POST['dirtshack_braap_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dirtshack_braap_nonce'] ) ), 'dirtshack_braap_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['dirtshack_braap_youtube_url'] ) ? wp_unslash( $_POST['dirtshack_braap_youtube_url'] ) : '';
	$url = esc_url_raw( trim( $raw ) );

	if ( $url ) {
		update_post_meta( $post_id, DIRTSHACK_BRAAP_META, $url );
	} else {
		delete_post_meta( $post_id, DIRTSHACK_BRAAP_META );
	}
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Pull the 11-char video ID out of any common YouTube URL form
 * (watch?v=, youtu.be/, /embed/, /shorts/, /v/) or a bare ID.
 */
function dirtshack_braap_youtube_id( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( preg_match( '~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|v/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		return $m[1];
	}
	if ( preg_match( '~^[A-Za-z0-9_-]{11}$~', $url ) ) {
		return $url; // already a bare ID
	}
	return '';
}

/**
 * The YouTube-hosted thumbnail for a video ID. hqdefault (480×360) is the size
 * that is guaranteed to exist for every video (maxres often 404s).
 */
function dirtshack_braap_thumb_url( $video_id ) {
	return 'https://i.ytimg.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg';
}

/**
 * Poster image for a video post: the featured image if one is set, otherwise the
 * YouTube thumbnail. Returns an array( 'html' => <img…>, 'has_thumb' => bool ).
 */
function dirtshack_braap_poster_html( $post_id, $img_size = 'medium_large' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $img_size, array( 'loading' => 'lazy', 'alt' => '' ) );
	}
	$vid = dirtshack_braap_youtube_id( get_post_meta( $post_id, DIRTSHACK_BRAAP_META, true ) );
	if ( $vid ) {
		return sprintf(
			'<img src="%s" alt="" loading="lazy" width="480" height="360" />',
			esc_url( dirtshack_braap_thumb_url( $vid ) )
		);
	}
	return '';
}

/**
 * The primary "Video Type" term for a post (first assigned) as a chip.
 *
 * @param int  $post_id Post.
 * @param bool $linked  Whether to render the chip as a link to the term archive.
 *                      MUST be false when the chip sits inside a card that is
 *                      itself an <a> — nested anchors are invalid HTML and the
 *                      parser will break the card layout. Defaults to a link
 *                      (used standalone on the single-video page).
 */
function dirtshack_braap_type_tag( $post_id, $linked = true ) {
	$terms = get_the_terms( $post_id, DIRTSHACK_BRAAP_TYPE_TAX );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}
	$t    = $terms[0];
	$name = esc_html( $t->name );
	if ( ! $linked ) {
		return sprintf( '<span class="ds-braap-tag">%s</span>', $name );
	}
	$link = get_term_link( $t );
	if ( is_wp_error( $link ) ) {
		return sprintf( '<span class="ds-braap-tag">%s</span>', $name );
	}
	return sprintf( '<a class="ds-braap-tag" href="%s">%s</a>', esc_url( $link ), $name );
}

/**
 * A static facade card for the archive grid / homepage section. The whole card
 * is a link to the single-video page; the play overlay is purely an affordance
 * (no iframe is loaded here). Cacheable, no JS.
 */
function dirtshack_braap_card( $post_id ) {
	$poster = dirtshack_braap_poster_html( $post_id, 'medium_large' );
	$tag    = dirtshack_braap_type_tag( $post_id, false ); // non-link chip: the card itself is an <a>
	ob_start();
	?>
	<a class="ds-braap-card" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<span class="ds-braap-card__media">
			<?php echo $poster; // phpcs:ignore WordPress.Security.EscapeOutput — img built from esc_url/get_the_post_thumbnail ?>
			<span class="ds-braap-card__play" aria-hidden="true">
				<svg viewBox="0 0 68 48" width="54" height="38"><path class="ds-yt-bg" d="M66.5 7.7a8 8 0 0 0-5.6-5.7C56 .6 34 .6 34 .6s-22 0-26.9 1.4a8 8 0 0 0-5.6 5.7A83 83 0 0 0 .5 24a83 83 0 0 0 1 16.3 8 8 0 0 0 5.6 5.7C12 47.4 34 47.4 34 47.4s22 0 26.9-1.4a8 8 0 0 0 5.6-5.7A83 83 0 0 0 67.5 24a83 83 0 0 0-1-16.3z"/><path d="M27 34V14l18 10z" fill="#fff"/></svg>
			</span>
		</span>
		<span class="ds-braap-card__body">
			<?php if ( $tag ) : ?>
				<span class="ds-braap-card__tags"><?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php endif; ?>
			<span class="ds-braap-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
		</span>
	</a>
	<?php
	return ob_get_clean();
}

/**
 * The click-to-load facade used on the SINGLE video page. Renders the poster +
 * play button with the video ID in a data attribute; assets/js/ds.js swaps in a
 * youtube-nocookie iframe on click/keyboard. If there is no video ID, nothing
 * is output.
 */
function dirtshack_braap_player( $post_id ) {
	$vid = dirtshack_braap_youtube_id( get_post_meta( $post_id, DIRTSHACK_BRAAP_META, true ) );
	if ( ! $vid ) {
		return '';
	}
	// Poster: prefer the featured image, else the YouTube thumbnail.
	$poster = dirtshack_braap_poster_html( $post_id, 'large' );
	if ( '' === $poster ) {
		$poster = sprintf( '<img src="%s" alt="" loading="lazy" />', esc_url( dirtshack_braap_thumb_url( $vid ) ) );
	}
	$label = sprintf( 'Play video: %s', get_the_title( $post_id ) );
	ob_start();
	?>
	<div class="ds-ytfacade" data-ytid="<?php echo esc_attr( $vid ); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr( $label ); ?>">
		<span class="ds-ytfacade__thumb"><?php echo $poster; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="ds-ytfacade__play" aria-hidden="true">
			<svg viewBox="0 0 68 48" width="68" height="48"><path class="ds-yt-bg" d="M66.5 7.7a8 8 0 0 0-5.6-5.7C56 .6 34 .6 34 .6s-22 0-26.9 1.4a8 8 0 0 0-5.6 5.7A83 83 0 0 0 .5 24a83 83 0 0 0 1 16.3 8 8 0 0 0 5.6 5.7C12 47.4 34 47.4 34 47.4s22 0 26.9-1.4a8 8 0 0 0 5.6-5.7A83 83 0 0 0 67.5 24a83 83 0 0 0-1-16.3z"/><path d="M27 34V14l18 10z" fill="#fff"/></svg>
		</span>
	</div>
	<?php
	return ob_get_clean();
}

// ─── Front-end CSS (inline, wp_head 9999) ────────────────────────────────────
//
// Same rationale as the rest of the child theme: the enqueued style.css is cached
// with a static ?ver on live, and Ohio's aggressive CSS often wins source-order
// ties — so anything that must reliably reach live and beat Ohio is injected
// inline late. These are all new .ds-braap-*/.ds-ytfacade classes (no Ohio
// collision), printed only on the surfaces that use them.
add_action( 'wp_head', 'dirtshack_braap_css', 9999 );
function dirtshack_braap_css() {
	$on_braap = ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( DIRTSHACK_BRAAP_CPT ) )
		|| is_singular( DIRTSHACK_BRAAP_CPT )
		|| is_tax( array( DIRTSHACK_BRAAP_TYPE_TAX, DIRTSHACK_BRAAP_CREATOR ) )
		|| is_front_page(); // homepage Braap section reuses the card styles
	if ( ! $on_braap ) {
		return;
	}
	?>
<style id="ds-braap-css">
/* Brand tokens (self-contained fallbacks) */
.ds-braap { --g:#C4E000; --d:#111; --bd:#e5e5e5; --r:16px; --max:1280px; --padx:clamp(1rem,5vw,3rem); }

/* ── Archive wrapper ── */
.ds-braap { background:#fff; }
.ds-braap__inner { max-width:var(--max); margin:0 auto; padding:40px var(--padx); box-sizing:border-box; }
.ds-braap__head { margin:0 0 1.5rem; }
.ds-braap__title { font-size:clamp(1.4rem,4vw,1.9rem); font-weight:800; letter-spacing:-.02em; margin:0; color:#111; line-height:1.15; }
.ds-braap__sub { margin:.5rem 0 0; color:#666; font-size:.95rem; }

/* ── Responsive grid: 2 col mobile / 3 tablet / 4 desktop ── */
.ds-braap-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; list-style:none; margin:0; padding:0; }

/* ── Card (static facade → links to single) ── */
.ds-braap-card { display:flex; flex-direction:column; background:#fff; border:1px solid var(--bd,#e5e5e5); border-radius:var(--r,16px); overflow:hidden; text-decoration:none; color:#111; transition:box-shadow .2s, transform .2s; }
.ds-braap-card:hover { box-shadow:0 8px 28px rgba(0,0,0,.10); transform:translateY(-2px); }
.ds-braap-card__media { position:relative; display:block; width:100%; aspect-ratio:16/9; overflow:hidden; background:#000; }
.ds-braap-card__media img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .35s; }
.ds-braap-card:hover .ds-braap-card__media img { transform:scale(1.05); }
.ds-braap-card__play { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; }
.ds-braap-card__play svg { filter:drop-shadow(0 2px 6px rgba(0,0,0,.45)); transition:transform .2s; }
.ds-braap-card:hover .ds-braap-card__play svg { transform:scale(1.08); }
.ds-yt-bg { fill:#212121; fill-opacity:.85; }
.ds-braap-card:hover .ds-yt-bg, .ds-ytfacade:hover .ds-yt-bg { fill:#C4E000; fill-opacity:1; }
.ds-braap-card:hover .ds-braap-card__play svg path[fill="#fff"] { fill:#111; }
.ds-braap-card__body { padding:.85rem 1rem 1rem; display:flex; flex-direction:column; gap:.4rem; flex:1; }
.ds-braap-card__tags { display:flex; flex-wrap:wrap; gap:.35rem; }
.ds-braap-tag { display:inline-block; font-size:.66rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:#111; background:var(--g,#C4E000); border-radius:999px; padding:.2rem .6rem; text-decoration:none; line-height:1.3; }
.ds-braap-tag:hover { opacity:.85; }
.ds-braap-card__title { font-size:.92rem; font-weight:700; line-height:1.3; color:#111; margin:0; }

/* ── Empty state ── */
.ds-braap-empty { padding:2rem 0; color:#666; font-size:1rem; }

/* ── Single video page ── */
.ds-braap-single { max-width:900px; margin:0 auto; padding:2rem var(--padx,1.5rem) 3rem; box-sizing:border-box; }
.ds-braap-single__tags { display:flex; flex-wrap:wrap; gap:.4rem; margin:0 0 .75rem; }
.ds-braap-single__title { font-size:clamp(1.5rem,5vw,2.2rem); font-weight:800; letter-spacing:-.02em; line-height:1.15; margin:0 0 1.25rem; color:#111; }
.ds-braap-single__meta { font-size:.8rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#888; margin:0 0 1.25rem; }
.ds-braap-single__commentary { font-size:1.02rem; line-height:1.7; color:#222; margin-top:1.75rem; }
.ds-braap-single__commentary p { margin:0 0 1.1rem; }
.ds-braap-single__commentary img { max-width:100%; height:auto; border-radius:10px; }
.ds-braap-creator { margin-top:1.5rem; font-size:.85rem; color:#555; }
.ds-braap-creator a { color:#111; font-weight:700; border-bottom:2px solid var(--g,#C4E000); text-decoration:none; }

/* ── Click-to-load facade (single page) ── */
.ds-ytfacade { position:relative; display:block; width:100%; aspect-ratio:16/9; background:#000; border-radius:var(--r,16px); overflow:hidden; cursor:pointer; }
.ds-ytfacade__thumb, .ds-ytfacade__thumb img { display:block; width:100%; height:100%; }
.ds-ytfacade__thumb img { object-fit:cover; transition:transform .35s, opacity .2s; }
.ds-ytfacade:hover .ds-ytfacade__thumb img { transform:scale(1.03); }
.ds-ytfacade__play { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; }
.ds-ytfacade__play svg { filter:drop-shadow(0 2px 8px rgba(0,0,0,.5)); transition:transform .2s; }
.ds-ytfacade:hover .ds-ytfacade__play svg { transform:scale(1.08); }
.ds-ytfacade:focus-visible { outline:3px solid #C4E000; outline-offset:3px; }
/* The injected iframe fills the facade box */
.ds-ytfacade iframe { position:absolute; inset:0; width:100%; height:100%; border:0; }

/* ── Tablet ≥768px: 3 col ── */
@media (min-width:768px) {
	.ds-braap__inner { padding:60px var(--padx); }
	.ds-braap-grid { grid-template-columns:repeat(3,1fr); gap:1.25rem; }
}
/* ── Desktop ≥1025px: 4 col ── */
@media (min-width:1025px) {
	.ds-braap__inner { padding:80px var(--padx); }
	.ds-braap-grid { grid-template-columns:repeat(4,1fr); gap:1.5rem; }
}
</style>
	<?php
}
