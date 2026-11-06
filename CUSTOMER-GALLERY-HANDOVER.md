# Customer Gallery: handover

Status on **6 Nov 2026**: **live** on shop.dirtshack.in. The repo changes are
**not committed yet**.

## What it is

Customers' photos of DirtShack parts fitted on their bikes, shown on the
matching product pages and on one gallery page. Nothing is stored on Bluehost,
and Bluehost does no extra work.

```
Facebook album "CUSTOMER GALLERY"   (caption contains a product slug, e.g. rear-lift-strap)
        │  every hour (or "Sync now")
        ▼
Cloudflare Worker "dirtshack-customer-gallery"
        │  copies Facebook's 1600 px + 480 px WebP versions, writes JSON lists
        ▼
Cloudflare R2 bucket "dirtshack-customer-gallery", served at https://gallery.dirtshack.in
        │  the visitor's browser fetches products/<slug>.json
        ▼
shop.dirtshack.in product pages + /customer-gallery/ page
```

## How to use it day to day

- **Add a photo:** post it to the **CUSTOMER GALLERY** album on the Dirt Shack
  Facebook Page. Put the product's slug anywhere in the caption (any extra text is
  shown as the caption). It shows up within the hour, or straight away with
  **Products → Customer Gallery → Sync now** in wp-admin.
- **Remove a photo:** delete it from the album, or remove the slug from its caption.
- **Find a product's slug:** wp-admin → Products → Customer Gallery lists every
  product's slug with a Copy button, plus the photo count per product.
- **Typos:** photos whose caption has no valid slug are listed on that admin page
  under "Not linked to a product", with links to fix them on Facebook.

## Where things are

| Thing | Where / ID |
|---|---|
| Worker code, tests, setup guide | `workers/customer-gallery/` (`README.md` there has the full setup) |
| WordPress module | `wp-content/themes/ohio-child/inc/customer-gallery.php` (loaded from `functions.php`) |
| Front-end script | `wp-content/themes/ohio-child/assets/js/customer-gallery.js` |
| Worker URL ("Sync now") | `https://dirtshack-customer-gallery.soham-b43.workers.dev/sync` (POST, `Authorization: Bearer <SYNC_SECRET>`) |
| Worker schedule | hourly at :17 UTC (:47 IST) |
| R2 bucket / domain | `dirtshack-customer-gallery` / `https://gallery.dirtshack.in` (Cloudflare account `b437e0918c3939609ecacb43e4e1cc77`) |
| Sync report | `https://gallery.dirtshack.in/status.json` |
| Gallery page | `https://shop.dirtshack.in/customer-gallery/` (WP page ID 246972, content `[customer_gallery]`) |
| Admin screen | wp-admin → Products → Customer Gallery |
| Meta app | "DirtShack Customer Gallery", App ID `1482368767073746`, **Development mode**, business portfolio dirtshack.in (`621646507593863`). Use case "Manage everything on your Page" with `pages_read_engagement` and `pages_show_list`. |
| Facebook Page / album | Dirt Shack Page `781377831718849`; album "CUSTOMER GALLERY" `122187502280968781` (set in `wrangler.toml`) |
| Token | Page token from system user "Customer Gallery Sync", never expires. Stored only as the Worker secret `FB_PAGE_TOKEN`. |

**Secrets (values are not written down anywhere in the repo):**
- `FB_PAGE_TOKEN` and `SYNC_SECRET` are Worker secrets.
- The same `SYNC_SECRET` is in the live `wp-config.php` as `DIRTSHACK_GALLERY_SYNC_SECRET`.
- The live `wp-config.php` also has `DIRTSHACK_GALLERY_URL` and `DIRTSHACK_GALLERY_SYNC_URL`.

**Cloudflare rules added for `gallery.dirtshack.in`** (zone dirtshack.in):
1. **Response header transform rule:** `Access-Control-Allow-Origin: *`.
2. **Cache rule #5**, placed after "Cache everything [Template]": eligible for cache, edge TTL and browser TTL both respect the file's own cache time. Photos are cached for 1 year, lists for 5 minutes, and `status.json` isn't cached.
3. **WAF custom rule:** blocks `/_sync/*`, the Worker's own record.

## Done

- [x] Worker built and tested: 30 offline tests (`cd workers/customer-gallery && npm test`).
- [x] Worker deployed; both secrets set; first sync OK, and the hourly cron confirmed working.
- [x] R2 bucket, custom domain, and the 3 Cloudflare rules; headers and caching checked live.
- [x] Meta app, system user and Page token; the Worker reads the album.
- [x] WordPress module deployed with `deploy/deploy.sh --push`, and `wp-config.php` lines added.
- [x] What's live on the shop:
  - Product pages show a "Customer Gallery" thumbnail strip between the main
    image and the description tabs (scrolls sideways; hidden with no photos).
  - Clicking a photo opens it full screen, with previous/next, arrow keys and swipe.
  - (2026-11-06: the strip replaced the grid after the tabs and the "See it on
    customers' bikes" link under Add to cart.)
- [x] `/customer-gallery/` page published (product filters, links to products).
- [x] WP Super Cache cleared and Cloudflare purged for `shop.dirtshack.in`; verified live.
- [x] Live now: 2 photos (Rear Lift Strap; KLX230 Pair Valve Delete Kit).

## Left / open

1. **Commit the repo work.** Uncommitted:
   - `workers/` (all new)
   - `inc/customer-gallery.php`
   - `assets/js/customer-gallery.js`
   - `functions.php` (the require line)
   - `README.md` (folder map)
   - `.gitignore` (Worker local files)
   - this handover file
2. **`enduro-footpegs` photo:** not shown, because no product with that slug exists on the shop (404). Either publish that product or change the caption to an existing slug.
3. **Optional:** add real captions to the album photos. They're just the slug now, so no caption text shows on the site.
4. **Meta app stays in Development mode.** That's fine for reading your own Page. Only publish it if a sync ever reports a permissions error.

## Things learned the hard way

- **Graph API v26+ rejects `?ids=` multi-photo lookups.** The Worker now does one
  `/{photo-id}?fields=images,webp_images` request per photo. That's about 14 new
  photos per hourly run on the free plan (50 outside requests per run); a bigger
  backlog continues on later runs.
- **The installed wrangler (4.100.0)** only supports compatibility dates up to
  2026-06-18, so `wrangler.toml` uses `2026-06-01`.
- **Run `wrangler` from `workers/customer-gallery`.** Run elsewhere, it complains
  about "static files" because it can't find `wrangler.toml`.
- **Commands that ask for hidden input** (`read -s`, `wrangler secret put`) must be
  run in a real Terminal window, not with `!` in Claude. Use `read -rs VAR`, which
  works in both bash and zsh.
- **Meta blocks Business Settings** in Claude's built-in browser as a "new device"
  for about an hour. Do system-user steps in your own browser.
- **Two caches sit in front of product pages:** WP Super Cache (server) and
  Cloudflare (4 h). The gallery itself never needs purging: photos load in the
  browser from `gallery.dirtshack.in`. Purge only after deploying theme changes.
- **`deploy.sh` dry run** showed `plugins/ohio-extra/error_log` (a server PHP log)
  as a deletion. That's harmless; it was removed on push.
- **Ohio's product layouts** never fire `woocommerce_after_single_product_summary`.
  The strip is printed from the `wc_get_template_part` filter for
  `single-product/tabs/tabs`, which all 8 layouts load right after the image +
  summary block. Fallback: start of `single-product/up-sells.php`.

## Useful checks

1. Last sync report:
   ```sh
   curl -s "https://gallery.dirtshack.in/status.json?t=$(date +%s)"
   ```
2. One product's photo list:
   ```sh
   curl -s https://gallery.dirtshack.in/products/rear-lift-strap.json
   ```
3. Manual sync from Terminal. It prompts for the secret without showing it:
   ```sh
   printf "SYNC_SECRET: "; read -rs S; echo; curl -sS -X POST -H "Authorization: Bearer $S" "https://dirtshack-customer-gallery.soham-b43.workers.dev/sync?recheck=1"
   ```
4. Worker logs: Cloudflare dashboard → Workers & Pages → dirtshack-customer-gallery → Observability. Or live:
   ```sh
   cd workers/customer-gallery && npx wrangler tail
   ```
