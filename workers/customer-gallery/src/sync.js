/**
 * One sync run: Facebook album → R2.
 *
 *  1. List the album (captions only, so the reply stays small).
 *  2. Photos gone from the album: delete their files.
 *  3. New photos, edited captions, photos still waiting, and once a day every
 *     photo: check the caption's slugs against the shop's products.
 *  4. Matched photos without files: copy Facebook's ~1600 px and ~480 px
 *     versions into R2, as many as this run's request limit allows.
 *  5. Rewrite products/<slug>.json and all.json where they changed, and
 *     always write status.json.
 *
 * The free Workers plan allows 50 outside requests and 10 ms of CPU per run, so
 * nothing is resized here and a big backlog is copied over several runs.
 */

import { parseCaption } from './caption.js';
import { BudgetExhausted, describeError } from './errors.js';
import { getImageSizes, listAlbum, pickRenditions } from './facebook.js';
import { loadCatalog } from './shop.js';
import { deletePhotoFiles, loadState, putJson, saveState, sha1, STATUS_KEY } from './store.js';

const DAY_MS = 24 * 60 * 60 * 1000;
/** A photo whose copy keeps failing is retried only at the daily recheck after this many tries. */
export const MAX_FAILURES = 5;
const MAX_IMAGE_BYTES = 10 * 1024 * 1024;
const USER_AGENT = 'dirtshack-customer-gallery/1.0 (+https://shop.dirtshack.in)';
const IMAGE_TYPES = { 'image/webp': 'webp', 'image/jpeg': 'jpg', 'image/png': 'png' };
const PHOTO_CACHE = 'public, max-age=31536000, immutable';
const LIST_CACHE = 'public, max-age=300';
const STATUS_CACHE = 'no-cache';

/**
 * @param {Record<string, any>} env Worker bindings, vars and secrets.
 */
export function readConfig(env) {
	const missing = [];
	if (!env.FB_PAGE_TOKEN) missing.push('FB_PAGE_TOKEN (secret)');
	if (!String(env.FB_ALBUM_ID || '').trim()) missing.push('FB_ALBUM_ID');
	if (!String(env.GALLERY_URL || '').trim()) missing.push('GALLERY_URL');
	const max = parseInt(env.MAX_SUBREQUESTS, 10);
	return {
		missing,
		token: String(env.FB_PAGE_TOKEN || ''),
		albumId: String(env.FB_ALBUM_ID || '').trim(),
		graphVersion: String(env.FB_GRAPH_VERSION || 'v27.0').trim(),
		shopUrl: trimSlash(env.SHOP_URL || 'https://shop.dirtshack.in'),
		galleryUrl: trimSlash(env.GALLERY_URL || ''),
		maxSubrequests: Number.isFinite(max) ? Math.min(Math.max(max, 5), 10000) : 45,
	};
}

/**
 * Run one sync. Never throws for Facebook, shop or copy problems; those end up
 * in the returned status (also written to status.json).
 *
 * @param {Record<string, any>} env
 * @param {{trigger?: string, recheck?: boolean, now?: Date|string|number, fetch?: typeof fetch}} [options]
 *   `recheck` forces the daily recheck; `now` and `fetch` are for tests.
 */
export async function runSync(env, options = {}) {
	const bucket = env.GALLERY;
	if (!bucket) {
		throw new Error('The GALLERY R2 bucket binding is missing (see wrangler.toml).');
	}
	const now = options.now !== undefined ? new Date(options.now) : new Date();
	const fetchImpl = options.fetch || ((url, init) => fetch(url, init));
	const cfg = readConfig(env);

	let budget = cfg.maxSubrequests;
	const http = async (url, init = {}) => {
		if (budget <= 0) {
			throw new BudgetExhausted();
		}
		budget--;
		return fetchImpl(url, { ...init, headers: { 'user-agent': USER_AGENT, ...(init.headers || {}) } });
	};

	const status = {
		ok: true,
		error: null,
		warnings: [],
		trigger: options.trigger || 'cron',
		lastRun: now.toISOString(),
	};
	const { state, etag, warning } = await loadState(bucket);
	if (warning) {
		status.warnings.push(warning);
	}
	let changed = false;
	let rechecked = false;
	let albumCount = null;

	try {
		if (cfg.missing.length) {
			throw new Error(`Missing settings: ${cfg.missing.join(', ')}.`);
		}

		// 1. The album.
		const { photos: album, complete } = await listAlbum(http, cfg);
		albumCount = album.length;
		const knownIds = Object.keys(state.photos);
		if (album.length === 0 && knownIds.length > 0) {
			throw new Error(
				'Facebook returned no photos for the album, so nothing was changed. Check FB_ALBUM_ID and that the token can read the Page.',
			);
		}
		if (!complete) {
			status.warnings.push('Only part of the album could be read this run (request limit), so removed photos were not cleaned up.');
		}

		// 2. Photos removed from the album.
		if (complete) {
			const inAlbum = new Set(album.map((p) => p.id));
			for (const id of knownIds) {
				if (!inAlbum.has(id)) {
					await deletePhotoFiles(bucket, id);
					delete state.photos[id];
					changed = true;
				}
			}
		}

		// 3. Photos to (re)check against the shop's products.
		const recheck = Boolean(options.recheck) || !state.lastRecheck || now - Date.parse(state.lastRecheck) >= DAY_MS;
		const toCheck = album.filter((p) => {
			const prev = state.photos[p.id];
			if (!prev || recheck || prev.caption !== (p.name || '')) {
				return true;
			}
			return prev.status === 'waiting' && (prev.failures || 0) < MAX_FAILURES;
		});

		if (toCheck.length) {
			let catalog = null;
			try {
				catalog = await loadCatalog(http, cfg);
			} catch (err) {
				status.ok = false;
				status.error = `Could not read the shop's product list (${describeError(err)}). New and edited photos will be tried again next run.`;
			}

			if (catalog) {
				if (JSON.stringify(catalog) !== JSON.stringify(state.products)) {
					state.products = catalog;
					changed = true;
				}
				const slugSet = new Set(Object.keys(catalog));
				const toCopy = [];
				for (const p of toCheck.sort(newestFirstFb)) {
					const prev = state.photos[p.id];
					const caption = p.name || '';
					const { slugs, caption: text } = parseCaption(caption, slugSet);
					const entry = {
						id: p.id,
						created: normaliseTime(p.created_time),
						fb: p.link || `https://www.facebook.com/${p.id}`,
						caption,
						text,
						slugs,
						files: (prev && prev.files) || null,
						status: 'unmatched',
					};
					if (slugs.length && entry.files) {
						entry.status = 'published';
					} else if (slugs.length) {
						entry.status = 'waiting';
						const sameCaption = prev && prev.caption === caption;
						entry.failures = sameCaption ? prev.failures || 0 : 0;
						if (sameCaption && prev.error) {
							entry.error = prev.error;
						}
						toCopy.push(entry);
					}
					if (JSON.stringify(prev) !== JSON.stringify(entry)) {
						changed = true;
					}
					state.photos[p.id] = entry;
				}

				// 4. Copy files for matched photos that don't have them yet.
				if (toCopy.length && (await copyPhotos(toCopy, { http, bucket, cfg, budgetLeft: () => budget }))) {
					changed = true;
				}
				if (recheck) {
					state.lastRecheck = now.toISOString();
					rechecked = true;
					changed = true;
				}
			}
		}
	} catch (err) {
		status.ok = false;
		status.error = describeError(err);
	}

	// 5. Outputs. Rechecks rewrite everything, in case a file was lost.
	if (changed || !state.outputs['all.json']) {
		try {
			await writeOutputs(bucket, state, cfg, rechecked);
		} catch (err) {
			status.ok = false;
			status.error = [status.error, `Could not write the photo lists: ${describeError(err)}`].filter(Boolean).join(' ');
		}
	}

	state.lastRun = status.lastRun;
	if (status.ok) {
		state.lastSuccess = status.lastRun;
	}
	fillStatus(status, state, albumCount);

	if (!(await saveState(bucket, state, etag))) {
		status.warnings.push('Another sync ran at the same time; its record was kept.');
	}
	await putJson(bucket, STATUS_KEY, JSON.stringify(status, null, 1), STATUS_CACHE);
	return status;
}

/**
 * Copy files for as many waiting photos as the request limit allows. Each
 * photo takes one Graph lookup for its image sizes and up to two downloads.
 *
 * @returns {Promise<boolean>} Whether any entry changed.
 */
async function copyPhotos(entries, { http, bucket, cfg, budgetLeft }) {
	let changed = false;
	for (const entry of entries) {
		if (budgetLeft() < 3) {
			break;
		}
		try {
			const picked = pickRenditions(await getImageSizes(http, cfg, entry.id));
			if (!picked) {
				throw new Error('Facebook returned no image sizes for this photo');
			}
			const large = await copyImage(http, bucket, picked.large, `photos/${entry.id}/large`);
			const thumb =
				picked.thumb === picked.large ? large : await copyImage(http, bucket, picked.thumb, `photos/${entry.id}/thumb`);
			entry.files = { large, thumb };
			entry.status = 'published';
			delete entry.failures;
			delete entry.error;
			changed = true;
		} catch (err) {
			if (err instanceof BudgetExhausted) {
				break;
			}
			entry.failures = (entry.failures || 0) + 1;
			entry.error = describeError(err);
			changed = true;
		}
	}
	return changed;
}

/** Download one Facebook image and store it in R2 unchanged. */
async function copyImage(http, bucket, rendition, baseKey) {
	const res = await http(rendition.source);
	if (!res.ok) {
		throw new Error(`image download failed (HTTP ${res.status})`);
	}
	const type = (res.headers.get('content-type') || '').split(';')[0].trim().toLowerCase();
	const ext = IMAGE_TYPES[type];
	if (!ext) {
		throw new Error(`unexpected image type "${type || 'none'}"`);
	}
	const body = await res.arrayBuffer();
	if (!body.byteLength || body.byteLength > MAX_IMAGE_BYTES) {
		throw new Error(`unexpected image size (${body.byteLength} bytes)`);
	}
	const key = `${baseKey}.${ext}`;
	await bucket.put(key, body, { httpMetadata: { contentType: type, cacheControl: PHOTO_CACHE } });
	return { key, width: rendition.width, height: rendition.height };
}

/**
 * products/<slug>.json for every product with photos, and all.json. Files
 * whose content hasn't changed are skipped unless `force` is set; lists for
 * products that no longer have photos are deleted.
 */
async function writeOutputs(bucket, state, cfg, force) {
	const published = Object.values(state.photos)
		.filter((p) => p.status === 'published' && p.files && p.slugs.length)
		.sort(newestFirst);
	const toEntry = (p) => ({
		id: p.id,
		caption: p.text,
		date: p.created,
		fb: p.fb,
		thumb: fileOut(cfg, p.files.thumb),
		large: fileOut(cfg, p.files.large),
		products: p.slugs,
	});

	const bySlug = new Map();
	const all = [];
	for (const p of published) {
		const entry = toEntry(p);
		all.push(entry);
		for (const slug of p.slugs) {
			if (!bySlug.has(slug)) bySlug.set(slug, []);
			bySlug.get(slug).push(entry);
		}
	}

	const wanted = new Map();
	const products = {};
	for (const [slug, photos] of bySlug) {
		wanted.set(`products/${slug}.json`, { slug, photos });
		const known = state.products[slug];
		products[slug] = known ? { name: known.name, url: known.url } : { name: slug, url: `${cfg.shopUrl}/product/${slug}/` };
	}
	wanted.set('all.json', { products, photos: all });

	for (const [key, data] of wanted) {
		const body = JSON.stringify(data);
		const hash = await sha1(body);
		if (!force && state.outputs[key] === hash) {
			continue;
		}
		await putJson(bucket, key, body, LIST_CACHE);
		state.outputs[key] = hash;
	}
	for (const key of Object.keys(state.outputs)) {
		if (!wanted.has(key)) {
			await bucket.delete(key);
			delete state.outputs[key];
		}
	}
}

function fillStatus(status, state, albumCount) {
	const photos = Object.values(state.photos).sort(newestFirst);
	const waiting = photos.filter((p) => p.status === 'waiting');
	const stuck = waiting.filter((p) => (p.failures || 0) >= MAX_FAILURES);
	status.lastSuccess = state.lastSuccess;
	status.lastRecheck = state.lastRecheck;
	status.counts = {
		album: albumCount,
		published: photos.filter((p) => p.status === 'published').length,
		unmatched: photos.filter((p) => p.status === 'unmatched').length,
		waiting: waiting.length,
	};
	status.unmatched = photos
		.filter((p) => p.status === 'unmatched')
		.map((p) => ({ id: p.id, caption: p.caption, fb: p.fb, date: p.created }));
	status.waiting = waiting.map((p) => ({ id: p.id, fb: p.fb, failures: p.failures || 0, error: p.error || null }));
	if (waiting.length > stuck.length) {
		const n = waiting.length - stuck.length;
		status.warnings.push(`${n} photo${n === 1 ? ' is' : 's are'} waiting to be copied; later runs continue.`);
	}
	if (stuck.length) {
		status.warnings.push(
			`${stuck.length} photo${stuck.length === 1 ? '' : 's'} failed to copy ${MAX_FAILURES} times and will be retried at the daily recheck.`,
		);
	}
}

function fileOut(cfg, file) {
	return { url: `${cfg.galleryUrl}/${file.key}`, width: file.width, height: file.height };
}

/** Facebook writes "2026-11-06T10:00:00+0000"; make it standard ISO 8601. */
function normaliseTime(value) {
	if (typeof value !== 'string') return null;
	return value.replace(/([+-]\d\d)(\d\d)$/, '$1:$2');
}

function newestFirst(a, b) {
	return String(b.created || '').localeCompare(String(a.created || '')) || String(b.id).localeCompare(String(a.id));
}

function newestFirstFb(a, b) {
	return String(b.created_time || '').localeCompare(String(a.created_time || '')) || String(b.id).localeCompare(String(a.id));
}

function trimSlash(url) {
	return String(url).trim().replace(/\/+$/, '');
}
