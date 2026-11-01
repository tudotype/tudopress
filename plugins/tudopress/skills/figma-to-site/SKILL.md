---
name: figma-to-site
description: Builds a friend's or client's WordPress site in WordPress Studio from Figma pages that already hold the content and most images (artists, studios, small practices), to about 80–90 % done, so the owner can take over and publish it on WordPress.com. Use when the user shares Figma links for a site to build ("build <name>'s site from this Figma", "turn these Figma pages into the WordPress site", "the artist's pages are in Figma"). Uses the TudoPress kit; for faithful rebuilds of an existing live site use Team51's html-to-block-theme instead.
---

# Figma to site

KIT = `${TUDOPRESS_KIT:-~/Documents/Github-Repository/tudopress}`. Read `KIT/PLAYBOOK.md` and `KIT/docs/design-basics.md` first. If the private skill `/tudotype:design-review` is installed, use it in "set the rules" mode at step 3 and in review mode at step 8; otherwise apply `design-basics.md`.

"80–90 % done" means: every page built from Figma with real text and images, the design system in `theme.json`, menus, the owner's dashboard set up, checks passing. What's left for the owner: final copy tweaks, missing images, their account, plan, domain and the publish button.

## 1. Inputs

- Figma links (file and page/frame node IDs) and which frames are desktop and which phone.
- The owner: name, language, who edits, their WordPress.com account (or not yet).
- Anything not in Figma: contact details, links to shops, social profiles.

Load the Figma tools (official Figma MCP: `get_metadata`, `get_design_context`, `get_variable_defs`, `get_screenshot`, `download_assets`). Read-only on Figma: never write to the file.

## 2. Inventory

From `get_metadata` on each page, list frames → future pages, and within each frame the sections in order. Write `~/Studio/<slug>/BRIEF.md` (after step 4 creates the folder, or keep it in the session scratchpad until then):
- pages and their sections, mapped to the kit's patterns where they fit and to new patterns where they don't;
- **repeated content** (artworks, exhibitions, projects, press): decide its home. A handful → sections in pages. A growing list the owner will add to → posts in a category ("Works", "Exhibitions") shown with a Query Loop, so adding one is "Write a post". A custom post type only if it needs its own fields (year, medium, dimensions); then add fields with `hwh_field()` help tips;
- images per section, with their Figma node IDs;
- text styles and colour variables found (`get_variable_defs`).

## 3. Design rules

From Figma's text styles and colours: collapse them to a small set of roles and a palette (the Twenty Twenty-Five slugs), check every text/background pair with `KIT/bin/contrast.py` after writing theme.json, and note what was merged and why in `~/Studio/<slug>/DESIGN.md`. Fonts: if Figma uses a commercial font, check the licence covers web use before shipping it; otherwise pick the nearest open font and say so. Phone frames set the fluid minimum sizes.

## 4. The site

```bash
KIT/bin/new-site.sh <slug> "<Site name>" --lang <xx_XX> --tz <Area/City>
```
Then theme.json from step 3; style variations removed or adapted (ask before deleting: 🔴 path).

## 5. Images

- Export from Figma at 2× the largest display size (`download_assets` or `get_screenshot` per node), into a work folder **outside** the site and outside Dropbox (`~/Tudotype-media/<slug>/figma/` or the scratchpad).
- Convert to WebP or JPEG, ≤ 2400 px long side, sensible quality; keep originals.
- Import: `studio wp media import <file> --title="…" --alt="…"`. Every meaningful image gets alt text (what it shows; for artworks: title, year, medium if known — ask the owner for missing ones and mark them "ALT TO CONFIRM"). Decorative shapes: empty alt.
- Artworks are the owner's; photos of exhibitions may belong to photographers: list credits to confirm in BRIEF.md.

## 6. Pages

- Build each page's content as **expanded block markup** with the kit's patterns as a base (named groups, presets only, no literal colours or sizes), real Figma text, imported image IDs and URLs.
- Write it with `studio wp post update <id> --post_content="$(cat page.html)"` or create pages with `studio wp post create`; Home uses the template "Page without title".
- Text: copy exactly from Figma. Fix only typography (curly quotes, dashes); list wording you'd change in BRIEF.md "Copy to approve", don't change it.
- Menus: create the navigation with the pages in Figma's order.
- New section types: add them as patterns in the theme (`patterns/<name>.php`, category `<slug>-sections`) so the owner can reuse them.

## 7. Owner's dashboard

Settings → TudoPress (builder = the user, guide page), the Site guide from `KIT/docs/site-guide-template.md` with the site's own tasks ("Add a work": Write a post → category Works → featured image = the artwork → title, year…), and a Start here button for it (`hwh_start_tasks` in the theme's functions.php).

## 8. Check

1. `KIT/bin/check-theme.py` → CHECK_OK.
2. Every template, part, pattern and page parses with no invalid blocks (snippet: `/tudopress:new-site` § 6).
3. Side by side with Figma: `get_screenshot` of each frame vs the page at the frame's width in the browser pane (`resize_window`). Fix the system first (theme.json, patterns), page-only fixes last.
4. `KIT/bin/measure-page.js` at desktop and 390 px on every page: contrast failures, sizes, tap targets, empty paragraphs, headings.
5. Dashboard → Ready to launch?: what's left.

## 9. Hand-over (to get it on WordPress.com)

Recommend route A unless the owner is comfortable with Studio:

- **A. You push, they publish.** The owner creates their WordPress.com account and site, buys the plan (usually Personal) and invites the user as Administrator. The user pushes with Studio Sync (`/tudopress:launch` § 2). The site stays in "Coming soon" until the owner presses **Launch** in their dashboard. They own it from the first minute.
- **B. They take the Studio site.** `studio export ~/Desktop/<slug>.zip` from the site folder; the owner installs WordPress Studio, imports the file (Add site → Import), and pushes to their WordPress.com site with Sync. More steps for them; useful if they want to keep working locally.

End with a numbered list for the owner and the user: copy to approve, alt text to confirm, images missing, credits, and the account/plan/domain steps.
