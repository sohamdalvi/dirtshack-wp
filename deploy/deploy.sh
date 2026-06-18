#!/usr/bin/env bash
# =============================================================================
# DirtShack → Bluehost deploy script
# Syncs only changed theme and plugin files over SSH/rsync.
# Usage:
#   ./deploy.sh          — dry run (shows what WOULD change, touches nothing)
#   ./deploy.sh --push   — live deploy
# =============================================================================

# ── Configure these once ─────────────────────────────────────────────────────
REMOTE_USER="pgezuwmy"
REMOTE_HOST="119.18.49.18"
REMOTE_ROOT="/home/${REMOTE_USER}/public_html/wp-content"
SSH_PORT=22                                 # Bluehost default; change if needed
# Optional: path to a specific SSH key (leave blank to use ~/.ssh/id_rsa)
SSH_KEY=""
# =============================================================================

LOCAL_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../wp-content" && pwd )"

# ── Colour helpers ────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
info()    { echo -e "${GREEN}▶${NC} $*"; }
warn()    { echo -e "${YELLOW}⚠${NC}  $*"; }
error()   { echo -e "${RED}✖${NC}  $*"; }

# ── Argument parsing ──────────────────────────────────────────────────────────
DRY_RUN=true
if [[ "$1" == "--push" ]]; then
  DRY_RUN=false
fi

# ── Validate config ───────────────────────────────────────────────────────────
if [[ "$REMOTE_USER" == "YOUR_CPANEL_USERNAME" ]]; then
  error "Edit deploy.sh and set REMOTE_USER before running."
  exit 1
fi

# ── Build SSH / rsync options ─────────────────────────────────────────────────
SSH_OPTS="-p ${SSH_PORT} -o StrictHostKeyChecking=no -o ConnectTimeout=10"
[[ -n "$SSH_KEY" ]] && SSH_OPTS+=" -i ${SSH_KEY}"

RSYNC_OPTS=(
  --archive            # recursive, preserve permissions/timestamps/symlinks
  --compress           # compress during transfer
  --human-readable     # readable sizes
  --itemize-changes    # show exactly which files changed
  --checksum           # compare by content, not just timestamp (most reliable)
  --delete             # remove remote files deleted locally
  -e "ssh ${SSH_OPTS}"
)

$DRY_RUN && RSYNC_OPTS+=(--dry-run)

# ── What gets synced ──────────────────────────────────────────────────────────
# Each entry: LOCAL_DIR  REMOTE_DIR
declare -a SYNC_PAIRS=(
  "themes/ohio-child/                    themes/ohio-child/"
  "themes/ohio/                          themes/ohio/"
  "plugins/ohio-extra/                   plugins/ohio-extra/"
  "plugins/ohio-portfolio/               plugins/ohio-portfolio/"
  "mu-plugins/dirtshack-gst-report.php   mu-plugins/dirtshack-gst-report.php"
)

# ── Safety: never sync these even if paths are changed above ─────────────────
GLOBAL_EXCLUDES=(
  --exclude="*.log"
  --exclude=".DS_Store"
  --exclude=".git/"
  --exclude="node_modules/"
  --exclude="cache/"
  --exclude="wpo-cache/"
)

# ── Banner ────────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════════════"
echo "  DirtShack Deploy"
if $DRY_RUN; then
  warn "DRY RUN — no files will be changed on the server."
  warn "Run with --push to deploy for real."
else
  info "LIVE DEPLOY to ${REMOTE_USER}@${REMOTE_HOST}"
fi
echo "════════════════════════════════════════════════════"
echo ""

# ── Run rsync for each pair ───────────────────────────────────────────────────
CHANGED=0
for pair in "${SYNC_PAIRS[@]}"; do
  LOCAL_DIR=$(echo "$pair" | awk '{print $1}')
  REMOTE_DIR=$(echo "$pair" | awk '{print $2}')

  LOCAL_PATH="${LOCAL_ROOT}/${LOCAL_DIR}"
  REMOTE_PATH="${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_ROOT}/${REMOTE_DIR}"

  if [[ ! -e "$LOCAL_PATH" ]]; then
    warn "Skipping ${LOCAL_DIR} — path not found locally."
    continue
  fi

  info "Syncing  ${LOCAL_DIR%/}"
  echo "  └─ → ${REMOTE_ROOT}/${REMOTE_DIR%/}"

  OUTPUT=$(rsync "${RSYNC_OPTS[@]}" "${GLOBAL_EXCLUDES[@]}" \
    "$LOCAL_PATH" "$REMOTE_PATH" 2>&1)
  EXIT_CODE=$?

  if [[ $EXIT_CODE -ne 0 ]]; then
    error "rsync failed for ${LOCAL_DIR} (exit ${EXIT_CODE}):"
    echo "$OUTPUT"
    exit $EXIT_CODE
  fi

  # Count changed files (lines starting with >, <, c, h)
  FILE_CHANGES=$(echo "$OUTPUT" | grep -cE '^[><ch]' || true)
  if [[ $FILE_CHANGES -gt 0 ]]; then
    echo "$OUTPUT" | grep -E '^[><ch]' | head -20
    [[ $FILE_CHANGES -gt 20 ]] && echo "  ... and $((FILE_CHANGES - 20)) more"
    CHANGED=$((CHANGED + FILE_CHANGES))
  else
    echo "  ✓ No changes."
  fi
  echo ""
done

# ── Summary ───────────────────────────────────────────────────────────────────
echo "════════════════════════════════════════════════════"
if $DRY_RUN; then
  if [[ $CHANGED -gt 0 ]]; then
    warn "${CHANGED} file(s) would be updated. Run with --push to deploy."
  else
    info "Nothing to sync — remote is already up to date."
  fi
else
  if [[ $CHANGED -gt 0 ]]; then
    info "${CHANGED} file(s) synced to production."
    info "Remember to purge the cache on dirtshack.in !"
  else
    info "Nothing changed — remote was already up to date."
  fi
fi
echo "════════════════════════════════════════════════════"
echo ""
