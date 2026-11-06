/**
 * DirtShack — small, cache-safe site behaviours.
 *
 * Loaded site-wide (deferred). Keep this tiny: the homepage HTML is fully
 * page-cached, so anything per-visitor must run here on the client, never in PHP.
 *
 * Handles the dismissible marketplace announcement bar, the click-to-load
 * YouTube facades, and the product page's "Watch the video guide" link. For the
 * bar, the no-flash "is it already dismissed?" read happens in a tiny inline
 * <head> script (see dirtshack_xpromo_nofouc_script in functions.php) that
 * toggles the `ds-xpromo-off` class on <html> before first paint, so there is no
 * layout shift. This file only wires up the click-to-dismiss handler.
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
	// On the single Braap video page and the product "Video guide" tab the embed
	// renders as a static "facade" (poster image + play button, see
	// dirtshack_yt_facade in PHP). No iframe
	// exists until the visitor interacts — this keeps every page that lists videos
	// (archive grid, homepage) free of heavy live iframes on the shared VPS. On
	// click / Enter / Space we swap in a youtube-nocookie iframe with autoplay.
	function loadFacade( facade ) {
		var id = facade.getAttribute( 'data-ytid' );
		if ( ! id || facade.dataset.dsLoaded === '1' ) {
			return;
		}
		facade.dataset.dsLoaded = '1';
		pauseFacades( facade );
		var iframe = document.createElement( 'iframe' );
		// enablejsapi lets pauseFacades() talk to the player.
		iframe.setAttribute(
			'src',
			'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( id ) +
				'?autoplay=1&rel=0&modestbranding=1&enablejsapi=1'
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

	// Pause every playing facade video except the one in `except` (may be null),
	// so two videos never play at once, or one keeps talking from a hidden tab.
	function pauseFacades( except ) {
		var players = document.querySelectorAll( '.ds-ytfacade[data-ds-loaded="1"] iframe' );
		Array.prototype.forEach.call( players, function ( iframe ) {
			if ( except && except.contains( iframe ) ) {
				return;
			}
			iframe.contentWindow.postMessage( '{"event":"command","func":"pauseVideo","args":""}', '*' );
		} );
	}

	// ── Product video guide tab (see inc/product-video.php) ───────────────────
	//
	// "Watch the video guide" in the product summary opens Ohio's "Video guide"
	// tab and scrolls to the tabs. Without the tabs it stays a plain #anchor.
	function initVideoGuide() {
		var tab = document.querySelector( '.single-product-tabs .tabs-nav-link[data-ohio-tab="ds_video"]' );
		if ( ! tab ) {
			return;
		}
		Array.prototype.forEach.call( document.querySelectorAll( '.ds-video-jump' ), function ( link ) {
			link.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				tab.click();
				tab.closest( '.single-product-tabs' ).scrollIntoView( { behavior: 'smooth', block: 'start' } );
			} );
		} );
		Array.prototype.forEach.call( document.querySelectorAll( '.single-product-tabs .tabs-nav-link' ), function ( other ) {
			if ( other !== tab ) {
				other.addEventListener( 'click', function () {
					pauseFacades( null );
				} );
			}
		} );
	}

	function boot() {
		init();
		initFacades();
		initVideoGuide();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
