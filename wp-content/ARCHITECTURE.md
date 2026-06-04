# DirtShack — Project Architecture & Analysis
*Generated: 2026-05-29*

---

## 1. Executive Summary

DirtShack is a WooCommerce-powered e-commerce store built on WordPress, using the **Ohio theme v3.6.8** (by Colabrio/clbthemes.com). It was originally hosted on Bluehost and has been migrated into a LocalWP development environment. The site uses Razorpay as its payment gateway (India-focused), MSG91 for SMS notifications, and a suite of WooCommerce utility plugins for order management, shipping, invoicing, and checkout customisation.

There is **no child theme** — all theme customisation lives directly inside the Ohio parent theme. There is also **no custom plugin** (`dirtshack-core` is referenced in `.gitignore` but does not exist). Ohio's companion plugins (`ohio-extra`, `ohio-portfolio`) provide WPBakery/Elementor shortcodes, ACF-extended theme settings, and portfolio post types.

---

## 2. Architecture Document

### 2.1 WordPress Structure

| Item | Value |
|---|---|
| WordPress root | `app/public/` |
| DB name | `local` |
| Table prefix | `wp_` |
| Environment type | `local` (set in `wp-config.php`) |
| WP_DEBUG | `false` |
| WooCommerce version | **10.7.0** |

**Notable wp-config.php settings:**
- `WP_ENVIRONMENT_TYPE = 'local'` — useful for environment-aware code
- Standard LocalWP credentials (`root/root`, `localhost`)
- No custom `WP_HOME` or `WP_SITEURL` hardcoded (stored in DB)

### 2.2 Theme Structure

| Item | Detail |
|---|---|
| Active theme | **Ohio v3.6.8** (Colabrio) |
| Child theme | **None** — no child theme exists |
| Theme URI | `ohio.clbthemes.com` |

Ohio is a commercial theme from [clbthemes.com](https://clbthemes.com). Updates are delivered via the **Envato Market** plugin (authenticated via Envato token).

**Theme file organisation:**
```
themes/ohio/
├── functions.php         # Bootstraps all inc/ files
├── inc/
│   ├── init/             # theme.php, customizer.php, extras.php
│   ├── framework/        # Core Ohio framework (options, layout, icon_manager, settings)
│   │   └── components/   # Reusable UI components
│   ├── dynamic_css/      # Customizer-driven CSS generation
│   ├── tgmpa/            # Plugin activation (ACF, WooCommerce, OCDI setup)
│   │   └── woocommerce_setup.php  # ← KEY FILE: all WooCommerce hooks/filters
│   ├── menu.php          # Mega menu
│   ├── sidebars.php      # Sidebar registration
│   ├── template_tags.php # Custom template tags
│   └── wp_overrides.php  # WP core overrides
├── woocommerce/          # 50+ overridden WooCommerce templates (see §2.3)
├── page_templates/       # page_for-builder.php, page_for-posts.php, page_for-projects.php
└── layouts/              # CSS layout helpers (content-sidebar, sidebar-content)
```

**Key theme customisations (in `inc/tgmpa/woocommerce_setup.php`):**
- WooCommerce default styles disabled (`woocommerce_enqueue_styles` → empty array)
- Products per page hardcoded to 12 via `woocommerce_product_query`
- Product summary hook order re-arranged (meta moved to priority 15, before add-to-cart)
- Custom AJAX add-to-cart handlers: `ohio_ajax_add_to_cart_woo` and `ohio_ajax_add_to_cart_woo_single`
- Custom AJAX product search: `ohio_ajax_search` with category filtering
- Cart fragment update for header cart icon and total
- WooCommerce notices reformatted (link order swapped, button class stripped)
- YITH Wishlist compatibility shim included (though YITH plugin itself is not installed)

### 2.3 WooCommerce Structure

**Version:** 10.7.0 (latest major as of mid-2025)

**Overridden WooCommerce templates (inside `themes/ohio/woocommerce/`):**

| Area | Overridden Templates |
|---|---|
| Archive/Shop | `archive-product.php`, `content-product.php`, `archive-views/type_1,2.php` |
| Cart | `cart.php`, `cart-totals.php`, `mini-cart.php`, `cross-sells.php`, `cart-empty.php` |
| Checkout | `form-checkout.php`, `form-billing.php`, `payment.php`, `review-order.php`, `thankyou.php` |
| My Account | `my-account.php`, `navigation.php`, `orders.php`, `downloads.php`, `my-address.php`, `form-login.php` |
| Single Product | 20+ templates including 8 layout variants (`type_1`–`type_4` with reverse options) |
| Notices | `error.php`, `notice.php`, `success.php` |
| Order | `order-details.php`, `order-details-customer.php` |
| Loop | `loop-start/end.php`, `pagination.php`, `orderby.php`, `sale-flash.php`, `no-products-found.php` |

This is a **heavily customised WooCommerce front-end**. Almost every customer-facing template is overridden. Any WooCommerce update must be followed by a template compatibility check.

**Payment Gateway:** Razorpay (`woo-razorpay` v4.8.4) — India-focused payment processor.

**Shipping:** Flexible Shipping v6.7.3 — weight/total-based shipping rules.

**Tax:** WooCommerce Tax / WooCommerce Services v3.6.3.

### 2.4 Plugin Ecosystem

| Plugin | Version | Purpose | Critical | Notes |
|---|---|---|---|---|
| **WooCommerce** | 10.7.0 | Core e-commerce engine | ✅ Yes | Foundation of the store |
| **Advanced Custom Fields PRO** | 6.7.1 | Custom fields framework used by Ohio theme | ✅ Yes | Required by Ohio Extra |
| **Ohio Extra** | 3.6.8 | WPBakery/Elementor widgets, ACF theme settings | ✅ Yes | Theme companion — must match theme version |
| **Ohio Portfolio** | 1.1.3 | Portfolio CPT + taxonomies | ⚠️ Maybe | Only needed if portfolio is used |
| **WPBakery Page Builder** | 8.7.2 | Drag-and-drop page builder (legacy) | ✅ Yes | Many pages likely use `[vc_*]` shortcodes |
| **Razorpay for WooCommerce** | 4.8.4 | Payment gateway (India) | ✅ Yes | Live API keys stored in DB |
| **Flexible Shipping** | 6.7.3 | Weight/cart-total shipping methods | ✅ Yes | Shipping rules in DB |
| **Checkout Field Editor Pro** | 2.1.8 | Custom checkout fields | ✅ Yes | Custom fields in DB options |
| **Custom Order Status Manager** | 2.0 | Custom WooCommerce order statuses | ✅ Yes | Custom statuses stored in DB |
| **PDF Invoices & Packing Slips** | 5.12.2 | Auto-generate PDF invoices | ✅ Yes | Invoice templates in uploads/ |
| **Conditional Add to Cart** | 1.2.1 | Rule-based Add to Cart button logic | ⚠️ Maybe | Depends on business rules |
| **MSG91 for WooCommerce** | 1.0.0 | SMS notifications via MSG91 (India) | ⚠️ Maybe | Needs MSG91 API key in DB |
| **Ship to Different Address** | 1.1 | Controls default state of shipping address toggle | Low | Minor UX tweak |
| **WooCommerce Services / Tax** | 3.6.3 | Tax calculation | ⚠️ Maybe | May need to be replaced with Indian GST solution |
| **Order Export & Import** | 2.7.3 | Bulk order CSV export/import | Low | Admin utility |
| **Ever Accounting** | 2.2.8 | Accounting/invoicing plugin | ⚠️ Maybe | |
| **Ever Accounting – WooCommerce** | 1.0.2 | Syncs WooCommerce orders to Ever Accounting | ⚠️ Maybe | Depends on accounting workflow |
| **reCAPTCHA for WooCommerce** | 1.4.7 | Google reCAPTCHA on checkout/login | ✅ Yes | reCAPTCHA keys in DB |
| **APCu Manager** | 4.4.0 | PHP APCu object cache management | Low | Performance — may not work on all hosts |
| **WP-Optimize** | 4.5.4 | Caching, minification, DB cleanup | ⚠️ Maybe | Cache files in `wpo-cache/` |
| **UpdraftPlus** | 1.26.4 | Backup plugin | Low | Replace with host-level backups on production |
| **WPvivid Backup** | 0.9.128 | Secondary backup/staging | Low | Redundant with UpdraftPlus |
| **WP Mail SMTP** | 4.8.0 | Reliable email delivery via SMTP/API | ✅ Yes | SMTP credentials in DB |
| **Google Site Kit** | 1.179.0 | Google Analytics/Search Console integration | Low | Analytics only |
| **Smash Balloon Instagram Feed** | 6.11.0 | Instagram feed display | Low | Social proof widget |
| **Envato Market** | 2.0.14 | Auto-updates for Ohio theme from Envato | ⚠️ Maybe | Envato token in DB |
| **Duplicate Page** | 4.5.9 | Clone pages/posts | Low | Admin utility |
| **Disable XML-RPC** | 1.0.1 | Security — disables XML-RPC endpoint | ✅ Yes | Security hardening |
| **ACF: WooCommerce** | N/A | (Part of ohio-extra, not separate) | — | — |

**mu-plugins:**
- `sso.php` (v0.5) — LocalWP SSO login helper. **This is a LocalWP-specific plugin** and must never be deployed to production.

### 2.5 Custom Development

| Item | Location | Details |
|---|---|---|
| Custom AJAX: add to cart | `themes/ohio/inc/tgmpa/woocommerce_setup.php` | `ohio_ajax_add_to_cart_woo`, `ohio_ajax_add_to_cart_woo_single` |
| Custom AJAX: product search | `themes/ohio/inc/tgmpa/woocommerce_setup.php` | `ohio_ajax_search` + `ohio_ajax_draw` |
| Custom CPT: Portfolio | `plugins/ohio-portfolio/ohio-portfolio.php` | `ohio_portfolio` post type |
| Custom Taxonomy: Portfolio Cat/Tags | `plugins/ohio-portfolio/ohio-portfolio.php` | `ohio_portfolio_category`, `ohio_portfolio_tags` |
| WooCommerce hook reordering | `themes/ohio/inc/tgmpa/woocommerce_setup.php` | Product summary hooks re-registered |
| Custom sidebar: Shop | `themes/ohio/inc/tgmpa/woocommerce_setup.php` | `wc_shop` sidebar |
| Theme Options framework | `themes/ohio/inc/framework/` | `OhioOptions` class — all theme settings |
| Dynamic CSS generation | `themes/ohio/inc/dynamic_css/` | Customizer → inline CSS |
| Page templates | `themes/ohio/page_templates/` | For-builder, for-posts, for-projects |
| WPBakery shortcodes | `plugins/ohio-extra/` | Dozens of custom `[vc_*]` / Elementor widgets |

**No custom REST API endpoints** were found.
**No custom cron jobs** were found beyond those registered by installed plugins.
**No custom database tables** were found outside of standard WooCommerce and plugin tables.

---

## 3. Database Dependencies

### Standard WooCommerce Tables (prefix: `wp_`)
- `wp_woocommerce_order_items`, `wp_woocommerce_order_itemmeta`
- `wp_woocommerce_shipping_zones`, `wp_woocommerce_shipping_zone_methods`
- `wp_woocommerce_payment_tokens`, `wp_woocommerce_sessions`
- `wp_woocommerce_tax_rates`, `wp_woocommerce_tax_rate_locations`
- `wp_woocommerce_attribute_taxonomies`

### Plugin-Specific Tables
- **Custom Order Status Manager:** Statuses stored in `wp_options` (not a separate table)
- **Flexible Shipping:** Shipping methods/rules in `wp_posts` / `wp_options`
- **Ever Accounting:** Likely creates `wp_ea_*` tables
- **Instagram Feed:** `wp_sbi_*` tables for feed cache

### Critical `wp_options` Entries
- `siteurl`, `home` — must be updated when migrating environments
- `woocommerce_*` — all WooCommerce settings
- `ohio_*` / theme `mods_ohio` — all theme customiser settings
- Razorpay API keys
- MSG91 API credentials
- WP Mail SMTP credentials
- reCAPTCHA site/secret keys
- Envato Market token
- Google Site Kit tokens

---

## 4. Git Strategy Assessment

### Current State
The git repo is inside `wp-content/` (not the WordPress root). The existing `.gitignore` has a good start but references `plugins/dirtshack-core/` which **does not exist**.

### What Should Be in Git

| Include | Reason |
|---|---|
| `themes/ohio/` | All theme files (no child theme, so this IS your custom code) |
| `plugins/ohio-extra/` | Theme companion — treat as custom |
| `plugins/ohio-portfolio/` | Theme companion — treat as custom |
| Any future custom plugin | e.g., `dirtshack-core` when created |
| `.gitignore` | This file |

### What Should Be Excluded from Git

| Exclude | Reason |
|---|---|
| `uploads/` | Media files (149 MB+), environment-specific |
| `cache/`, `wpo-cache/` | Generated cache — always excluded |
| `wpvividbackups/`, `wpvivid_uploads/`, `wpvivid_staging/` | Backup archives |
| `webtoffee_export/` | Export files |
| `upgrade/` | WordPress upgrade temp files |
| `languages/` | Can be re-generated; bloats repo |
| All third-party plugins | Managed via Composer or re-downloaded; not custom code |
| All default/fallback themes | `twentytwentyfive`, `twentytwentyfour`, `twentytwentythree` |

### Recommended `.gitignore` for `wp-content/`

```gitignore
# ─── Generated / environment-specific ───────────────────────
uploads/
cache/
wpo-cache/
upgrade/
debug.log

# ─── Backups & exports ───────────────────────────────────────
wpvividbackups/
wpvivid_uploads/
wpvivid_staging/
webtoffee_export/

# ─── Third-party plugins (not custom code) ───────────────────
plugins/*

# ─── Custom / theme-companion plugins — keep these ───────────
!plugins/ohio-extra/
!plugins/ohio-portfolio/
# Uncomment when created:
# !plugins/dirtshack-core/

# ─── Themes ──────────────────────────────────────────────────
!themes/
themes/twentytwenty*/

# ─── mu-plugins: exclude LocalWP SSO ─────────────────────────
mu-plugins/

# ─── Languages (regenerated on demand) ───────────────────────
languages/

# ─── OS / editor files ───────────────────────────────────────
.DS_Store
.idea/
.vscode/
*.log
```

---

## 5. Risk Assessment

### 🔴 High Risk

| Risk | Detail |
|---|---|
| **No child theme** | All customisations are inside the Ohio parent theme. Every Ohio theme update will overwrite your changes. You must diff carefully before updating, or create a child theme. |
| **50+ overridden WooCommerce templates** | Every WooCommerce major/minor update may break these templates. Must run `WooCommerce > Status > Tools > Outdated Templates` after every WC update. |
| **WPBakery Page Builder dependency** | WPBakery is legacy and creates `[vc_*]` shortcode lock-in. Migrating to Gutenberg later will be costly. |
| **LocalWP SSO mu-plugin** | `mu-plugins/sso.php` must be removed before any production deployment. |

### 🟡 Medium Risk

| Risk | Detail |
|---|---|
| **Ohio theme version lock** | Ohio v3.6.8 is tied to `ohio-extra` v3.6.8 and `ohio-portfolio` v1.1.3. These three must always be updated together. |
| **Razorpay live keys in DB** | Production API keys are in `wp_options`. A DB export to a dev environment carries live keys — scrub them. |
| **MSG91 / reCAPTCHA credentials in DB** | Same concern as Razorpay. Use environment-specific option overrides or WP_CONFIG constants. |
| **WP-Optimize cache in git history** | The current git repo has deleted cache files in its history (`git status` shows hundreds of `D cache/...` deletions). The `.gitignore` needs to be corrected and history cleaned. |
| **Two backup plugins installed** | UpdraftPlus + WPvivid are both installed. This is redundant and can cause conflicts. Choose one. |
| **WooCommerce Tax / Services** | This plugin connects to WooCommerce.com and is primarily US-focused. For India, a dedicated GST plugin may be more appropriate. |

### 🟢 Low Risk / Technical Debt

| Item | Detail |
|---|---|
| `dirtshack-core` referenced in `.gitignore` but doesn't exist | Clean up or create the plugin |
| APCu Manager | APCu may not be available on all hosting environments |
| YITH Wishlist shim in Ohio theme | Ohio includes a compatibility shim for YITH Wishlist, but the plugin is not installed. Dead code. |
| Three default WordPress themes present | `twentytwenty*` themes waste disk space and should be deleted |

---

## 6. Development Recommendations

### Local Development Workflow
1. Use LocalWP as-is — it works well.
2. Set `WP_DEBUG = true` and `WP_DEBUG_LOG = true` in `wp-config.php` during active development.
3. Override Razorpay to test mode in `wp-config.php`: `define('RAZORPAY_MODE', 'test');` (check plugin-specific constant name).
4. Override `siteurl`/`home` is handled automatically by LocalWP.

### Git Workflow
1. The git repo should live at `wp-content/` (as it does now) — this is the correct scope.
2. Update `.gitignore` using the recommended version above.
3. **Immediately create a child theme** before making any design changes. Without it, Ohio updates will overwrite all front-end customisations.
4. Consider creating a `dirtshack-core` plugin for any business logic (custom hooks, custom order statuses, custom post types) rather than putting code in the theme.
5. Branch strategy: `main` = production-ready, `develop` = integration, feature branches per feature.

### Deployment Workflow
1. Use WPvivid or UpdraftPlus (pick one) for full site backups before deploying.
2. **Pre-deploy checklist:**
   - Run WooCommerce template compatibility check
   - Verify `mu-plugins/sso.php` is NOT present on production
   - Scrub API keys from any DB export used in staging
   - Clear all caches post-deploy
3. For DB migration: use WP-CLI (`wp search-replace 'http://dirtshack.local' 'https://yourdomain.com'`) or Migrate DB Pro.

### Backup Strategy
- Choose one backup plugin (recommend UpdraftPlus for simplicity).
- Back up daily to cloud storage (Google Drive / Dropbox / S3).
- Keep 30 days of daily backups.
- Always back up before plugin or theme updates.

### WooCommerce Update Strategy
1. Back up database and files.
2. Update WooCommerce in staging first.
3. After update, check: **WooCommerce → Status → System Status** for outdated templates.
4. Manually diff any flagged templates against WooCommerce's bundled versions and merge changes.
5. Test: checkout flow, cart, product pages, order emails.

### Theme Update Strategy
1. Always update `ohio-extra` and `ohio-portfolio` at the same time as `ohio` theme — they are versioned together (currently all at 3.6.8).
2. Review changelog for template changes before updating.
3. **Long-term: create a child theme** to insulate custom code from updates.

---

## 7. Important Context for Future Development

> This section provides everything a developer or AI assistant needs before touching this codebase.

### Stack at a Glance
- **WordPress** + **WooCommerce 10.7.0** + **Ohio Theme 3.6.8** (no child theme)
- **WPBakery Page Builder** — most pages use `[vc_*]` shortcodes
- **Advanced Custom Fields PRO** — used for theme options and meta fields
- **Razorpay** — payment gateway (India)
- **LocalWP** running locally at `dirtshack.local`

### Before Making Any Change
- There is **no child theme**. Edits to `themes/ohio/` are directly in the parent theme and will be lost on theme update.
- WooCommerce templates are overridden in `themes/ohio/woocommerce/`. Always check WC template version compatibility.
- Ohio uses its own options framework (`OhioOptions::get()`), not standard WordPress theme mods, for most settings.
- The `mu-plugins/sso.php` is a LocalWP auto-login helper — it will not exist on production.

### Key Custom Hooks (from `themes/ohio/inc/tgmpa/woocommerce_setup.php`)
- `wp_ajax_ohio_ajax_add_to_cart_woo` / `_nopriv_` — AJAX add to cart (archive pages)
- `wp_ajax_ohio_ajax_add_to_cart_woo_single` / `_nopriv_` — AJAX add to cart (single product)
- `wp_ajax_ohio_ajax_search` / `_nopriv_` — AJAX product search with category filter

### Custom Post Types
- `ohio_portfolio` — Portfolio items (registered by `ohio-portfolio` plugin)
  - Taxonomy: `ohio_portfolio_category`
  - Taxonomy: `ohio_portfolio_tags`

### Environment-Specific Items to Never Commit
- `mu-plugins/sso.php` (LocalWP only)
- `wp-content/cache/` and `wp-content/wpo-cache/` (generated)
- Backup archives (`wpvividbackups/`)

### Plugin Inter-dependencies
- `ohio-extra` requires `advanced-custom-fields-pro` (ACF PRO)
- `eac-woocommerce` requires `wp-ever-accounting`
- `ohio-extra`, `ohio-portfolio`, and `ohio` theme must be on matching versions (currently 3.6.8)

---

## 8. Project Map

```
wp-content/
│
├── .git/                          ← Git repo root
├── .gitignore                     ← Needs updating (see §4)
│
├── mu-plugins/
│   └── sso.php                    ← LocalWP SSO — DO NOT deploy to production
│
├── themes/
│   ├── ohio/                      ← ACTIVE THEME (v3.6.8, Colabrio)
│   │   ├── functions.php          ← Theme bootstrap
│   │   ├── inc/
│   │   │   ├── framework/         ← OhioOptions, layout, icon manager
│   │   │   ├── dynamic_css/       ← Customizer → CSS
│   │   │   ├── init/              ← Theme init, customizer, extras
│   │   │   └── tgmpa/
│   │   │       └── woocommerce_setup.php  ← ⭐ KEY: WC hooks/AJAX/filters
│   │   └── woocommerce/           ← 50+ overridden WC templates
│   └── twentytwenty*/             ← Default themes (unused, safe to delete)
│
├── plugins/
│   ├── ohio-extra/                ← Theme companion (v3.6.8) — WPBakery/Elementor widgets
│   ├── ohio-portfolio/            ← CPT: ohio_portfolio + taxonomies
│   ├── woocommerce/               ← WooCommerce core (v10.7.0)
│   ├── woo-razorpay/              ← Payment: Razorpay (v4.8.4)
│   ├── flexible-shipping/         ← Shipping rules
│   ├── advanced-custom-fields-pro/← ACF PRO (required by Ohio)
│   ├── js_composer/               ← WPBakery Page Builder (v8.7.2)
│   ├── woo-checkout-field-editor-pro/ ← Custom checkout fields
│   ├── bp-custom-order-status-for-woocommerce/ ← Custom order statuses
│   ├── msg91-for-woocommerce/     ← SMS notifications (India)
│   ├── woocommerce-pdf-invoices-packing-slips/ ← PDF invoices
│   ├── conditional-add-to-cart/   ← Rule-based cart button
│   ├── recaptcha-woo/             ← reCAPTCHA on checkout/login
│   ├── wp-mail-smtp/              ← SMTP email delivery
│   ├── woocommerce-services/      ← WC Tax
│   ├── flexible-shipping/         ← Weight/total-based shipping
│   ├── wp-ever-accounting/        ← Accounting
│   ├── eac-woocommerce/           ← WC ↔ Ever Accounting sync
│   ├── google-site-kit/           ← Analytics
│   ├── instagram-feed/            ← Instagram widget
│   ├── wp-optimize/               ← Cache & performance
│   ├── updraftplus/               ← Backups
│   ├── wpvivid-backuprestore/     ← Backups (redundant)
│   ├── envato-market/             ← Ohio theme auto-updates
│   ├── ship-to-a-different-address-checked-unchecked/
│   ├── order-import-export-for-woocommerce/
│   ├── duplicate-page/
│   ├── disable-xml-rpc/           ← Security
│   └── apcu-manager/              ← PHP cache manager
│
├── uploads/                       ← Media (149 MB) — NOT in git
│
├── wpo-cache/                     ← WP-Optimize cache — NOT in git
├── wpvividbackups/                ← Backup archives — NOT in git
└── webtoffee_export/              ← Order exports — NOT in git
```
