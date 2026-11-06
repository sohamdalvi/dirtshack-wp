/**
 * DirtShack — Customer Gallery (see inc/customer-gallery.php).
 *
 * The photos and their JSON lists live on the Customer Gallery host
 * (Cloudflare R2, filled from Facebook by workers/customer-gallery), not on
 * this server. Pages carry an empty, hidden container that this script fills:
 *
 *   data-ds-cgallery="product"  <host>/products/<slug>.json → the product page
 *                               thumbnail strip (between image and description)
 *   data-ds-cgallery="all"      <host>/all.json → the [customer_gallery] page,
 *                               with product filters
 *
 * A product with no photos has no JSON file (404), and then nothing shows.
 * Everything read from the JSON goes into the page as text or as checked
 * addresses, never as HTML. Thumbnails link to the large photo, so without
 * <dialog> support a click simply opens the image.
 */
( function () {
	'use strict';

	var PAGE_SIZE = 24; // Gallery page: thumbnails per "Show more".
	var ICONS = {
		close: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none"/></svg>',
		prev: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
		next: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>'
	};

	// ── Helpers ──────────────────────────────────────────────────────────────

	function getJson( url ) {
		return fetch( url, { credentials: 'omit' } ).then( function ( res ) {
			if ( res.status === 404 ) {
				return null;
			}
			if ( ! res.ok ) {
				throw new Error( 'HTTP ' + res.status );
			}
			return res.json();
		} );
	}

	function startsWith( value, prefix ) {
		return typeof value === 'string' && value.slice( 0, prefix.length ) === prefix;
	}

	// Photos whose files really are on the gallery host; anything else is dropped.
	function cleanPhotos( list, source ) {
		if ( ! Array.isArray( list ) ) {
			return [];
		}
		return list.filter( function ( p ) {
			return p && p.thumb && p.large &&
				startsWith( p.thumb.url, source + '/' ) && startsWith( p.large.url, source + '/' );
		} );
	}

	function el( tag, className, text ) {
		var node = document.createElement( tag );
		if ( className ) {
			node.className = className;
		}
		if ( text !== undefined && text !== null ) {
			node.textContent = text;
		}
		return node;
	}

	function plural( n, one, many ) {
		return n === 1 ? one : n + ' ' + many;
	}

	// The caption's first line, shortened, or a fallback.
	function altText( photo, fallback ) {
		var line = String( photo.caption || '' ).split( '\n' )[ 0 ].trim();
		if ( ! line ) {
			return fallback;
		}
		return line.length > 120 ? line.slice( 0, 117 ) + '…' : line;
	}

	function thumbItem( photo, index, alt ) {
		var li = el( 'li', 'ds-cgallery__item' );
		var link = el( 'a', 'ds-cgallery__thumb' );
		link.href = photo.large.url;
		link.setAttribute( 'data-index', String( index ) );
		var img = el( 'img' );
		img.src = photo.thumb.url;
		if ( photo.thumb.width && photo.thumb.height ) {
			img.width = photo.thumb.width;
			img.height = photo.thumb.height;
		}
		img.alt = alt;
		img.loading = 'lazy';
		img.decoding = 'async';
		link.appendChild( img );
		li.appendChild( link );
		return li;
	}

	function canUseDialog() {
		return typeof window.HTMLDialogElement === 'function' &&
			typeof document.createElement( 'dialog' ).showModal === 'function';
	}

	// ── Lightbox ─────────────────────────────────────────────────────────────

	var box = null;

	function iconButton( className, label, icon ) {
		var btn = el( 'button', 'ds-cglb__btn ' + className );
		btn.type = 'button';
		btn.setAttribute( 'aria-label', label );
		btn.innerHTML = icon; // Fixed markup from ICONS above.
		return btn;
	}

	function getBox() {
		if ( box ) {
			return box;
		}
		var dialog = el( 'dialog', 'ds-cglb' );
		dialog.setAttribute( 'aria-label', 'Customer Gallery photo' );
		var close = iconButton( 'ds-cglb__close', 'Close', ICONS.close );
		var prev = iconButton( 'ds-cglb__prev', 'Previous photo', ICONS.prev );
		var next = iconButton( 'ds-cglb__next', 'Next photo', ICONS.next );
		var figure = el( 'figure', 'ds-cglb__figure' );
		var img = el( 'img', 'ds-cglb__img' );
		var caption = el( 'figcaption' );
		var text = el( 'p', 'ds-cglb__text' );
		var meta = el( 'p', 'ds-cglb__meta' );
		var products = el( 'span', 'ds-cglb__products' );
		var count = el( 'span', 'ds-cglb__count' );
		meta.appendChild( products );
		meta.appendChild( count );
		caption.appendChild( text );
		caption.appendChild( meta );
		figure.appendChild( img );
		figure.appendChild( caption );
		dialog.appendChild( figure );
		dialog.appendChild( close );
		dialog.appendChild( prev );
		dialog.appendChild( next );
		document.body.appendChild( dialog );

		box = {
			dialog: dialog, img: img, text: text, products: products, count: count,
			prev: prev, next: next, photos: [], index: 0, opener: null, describe: null
		};

		// Release the page scroll straight away on every way out; the dialog's
		// "close" event (below) can arrive a little later.
		function unlock() {
			document.documentElement.classList.remove( 'ds-cglb-open' );
		}
		function shut() {
			unlock();
			dialog.close();
		}
		close.addEventListener( 'click', shut );
		dialog.addEventListener( 'cancel', unlock ); // Esc
		prev.addEventListener( 'click', function () {
			show( box.index - 1 );
		} );
		next.addEventListener( 'click', function () {
			show( box.index + 1 );
		} );
		// A click on the dark area around the photo closes it.
		dialog.addEventListener( 'click', function ( e ) {
			if ( e.target === dialog || e.target === figure ) {
				shut();
			}
		} );
		dialog.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowLeft' ) {
				show( box.index - 1 );
			} else if ( e.key === 'ArrowRight' ) {
				show( box.index + 1 );
			}
		} );
		var startX = null;
		dialog.addEventListener( 'pointerdown', function ( e ) {
			startX = e.pointerType === 'mouse' ? null : e.clientX;
		} );
		dialog.addEventListener( 'pointerup', function ( e ) {
			if ( startX === null || box.photos.length < 2 ) {
				return;
			}
			var dx = e.clientX - startX;
			startX = null;
			if ( Math.abs( dx ) > 50 ) {
				show( box.index + ( dx < 0 ? 1 : -1 ) );
			}
		} );
		dialog.addEventListener( 'close', function () {
			unlock();
			var opener = typeof box.opener === 'function' ? box.opener( box.index ) : box.opener;
			if ( opener && document.contains( opener ) ) {
				opener.focus();
			}
		} );
		return box;
	}

	function show( index ) {
		var n = box.photos.length;
		box.index = ( ( index % n ) + n ) % n;
		var photo = box.photos[ box.index ];
		var info = box.describe( photo );

		box.img.removeAttribute( 'src' ); // Don't show the previous photo while this one loads.
		if ( photo.large.width && photo.large.height ) {
			box.img.width = photo.large.width;
			box.img.height = photo.large.height;
		} else {
			box.img.removeAttribute( 'width' );
			box.img.removeAttribute( 'height' );
		}
		box.img.alt = info.alt;
		box.img.src = photo.large.url;

		box.text.textContent = photo.caption || '';
		box.text.hidden = ! photo.caption;

		box.products.textContent = '';
		info.products.forEach( function ( p, i ) {
			if ( i ) {
				box.products.appendChild( document.createTextNode( ', ' ) );
			}
			var link = el( 'a', '', p.name );
			link.href = p.url;
			box.products.appendChild( link );
		} );
		box.products.hidden = ! info.products.length;

		box.count.textContent = n > 1 ? ( box.index + 1 ) + ' / ' + n : '';
		box.prev.hidden = box.next.hidden = n < 2;

		// Warm the cache for the neighbours.
		if ( n > 1 ) {
			[ box.index + 1, box.index - 1 + n ].forEach( function ( i ) {
				( new Image() ).src = box.photos[ i % n ].large.url;
			} );
		}
	}

	/**
	 * @param {Array}    photos
	 * @param {number}   index
	 * @param {Element|Function} opener Gets focus back on close; a function gets the current index.
	 * @param {Function} describe photo → {alt, products: [{name, url}]}
	 */
	function openBox( photos, index, opener, describe ) {
		var b = getBox();
		b.photos = photos;
		b.opener = opener;
		b.describe = describe;
		show( index );
		if ( ! b.dialog.open ) {
			document.documentElement.classList.add( 'ds-cglb-open' );
			b.dialog.showModal();
		}
	}

	// Thumbnail clicks inside `grid` open the lightbox on photos().
	function wireGrid( grid, photos, describe ) {
		grid.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( 'a.ds-cgallery__thumb[data-index]' );
			if ( ! link || ! canUseDialog() || e.ctrlKey || e.metaKey || e.shiftKey ) {
				return;
			}
			e.preventDefault();
			// On close, focus (and scroll into view) the thumbnail of the photo the
			// visitor ended on, not just the one they opened.
			openBox( photos(), parseInt( link.getAttribute( 'data-index' ), 10 ) || 0, function ( index ) {
				return grid.querySelector( 'a.ds-cgallery__thumb[data-index="' + index + '"]' ) || link;
			}, describe );
		} );
	}

	// ── Product page ─────────────────────────────────────────────────────────

	// Arrow buttons beside a sideways-scrolling strip: shown only when the
	// photos overflow, and greyed out at either end.
	function wireStrip( track, prev, next ) {
		function update() {
			var max = track.scrollWidth - track.clientWidth;
			prev.hidden = next.hidden = max <= 1;
			prev.disabled = track.scrollLeft <= 1;
			next.disabled = track.scrollLeft >= max - 1;
		}
		function step( dir ) {
			track.scrollBy( { left: dir * Math.max( track.clientWidth * 0.8, 120 ), behavior: 'smooth' } );
		}
		prev.addEventListener( 'click', function () {
			step( -1 );
		} );
		next.addEventListener( 'click', function () {
			step( 1 );
		} );
		track.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	}

	function initProduct( section ) {
		var source = section.getAttribute( 'data-source' );
		var slug = section.getAttribute( 'data-product' );
		var name = section.getAttribute( 'data-name' ) || '';
		if ( ! source || ! slug ) {
			return;
		}
		getJson( source + '/products/' + encodeURIComponent( slug ) + '.json' ).then( function ( data ) {
			var photos = cleanPhotos( data && data.photos, source );
			if ( ! photos.length ) {
				return; // No photos: the whole section stays hidden.
			}
			var track = section.querySelector( '.ds-cgallery__track' );
			var count = section.querySelector( '.ds-cgallery__count' );
			var fallbackAlt = 'Customer photo: ' + name;

			photos.forEach( function ( photo, i ) {
				track.appendChild( thumbItem( photo, i, altText( photo, fallbackAlt ) ) );
			} );
			if ( count ) {
				count.textContent = '(' + plural( photos.length, '1 photo', 'photos' ) + ')';
			}
			wireGrid( track, function () {
				return photos;
			}, function ( photo ) {
				return { alt: altText( photo, fallbackAlt ), products: [] };
			} );

			( section.closest( '.ds-cgallery-wrap' ) || section ).hidden = false;
			wireStrip( track, section.querySelector( '.ds-cgallery__nav--prev' ), section.querySelector( '.ds-cgallery__nav--next' ) );
		} ).catch( function () {
			// Gallery host unreachable: the section simply stays hidden.
		} );
	}

	// ── Gallery page ([customer_gallery]) ────────────────────────────────────

	function initAll( root ) {
		var source = root.getAttribute( 'data-source' );
		var shop = root.getAttribute( 'data-shop' ) || '';
		var grid = root.querySelector( '.ds-cgallery__grid' );
		var more = root.querySelector( '.ds-cgallery__more' );
		var filters = root.querySelector( '.ds-cgallery__filters' );
		var status = root.querySelector( '.ds-cgallery__status' );
		if ( ! source || ! grid ) {
			return;
		}

		function done( message ) {
			root.removeAttribute( 'aria-busy' );
			status.textContent = message || '';
			status.hidden = ! message;
		}

		getJson( source + '/all.json' ).then( function ( data ) {
			var photos = cleanPhotos( data && data.photos, source );
			var catalog = ( data && data.products ) || {};
			if ( ! photos.length ) {
				done( 'No photos yet. Check back soon!' );
				return;
			}

			// Only products with a name and a link on this shop are shown.
			function product( slug ) {
				var p = catalog[ slug ];
				return p && typeof p.name === 'string' && startsWith( p.url, shop ) ? { slug: slug, name: p.name, url: p.url } : null;
			}
			// The product being filtered on comes first.
			function productsOf( photo ) {
				var ps = ( Array.isArray( photo.products ) ? photo.products : [] ).map( product ).filter( Boolean );
				return ps.sort( function ( a, b ) {
					return ( b.slug === current ) - ( a.slug === current );
				} );
			}

			var counts = {};
			photos.forEach( function ( photo ) {
				productsOf( photo ).forEach( function ( p ) {
					counts[ p.slug ] = ( counts[ p.slug ] || 0 ) + 1;
				} );
			} );
			var slugs = Object.keys( counts ).sort( function ( a, b ) {
				return catalog[ a ].name.localeCompare( catalog[ b ].name );
			} );

			var current = '';
			var list = photos;
			var shown = 0;
			var chips = [];

			function pageItem( photo, index ) {
				var ps = productsOf( photo );
				var li = thumbItem( photo, index, altText( photo, ps.length ? 'Customer photo: ' + ps[ 0 ].name : 'Customer photo' ) );
				if ( ps.length ) {
					var link = el( 'a', 'ds-cgallery__product', ps[ 0 ].name + ( ps.length > 1 ? ' +' + ( ps.length - 1 ) : '' ) );
					link.href = ps[ 0 ].url;
					li.appendChild( link );
				}
				return li;
			}

			function renderMore() {
				var end = Math.min( shown + PAGE_SIZE, list.length );
				var first = null;
				for ( ; shown < end; shown++ ) {
					var item = pageItem( list[ shown ], shown );
					first = first || item;
					grid.appendChild( item );
				}
				more.hidden = shown >= list.length;
				return first;
			}

			function apply( slug, updateAddress ) {
				current = counts[ slug ] ? slug : '';
				list = current ? photos.filter( function ( photo ) {
					return Array.isArray( photo.products ) && photo.products.indexOf( current ) !== -1;
				} ) : photos;
				grid.textContent = '';
				shown = 0;
				renderMore();
				chips.forEach( function ( chip ) {
					chip.setAttribute( 'aria-pressed', chip.getAttribute( 'data-slug' ) === current ? 'true' : 'false' );
				} );
				if ( updateAddress && window.history && window.URL ) {
					var url = new URL( window.location.href );
					if ( current ) {
						url.searchParams.set( 'product', current );
					} else {
						url.searchParams.delete( 'product' );
					}
					window.history.replaceState( null, '', url.toString() );
				}
			}

			function chip( slug, label, n ) {
				var btn = el( 'button', 'ds-cgallery__chip', label );
				btn.type = 'button';
				btn.setAttribute( 'data-slug', slug );
				btn.appendChild( el( 'small', '', String( n ) ) );
				btn.addEventListener( 'click', function () {
					apply( slug, true );
				} );
				chips.push( btn );
				filters.appendChild( btn );
			}

			if ( slugs.length > 1 ) {
				chip( '', 'All', photos.length );
				slugs.forEach( function ( slug ) {
					chip( slug, catalog[ slug ].name, counts[ slug ] );
				} );
				filters.hidden = false;
			}

			more.addEventListener( 'click', function () {
				var first = renderMore();
				if ( first ) {
					first.querySelector( 'a' ).focus();
				}
			} );
			wireGrid( grid, function () {
				return list;
			}, function ( photo ) {
				var ps = productsOf( photo );
				return { alt: altText( photo, ps.length ? 'Customer photo: ' + ps[ 0 ].name : 'Customer photo' ), products: ps };
			} );

			var wanted = '';
			try {
				wanted = new URLSearchParams( window.location.search ).get( 'product' ) || '';
			} catch ( e ) {
				wanted = '';
			}
			apply( wanted || root.getAttribute( 'data-product' ) || '', false );
			done( '' );
		} ).catch( function () {
			done( 'The gallery could not be loaded just now. Please try again later.' );
		} );
	}

	// ── Boot ─────────────────────────────────────────────────────────────────

	function boot() {
		Array.prototype.forEach.call( document.querySelectorAll( '[data-ds-cgallery]' ), function ( node ) {
			var mode = node.getAttribute( 'data-ds-cgallery' );
			if ( mode === 'product' ) {
				initProduct( node );
			} else if ( mode === 'all' ) {
				initAll( node );
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
