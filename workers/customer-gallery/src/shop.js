/**
 * The shop's published products, from WooCommerce's public Store API.
 *
 * Only id, slug, name and permalink are requested (_fields), which keeps the
 * reply about 100 times smaller than the full product data: the free Workers
 * plan allows 10 ms of CPU per run, and parsing is most of it.
 */

const MAX_PAGES = 10;

/**
 * @param {(url: string, init?: RequestInit) => Promise<Response>} http Budgeted fetch.
 * @param {{shopUrl: string}} cfg
 * @returns {Promise<Record<string, {id: number, name: string, url: string}>>} Keyed by slug.
 */
export async function loadCatalog(http, cfg) {
	const products = {};
	let page = 1;
	let totalPages = 1;
	do {
		const url = `${cfg.shopUrl}/wp-json/wc/store/v1/products?per_page=100&page=${page}&_fields=id,slug,name,permalink`;
		const res = await http(url, { headers: { accept: 'application/json' } });
		if (!res.ok) {
			throw new Error(`the shop answered HTTP ${res.status}`);
		}
		let list;
		try {
			list = await res.json();
		} catch {
			throw new Error('the shop did not send JSON');
		}
		if (!Array.isArray(list)) {
			throw new Error('unexpected reply from the shop');
		}
		for (const p of list) {
			if (p && typeof p.slug === 'string' && p.slug) {
				products[p.slug] = {
					id: p.id,
					name: decodeEntities(String(p.name || p.slug)),
					url: typeof p.permalink === 'string' ? p.permalink : `${cfg.shopUrl}/product/${p.slug}/`,
				};
			}
		}
		totalPages = parseInt(res.headers.get('x-wp-totalpages') || '1', 10) || 1;
		page++;
	} while (page <= totalPages && page <= MAX_PAGES);

	if (!Object.keys(products).length) {
		throw new Error('the shop returned no products');
	}
	return products;
}

/** WordPress may send names with HTML entities (&amp;, &#8211;). */
function decodeEntities(text) {
	return text
		.replace(/&#(\d+);/g, (_, n) => String.fromCodePoint(Number(n)))
		.replace(/&#x([0-9a-f]+);/gi, (_, n) => String.fromCodePoint(parseInt(n, 16)))
		.replace(/&quot;/g, '"')
		.replace(/&#039;|&apos;/g, "'")
		.replace(/&lt;/g, '<')
		.replace(/&gt;/g, '>')
		.replace(/&amp;/g, '&');
}
