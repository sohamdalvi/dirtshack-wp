/**
 * DirtShack — "Rider Stories" carousel on product pages (see inc/customer-gallery-instagram.php).
 *
 * Each card holds a post link in data-permalink. A card's Instagram embed is
 * added when the card scrolls into view, so each post's first frame shows with
 * no uploaded thumbnails. Instagram's script loads once, on the first card.
 * Browsers without IntersectionObserver embed every card straight away.
 *
 * Only instagram.com post, reel or tv addresses are used.
 */
( function () {
	'use strict';

	var EMBED_SRC = 'https://www.instagram.com/embed.js';
	var PERMALINK_RE = /^https:\/\/(www\.)?instagram\.com\/(p|reel|tv)\/[A-Za-z0-9_-]+\/?(\?[^\s]*)?$/;
	var scriptRequested = false;

	function processEmbeds() {
		if ( window.instgrm && window.instgrm.Embeds ) {
			window.instgrm.Embeds.process();
		}
	}

	function loadEmbedScript() {
		if ( window.instgrm && window.instgrm.Embeds ) {
			processEmbeds();
			return;
		}
		if ( scriptRequested ) {
			return;
		}
		scriptRequested = true;
		var s = document.createElement( 'script' );
		s.src = EMBED_SRC;
		s.async = true;
		s.onload = processEmbeds;
		document.body.appendChild( s );
	}

	function mount( holder ) {
		if ( holder.getAttribute( 'data-mounted' ) === '1' ) {
			return;
		}
		holder.setAttribute( 'data-mounted', '1' );
		var box = document.createElement( 'blockquote' );
		box.className = 'instagram-media';
		box.setAttribute( 'data-instgrm-permalink', holder.getAttribute( 'data-permalink' ) );
		box.setAttribute( 'data-instgrm-version', '14' );
		holder.appendChild( box );
		loadEmbedScript();
	}

	function boot() {
		var holders = Array.prototype.filter.call( document.querySelectorAll( '[data-ds-rider] .ds-rs__embed' ), function ( el ) {
			return PERMALINK_RE.test( el.getAttribute( 'data-permalink' ) || '' );
		} );
		if ( ! holders.length ) {
			return;
		}
		if ( 'IntersectionObserver' in window ) {
			var io = new IntersectionObserver( function ( entries, observer ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						mount( entry.target );
						observer.unobserve( entry.target );
					}
				} );
			}, { root: holders[0].closest( '.ds-rs__track' ), rootMargin: '0px 200px 0px 200px' } );
			holders.forEach( function ( el ) {
				io.observe( el );
			} );
		} else {
			holders.forEach( mount );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
