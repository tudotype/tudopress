#!/usr/bin/env bash
# Zips a site's theme and the helper plugin for upload to WordPress.com
# (Appearance → Themes → Add → Upload; Plugins → Add New → Upload).
#
#   bin/package.sh <studio-site-folder>      e.g. bin/package.sh ~/Studio/maria-ceramics
#
# Writes dist/<site>/<theme>.zip and tudopress-helper.zip in this kit (git ignores dist/). Notes (*.md), dotfiles and
# work files stay out: WordPress.com serves every file in a theme by its address.
set -euo pipefail
KIT="$(cd "$(dirname "$0")/.." && pwd)"
SITE="$(cd "${1:?usage: package.sh <studio-site-folder>}" && pwd)"
THEME="$(cd "$SITE" && studio wp theme list --status=active --field=name)"
OUT="$KIT/dist/$(basename "$SITE")"
mkdir -p "$OUT"
for pair in "themes/$THEME:$THEME" "plugins/tudopress-helper:tudopress-helper"; do
	SRC="${pair%%:*}"; NAME="${pair##*:}"
	( cd "$SITE/wp-content/$(dirname "$SRC")" && zip -qr "$OUT/$NAME.zip" "$NAME" -x '*.md' -x '*/.*' -x '*.log' )
	echo "✓ $OUT/$NAME.zip ($(du -h "$OUT/$NAME.zip" | cut -f1))"
done
