# DirtShack — WordPress / WooCommerce Project Repo

Version-controlled source for the DirtShack site. Tracks **custom code, deploy
tooling, and database structure only** — not the WordPress core, not media, and
not any live order/customer/classifieds data.

## Layout

```
dirtshack-wp/
├── wp-content/
│   ├── themes/
│   │   ├── ohio/            # Parent theme (Colabrio) — vendored for reference
│   │   └── ohio-child/      # Active child theme — ALL custom code lives here
│   └── plugins/
│       ├── ohio-extra/      # Theme companion plugin
│       └── ohio-portfolio/  # Theme companion plugin
├── database/
│   ├── schema.sql           # Structure-only export (CREATE TABLEs, no data)
│   └── migrations/          # Hand-written, versioned SQL migration scripts
├── deploy/
│   └── deploy.sh            # rsync-over-SSH deploy script (see ARCHITECTURE.md)
├── .gitignore
└── README.md
```

`wp-content/ARCHITECTURE.md` documents the homepage build, DB fixes, and gotchas.

## What is committed

- `ohio-child` theme + `ohio` parent theme
- `ohio-extra` / `ohio-portfolio` companion plugins
- `database/schema.sql` — table structure only
- Custom SQL migration scripts under `database/migrations/`
- Deploy script under `deploy/`
- Plugin-config / settings exports that are code-like and contain no PII
  (WooCommerce settings, ACF field-group exports, WPCode snippets, Elementor
  templates, WP All Import/Export profiles) — add under the relevant folder

## What is NOT committed (see `.gitignore`)

- WordPress core (`wp-admin/`, `wp-includes/`, `wp-*.php`)
- `wp-content/uploads/` and any media
- Cache directories (`cache/`, `wpo-cache/`, `upgrade/`)
- LocalWP logs and `*.log`
- Third-party plugins (managed via WP admin / Envato)
- **Database dumps containing live data** — orders, users, classifieds.
  Only the schema export and migration scripts go in git.

## Database

`database/schema.sql` is a structure-only snapshot (data rows stripped). To
regenerate it from a LocalWP full dump:

```sh
grep -v '^INSERT INTO' full-dump.sql > database/schema.sql
```

Apply schema-changing operations as new files in `database/migrations/`
(e.g. `001-add-foo-table.sql`) so DB changes are reviewable and replayable.

## Deploy

See `deploy/deploy.sh` and `wp-content/ARCHITECTURE.md`. The script rsyncs the
child theme, parent theme, and companion plugins to the live host over SSH.
Note: the script originally ran from `wp-content/`; adjust paths if invoking it
from this repo layout. Always purge the host cache after deploying.
