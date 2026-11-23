/**
 * Product aliases ("moved" products): when a product gets a new version, its
 * old slug can point at the new one, so photos captioned with the old slug show
 * on the new product's page without editing each caption.
 *
 * The map is kept in WordPress (wp-admin → Products → Customer Gallery) and read
 * from the shop's public REST route. Only slugs are involved, and both are already
 * public on the shop.
 *
 *   { "rear-lift-strap": "rear-lift-strap-v2" }
 *
 * A photo captioned with an alias key gets the target too. If the old product is
 * still live, the photo stays on it as well.
 */

const SLUG_RE = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
const MAX_ALIASES = 500;

/**
 * @param {(url: string, init?: RequestInit) => Promise<Response>} http Budgeted fetch.
 * @param {{shopUrl: string}} cfg
 * @returns {Promise<Record<string, string>>} Old slug → new slug, keys sorted.
 */
export async function loadAliases(http, cfg) {
	const res = await http(`${cfg.shopUrl}/wp-json/dirtshack/v1/gallery-aliases`, { headers: { accept: 'application/json' } });
	if (!res.ok) {
		throw new Error(`the shop answered HTTP ${res.status} for the alias map`);
	}
	let data;
	try {
		data = await res.json();
	} catch {
		throw new Error('the alias map was not JSON');
	}
	return cleanAliases(data);
}

/**
 * Keep only well-formed pairs, sorted by old slug so the same map always gives
 * the same JSON.
 *
 * @param {unknown} data
 * @returns {Record<string, string>}
 */
export function cleanAliases(data) {
	if (!data || typeof data !== 'object' || Array.isArray(data)) {
		throw new Error('unexpected alias map');
	}
	const out = {};
	for (const [from, to] of Object.entries(data).slice(0, MAX_ALIASES)) {
		if (typeof from === 'string' && typeof to === 'string' && SLUG_RE.test(from) && SLUG_RE.test(to) && from !== to) {
			out[from] = to;
		}
	}
	return Object.fromEntries(Object.entries(out).sort(([a], [b]) => (a < b ? -1 : a > b ? 1 : 0)));
}

/** Canonical JSON of a map, for comparing maps between runs. */
export function aliasKey(aliases) {
	return JSON.stringify(aliases);
}

/**
 * The live product slugs a photo belongs to after aliases are applied.
 *
 * @param {string[]} matched Slugs found in the caption (may include alias keys).
 * @param {Record<string, string>} aliases
 * @param {Set<string>} live Slugs of the shop's published products.
 * @returns {string[]} In caption order, without repeats.
 */
export function resolveSlugs(matched, aliases, live) {
	const out = [];
	const add = (slug) => {
		if (live.has(slug) && !out.includes(slug)) {
			out.push(slug);
		}
	};
	for (const slug of matched) {
		add(slug);
		if (Object.prototype.hasOwnProperty.call(aliases, slug)) {
			add(aliases[slug]);
		}
	}
	return out;
}
