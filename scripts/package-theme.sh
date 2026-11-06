#!/usr/bin/env bash
# Build dist/enjoy-naturally-theme.zip for Appearance → Themes → Upload.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME="$ROOT/wp-content/themes/enjoy-naturally"
OUT="$ROOT/dist"
ZIP="$OUT/enjoy-naturally-theme.zip"

if [[ ! -f "$THEME/style.css" ]]; then
  echo "Theme not found at $THEME" >&2
  exit 1
fi

mkdir -p "$OUT"
rm -f "$ZIP"

(
  cd "$ROOT/wp-content/themes"
  zip -r "$ZIP" enjoy-naturally \
    -x "*.DS_Store" \
    -x "*__MACOSX*" \
    -x "*.git*"
)

echo "Wrote $ZIP"
unzip -l "$ZIP" | head -n 8
