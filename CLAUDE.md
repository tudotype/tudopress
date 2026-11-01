# CLAUDE.md — TudoPress kit

The kit João uses to build WordPress sites for friends. `README.md` says what's here; `PLAYBOOK.md` holds the rules and their reasons. Read the playbook before changing the theme or the plugin.

## Working here

- **This repository is the template.** Sites made from it live in `~/Studio/<slug>/` with their own renamed copy of the theme. A fix that applies to every site goes here; a change for one friend goes in their site.
- **The theme is renamed per site** by `bin/new-site.sh` (one pass: `tudopress_` → prefix, `tudopress` → slug, `TudoPress` → site name). Keep the theme's own text free of those words except as identifiers, or a rename garbles it.
- **The helper plugin isn't renamed**: it's one plugin on every site, updated by copying the new version over.
- After any change to `theme/`: `bin/check-theme.py theme/tudopress` must print `CHECK_OK`, and every template, part and pattern must parse as valid blocks in the editor (snippet in `plugins/tudopress/skills/new-site/SKILL.md` §6). Test on a throwaway Studio site, never on a friend's.
- After any change to `plugin/`: `php -l` on every file (`~/.studio/php-bin/*/php -l`), then load wp-admin as a builder and as a non-builder.
- Block markup follows Gutenberg's serialization exactly (one delimiter per line, a blank line between siblings, the wrapper element on the opening line of its first child). No HTML comments inside block markup. Every group has `metadata.name`.
- PHP follows the WordPress coding standards, tabs, everything escaped at output. Plain JS with `wp.*` globals: no build step, ever.
- Skills in `plugins/tudopress/skills/`. Bump `plugins/tudopress/.claude-plugin/plugin.json` version on every skill change, then `claude plugin update tudopress@tudopress`.
- Commit only when João asks.

## Public-safe by rule

This repository may become public. It holds only **basic, widely published knowledge** (WCAG, Butterick's public rules, WordPress and Team51/Ipsum conventions) and **neutral tools** (they measure; they don't judge). João's design method is his intellectual property and lives only in the private `~/Documents/Github-Repository/tudotype-skills` (`/tudotype:design-review`).

Never write here: anything from the private `design-review` skill (its own files list what it covers; don't summarise them here either), João's design judgement on a project, any client's name, findings or review. When something learnt on a project is generic and published elsewhere, add it to `docs/design-basics.md` with its public source; when it's João's judgement, it goes in the private skill. Unsure: ask.

Git history keeps everything ever committed: check a change against this list *before* committing, not before publishing.
