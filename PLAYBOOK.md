# Playbook: WordPress sites for friends

What building tudotype.com taught, cut down to what applies to almost every small WordPress site, plus what Automattic's Team51 plugin and the Ipsum default theme add. The kit in this repository puts most of it into code; this file says why.

Dated facts were checked on the date given. Prices and plan limits change: check them again before you promise anything to a friend.

---

## 1. The deal

- **The friend owns everything that costs money or carries their name**: the WordPress.com account and plan, the domain, the payment card, the email. You are an **administrator on their site**, not their host. Then you can step away without the site going with you.
- **Hosting:** WordPress.com. Since April 2026 the **Personal** plan allows uploaded themes, plugins and Studio Sync (checked 2026-10-06; about $4/month paid yearly, $9 monthly). Business adds SFTP, SSH, WP-CLI, staging and server backups: only for shops or sites that need them. Details: [docs/hosting-and-costs.md](docs/hosting-and-costs.md).
- **Local first:** build in WordPress Studio (free). Show it on a phone with a Studio **preview site** (free, expires 7 days after its last update) before anyone pays for anything.
- **Scope in one sentence**, written before you start: who the site is for and the one thing a visitor should do. Most friend sites are 3–6 pages and a news section.

## 2. The workflow

1. **Brief** (15 minutes with the friend): the sentence above, their pages, their words (texts they already wrote: Instagram captions, menus, flyers), their photos, colours or a logo if they have them, the language, who will edit the site.
2. **Make the site:** `bin/new-site.sh <slug> "<Name>"` (Studio site, the theme renamed for them, the helper plugin, sane settings, starter pages). Or, with Claude, `/tudopress:new-site`.
3. **Design in `theme.json` only:** palette, fonts, sizes, spacing. Try it with the colour variations. No CSS unless `theme.json` can't say it.
4. **Pages from patterns:** each page starts from a layout (Home, About, Contact) and grows with sections. Content goes into the **pages**, never into templates or PHP.
5. **The site guide:** a private page in the friend's words (`docs/site-guide-template.md`).
6. **Check:** `bin/check-theme.py`, the editor (every template and pattern opens without "invalid content"), four widths (1440, 1024, 768, 375), a second colour variation, a phone on the preview link.
7. **Launch:** `/tudopress:launch`: plan, push, domain, the launch checklist on the dashboard.
8. **Hand-off:** `/tudopress:handoff`: their account, simple menu on, a 30-minute session where *they* drive.

## 3. Theme rules

From tudotype.com's mistakes, Team51's standards and Ipsum's conventions.

- **`theme.json` is the authority.** Colours, fonts, sizes, spacing, block defaults live there and stay editable in Appearance → Editor → Styles. A value in both `theme.json` and CSS is a bug.
- **Presets, never literals.** `var:preset|color|accent-1`, never `#c2410c`, in templates, patterns and pages. A literal freezes one colour variation into all of them.
- **Use the Twenty Twenty-Five palette slugs** (`base`, `contrast`, `accent-1` … `accent-6`), as Ipsum does: patterns from other themes and from WordPress pick up the friend's colours.
- **Block styles as JSON** (`styles/blocks/*.json`, WordPress 6.6+): they show up in Global Styles, so the friend can change a "Card" once for the whole site. Never style with a hand-typed CSS class: it hides under "Additional CSS class(es)" and breaks when blocks are copied.
- **Templates and parts are plain block markup.** No PHP, no shortcodes for content. **No `front-page.html`:** the home page is a page (Settings → Reading), editable like any other.
- **Every template:** a group with `"tagName":"main"` (it gives the page a main landmark, and core adds the skip link), and `core/post-content` with a constrained layout, so blocks in a page can be wide or full width.
- **Name every group** (`"metadata":{"name":"Hero"}`). List View then reads like an outline, for you and for the friend.
- **No HTML comments inside block markup.** A `<!-- note -->` makes the whole enclosing block invalid in the editor ("Block contains unexpected or invalid content"). On tudotype.com it hid entire pages in the Site Editor. Notes go in a sidecar file or PHP comments.
- **Block themes don't load `style.css` by themselves.** Enqueue it, and add it as an editor style so the editor looks like the site.
- **No build step.** Plain CSS and JS that work as a zip upload.
- **Fonts:** system fonts by default. The friend can add Google fonts in Styles → Typography (WordPress's Font Library downloads them to the site; nothing loads from Google). Never ship commercial font files you don't have a web licence for.
- **Content patterns are for starting, not storing.** Page content lives in the page. A pattern that holds visitor text in PHP can only be changed by a developer.
- **Site Editor saves shadow the theme.** Once someone edits a template or part in the Site Editor, WordPress keeps a copy in the database that wins over the file. Before editing a theme file, check: `studio wp post list --post_type=wp_template,wp_template_part`. To go back to the file: Site Editor → template → Reset.
- **Check with `bin/check-theme.py`** after every change to templates, parts or patterns.

## 4. The dashboard is a product too

Your friend will spend more time in wp-admin than on the site. Full guidance: [docs/backoffice-for-humans.md](docs/backoffice-for-humans.md). In short:

- **A "Start here" panel** with the six things they actually do, in their words ("Edit the home page", "Write a news post", "Change the menu").
- **Fewer doors, none locked.** Hide Tools, Plugins, the code editors and (when comments are off) Comments; a switch in their profile brings everything back. Hiding without a way back creates support calls.
- **A shorter block list**: the blocks they'll use and the site's own sections first; WordPress.org's pattern directory off. Blocks already on a page keep working.
- **Every field explains itself**: where its value shows up, what empty means, an example. (`hwh_help_tip()` / `hwh_field()` in the plugin.) Custom fields get a box with plain labels, never the raw "Custom Fields" box.
- **Checkboxes named after the result**, not the mechanism: "Highlight on the news page", not "Sticky".
- **Advise, never block.** The pre-publish checklist points at missing alt text, skipped heading levels, "click here" links, a missing featured image; it never stops a publish.
- **A site guide** they can open from the toolbar, written for this site.
- **Their own icon and name on the login screen**, so they know they're in the right place.

## 5. Content and rights

- **Their words.** Copy comes from the friend. Anything you draft is marked DRAFT until they approve it.
- **Their pictures**, or pictures with written rights. Openverse is switched off in the editor because its licences need a credit line people forget.
- **Alt text on every meaningful image**, empty on decorative ones ("Mark as decorative").
- **One H1 per page** (the title, or the hero on pages without a title), then H2, H3 in order. Size with typography settings, never by picking a heading level.
- **Links say where they go.**
- **Light media.** Resize photos before upload (2400 px on the long side is plenty); WordPress.com's CDN serves smaller versions and WebP by itself. Videos: short, silent loops under ~5 MB, a poster image, no autoplay for anything with sound, nothing that moves for people who ask for reduced motion.

## 6. Quiet defaults

On by default in the helper plugin, each one a checkbox in Settings → TudoPress:

- **Comments off everywhere** (most small sites only get spam), and the Discussion panel gone.
- **Login names private:** author and date archives redirect home, the user list is hidden from visitors, no user sitemap.
- **YouTube in privacy mode** (youtube-nocookie.com).
- **No cookie banner unless something sets cookies.** Add analytics only if the friend will look at it; Jetpack Stats on WordPress.com needs no banner for basic counts (check for the friend's country).
- **A privacy policy page**, published before the site has a form or a newsletter.
- **Search engines off while building, on at launch** (the launch checklist reminds you).

## 7. Studio and WordPress.com gotchas

- Run WP-CLI as `studio wp …` from the site folder.
- Studio sites use **SQLite**. Most plugins don't care; some break (MailPoet's migration crashed every page on tudotype.com). Test plugins locally; if one breaks WP-CLI, `studio wp --skip-plugins=<slug> …`.
- **Preview sites are public to anyone with the link**, and files in uploads and the theme are readable by direct URL even behind a password gate. Nothing private on a preview.
- **`.deployignore`** in the site root (gitignore syntax) keeps notes, logs and work files out of previews and Studio Sync pushes. WordPress.com serves any file in a theme as a static file.
- Local media is slow in Studio (about 130 KB/s): judge video loading on the preview, not on localhost.
- **WordPress.com has no `.htaccess`.** Redirects go through PHP or the Redirection plugin. After a theme upload, re-save Settings → Permalinks.
- **Forms and newsletters:** Jetpack Forms and Jetpack Newsletter on WordPress.com keep submissions and subscribers on WordPress.com, so a Studio push can't wipe them. Avoid plugins that store subscribers in the site's database if you'll ever push over it.
- **Never push a local database over a live site** once the friend has started editing there. After launch, the live site is the source of truth; pull it before you work on it.

## 8. Moving an existing site

- **From another website (Squarespace, Wix, an old WordPress):** `studio create --from <url>` imports a static capture into a new Studio site through the Static Site Importer. Good for content; expect to rebuild the design with the kit's theme.
- **High-fidelity rebuild:** Team51's `html-to-block-theme` plugin (see [docs/sources.md](docs/sources.md)) mirrors a live site and rebuilds it as a block theme to their standards. Slow and thorough; worth it when the friend loves their current design.
- **Old WordPress sites on WordPress.com that are disconnected:** the Wayback Machine keeps most pages (`curl` with `id_` snapshots gives the original HTML).
- **Domains:** keep email working. Copy the MX, SPF, DKIM and DMARC records before you change nameservers.

## 9. Care after launch

- **Updates:** WordPress.com updates core and plugins. Check the site after major WordPress releases.
- **Backups:** WordPress.com Business and above has daily backups; on Personal, export content (Tools → Export) after big changes and keep it with the friend.
- **Once a year:** renew the domain on time, read the launch checklist again, remove plugins nobody uses.

## 10. Where this came from

- **tudotype.com** (2026): theme.json authority, the editor-breaking HTML comments, Site Editor shadows, help tips on every field, checkboxes named after the result, Studio and WordPress.com limits, privacy defaults, light video.
- **Team51's html-to-block-theme** (Automattic Special Projects): named groups, post content at content width, no `front-page.html`, settings as attributes not classes, a standards audit that fails the build.
- **Ipsum** (WordPress 7.2's proposed default theme, in development): Twenty Twenty-Five palette slugs, colour and typography variations, block styles as JSON, "the theme ships with as little CSS as possible".
