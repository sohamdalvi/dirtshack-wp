import { test } from 'node:test';
import assert from 'node:assert/strict';
import { pickRenditions } from '../src/facebook.js';

const sizes = (widths, ext) => widths.map((w) => ({ width: w, height: w, source: `https://cdn.test/1/${w}.${ext}` }));

test('picks WebP: the biggest size up to 1600 wide and the smallest from 480 wide', () => {
	const r = pickRenditions({ webp_images: sizes([130, 2048, 480, 1440, 720], 'webp'), images: sizes([2048], 'jpg') });
	assert.equal(r.large.source, 'https://cdn.test/1/1440.webp');
	assert.equal(r.thumb.source, 'https://cdn.test/1/480.webp');
});

test('falls back to JPEG sizes when there are no WebP ones', () => {
	const r = pickRenditions({ images: sizes([960, 320], 'jpg') });
	assert.equal(r.large.source, 'https://cdn.test/1/960.jpg');
	assert.equal(r.thumb.source, 'https://cdn.test/1/960.jpg');
	assert.equal(r.thumb, r.large);
});

test('a small photo uses one size for both', () => {
	const r = pickRenditions({ webp_images: sizes([130, 320], 'webp') });
	assert.equal(r.large.width, 320);
	assert.equal(r.thumb, r.large);
});

test('when every size is wider than 1600, the smallest is the large copy', () => {
	const r = pickRenditions({ webp_images: sizes([4096, 2048], 'webp') });
	assert.equal(r.large.width, 2048);
	assert.equal(r.thumb, r.large);
});

test('ignores broken entries and returns null when nothing is usable', () => {
	assert.equal(pickRenditions({ webp_images: [{ width: 0, height: 0, source: 'x' }, null], images: [] }), null);
	assert.equal(pickRenditions(undefined), null);
	const r = pickRenditions({ webp_images: [{ width: 800, height: 600, source: 'http://insecure/800.webp' }, ...sizes([600], 'webp')] });
	assert.equal(r.large.width, 600);
});
