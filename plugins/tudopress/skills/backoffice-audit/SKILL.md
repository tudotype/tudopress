---
name: backoffice-audit
description: Audits how easy a WordPress site's admin and editor are for a non-technical owner, by walking wp-admin as them: login, dashboard, menu, editor, inserter, templates, custom fields, help, and the theme's block markup. Reports what to fix, ranked. Use when the user asks "is this easy enough for <friend>?", "check the back office", "audit the admin", "review the editor experience", or before a hand-off. Works on any WordPress site in Studio, not only TudoPress ones. Read-only.
---

# Back-office audit

Read-only: never change the site during the audit. KIT = `${TUDOPRESS_KIT:-~/Documents/Github-Repository/tudopress}`. The criteria are `KIT/docs/backoffice-for-humans.md`; read it first.

## Inputs

The Studio site folder (ask if more than one fits; `studio list`). Find its URL and theme: `cd <site> && studio status`, `studio wp theme list --status=active`.

## Steps

1. **Theme markup:** `KIT/bin/check-theme.py <site>/wp-content/themes/<theme>`. Note failures (they break or confuse the editor) and warnings.
2. **Database overrides:** `studio wp post list --post_type=wp_template,wp_template_part --fields=ID,post_name,post_modified`. Templates edited in the Site Editor win over theme files; list them.
3. **Block validity** in the browser pane (auto-login: `http://localhost:<port>/studio-auto-login?redirect_to=%2Fwp-admin%2F`): parse templates, parts, patterns and every published page with `wp.blocks.parse` and list blocks with `isValid === false` (snippet in `/tudopress:new-site` step 6).
4. **As the owner.** If the site uses TudoPress Helper, check whether the owner is a builder (Settings → TudoPress); to see their view, use a test Editor account or ask the user to log in as the friend. Never change the friend's password. Walk:
   - Login screen: site icon and name?
   - Dashboard: is the first thing they see a list of their tasks? WordPress news and clutter?
   - Menu: items they'll never use? Custom post types with clear names and icons?
   - New page: are layouts offered? Only the site's own?
   - Inserter: how many blocks? Are the site's sections findable under Patterns?
   - Publishing: is there a pre-publish checklist?
   - Custom fields: every field labelled in plain words, with a help tip saying where it shows and what empty means? The raw "Custom Fields" box?
   - Settings the owner needs: where are they? (Customizer in a block theme = hard to find.)
   - Help: is there a site guide, linked from where they'll look?
5. **Hard-coded content:** grep the theme for visitor text in templates, parts and PHP patterns that isn't a starter pattern (`grep -rn '<p>\|<h[1-6]' templates parts patterns`), and shortcodes or `core/html` blocks holding content. Each one is a developer-only edit.
6. **Contrast of the admin-facing colours** isn't in scope; front-end accessibility only where it shows up in the editor (headings order in templates, image alt in patterns).

## Report

In chat, not a file (unless the user asks):

```
Back office: <site> — <date>
<n> must-fix · <n> should-fix · <n> nice
```
Then one numbered list across all sections (the user answers item by item), each: what the owner experiences, where (path or screen), the fix, and whether TudoPress Helper already does it (`setting` to switch on) or it needs code. Must-fix = something breaks or blocks editing (invalid blocks, content only editable in PHP). Should-fix = confusion or support calls. Nice = polish.

Offer to fix the list afterwards; that's a separate step with the user's go.
