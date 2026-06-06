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

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
