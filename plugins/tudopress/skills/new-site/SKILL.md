---
name: new-site
description: Starts a WordPress website for a friend or small client with the TudoPress kit, from a short brief to a working local Studio site with their pages, colours and a site guide. Use whenever the user wants to "make a site for <someone>", "spin up a WordPress site", "start a website for my friend's café / studio / shop", "move <someone> to WordPress", or rebuild a friend's existing site (Squarespace, Wix, old WordPress) on WordPress.com. Not for tudotype.com itself.
---

# New site

Takes a friend's site from brief to a local WordPress Studio site that looks like theirs, with their content in editable pages and a dashboard they can use. Everything stays local and reversible until the user decides to launch (`/tudopress:launch`).

The kit lives at `${TUDOPRESS_KIT:-~/Documents/Github-Repository/tudopress}` (call it KIT). Read `KIT/PLAYBOOK.md` once per session before building; it holds the rules this skill assumes.

## 1. Brief

Ask only what you can't find. One message, these questions, skip the ones already answered:

1. Whose site, and its name? (→ slug: lowercase, hyphens: `maria-ceramics`)
2. In one sentence: who is it for, and what should a visitor do? (book, buy, call, read, visit)
3. Which pages? (default: Home, About, Contact, plus News if they'll write)
4. What do they already have? An existing site URL, texts (Instagram, menus, flyers), photos, a logo, colours.
5. Language and time zone? (default Portuguese/Lisbon if unsaid and they're in Portugal; ask otherwise)
6. Who will edit it, and how comfortable are they with computers?

Write the answers to `~/Studio/<slug>/BRIEF.md` after the site exists (step 2). It's not in `wp-content`, so it never reaches a preview or WordPress.com.

## 2. Make the site

```bash
KIT/bin/new-site.sh <slug> "<Site name>" --lang <pt_PT|en_GB|…> --tz <Area/City>
```

It refuses an existing slug. It creates `~/Studio/<slug>`, copies the theme renamed to `<slug>`, installs TudoPress Helper, sets permalinks, time zone, comments off, search engines off, deletes the sample post and page, and creates Home (template "Page without title"), About, Contact and a private Site guide. Note the URL it prints; never repeat the admin password in chat.

Then set the builder's email (the user's) so the help links work:
```bash
cd ~/Studio/<slug> && studio wp eval '$o = get_option("tudopress"); $o["builder_email"] = "<user email>"; $o["builder_name"] = "<user first name>"; update_option("tudopress", $o);'
```

**Rebuilding an existing site instead?**
- Content only, design new: create the site with `studio create --from <url>` in a separate folder to capture pages and images, then copy what you need into the kit site. Ask before downloading anything large.
- Same design, faithfully: suggest Team51's `html-to-block-theme` plugin (`KIT/docs/sources.md`): it's slow and pinned to Opus 5.5 at xhigh effort; the user decides.

## 3. Design (theme.json only)

Edit `~/Studio/<slug>/wp-content/themes/<slug>/theme.json`:
- **Palette:** keep the slugs (`base`, `contrast`, `accent-1` … `accent-6`); change the values from the friend's logo or photos. Check contrast: `contrast` on `base` ≥ 7:1, `accent-4` (muted text) on `base` ≥ 4.5:1, button text on its background ≥ 4.5:1. Update the colour variations in `styles/colors/` so each still makes sense, or remove the ones that don't (ask first: 🔴 path).
- **Type:** system fonts unless the friend has a brand font with a web licence. Google fonts: tell the user the friend can add them in Styles → Typography (Font Library), or bundle WOFF2 files under `assets/fonts/` with `fontFace` in theme.json. Never commercial fonts without a web licence.
- **Layout widths, spacing, radius** to taste.
- CSS only for what theme.json can't say, in `style.css`, each rule with a comment saying why.

## 4. Pages

- Open each starter page in the editor and choose its layout ("Choose a pattern" appears for a new, empty page). Writing content with WP-CLI instead: a pattern reference (`<!-- wp:pattern {"slug":"<slug>/page-home"} /-->`) renders on the front end, but save **expanded block markup** for real content (copy from the pattern files, replacing the PHP strings), so the page holds the blocks the friend will edit.
- **Their words.** Use texts they gave you. Anything you write is a draft: start it with "DRAFT:" or list it in BRIEF.md under "Copy to approve".
- **Their photos** into the media library (`studio wp media import <file> --title=… --alt=…`), resized to ≤ 2400 px on the long side. Always set alt text. No third-party images without written rights.
- Rewrite the theme's pattern strings into the site's language (`patterns/*.php`), keeping `esc_html_e( '…', '<slug>' )`.
- Menu: Site Editor → Navigation, or tell the user to make it there (the launch checklist only counts a menu with chosen links).
- Site title, tagline and site icon in Settings → General.

## 5. Site guide

Fill `KIT/docs/site-guide-template.md` for this site, in the friend's language, and put it into the private Site guide page as blocks. Add their site-specific tasks. If the site has a custom post type, add it to the Start here panel with the `hwh_start_tasks` filter in the theme's `functions.php`.

## 6. Check

1. `KIT/bin/check-theme.py ~/Studio/<slug>/wp-content/themes/<slug>` → `CHECK_OK`.
2. In the editor (browser pane, auto-login `http://localhost:<port>/studio-auto-login?redirect_to=…`), parse every template, part, pattern and page and confirm no block is invalid:
   ```js
   const flat=(b,o=[])=>(b.forEach(x=>{o.push(x);flat(x.innerBlocks||[],o)}),o);
   const t=await wp.apiFetch({path:'/wp/v2/templates?context=edit&per_page=100'});
   t.map(x=>x.slug+': '+flat(wp.blocks.parse(x.content.raw)).filter(b=>b.isValid===false).map(b=>b.name))
   ```
   (same for `/wp/v2/template-parts`, `/wp/v2/block-patterns/patterns` → `content`, and pages → `content.raw`).
3. Front end at 1440, 1024, 768 and 375 wide, and in a second colour variation. Look for overflow, unreadable contrast, empty image placeholders left on published pages.
4. Dashboard → Ready to launch? Note what's still to do.

## 7. Report

End with: the local URL, what's built, a numbered list of what the user or the friend must supply or approve (copy marked DRAFT, photos, logo, rights), and the launch checklist's open items. Offer `/tudopress:launch` when they're ready, or a Studio preview link (`studio preview create` from the site folder) to show the friend on a phone; previews are public to anyone with the link and expire after 7 days.

## Rules

- Never touch an existing Studio site except the one you just made. Never delete without listing paths as 🔴 and asking.
- Nothing goes online (preview, push) without the user's go.
- Don't copy the friend's private documents into `wp-content`.
