#!/usr/bin/env bash
# =============================================================================
# Re-apply the DirtShack cart-JS patch after an Ohio theme update.
#
# Ohio's assets/js/woocommerce.min.js refreshes the cart with a jQuery call that
# was written as `.on("load", URL+" .selector", cb)` instead of the AJAX
# `.load(URL+" .selector", cb)`. jQuery 3.7 throws on the URL-as-selector and the
# cart stops updating live. We ship a patched COPY in the child theme and swap it
# in via functions.php (dirtshack_fix_ohio_cart_js). That copy must be refreshed
# whenever Ohio is updated.
#
# Usage:  bash repatch-ohio-cart.sh        (run from wp-content/, after updating Ohio)
# Run it on BOTH local and live (or run, then deploy.sh --push).
# =============================================================================
set -euo pipefail

ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SRC="$ROOT/themes/ohio/assets/js/woocommerce.min.js"
DST="$ROOT/themes/ohio-child/assets/js/ohio-woocommerce-jq3fix.js"

BUG='.on("load",o+" '          # the broken pattern (URL prepended to a selector)

[[ -f "$SRC" ]] || { echo "✖ Ohio source not found: $SRC"; exit 1; }

n_bug="$(grep -o "$BUG" "$SRC" | wc -l | tr -d ' ')"
echo "Ohio woocommerce.min.js buggy '.on(\"load\",o+' occurrences: $n_bug"

if [[ "$n_bug" == "0" ]]; then
  echo "ℹ Ohio no longer has the bug (maybe they fixed it)."
  echo "  → If so, the override is no longer needed: you can DELETE"
  echo "    themes/ohio-child/assets/js/ohio-woocommerce-jq3fix.js and remove"
  echo "    dirtshack_fix_ohio_cart_js() from the child functions.php."
  echo "  Refreshing the copy anyway (unpatched) so it stays current:"
fi

cp "$SRC" "$DST"

# Apply the two fixes (idempotent; runs whether or not the bug is present)
python3 - "$DST" <<'PY'
import sys
p = sys.argv[1]
s = open(p).read()
before = s.count('.on("load",o+" ')
s = s.replace('.on("load",o+" .shop_table.cart:eq(0) > *"', '.load(o+" .shop_table.cart:eq(0) > *"')
s = s.replace('.on("load",o+" .cart_totals:eq(0) > *"',    '.load(o+" .cart_totals:eq(0) > *"')
after = s.count('.on("load",o+" ')
loadcount = s.count('.load(o+" ')
open(p, 'w').write(s)
print("  patched: %d buggy -> %d remaining; .load(o+ now: %d" % (before, after, loadcount))
PY

echo "✓ Re-patched: $DST"
echo "Next: deploy (deploy.sh --push), purge WP Super Cache, hard-refresh, and test"
echo "      add-to-cart + cart quantity update."
