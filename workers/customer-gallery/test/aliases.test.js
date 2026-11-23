import { test } from 'node:test';
import assert from 'node:assert/strict';
import { runSync } from '../src/sync.js';
import { cleanAliases, resolveSlugs } from '../src/aliases.js';
import { FakeBucket, FakeWorld, makeEnv } from './fakes.js';

const T0 = '2026-11-06T10:00:00Z';
const hoursLater = (h) => new Date(Date.parse(T0) + h * 3600e3);

const PRODUCTS = [
	{ id: 1, slug: 'rear-lift-strap', name: 'Rear Lift Strap' },
	{ id: 2, slug: 'rear-lift-strap-v2', name: 'Rear Lift Strap v2' },
	{ id: 3, slug: 'bar-ends', name: 'Bar Ends' },
];
const OLD_CAPTION = { id: '201', name: 'Fitted it on the KLX rear-lift-strap', created_time: '2026-10-01T08:00:00+0000' };
const RETIRED_CAPTION = { id: '202', name: 'Still fits: retired-strap', created_time: '2026-10-02T08:00:00+0000' };

function setup(photos, { products = PRODUCTS, aliases = {} } = {}) {
	const bucket = new FakeBucket();
	const world = new FakeWorld({ photos, products, aliases });
	const env = makeEnv(bucket);
	const run = (opts = {}) => runSync(env, { now: T0, fetch: world.fetch, ...opts });
	return { bucket, world, run };
}

const slugsOf = (bucket, slug) => (bucket.read(`products/${slug}.json`)?.photos || []).map((p) => p.id);

test('an alias repoints photos from the old product to the new version, without editing captions', async () => {
	const { bucket, world, run } = setup([OLD_CAPTION]);
	await run();
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap'), ['201']);
	assert.equal(bucket.read('products/rear-lift-strap-v2.json'), undefined);

	world.aliases = { 'rear-lift-strap': 'rear-lift-strap-v2' };
	const status = await run({ now: hoursLater(1) });
	assert.equal(status.ok, true, status.error);
	// Old product is still live, so the photo stays on it as well.
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap'), ['201']);
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), ['201']);
	assert.equal(bucket.read('products/rear-lift-strap-v2.json').photos[0].caption, 'Fitted it on the KLX');
});

test('removing an alias takes the photo off the new version again', async () => {
	const { bucket, world, run } = setup([OLD_CAPTION], { aliases: { 'rear-lift-strap': 'rear-lift-strap-v2' } });
	await run();
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), ['201']);

	world.aliases = {};
	await run({ now: hoursLater(1) });
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), []);
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap'), ['201']);
});

test('an old slug whose product has been removed still routes photos to the new version', async () => {
	const { bucket, run } = setup([RETIRED_CAPTION], { aliases: { 'retired-strap': 'rear-lift-strap-v2' } });
	const status = await run();
	assert.equal(status.ok, true, status.error);
	assert.equal(status.counts.published, 1);
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), ['202']);
	assert.equal(bucket.read('products/retired-strap.json'), undefined);
});

test('an alias to a product that is not live is ignored', async () => {
	const { bucket, run } = setup([OLD_CAPTION], { aliases: { 'rear-lift-strap': 'no-such-product' } });
	const status = await run();
	assert.deepEqual(status.counts, { album: 1, published: 1, unmatched: 0, waiting: 0 });
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap'), ['201']);
	assert.equal(bucket.read('products/no-such-product.json'), undefined);
});

test('if the alias map cannot be read, the last map that worked still applies', async () => {
	const { bucket, world, run } = setup([OLD_CAPTION], { aliases: { 'rear-lift-strap': 'rear-lift-strap-v2' } });
	await run();
	world.aliasStatus = 500;
	const status = await run({ now: hoursLater(1) });
	assert.ok(status.warnings.some((w) => w.includes('alias map could not be read')));
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), ['201']);
});

test('a map that was not applied is retried on the next run', async () => {
	// The shop product list fails on the run where the alias changes, so the change
	// must wait for a run that can apply it.
	const { bucket, world, run } = setup([OLD_CAPTION]);
	await run();
	world.aliases = { 'rear-lift-strap': 'rear-lift-strap-v2' };
	world.shopStatus = 500;
	const failed = await run({ now: hoursLater(1) });
	assert.equal(failed.ok, false);
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), []);

	world.shopStatus = 200;
	const status = await run({ now: hoursLater(2) });
	assert.equal(status.ok, true, status.error);
	assert.deepEqual(slugsOf(bucket, 'rear-lift-strap-v2'), ['201']);
});

test('cleanAliases keeps only valid slug pairs, sorted by old slug', () => {
	assert.deepEqual(
		cleanAliases({ 'b-old': 'b-new', 'a-old': 'a-new', 'Bad Slug': 'x', same: 'same', ok: 42, 'c-old': 'c_new' }),
		{ 'a-old': 'a-new', 'b-old': 'b-new' },
	);
	assert.throws(() => cleanAliases([]), /unexpected alias map/);
	assert.throws(() => cleanAliases('nope'), /unexpected alias map/);
});

test('resolveSlugs adds the target only when it is live, and keeps the caption slug if live', () => {
	const live = new Set(['rear-lift-strap', 'rear-lift-strap-v2']);
	const aliases = { 'rear-lift-strap': 'rear-lift-strap-v2', 'gone': 'rear-lift-strap-v2' };
	assert.deepEqual(resolveSlugs(['rear-lift-strap'], aliases, live), ['rear-lift-strap', 'rear-lift-strap-v2']);
	assert.deepEqual(resolveSlugs(['gone'], aliases, live), ['rear-lift-strap-v2']);
	assert.deepEqual(resolveSlugs(['gone', 'rear-lift-strap-v2'], aliases, live), ['rear-lift-strap-v2']);
	assert.deepEqual(resolveSlugs(['bar-ends'], aliases, live), []);
});
