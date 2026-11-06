/**
 * R2 helpers: the sync's own record, JSON outputs and photo files.
 *
 * The record lives in the same bucket as the photos (R2 reads are strongly
 * consistent, unlike KV). It is reachable at <GALLERY_URL>/_sync/state.json but
 * holds only what the public Facebook album already shows; the README has an
 * optional rule to block that path.
 */

export const STATE_KEY = '_sync/state.json';
export const STATUS_KEY = 'status.json';
const JSON_TYPE = 'application/json; charset=utf-8';

export function emptyState() {
	return {
		version: 1,
		photos: {},
		products: {},
		outputs: {},
		lastRecheck: null,
		lastRun: null,
		lastSuccess: null,
	};
}

/**
 * @param {R2Bucket} bucket
 * @returns {Promise<{state: ReturnType<typeof emptyState>, etag: string|null, warning: string|null}>}
 */
export async function loadState(bucket) {
	const obj = await bucket.get(STATE_KEY);
	if (!obj) {
		return { state: emptyState(), etag: null, warning: null };
	}
	try {
		const saved = await obj.json();
		return { state: { ...emptyState(), ...saved }, etag: obj.etag, warning: null };
	} catch {
		// Starting over is safe: copies go to the same file names, and nothing
		// is deleted without a record of it.
		return { state: emptyState(), etag: obj.etag, warning: 'The sync record was unreadable, so this run started a new one.' };
	}
}

/**
 * Save the record unless another run saved it since we read it.
 *
 * @returns {Promise<boolean>} false if another run got there first.
 */
export async function saveState(bucket, state, etag) {
	const options = { httpMetadata: { contentType: JSON_TYPE, cacheControl: 'no-store' } };
	if (etag) {
		options.onlyIf = { etagMatches: etag };
	}
	const result = await bucket.put(STATE_KEY, JSON.stringify(state), options);
	return result !== null;
}

export async function putJson(bucket, key, body, cacheControl) {
	await bucket.put(key, body, { httpMetadata: { contentType: JSON_TYPE, cacheControl } });
}

/** Delete every file stored for a photo, including any left by an unfinished copy. */
export async function deletePhotoFiles(bucket, id) {
	const listed = await bucket.list({ prefix: `photos/${id}/` });
	const keys = listed.objects.map((o) => o.key);
	if (keys.length) {
		await bucket.delete(keys);
	}
}

/** Hex SHA-1, used only to skip rewriting JSON files that haven't changed. */
export async function sha1(text) {
	const digest = await crypto.subtle.digest('SHA-1', new TextEncoder().encode(text));
	return [...new Uint8Array(digest)].map((b) => b.toString(16).padStart(2, '0')).join('');
}
