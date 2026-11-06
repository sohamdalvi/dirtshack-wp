import { test } from 'node:test';
import assert from 'node:assert/strict';
import { parseCaption } from '../src/caption.js';

const SLUGS = new Set(['gear-shaft-saver-klx230', 'rear-lift-strap', 'bar-ends']);

test('a slug on its own line links the photo and is left out of the caption', () => {
	const r = parseCaption("Rahul's KLX 230 after a day on the trails 🔥\ngear-shaft-saver-klx230", SLUGS);
	assert.deepEqual(r.slugs, ['gear-shaft-saver-klx230']);
	assert.equal(r.caption, "Rahul's KLX 230 after a day on the trails 🔥");
});

test('hashtags, capitals and product links all count', () => {
	const r = parseCaption(
		'Loading up #Rear-Lift-Strap and BAR-ENDS https://shop.dirtshack.in/product/gear-shaft-saver-klx230/?ref=fb',
		SLUGS,
	);
	assert.deepEqual(r.slugs, ['gear-shaft-saver-klx230', 'rear-lift-strap', 'bar-ends']);
	assert.equal(r.caption, 'Loading up and');
});

test('each product is listed once', () => {
	const r = parseCaption('bar-ends bar-ends #bar-ends', SLUGS);
	assert.deepEqual(r.slugs, ['bar-ends']);
	assert.equal(r.caption, '');
});

test('slugs inside longer words do not count', () => {
	const r = parseCaption('xgear-shaft-saver-klx230 gear-shaft-saver-klx230s rear-lift', SLUGS);
	assert.deepEqual(r.slugs, []);
	assert.equal(r.caption, 'xgear-shaft-saver-klx230 gear-shaft-saver-klx230s rear-lift');
});

test('punctuation next to a removed slug is tidied', () => {
	const r = parseCaption('Fitted gear-shaft-saver-klx230, rear-lift-strap today.', SLUGS);
	assert.deepEqual(r.slugs, ['gear-shaft-saver-klx230', 'rear-lift-strap']);
	assert.equal(r.caption, 'Fitted, today.');
});

test('lines left with only symbols are dropped, emoji lines are kept', () => {
	const r = parseCaption('Sunday ride\n→ gear-shaft-saver-klx230\n🔥 bar-ends', SLUGS);
	assert.equal(r.caption, 'Sunday ride\n🔥');
});

test('other links stay in the caption', () => {
	const r = parseCaption('Video: https://youtu.be/abc123 rear-lift-strap', SLUGS);
	assert.deepEqual(r.slugs, ['rear-lift-strap']);
	assert.equal(r.caption, 'Video: https://youtu.be/abc123');
});

test('a link to a product that is not in the shop is still removed from the caption', () => {
	const r = parseCaption('Old one https://shop.dirtshack.in/product/retired-thing/', SLUGS);
	assert.deepEqual(r.slugs, []);
	assert.equal(r.caption, 'Old one');
});

test('blank lines are collapsed and an empty caption is fine', () => {
	assert.equal(parseCaption('Line one\n\n\n\nbar-ends\n\nLine two', SLUGS).caption, 'Line one\n\nLine two');
	assert.deepEqual(parseCaption(undefined, SLUGS), { slugs: [], caption: '' });
});
