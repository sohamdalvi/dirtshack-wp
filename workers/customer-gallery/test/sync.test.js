import { test } from 'node:test';
import assert from 'node:assert/strict';
import { runSync, MAX_FAILURES } from '../src/sync.js';
import { FakeBucket, FakeWorld, makeEnv, GALLERY_URL, TOKEN } from './fakes.js';

const PRODUCTS = [
	{ id: 1, slug: 'gear-shaft-saver-klx230', name: 'Gear Shaft Saver for KLX 230' },
	{ id: 2, slug: 'rear-lift-strap', name: 'Rear Lift Strap' },
	{ id: 3, slug: 'bar-ends', name: 'Bar Ends &amp; Caps' },
];
const T0 = '2026-11-06T10:00:00Z';
const hoursLater = (h) => new Date(Date.parse(T0) + h * 3600e3);

const P1 = {
	id: '101',
	name: "Rahul's KLX 230 after a day on the trails 🔥\ngear-shaft-saver-klx230",
	created_time: '2026-11-01T08:00:00+0000',
};
const P2 = {
	id: '102',
	name: 'Loading up for Saturday https://shop.dirtshack.in/product/rear-lift-strap/ #bar-ends',
	created_time: '2026-11-02T08:00:00+0000',
};
const P3 = { id: '103', name: 'Great day out, no product here', created_time: '2026-11-03T08:00:00+0000' };

function setup(photos = [P1, P2, P3], extraEnv = {}) {
	const bucket = new FakeBucket();
	const world = new FakeWorld({ photos, products: PRODUCTS });
	const env = makeEnv(bucket, extraEnv);
	const run = (opts = {}) => runSync(env, { now: T0, fetch: world.fetch, ...opts });
	return { bucket, world, env, run };
}

test('first run copies matched photos and writes the per-product lists', async () => {
	const { bucket, run } = setup();
	const status = await run();

	assert.equal(status.ok, true, status.error);
	assert.deepEqual(status.counts, { album: 3, published: 2, unmatched: 1, waiting: 0 });
	assert.deepEqual(
		status.unmatched.map((p) => p.id),
		['103'],
	);
	assert.deepEqual(bucket.keys('photos/'), [
		'photos/101/large.webp',
		'photos/101/thumb.webp',
		'photos/102/large.webp',
		'photos/102/thumb.webp',
	]);
	assert.equal(bucket.objects.get('photos/101/large.webp').httpMetadata.cacheControl, 'public, max-age=31536000, immutable');
	assert.equal(bucket.objects.get('photos/101/large.webp').httpMetadata.contentType, 'image/webp');

	const gear = bucket.read('products/gear-shaft-saver-klx230.json');
	assert.deepEqual(gear, {
		slug: 'gear-shaft-saver-klx230',
		photos: [
			{
				id: '101',
				caption: "Rahul's KLX 230 after a day on the trails 🔥",
				date: '2026-11-01T08:00:00+00:00',
				fb: 'https://www.facebook.com/photo/?fbid=101',
				thumb: { url: `${GALLERY_URL}/photos/101/thumb.webp`, width: 480, height: 360 },
				large: { url: `${GALLERY_URL}/photos/101/large.webp`, width: 1440, height: 1080 },
				products: ['gear-shaft-saver-klx230'],
			},
		],
	});

	const strap = bucket.read('products/rear-lift-strap.json');
	assert.equal(strap.photos[0].caption, 'Loading up for Saturday');
	assert.deepEqual(strap.photos[0].products, ['rear-lift-strap', 'bar-ends']);
	assert.equal(bucket.read('products/bar-ends.json').photos[0].id, '102');

	const all = bucket.read('all.json');
	assert.deepEqual(
		all.photos.map((p) => p.id),
		['102', '101'],
	);
	assert.deepEqual(all.products['bar-ends'], { name: 'Bar Ends & Caps', url: 'https://shop.test/product/bar-ends/' });
	assert.equal(bucket.read('status.json').counts.published, 2);
	assert.ok(!JSON.stringify(bucket.read('_sync/state.json')).includes(TOKEN));
});

test('a run with nothing new only reads the album and writes its record and status', async () => {
	const { bucket, world, run } = setup();
	await run();
	bucket.resetLog();
	world.calls = [];

	const status = await run({ now: hoursLater(1) });
	assert.equal(status.ok, true);
	assert.deepEqual(world.calls, ['album']);
	assert.deepEqual(bucket.puts.sort(), ['_sync/state.json', 'status.json']);
	assert.deepEqual(bucket.deletes, []);
});

test('editing captions on Facebook moves photos between products', async () => {
	const { bucket, world, run } = setup();
	await run();
	world.photos.find((p) => p.id === '103').name = 'Great day out with the bar-ends';
	world.photos.find((p) => p.id === '101').name = 'Just the bike';
	world.calls = [];
	bucket.resetLog();

	const status = await run({ now: hoursLater(1) });
	assert.deepEqual(status.counts, { album: 3, published: 2, unmatched: 1, waiting: 0 });
	assert.equal(world.count('shop'), 1);
	assert.equal(world.count('image'), 2, 'only the newly matched photo is downloaded');
	assert.equal(bucket.read('products/gear-shaft-saver-klx230.json'), undefined);
	assert.ok(bucket.deletes.includes('products/gear-shaft-saver-klx230.json'));
	assert.deepEqual(
		bucket.read('products/bar-ends.json').photos.map((p) => p.id),
		['103', '102'],
	);
	assert.equal(bucket.read('products/bar-ends.json').photos[0].caption, 'Great day out with the');
	assert.ok(bucket.objects.has('photos/101/large.webp'), 'files stay in case the photo is linked again');
});

test('a photo removed from the album loses its files and its place in the lists', async () => {
	const { bucket, world, run } = setup();
	await run();
	world.photos = world.photos.filter((p) => p.id !== '101');
	world.calls = [];

	const status = await run({ now: hoursLater(1) });
	assert.equal(world.count('shop'), 0, 'removals need no product check');
	assert.deepEqual(bucket.keys('photos/101/'), []);
	assert.equal(bucket.read('products/gear-shaft-saver-klx230.json'), undefined);
	assert.equal(status.counts.published, 1);
	assert.deepEqual(
		bucket.read('all.json').photos.map((p) => p.id),
		['102'],
	);
});

test('a backlog bigger than the request limit is copied over several runs, newest first', async () => {
	const photos = Array.from({ length: 12 }, (_, i) => ({
		id: String(200 + i),
		name: `ride ${i} rear-lift-strap`,
		created_time: `2026-10-${String(10 + i).padStart(2, '0')}T08:00:00+0000`,
	}));
	const { bucket, run } = setup(photos, { MAX_SUBREQUESTS: '10' });

	// 10 requests: album, shop, then a size lookup and 2 downloads for each of 2 photos.
	let status = await run();
	assert.equal(status.ok, true);
	assert.deepEqual(status.counts, { album: 12, published: 2, unmatched: 0, waiting: 10 });
	assert.ok(status.warnings.some((w) => w.includes('10 photos are waiting')));
	assert.deepEqual(
		bucket.read('products/rear-lift-strap.json').photos.map((p) => p.id),
		['211', '210'],
	);

	for (let h = 1; h <= 5; h++) {
		status = await run({ now: hoursLater(h) });
	}
	assert.deepEqual(status.counts, { album: 12, published: 12, unmatched: 0, waiting: 0 });
	assert.equal(bucket.read('products/rear-lift-strap.json').photos.length, 12);
	assert.deepEqual(status.warnings, []);
});

test('if the shop cannot be read, nothing already published changes and new photos wait', async () => {
	const { bucket, world, run } = setup();
	await run();
	world.photos.push({ id: '104', name: 'New one gear-shaft-saver-klx230', created_time: '2026-11-04T08:00:00+0000' });
	world.shopStatus = 403;

	let status = await run({ now: hoursLater(1) });
	assert.equal(status.ok, false);
	assert.match(status.error, /HTTP 403/);
	assert.equal(status.counts.published, 2);
	assert.equal(bucket.read('products/gear-shaft-saver-klx230.json').photos.length, 1);

	// Also during the daily recheck: a failed shop read must not unlist anything.
	status = await run({ now: hoursLater(25) });
	assert.equal(status.ok, false);
	assert.equal(status.counts.published, 2);

	world.shopStatus = 200;
	status = await run({ now: hoursLater(26) });
	assert.equal(status.ok, true);
	assert.equal(status.counts.published, 3);
	assert.equal(bucket.read('products/gear-shaft-saver-klx230.json').photos.length, 2);
});

test('the daily recheck unlists photos whose product left the shop, and relists them later', async () => {
	const { bucket, world, run } = setup();
	await run();
	world.products = PRODUCTS.filter((p) => p.slug !== 'gear-shaft-saver-klx230');
	world.calls = [];

	let status = await run({ now: hoursLater(1) });
	assert.equal(world.count('shop'), 0, 'no recheck within a day');
	assert.ok(bucket.read('products/gear-shaft-saver-klx230.json'));

	status = await run({ now: hoursLater(25) });
	assert.equal(status.counts.unmatched, 2);
	assert.equal(bucket.read('products/gear-shaft-saver-klx230.json'), undefined);

	world.products = PRODUCTS;
	world.calls = [];
	status = await run({ now: hoursLater(50) });
	assert.equal(status.counts.published, 2);
	assert.equal(world.count('image'), 0, 'the kept files are reused');
	assert.equal(bucket.read('products/gear-shaft-saver-klx230.json').photos.length, 1);
});

test('an empty album reply never deletes anything', async () => {
	const { bucket, world, run } = setup();
	await run();
	world.photos = [];

	const status = await run({ now: hoursLater(1) });
	assert.equal(status.ok, false);
	assert.match(status.error, /no photos/);
	assert.equal(status.counts.published, 2);
	assert.ok(bucket.objects.has('photos/101/large.webp'));
	assert.ok(bucket.read('products/gear-shaft-saver-klx230.json'));
});

test('a partly read album does not delete the photos on unread pages', async () => {
	const photos = Array.from({ length: 6 }, (_, i) => ({
		id: String(300 + i),
		name: 'bar-ends',
		created_time: `2026-10-0${i + 1}T08:00:00+0000`,
	}));
	const { bucket, world, env, run } = setup(photos);
	await run();
	world.pageSize = 1;
	env.MAX_SUBREQUESTS = '5';

	const status = await run({ now: hoursLater(1) });
	assert.ok(status.warnings.some((w) => w.includes('Only part of the album')));
	assert.equal(status.counts.published, 6);
	assert.equal(bucket.read('products/bar-ends.json').photos.length, 6);
});

test('an album spread over several pages is read in full', async () => {
	const photos = Array.from({ length: 5 }, (_, i) => ({
		id: String(400 + i),
		name: 'rear-lift-strap',
		created_time: `2026-10-0${i + 1}T08:00:00+0000`,
	}));
	const { world, run } = setup(photos);
	world.pageSize = 2;
	const status = await run();
	assert.equal(world.count('album'), 3);
	assert.deepEqual(status.counts, { album: 5, published: 5, unmatched: 0, waiting: 0 });
});

test('an expired token is reported clearly and the token never appears in the status', async () => {
	const { bucket, world, run } = setup();
	world.tokenError = true;
	const status = await run();
	assert.equal(status.ok, false);
	assert.match(status.error, /expired/);
	assert.match(status.error, /wrangler secret put FB_PAGE_TOKEN/);
	assert.ok(!JSON.stringify(bucket.read('status.json')).includes(TOKEN));
});

test('a photo that keeps failing to copy stops being retried until the daily recheck', async () => {
	const { world, run } = setup([P1]);
	world.brokenImages.add('101');
	let status;
	for (let h = 0; h < MAX_FAILURES; h++) {
		status = await run({ now: hoursLater(h) });
	}
	assert.equal(status.waiting[0].failures, MAX_FAILURES);
	assert.match(status.waiting[0].error, /HTTP 403/);
	assert.ok(status.warnings.some((w) => w.includes('failed to copy')));

	world.calls = [];
	await run({ now: hoursLater(MAX_FAILURES) });
	assert.equal(world.count('image'), 0);

	world.brokenImages.clear();
	status = await run({ now: hoursLater(30) });
	assert.equal(status.counts.published, 1);
	assert.deepEqual(status.waiting, []);
});

test('missing settings are reported in the status instead of crashing', async () => {
	const bucket = new FakeBucket();
	const status = await runSync(
		{ GALLERY: bucket },
		{
			now: T0,
			fetch: async () => {
				throw new Error('no request expected');
			},
		},
	);
	assert.equal(status.ok, false);
	assert.equal(status.error, 'Missing settings: FB_PAGE_TOKEN (secret), FB_ALBUM_ID, GALLERY_URL.');
	assert.deepEqual(bucket.read('all.json'), { products: {}, photos: [] });
	assert.equal(bucket.read('status.json').ok, false);
});
