import { test } from 'node:test';
import assert from 'node:assert/strict';
import worker from '../src/index.js';
import { FakeBucket, FakeWorld, makeEnv } from './fakes.js';

const SECRET = 'a-long-random-secret';
const post = (path, auth) =>
	new Request(`https://customer-gallery.example.workers.dev${path}`, {
		method: 'POST',
		headers: auth ? { authorization: auth } : {},
	});

test('only POST /sync is answered', async () => {
	const env = makeEnv(new FakeBucket(), { SYNC_SECRET: SECRET });
	assert.equal((await worker.fetch(new Request('https://x.test/'), env)).status, 404);
	assert.equal((await worker.fetch(new Request('https://x.test/sync'), env)).status, 405);
});

test('manual sync is off without SYNC_SECRET and needs the right secret', async () => {
	assert.equal((await worker.fetch(post('/sync', `Bearer ${SECRET}`), makeEnv(new FakeBucket()))).status, 403);
	const env = makeEnv(new FakeBucket(), { SYNC_SECRET: SECRET });
	assert.equal((await worker.fetch(post('/sync'), env)).status, 401);
	assert.equal((await worker.fetch(post('/sync', 'Bearer wrong'), env)).status, 401);
	assert.equal((await worker.fetch(post('/sync', `Bearer ${SECRET}x`), env)).status, 401);
});

test('POST /sync with the secret runs a sync and returns its status', async (t) => {
	const world = new FakeWorld({
		photos: [{ id: '1', name: 'bar-ends', created_time: '2026-11-01T08:00:00+0000' }],
		products: [{ id: 9, slug: 'bar-ends', name: 'Bar Ends' }],
	});
	t.mock.method(globalThis, 'fetch', world.fetch);
	const bucket = new FakeBucket();
	const res = await worker.fetch(post('/sync?recheck=1', `Bearer ${SECRET}`), makeEnv(bucket, { SYNC_SECRET: SECRET }));
	assert.equal(res.status, 200);
	const status = await res.json();
	assert.equal(status.ok, true, status.error);
	assert.equal(status.trigger, 'manual');
	assert.equal(status.counts.published, 1);
	assert.ok(bucket.read('products/bar-ends.json'));
});
