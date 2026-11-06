/**
 * Facebook Graph API: list the album, look up image sizes, pick which to copy.
 */

import { BudgetExhausted, FacebookError } from './errors.js';

/** The large copy: Facebook's biggest size that is at most this wide. */
export const LARGE_MAX_WIDTH = 1600;
/** The thumbnail: Facebook's smallest size that is at least this wide. */
export const THUMB_MIN_WIDTH = 480;
/** At 100 photos a page, enough for 3,000 photos. */
const MAX_ALBUM_PAGES = 30;

/**
 * @param {{graphVersion: string, token: string}} cfg
 * @param {string} path e.g. "1234/photos" or a photo ID.
 * @param {Record<string, string>} params
 */
export function graphUrl(cfg, path, params) {
	const query = new URLSearchParams({ ...params, access_token: cfg.token });
	return `https://graph.facebook.com/${cfg.graphVersion}/${path}?${query}`;
}

/**
 * GET a Graph API URL and return the parsed body, or throw a FacebookError.
 * The URL holds the token, so it never goes into an error message.
 *
 * @param {(url: string) => Promise<Response>} http Budgeted fetch.
 * @param {string} url
 */
async function graphGet(http, url) {
	const res = await http(url);
	let body = null;
	try {
		body = await res.json();
	} catch {
		// Not JSON; reported by status below.
	}
	if (!res.ok || !body || body.error) {
		throw new FacebookError(body && body.error, res.status);
	}
	return body;
}

/**
 * Every photo in the album: id, caption (name), created_time and link.
 * `complete` is false when the run's request limit cut the listing short, in
 * which case photos on unread pages must not be treated as removed.
 *
 * @returns {Promise<{photos: Array<{id: string, name?: string, created_time?: string, link?: string}>, complete: boolean}>}
 */
export async function listAlbum(http, cfg) {
	const photos = [];
	let url = graphUrl(cfg, `${encodeURIComponent(cfg.albumId)}/photos`, {
		fields: 'id,name,created_time,link',
		limit: '100',
	});
	let pages = 0;
	try {
		while (url && pages < MAX_ALBUM_PAGES) {
			const body = await graphGet(http, url);
			pages++;
			for (const photo of Array.isArray(body.data) ? body.data : []) {
				if (photo && typeof photo.id === 'string') {
					photos.push(photo);
				}
			}
			const next = body.paging && body.paging.next;
			url = typeof next === 'string' && next.startsWith('https://graph.facebook.com/') ? next : null;
		}
	} catch (err) {
		if (err instanceof BudgetExhausted && pages > 0) {
			return { photos, complete: false };
		}
		throw err;
	}
	return { photos, complete: !url };
}

/**
 * Facebook's stored sizes of one photo. One request per photo: Graph API
 * v26+ rejects the ?ids= form that looked up several at once.
 *
 * @param {string} id Photo ID.
 * @returns {Promise<{images?: object[], webp_images?: object[]}>}
 */
export async function getImageSizes(http, cfg, id) {
	return graphGet(http, graphUrl(cfg, encodeURIComponent(id), { fields: 'images,webp_images' }));
}

/**
 * Choose the two sizes to copy from Facebook's stored sizes, preferring WebP.
 *
 * @param {{images?: object[], webp_images?: object[]}|undefined} info
 * @returns {{large: Rendition, thumb: Rendition}|null}
 *
 * @typedef {{source: string, width: number, height: number}} Rendition
 */
export function pickRenditions(info) {
	const usable = (list) =>
		(Array.isArray(list) ? list : []).filter(
			(i) => i && typeof i.source === 'string' && i.source.startsWith('https://') && i.width > 0 && i.height > 0,
		);
	let list = usable(info && info.webp_images);
	if (!list.length) {
		list = usable(info && info.images);
	}
	if (!list.length) {
		return null;
	}
	list.sort((a, b) => a.width - b.width);
	const large = [...list].reverse().find((i) => i.width <= LARGE_MAX_WIDTH) || list[0];
	let thumb = list.find((i) => i.width >= THUMB_MIN_WIDTH) || list[list.length - 1];
	if (thumb.width > large.width) {
		thumb = large;
	}
	return { large, thumb };
}
