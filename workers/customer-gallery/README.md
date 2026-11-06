# Customer Gallery sync (Cloudflare Worker)

Customer photos for the shop's product pages, kept entirely off Bluehost.

```
Facebook album ──hourly──▶ this Worker ──▶ R2 bucket at gallery.dirtshack.in
"Customer Gallery"                           photos/<id>/large.webp, thumb.webp
(caption names a product slug)               products/<slug>.json, all.json, status.json
                                                          │
                       shop product pages load products/<slug>.json in the browser
```

Every hour the Worker reads the album. Photos whose caption contains a shop
product's slug are copied into R2 at two sizes that Facebook already provides
(about 1600 px and 480 px wide, WebP). Nothing is resized here. A JSON list is
written for each product. The shop only reads those lists, so Bluehost stores
nothing and does no extra work.

## Captions

Put the product's slug (the end of its address) anywhere in the caption:

```
Rahul's KLX 230 after a day on the trails 🔥
gear-shaft-saver-klx230
```

- Several slugs put the photo on several product pages.
- `#gear-shaft-saver-klx230`, capitals, and full product links
  (`https://shop.dirtshack.in/product/gear-shaft-saver-klx230/`) also work.
- The site shows the caption without the slugs and product links.
- Only slugs of products the shop currently lists count. A typo leaves the photo
  off the site and lists it under `unmatched` in `status.json`. Fix the caption on
  Facebook and the next run picks it up.
- To take a photo down, delete it from the album or remove its slug.

## One-time setup

### 1. R2 bucket and its address

1. Cloudflare dashboard → **R2 Object Storage** → turn R2 on (Cloudflare may ask
   for a card; the free tier is 10 GB with no charge for downloads).
2. **Create bucket** named `dirtshack-customer-gallery`.
3. Bucket → **Settings → Custom Domains → Connect Domain** → `gallery.dirtshack.in`.
   (The `r2.dev` address is rate-limited and only meant for testing.)

### 2. Rules on the dirtshack.in zone

- **Let the shop's pages read the lists.** Rules → **Transform Rules → Response
  Header Transform Rule** → *Hostname equals* `gallery.dirtshack.in` → **Set static**
  header `Access-Control-Allow-Origin` = `*`. (A header rule applies to cached
  responses too, unlike the bucket's own CORS setting.)
- **Cache the JSON lists.** Rules → **Cache Rules** → *Hostname equals*
  `gallery.dirtshack.in` → **Eligible for cache**, Edge TTL **Use cache-control
  header if present**. Photos are cached for a year, lists for 5 minutes, and
  `status.json` not at all. (Cloudflare skips `.json` files without this rule.)
- **Optional:** Security → **WAF → Custom rules** → *Hostname equals*
  `gallery.dirtshack.in` AND *URI Path starts with* `/_sync/` → **Block**. That
  folder holds the Worker's own record. It contains nothing that isn't already
  public on the album, but it doesn't need to be readable.

### 3. Facebook token and album ID

The Worker needs a Page token even though the album is public. Use a system user
from the business portfolio that owns the Meta Shop, so the token isn't tied to
anyone's personal login:

1. developers.facebook.com → create an app of type **Business**, linked to the
   business portfolio.
2. Business Settings → **Users → System users** → add one. Give it access to the
   DirtShack Page and to the app.
3. **Generate token** → choose the app, expiry **Never**, permissions
   `pages_show_list` and `pages_read_engagement`.
4. Paste it into Meta's **Access Token Debugger** and check it says "Expires: Never".

The **album ID** is the number after `set=a.` in the album's address, for example
`facebook.com/media/set/?set=a.1234567890&type=3` → `1234567890`. Put it in
`FB_ALBUM_ID` in `wrangler.toml`.

### 4. Deploy

From this folder (`workers/customer-gallery`):

```sh
npx wrangler login
npx wrangler deploy
npx wrangler secret put FB_PAGE_TOKEN
npx wrangler secret put SYNC_SECRET
```

For `SYNC_SECRET`, paste a long random string (for example the output of
`openssl rand -hex 32`) and keep a copy. It turns on "Sync now". Each
`secret put` takes effect straight away.

### 5. First sync

```sh
curl -X POST -H "Authorization: Bearer <SYNC_SECRET>" \
  https://dirtshack-customer-gallery.<your-workers-subdomain>.workers.dev/sync
```

The reply is the same report as `https://gallery.dirtshack.in/status.json`.
`"ok": true` with `counts.published` above 0 means it works. Then check
`https://gallery.dirtshack.in/products/<slug>.json`. Add `?recheck=1` to the
`/sync` address to recheck every photo against the shop straight away.

### 6. Turn it on in WordPress

The shop side is `wp-content/themes/ohio-child/inc/customer-gallery.php` and
`assets/js/customer-gallery.js`. It stays off until the server's `wp-config.php`
has:

```php
define( 'DIRTSHACK_GALLERY_URL', 'https://gallery.dirtshack.in' );

// Optional: a "Sync now" button in the admin.
define( 'DIRTSHACK_GALLERY_SYNC_URL', 'https://dirtshack-customer-gallery.<your-workers-subdomain>.workers.dev/sync' );
define( 'DIRTSHACK_GALLERY_SYNC_SECRET', '<the SYNC_SECRET from step 4>' );
```

Then:

- **Product pages** get a "Customer Gallery" thumbnail strip between the main
  image and the description; photos open full screen, where visitors can page
  through them. It stays hidden until the browser finds photos for that product, so a product without photos looks the
  same as before. The page HTML doesn't change when photos do, so new photos
  never need a cache purge.
- **Gallery page:** create a page with the slug `customer-gallery` containing
  `[customer_gallery]`. It shows every photo with a filter per product.
  `[customer_gallery product="bar-ends"]` starts filtered, as
  does `?product=bar-ends` in the address.
- **Products → Customer Gallery** in the admin shows the last sync, errors,
  captions without a product slug (with links to fix them on Facebook), every
  product's slug with a Copy button, and "Sync now".

## What it writes

| R2 key | What | Cached |
|---|---|---|
| `photos/<fb-photo-id>/large.webp` | ~1600 px wide | 1 year |
| `photos/<fb-photo-id>/thumb.webp` | ~480 px wide | 1 year |
| `products/<slug>.json` | that product's photos, newest first | 5 min |
| `all.json` | every photo, plus product names and links | 5 min |
| `status.json` | last run, errors, unmatched and waiting photos | no |
| `_sync/state.json` | the Worker's record of what it has copied | no |

Files are named by Facebook photo ID, so a photo on two products is stored once.
If Facebook has no WebP version, the files end in `.jpg` instead.

`products/<slug>.json`:

```json
{
  "slug": "gear-shaft-saver-klx230",
  "photos": [
    {
      "id": "1234567890",
      "caption": "Rahul's KLX 230 after a day on the trails 🔥",
      "date": "2026-11-01T08:00:00+00:00",
      "fb": "https://www.facebook.com/photo/?fbid=1234567890",
      "thumb": { "url": "https://gallery.dirtshack.in/photos/1234567890/thumb.webp", "width": 480, "height": 360 },
      "large": { "url": "https://gallery.dirtshack.in/photos/1234567890/large.webp", "width": 1440, "height": 1080 },
      "products": ["gear-shaft-saver-klx230"]
    }
  ]
}
```

A product with no photos has no file, so its address answers 404. `all.json` has
the same photo entries plus `"products": { "<slug>": { "name", "url" } }`.

## How runs behave

- **Hourly** at 17 minutes past. A new photo shows on the site within about an
  hour plus 5 minutes of caching, or right after a manual `/sync`.
- **Free-plan limits** (50 outside requests and 10 ms of CPU per run): a run reads
  the album (1 request per 100 photos), the shop's product list (1), image sizes
  (1 per photo), then 2 downloads per photo. That's about 14 new photos per run; a bigger
  backlog continues on the following runs, newest photos first.
- **Daily recheck:** once a day every photo is checked against the shop again.
  Photos of a product that was deleted, renamed or hidden drop off the site but
  keep their files, and come back without another download once the caption or
  product matches again.
- **Safety:** if Facebook or the shop can't be read, nothing already on the site
  changes. An empty album reply never deletes anything, and removed photos are
  only cleaned up after a complete read of the album.
- **Copy failures:** a photo that fails to copy 5 times waits for the next daily
  recheck instead of being retried every hour.

## Problems

Errors appear in `status.json`, in the `/sync` reply, and in the Worker's logs
(dashboard → Workers → dirtshack-customer-gallery → Logs, or `npx wrangler tail`).

| Error | Fix |
|---|---|
| `Facebook: … (the token is invalid or has expired …)` | Make a new token (step 3) and run `npx wrangler secret put FB_PAGE_TOKEN`. |
| `Facebook: Unsupported get request …` | Wrong `FB_ALBUM_ID`, or the token's Page doesn't own the album. |
| `Facebook returned no photos for the album …` | Same checks. Nothing was deleted. |
| `Could not read the shop's product list (the shop answered HTTP 403)` | Cloudflare security on shop.dirtshack.in is blocking the Worker. Add a WAF skip rule for the path `/wp-json/wc/store/v1/products`. |
| `Missing settings: …` | Set the named variable in `wrangler.toml` or secret, then deploy. |

## Tests

```sh
npm test
```

Node 20 or newer, no install needed. The tests use stand-ins for Facebook, the
shop, the image server and R2, so they run offline.
