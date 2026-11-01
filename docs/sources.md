# Sources and what we took

Two resources recommended by Automatticians, with their own caveat that they "might not be the ideal offer yet". Read on 2026-10-31.

## Team51 `html-to-block-theme`

https://github.com/a8cteam51/claude-code-plugins/tree/trunk/plugins/html-to-block-theme

A Claude Code plugin from Automattic Special Projects. Point it at a folder of static HTML (a Claude Design export) or at a **live site** (it mirrors the pages first) and it rebuilds the design as a block theme on a Studio site, section by section, comparing against the original in Chrome until it matches.

**Use it when** a friend already has a site whose design they love and you'd rather not redraw it.

**Caveats**
- It takes a long time and a lot of tokens: it plans a blueprint, then builds and refines every view.
- It's pinned to `claude-opus-5-5` (builder at `xhigh` effort) and stops on another model.
- It needs the Claude in Chrome extension for the side-by-side comparison.
- Behaviour beyond core comes from the A8C Special Projects blocks monorepo, installed as plugins. Check each one works on the friend's WordPress.com plan.
- Many attributes it uses (per-viewport values, hover states, block visibility) need WordPress 7.1 or later.

**Install**
```bash
/plugin marketplace add a8cteam51/claude-code-plugins
/plugin install html-to-block-theme@a8cteam51-claude-code-plugins
```

**What the kit took from its standards** (its `references/standards.md`):
- Every group has a name (`metadata.name`).
- Every template gives `core/post-content` a constrained layout; full-bleed sections are `alignfull` blocks in the page.
- No `front-page.html`; the home page is a page.
- A setting the editor has a control for is an attribute, never a class.
- No HTML comments in block markup.
- `style.css` must be enqueued by `functions.php` in a block theme.
- Content lives in pages and the media library, not in PHP patterns.
- A standards audit that fails the build (our `bin/check-theme.py` is a smaller cousin).

## Ipsum

https://github.com/WordPress/ipsum

The theme proposed as WordPress 7.2's default, developed in the open. "A blank canvas built around the blogging experience", styled almost entirely through `theme.json` and Global Styles.

**Caveats**
- It requires **WordPress 7.2**, which isn't released yet (7.2 Beta 2 on 2026-10-31). Sites on 7.1 can't run it.
- It's blog-first: great for writers, less for a café or a studio.
- It's in development: version stays 1.0, no changelog.

**Use it when** a friend mainly writes, once 7.2 is out: activate Ipsum, pick a colour and type variation, and you may not need a custom theme at all.

**What the kit took**
- The Twenty Twenty-Five palette slugs (`base`, `contrast`, `accent-1` … `accent-6`), so other themes' patterns pick up the friend's colours.
- Colour and typography **style variations** as JSON in `styles/colors/` and `styles/typography/`.
- **Block style variations** as JSON in `styles/blocks/`.
- CSS only for what `theme.json` can't say, each rule with a comment saying why.
- `add_editor_style( 'style.css' )` so the editor matches the site.
- Not taken (yet): delegating every template to a hidden pattern (useful for translatable strings in themes for many languages; a friend's site has one language, so templates stay plain markup), and the Abilities API design guide (`inc/abilities.php`), worth adding once friends use AI agents on their own sites.
