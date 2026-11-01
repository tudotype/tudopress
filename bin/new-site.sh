#!/usr/bin/env bash
# Makes a new local WordPress site for a friend, with the starter theme (renamed for
# them) and the TudoPress Helper plugin, and the settings most small sites want.
#
#   bin/new-site.sh <slug> "<Site name>" [--lang pt_PT] [--tz Europe/Lisbon] [--wp 7.1]
#
# Creates ~/Studio/<slug> as a WordPress Studio site. It refuses a slug that already
# exists: it never touches an existing site.
set -euo pipefail

KIT="$(cd "$(dirname "$0")/.." && pwd)"
USAGE='usage: new-site.sh <slug> "<Site name>" [--lang xx_XX] [--tz Area/City] [--wp version]'
SLUG="${1:?$USAGE}"
NAME="${2:?$USAGE}"
shift 2
LANG_CODE=""; TZ_NAME="Europe/Lisbon"; WP_VERSION="auto-update"
while [ $# -gt 0 ]; do
	case "$1" in
		--lang) LANG_CODE="$2"; shift 2 ;;
		--tz) TZ_NAME="$2"; shift 2 ;;
		--wp) WP_VERSION="$2"; shift 2 ;;
		*) echo "Unknown option: $1" >&2; exit 1 ;;
	esac
done

[[ "$SLUG" =~ ^[a-z0-9-]+$ ]] || { echo "The slug takes lowercase letters, digits and hyphens: $SLUG" >&2; exit 1; }
SITE="$HOME/Studio/$SLUG"
[ -e "$SITE" ] && { echo "$SITE already exists. Pick another slug." >&2; exit 1; }
command -v studio >/dev/null || { echo "The Studio CLI is missing: Studio → Settings → General → Studio CLI for terminal." >&2; exit 1; }

PREFIX="${SLUG//-/_}"
THEME="$SITE/wp-content/themes/$SLUG"

echo "→ Creating the Studio site $SITE"
studio create --path "$SITE" --name "$NAME" --wp "$WP_VERSION" --skip-browser

echo "→ Theme: starter theme renamed to $SLUG"
cp -R "$KIT/theme/tudopress" "$THEME"
# Text domain, function prefix, pattern slugs and the theme name, in one pass (a slug that
# itself contains "tudopress" must not be renamed twice). The kit's copy stays as it is.
find "$THEME" -type f \( -name '*.php' -o -name '*.css' -o -name '*.json' -o -name '*.html' \) -print0 |
	HW_PREFIX="$PREFIX" HW_SLUG="$SLUG" HW_NAME="$NAME" xargs -0 perl -pi -e \
		's/(tudopress_|tudopress|TudoPress)/$1 eq "tudopress_" ? "$ENV{HW_PREFIX}_" : $1 eq "tudopress" ? $ENV{HW_SLUG} : $ENV{HW_NAME}/ge'

echo "→ Plugin: TudoPress Helper"
cp -R "$KIT/plugin/tudopress-helper" "$SITE/wp-content/plugins/tudopress-helper"

cd "$SITE"
# Keeps notes and work files out of preview sites and Studio Sync pushes.
printf '%s\n' '*.md' '*.log' 'wp-content/upgrade/' > .deployignore
wp() { studio wp "$@"; }
wp theme activate "$SLUG"
wp plugin activate tudopress-helper
# The activation hook sees no logged-in user from the CLI: make the first admin the builder.
wp eval '$o = get_option( "tudopress", array() ); $u = get_userdata( 1 ); $o["builders"] = array( 1 ); if ( empty( $o["builder_name"] ) ) { $o["builder_name"] = $u->display_name; } update_option( "tudopress", $o );'

echo "→ Settings most small sites want"
wp option update timezone_string "$TZ_NAME"
wp option update date_format "j F Y"
wp option update permalink_structure "/%postname%/"
wp option update default_comment_status closed
wp option update default_ping_status closed
wp option update blog_public 0
wp option update blogdescription ""
wp rewrite flush
if [ -n "$LANG_CODE" ]; then
	wp language core install "$LANG_CODE" --activate || echo "  (The language didn't download: set it later in Settings → General.)"
fi

echo "→ Removing the sample post and page, making the starter pages"
for SAMPLE in "post hello-world" "page sample-page"; do
	set -- $SAMPLE
	ID="$(wp post list --post_type="$1" --name="$2" --field=ID)"
	[ -n "$ID" ] && wp post delete "$ID" --force >/dev/null
done
HOME_ID="$(wp post create --post_type=page --post_status=publish --post_title='Home' --page_template=page-no-title --porcelain)"
wp post create --post_type=page --post_status=publish --post_title='About' --porcelain >/dev/null
wp post create --post_type=page --post_status=publish --post_title='Contact' --porcelain >/dev/null
GUIDE_ID="$(wp post create --post_type=page --post_status=private --post_title='Site guide' --porcelain)"
wp option update show_on_front page
wp option update page_on_front "$HOME_ID"
wp eval "\$o = get_option( 'tudopress' ); \$o['guide_page'] = $GUIDE_ID; update_option( 'tudopress', \$o );"

echo
echo "✓ $NAME is ready in $SITE"
studio status 2>/dev/null | grep -Ei 'url|user' || true
echo "  Next: open Home, About and Contact and pick a layout for each, write the Site guide,"
echo "  then work down Dashboard → Ready to launch?"
