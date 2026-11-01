# TudoPress

A starter kit for building WordPress websites for friends and small clients, and moving them onto WordPress.com, where they own the site and pay a few euros a month instead of a website-builder subscription.

It packs what building tudotype.com taught about **back offices people can actually use**, plus conventions from Automattic's Team51 `html-to-block-theme` plugin and WordPress's Ipsum theme.

## What's in it

| Path | What |
|---|---|
| [`PLAYBOOK.md`](PLAYBOOK.md) | **Start here.** The workflow and the rules that apply to almost every small site, and why |
| [`docs/backoffice-for-humans.md`](docs/backoffice-for-humans.md) | Making wp-admin easy for non-technical owners: principles and a checklist |
| [`docs/hosting-and-costs.md`](docs/hosting-and-costs.md) | WordPress.com plans, domains, email, what to tell the friend |
| [`docs/site-guide-template.md`](docs/site-guide-template.md) | The private "how this site works" page each friend gets |
| [`docs/design-basics.md`](docs/design-basics.md) | Basic, widely published design rules (type, contrast, structure, phone) and their sources |
| [`docs/rental-themes.md`](docs/rental-themes.md) | WordPress.com themes for a holiday rental, plans and what they include, how to re-run the search |
| [`docs/sources.md`](docs/sources.md) | Team51's plugin and Ipsum: what we took, caveats |
| `theme/tudopress/` | A plain block theme to copy and rename per site: everything in `theme.json`, two colour variations, a typography variation, a "Card" block style, 7 section patterns and 3 page layouts |
| `plugin/tudopress-helper/` | The dashboard plugin, the same on every site (below) |
| `bin/new-site.sh` | One command: a Studio site with the theme renamed, the plugin, sane settings and starter pages |
| `bin/check-theme.py` | Checks a block theme for what breaks or confuses the editor |
| `bin/package.sh` | Zips a site's theme and the plugin for upload to WordPress.com |
| `bin/token-tracker.py` | What a build costs: `start` / `mark "phase"` / `report` sum the tokens of a Claude Code session (and its sub-agents) per phase and model from its own transcripts; prices are passed in, never built in |
| `bin/contrast.py` | The WCAG contrast of every palette colour on every other, from a theme.json |
| `bin/measure-page.js` | Paste into a browser console: typefaces, font files, sizes, colours, contrast failures, headings, empty paragraphs, tap targets… of the rendered page |
| `plugins/tudopress/` | Claude Code skills: `new-site`, `figma-to-site`, `rental-site`, `launch`, `handoff`, `backoffice-audit` |

## TudoPress Helper

Settings → TudoPress. Each part is a checkbox.

- **Start here** panel first on the dashboard: "Edit the home page", "Write a news post", "Change the menu", "Colours and fonts"…, plus the site guide and "Ask <you>".
- **Simple menu** for everyone except builders (Tools, Plugins, code editors, Comments hidden), with a profile switch that brings it all back.
- **Shorter block list** and only the site's own patterns; no Openverse; no code editor for owners.
- **Before you publish**: alt text, heading jumps, "click here" links and bare web addresses, empty paragraphs used as spacing, missing featured image or excerpt, each with "Show me". Advises, never blocks.
- **Ready to launch?** for builders, the same checks in Tools → Site Health: name, tagline, icon, home page, menu, samples gone, privacy policy, time zone, permalinks, site guide, search engines.
- **Quiet defaults**: comments off, login names private (author/date archives, user list), YouTube privacy mode.
- **Help tips for your own fields**: `hwh_help_tip()` and `hwh_field()`.
- The site's icon and name on the login screen.

Filters: `hwh_start_tasks`, `hwh_hidden_blocks`, `hwh_launch_checks`.

## Quick start

```bash
~/Documents/Github-Repository/tudopress/bin/new-site.sh maria-ceramics "Maria Ceramics" --lang pt_PT --tz Europe/Lisbon
```

With Claude Code, install the skills once:

```bash
claude plugin marketplace add ~/Documents/Github-Repository/tudopress
```

```bash
claude plugin install tudopress@tudopress
```

Then `/tudopress:new-site`, `/tudopress:figma-to-site`, `/tudopress:rental-site`, `/tudopress:launch`, `/tudopress:handoff`, `/tudopress:backoffice-audit`.

## Requirements

WordPress Studio with its CLI (Studio → Settings → General → Studio CLI for terminal), WordPress 6.8+ (tested on 7.1.4), PHP 8.0+, Python 3 and Perl (both ship with macOS).

## Tested

2026-10-31 on a fresh Studio site (WordPress 7.1.4, PHP 8.4): every template, part and pattern parses as valid blocks in the editor; page layouts appear in "Choose a pattern" with no core patterns; nested section patterns expand on insert; the simple menu, hidden blocks, Openverse and code-editor switches, the pre-publish checklist, the settings screen, the author-archive redirect and the hidden user list all behave as described. Not yet tested: Studio Sync push to a Personal plan, the plugin on WordPress.com.

## Public and private

This repository is written to be publishable: it holds basic, widely published knowledge and neutral tools. João's own design method (how a site's design rules are set and reviewed) is **not** here; it lives in the private `tudotype/tudotype-skills` plugin (`/tudotype:design-review`), which the skills here call when it's installed and skip when it isn't.

## Licence

GPL-2.0-or-later, like WordPress. See [`LICENSE`](LICENSE).
