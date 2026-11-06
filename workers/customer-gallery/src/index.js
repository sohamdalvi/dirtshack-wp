/**
 * DirtShack Customer Gallery — Worker entry points.
 *
 *   scheduled   the hourly sync (cron in wrangler.toml)
 *   POST /sync  "Sync now", with header  Authorization: Bearer <SYNC_SECRET>
 *               Add ?recheck=1 to recheck every photo against the shop now.
 *               Off until the SYNC_SECRET secret is set.
 *
 * Anything else answers 404. The photos themselves are served by the R2
 * bucket's custom domain, not by this Worker.
 */

import { runSync } from './sync.js';

export default {
	async scheduled(controller, env, ctx) {
		ctx.waitUntil(runSync(env, { trigger: 'cron' }).then(logStatus, logFailure));
	},

	async fetch(request, env) {
		const url = new URL(request.url);
		if (url.pathname !== '/sync') {
			return new Response('Not found\n', { status: 404 });
		}
		if (request.method !== 'POST') {
			return json({ error: 'Use POST.' }, 405, { allow: 'POST' });
		}
		if (!env.SYNC_SECRET) {
			return json({ error: 'Manual sync is off. Set the SYNC_SECRET secret to turn it on.' }, 403);
		}
		if (!(await secretMatches(request.headers.get('authorization'), env.SYNC_SECRET))) {
			return json({ error: 'Wrong or missing secret.' }, 401);
		}
		try {
			const status = await runSync(env, { trigger: 'manual', recheck: url.searchParams.get('recheck') === '1' });
			logStatus(status);
			return json(status, 200);
		} catch (err) {
			logFailure(err);
			return json({ ok: false, error: err instanceof Error ? err.message : String(err) }, 500);
		}
	},
};

function json(data, status, headers = {}) {
	return new Response(JSON.stringify(data, null, 1), {
		status,
		headers: { 'content-type': 'application/json; charset=utf-8', 'cache-control': 'no-store', ...headers },
	});
}

/** Compare SHA-256 digests so the check takes the same time whatever was sent. */
async function secretMatches(header, secret) {
	const given = String(header || '').replace(/^Bearer\s+/i, '');
	if (!given) {
		return false;
	}
	const encoder = new TextEncoder();
	const [a, b] = await Promise.all(
		[given, String(secret)].map((s) => crypto.subtle.digest('SHA-256', encoder.encode(s))),
	);
	const x = new Uint8Array(a);
	const y = new Uint8Array(b);
	let diff = 0;
	for (let i = 0; i < x.length; i++) {
		diff |= x[i] ^ y[i];
	}
	return diff === 0;
}

function logStatus(status) {
	console.log(
		JSON.stringify({
			ok: status.ok,
			trigger: status.trigger,
			error: status.error,
			counts: status.counts,
			warnings: status.warnings,
		}),
	);
}

function logFailure(err) {
	console.error('customer-gallery sync failed:', err instanceof Error ? err.message : String(err));
}
