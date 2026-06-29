#!/usr/bin/env bash
# =============================================================================
# sync-local-wp.sh — Sync between Local WP and this git repo
#
# Direction:
#   --pull   Copy from Local WP → git repo  (after AI does dev work in Local WP)
#   --push   Copy from git repo → Local WP  (after you commit/edit here)
#
# Usage:
#   ./sync-local-wp.sh --pull          # dry run: see what would change
#   ./sync-local-wp.sh --pull --apply  # actually copy files
#   ./sync-local-wp.sh --push          # dry run: see what would change
#   ./sync-local-wp.sh --push --apply  # actually copy files
# =============================================================================

LOCAL_WP="/Users/sohamdalvi/Local Sites/dirtshack/app/public/wp-content"
GIT_REPO="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/wp-content"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'; NC='\033[0m'
info()    { echo -e "${GREEN}▶${NC} $*"; }
warn()    { echo -e "${YELLOW}⚠${NC}  $*"; }
error()   { echo -e "${RED}✖${NC}  $*"; }
section() { echo -e "${CYAN}$*${NC}"; }

# ── Argument parsing ──────────────────────────────────────────────────────────
DIRECTION=""
APPLY=false

for arg in "$@"; do
  case "$arg" in
    --pull)  DIRECTION="pull" ;;
    --push)  DIRECTION="push" ;;
    --apply) APPLY=true ;;
  esac
done

if [[ -z "$DIRECTION" ]]; then
  error "Missing direction. Usage:"
  echo "  $0 --pull [--apply]   # Local WP → git repo"
  echo "  $0 --push [--apply]   # git repo → Local WP"
  exit 1
fi

# ── Validate paths ────────────────────────────────────────────────────────────
if [[ ! -d "$LOCAL_WP" ]]; then
  error "Local WP path not found: $LOCAL_WP"
  error "Is Local by Flywheel running and the site started?"
  exit 1
fi

if [[ ! -d "$GIT_REPO" ]]; then
  error "Git repo wp-content not found: $GIT_REPO"
  exit 1
fi

# ── Sync pairs: paths relative to wp-content root ────────────────────────────
# Add new paths here as the project grows.
SYNC_PATHS=(
  "themes/ohio-child"
  "themes/ohio"
  "plugins/ohio-extra"
  "plugins/ohio-portfolio"
  "mu-plugins/dirtshack-gst-report.php"
)

# ── Excludes ──────────────────────────────────────────────────────────────────
EXCLUDES=(
  --exclude="*.log"
  --exclude=".DS_Store"
  --exclude=".git/"
  --exclude="node_modules/"
  --exclude="cache/"
  --exclude="*.map"
)

# ── Direction labels ──────────────────────────────────────────────────────────
if [[ "$DIRECTION" == "pull" ]]; then
  SRC_ROOT="$LOCAL_WP"
  DST_ROOT="$GIT_REPO"
  SRC_LABEL="Local WP"
  DST_LABEL="git repo"
else
  SRC_ROOT="$GIT_REPO"
  DST_ROOT="$LOCAL_WP"
  SRC_LABEL="git repo"
  DST_LABEL="Local WP"
fi

# ── Banner ────────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════════════"
section "  DirtShack Local ↔ Git Repo Sync"
echo "  Direction : ${SRC_LABEL} → ${DST_LABEL}"
if $APPLY; then
  info "LIVE SYNC — files will be overwritten."
else
  warn "DRY RUN — no files will be changed."
  warn "Add --apply to actually sync."
fi
echo "════════════════════════════════════════════════════"
echo ""

# ── Rsync options ─────────────────────────────────────────────────────────────
RSYNC_OPTS=(
  --archive
  --checksum
  --human-readable
  --itemize-changes
  --delete
)
$APPLY || RSYNC_OPTS+=(--dry-run)

# ── Run sync ──────────────────────────────────────────────────────────────────
TOTAL_CHANGES=0

for rel_path in "${SYNC_PATHS[@]}"; do
  SRC="${SRC_ROOT}/${rel_path}"
  DST="${DST_ROOT}/${rel_path}"

  # For directories: ensure trailing slash on src (rsync sync-contents semantics)
  if [[ -d "$SRC" ]]; then
    SRC="${SRC%/}/"
    DST="${DST%/}/"
    $APPLY && mkdir -p "$DST"
  elif [[ -f "$SRC" ]]; then
    # Ensure parent directory exists for single-file syncs
    $APPLY && mkdir -p "$(dirname "$DST")"
  else
    warn "Skipping ${rel_path} — not found in ${SRC_LABEL}."
    continue
  fi

  info "Syncing  ${rel_path}"

  OUTPUT=$(rsync "${RSYNC_OPTS[@]}" "${EXCLUDES[@]}" "$SRC" "$DST" 2>&1)
  EXIT_CODE=$?

  if [[ $EXIT_CODE -ne 0 ]]; then
    error "rsync failed for ${rel_path} (exit ${EXIT_CODE}):"
    echo "$OUTPUT"
    exit $EXIT_CODE
  fi

  # rsync --itemize-changes prints one line per file it considers. openrsync (the
  # rsync shipped with macOS) emits a no-op line for an explicitly-named single
  # file even when it's identical — ">f......." with ALL dots after the type, i.e.
  # no attribute differs — only during --dry-run. The real --apply run correctly
  # skips it, which is why a dry run could report 2 files but --apply copies 1. A
  # genuine change always carries at least one non-dot flag (e.g. ">fcst...."), so
  # keep only those lines. This makes the dry-run count match what --apply does.
  CHANGED=$(echo "$OUTPUT" | awk '/^[<>ch][fdDLS]/ && substr($1,3) ~ /[^.]/')
  if [[ -n "$CHANGED" ]]; then
    FILE_CHANGES=$(echo "$CHANGED" | wc -l | tr -d ' ')
    echo "$CHANGED" | head -20
    [[ $FILE_CHANGES -gt 20 ]] && echo "  ... and $((FILE_CHANGES - 20)) more"
    TOTAL_CHANGES=$((TOTAL_CHANGES + FILE_CHANGES))
  else
    echo "  ✓ Already in sync."
  fi
  echo ""
done

# ── Summary ───────────────────────────────────────────────────────────────────
echo "════════════════════════════════════════════════════"
if [[ $TOTAL_CHANGES -eq 0 ]]; then
  info "Everything is already in sync."
elif $APPLY; then
  info "${TOTAL_CHANGES} file(s) copied from ${SRC_LABEL} to ${DST_LABEL}."
  if [[ "$DIRECTION" == "pull" ]]; then
    echo ""
    warn "Don't forget to: cd $(dirname "$0") && git add -p && git commit"
  fi
else
  warn "${TOTAL_CHANGES} file(s) would be updated. Run with --apply to sync."
fi
echo "════════════════════════════════════════════════════"
echo ""
