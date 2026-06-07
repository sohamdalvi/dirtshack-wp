/**
 * DirtShack — small, cache-safe site behaviours.
 *
 * Loaded site-wide (deferred). Keep this tiny: the homepage HTML is fully
 * page-cached, so anything per-visitor must run here on the client, never in PHP.
 *
 * Currently handles only the dismissible marketplace announcement bar. The
 * no-flash "is it already dismissed?" read happens in a tiny inline <head>
 * script (see dirtshack_xpromo_nofouc_script in functions.php) that toggles the
 * `ds-xpromo-off` class on <html> before first paint, so there is no layout
 * shift. This file only wires up the click-to-dismiss handler.
 */
( function () {
	'use strict';

	var STORAGE_KEY = 'dsXpromoDismissed';

	function init() {
		var bar = document.getElementById( 'ds-xpromo' );
		if ( ! bar ) {
			return;
		}
		var closeBtn = bar.querySelector( '.ds-xpromo__close' );
		if ( ! closeBtn ) {
			return;
		}
		closeBtn.addEventListener( 'click', function () {
			document.documentElement.classList.add( 'ds-xpromo-off' );
			try {
				window.localStorage.setItem( STORAGE_KEY, '1' );
			} catch ( e ) {
				/* private mode / storage disabled — bar just won't persist */
			}
		} );
	}

	// ── YouTube facade: click-to-load lazy embed ──────────────────────────────
	//
	// On the single Braap video page the embed renders as a static "facade"
	// (poster image + play button, see dirtshack_braap_player in PHP). No iframe
	// exists until the visitor interacts — this keeps every page that lists videos
	// (archive grid, homepage) free of heavy live iframes on the shared VPS. On
	// click / Enter / Space we swap in a youtube-nocookie iframe with autoplay.
	function loadFacade( facade ) {
		var id = facade.getAttribute( 'data-ytid' );
		if ( ! id || facade.dataset.dsLoaded === '1' ) {
			return;
		}
		facade.dataset.dsLoaded = '1';
		var iframe = document.createElement( 'iframe' );
		iframe.setAttribute(
			'src',
			'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( id ) +
				'?autoplay=1&rel=0&modestbranding=1'
		);
		iframe.setAttribute( 'title', facade.getAttribute( 'aria-label' ) || 'YouTube video' );
		iframe.setAttribute(
			'allow',
			'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
		);
		iframe.setAttribute( 'allowfullscreen', '' );
		iframe.setAttribute( 'loading', 'eager' );
		facade.innerHTML = '';
		facade.appendChild( iframe );
	}

	function initFacades() {
		var facades = document.querySelectorAll( '.ds-ytfacade[data-ytid]' );
		Array.prototype.forEach.call( facades, function ( facade ) {
			facade.addEventListener( 'click', function () {
				loadFacade( facade );
			} );
			facade.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar' ) {
					e.preventDefault();
					loadFacade( facade );
				}
			} );
		} );
	}

	function boot() {
		init();
		initFacades();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
