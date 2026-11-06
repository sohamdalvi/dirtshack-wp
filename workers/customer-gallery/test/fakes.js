/**
 * Test stand-ins for R2, Facebook's Graph API and image CDN, and the shop's
 * Store API. Nothing here touches the network.
 */

export const ALBUM = '9001';
export const TOKEN = 'test-page-token';
export const SHOP = 'https://shop.test';
export const GALLERY_URL = 'https://gallery.test';
const GRAPH_VERSION = 'v27.0';

export function makeEnv(bucket, extra = {}) {
	return {
		GALLERY: bucket,
		FB_PAGE_TOKEN: TOKEN,
		FB_ALBUM_ID: ALBUM,
		GALLERY_URL,
		SHOP_URL: SHOP,
		FB_GRAPH_VERSION: GRAPH_VERSION,
		MAX_SUBREQUESTS: '45',
		...extra,
	};
}

/** In-memory R2 bucket with the parts of the API the Worker uses. */
export class FakeBucket {
	constructor() {
		this.objects = new Map();
		this.puts = [];
		this.deletes = [];
		this.etagCounter = 0;
	}

	async get(key) {
		const o = this.objects.get(key);
		if (!o) return null;
		const text = () => (typeof o.body === 'string' ? o.body : new TextDecoder().decode(o.body));
		return { key, etag: o.etag, httpMetadata: o.httpMetadata, text: async () => text(), json: async () => JSON.parse(text()) };
	}

	async put(key, body, options = {}) {
		const current = this.objects.get(key);
		if (options.onlyIf && options.onlyIf.etagMatches && (!current || current.etag !== options.onlyIf.etagMatches)) {
			return null;
		}
		const etag = `etag-${++this.etagCounter}`;
		this.objects.set(key, { body, etag, httpMetadata: options.httpMetadata || {} });
		this.puts.push(key);
		return { key, etag };
	}

	async delete(keys) {
		for (const key of Array.isArray(keys) ? keys : [keys]) {
			this.objects.delete(key);
			this.deletes.push(key);
		}
	}

	async list({ prefix = '' } = {}) {
		return {
			objects: [...this.objects.keys()].filter((k) => k.startsWith(prefix)).map((key) => ({ key })),
			truncated: false,
		};
	}

	/** Parsed JSON of an object, or undefined. */
	read(key) {
		const o = this.objects.get(key);
		if (!o) return undefined;
		return JSON.parse(typeof o.body === 'string' ? o.body : new TextDecoder().decode(o.body));
	}

	keys(prefix = '') {
		return [...this.objects.keys()].filter((k) => k.startsWith(prefix)).sort();
	}

	resetLog() {
		this.puts = [];
		this.deletes = [];
	}
}

/**
 * Facebook album + image CDN + shop, behind one fetch function.
 * Photos: {id, name, created_time, widths?}. Products: {id, slug, name}.
 */
export class FakeWorld {
	constructor({ photos = [], products = [] } = {}) {
		this.photos = photos.map((p) => ({ ...p }));
		this.products = products.map((p) => ({ ...p }));
		this.pageSize = 100;
		this.calls = [];
		this.shopStatus = 200;
		this.tokenError = false;
		this.brokenImages = new Set();
		this.brokenSizes = new Set();
		this.fetch = this.fetch.bind(this);
	}

	count(kind) {
		return this.calls.filter((c) => c === kind).length;
	}

	async fetch(url) {
		const u = new URL(url);

		if (u.hostname === 'graph.facebook.com') {
			if (u.searchParams.get('access_token') !== TOKEN) {
				throw new Error('Graph request without the token');
			}
			if (this.tokenError) {
				this.calls.push('graph-error');
				return Response.json(
					{ error: { message: 'Error validating access token: Session has expired.', type: 'OAuthException', code: 190 } },
					{ status: 400 },
				);
			}
			if (u.pathname === `/${GRAPH_VERSION}/${ALBUM}/photos`) {
				this.calls.push('album');
				const limit = Math.min(parseInt(u.searchParams.get('limit'), 10), this.pageSize);
				const after = parseInt(u.searchParams.get('after') || '0', 10);
				const data = this.photos.slice(after, after + limit).map((p) => ({
					id: p.id,
					name: p.name,
					created_time: p.created_time,
					link: `https://www.facebook.com/photo/?fbid=${p.id}`,
				}));
				const body = { data };
				if (after + limit < this.photos.length) {
					const next = new URL(u);
					next.searchParams.set('after', String(after + limit));
					body.paging = { next: next.toString() };
				}
				return Response.json(body);
			}
			if (u.searchParams.has('ids')) {
				// What Graph API v26+ answers to the old multi-photo lookup.
				this.calls.push('ids-rejected');
				return Response.json({ error: { message: 'The ids query parameter is deprecated in v26.0+.', code: 100 } }, { status: 400 });
			}
			const photoMatch = u.pathname.match(new RegExp(`^/${GRAPH_VERSION}/(\\w+)$`));
			if (photoMatch && u.searchParams.get('fields') === 'images,webp_images') {
				this.calls.push('sizes');
				const p = this.photos.find((x) => x.id === photoMatch[1]);
				if (!p || this.brokenSizes.has(p.id)) {
					return Response.json({ error: { message: 'Unsupported get request.', code: 100 } }, { status: 400 });
				}
				return Response.json({ id: p.id, images: sizesOf(p, 'jpg'), webp_images: sizesOf(p, 'webp') });
			}
			return Response.json({ error: { message: 'Unknown path components', code: 100 } }, { status: 400 });
		}

		if (u.origin === SHOP) {
			this.calls.push('shop');
			if (u.pathname !== '/wp-json/wc/store/v1/products' || u.searchParams.get('_fields') !== 'id,slug,name,permalink') {
				throw new Error(`unexpected shop request ${url}`);
			}
			if (this.shopStatus !== 200) {
				return new Response('blocked', { status: this.shopStatus });
			}
			return Response.json(
				this.products.map((p) => ({ id: p.id, slug: p.slug, name: p.name, permalink: `${SHOP}/product/${p.slug}/` })),
				{ headers: { 'x-wp-totalpages': '1' } },
			);
		}

		if (u.hostname === 'cdn.test') {
			this.calls.push('image');
			const [, id, file] = u.pathname.split('/');
			if (this.brokenImages.has(id)) {
				return new Response('Forbidden', { status: 403 });
			}
			const type = file.endsWith('.webp') ? 'image/webp' : 'image/jpeg';
			return new Response(new Uint8Array([82, 73, 70, 70, 1, 2, 3, 4]), { headers: { 'content-type': type } });
		}

		throw new Error(`unexpected fetch ${url}`);
	}
}

function sizesOf(photo, ext) {
	const widths = photo.widths || [2048, 1440, 960, 720, 480, 320, 130];
	return widths.map((w) => ({ width: w, height: Math.round(w * 0.75), source: `https://cdn.test/${photo.id}/${w}.${ext}` }));
}
