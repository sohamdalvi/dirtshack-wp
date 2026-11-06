/**
 * Captions: which shop products a photo belongs to, and the text to show.
 *
 * A photo belongs to a product when its caption contains that product's slug
 * as a whole word (gear-shaft-saver-klx230), as a hashtag
 * (#gear-shaft-saver-klx230) or inside a product link
 * (https://shop.dirtshack.in/product/gear-shaft-saver-klx230/). Case doesn't
 * matter. Words that aren't slugs of published products are ignored.
 *
 * The text shown on the site is the caption without the product links and slugs.
 */

const URL_RE = /https?:\/\/[^\s<>"]+/gi;
const PRODUCT_PATH_RE = /\/product\/([a-z0-9-]+)/i;
const WORD_RE = /[a-z0-9]+(?:-[a-z0-9]+)*/g;
// A line left with nothing worth showing once slugs are removed.
const HAS_CONTENT_RE = /[\p{L}\p{N}\p{Extended_Pictographic}]/u;

/**
 * @param {string|undefined|null} text The Facebook caption.
 * @param {Set<string>} slugSet Slugs of the shop's published products.
 * @returns {{slugs: string[], caption: string}} Matched slugs in caption order, and the text to show.
 */
export function parseCaption(text, slugSet) {
	const raw = typeof text === 'string' ? text : '';
	const slugs = [];
	const add = (slug) => {
		if (slugSet.has(slug) && !slugs.includes(slug)) {
			slugs.push(slug);
		}
	};

	for (const [url] of raw.matchAll(URL_RE)) {
		const m = PRODUCT_PATH_RE.exec(url);
		if (m) {
			add(m[1].toLowerCase());
		}
	}
	const words = raw.replace(URL_RE, ' ').toLowerCase().match(WORD_RE) || [];
	for (const word of words) {
		add(word);
	}

	const lines = [];
	for (const line of raw.split(/\r?\n/)) {
		let shown = line.replace(URL_RE, (url) => (PRODUCT_PATH_RE.test(url) ? '' : url));
		for (const slug of slugs) {
			shown = shown.replace(new RegExp(`(^|[^a-z0-9#-])#?${slug}(?![a-z0-9-])`, 'gi'), '$1');
		}
		if (shown !== line) {
			// "Fitted gear-shaft-saver-klx230, today" → "Fitted, today"
			shown = shown.replace(/[ \t]+([.,;:!?])/g, '$1');
			if (!HAS_CONTENT_RE.test(shown)) {
				continue;
			}
		}
		lines.push(shown.replace(/[ \t]+/g, ' ').trim());
	}

	const caption = lines
		.join('\n')
		.replace(/\n{3,}/g, '\n\n')
		.trim();
	return { slugs, caption };
}
