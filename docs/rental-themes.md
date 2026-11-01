# Themes for a holiday rental

From WordPress.com's public theme catalogue, **read 2026-10-31**. Screenshots compared side by side; no theme was installed or tested for a rental yet. Re-run the search (below) before choosing: the catalogue changes.

**What a plan includes.** The plan catalogue (`wpcom-plans-list`, EUR, yearly) lists *personal*, *premium* and *partner* themes under Personal and up. A theme's "tier" in the catalogue is a label, not a price you pay separately; some premium themes also show a `price` (€55) in the API, so **confirm on the theme's page that it is included in the plan** before promising it. The Free plan gets free-tier themes only.

## Shortlist (block themes)

| Theme | Tier | Why for a rental | Watch out for |
|---|---|---|---|
| **The Shore** | premium | Described as "designed specifically for hotels and resorts"; three colour variations; image-led hero, rooms / restaurant / spa style sections. Demo: theshoredemo.wordpress.com | Its hero likely uses the Cover block, listed as Premium+ only: test on Personal first |
| **Vibrrrant** | personal | "Tailored to short-term rental businesses", colourful, with a grid of apartments by city. Demo: vibrrrantdemo.wordpress.com | Loud look, made for several units; wrong for a quiet single house |
| **Homelot** | premium | Real-estate agency, editorial type, listing and agent layouts. Demo: homelotdemo.wordpress.com | Built for an agency's many listings |
| Hevor, Urbem, Outland, Bute | premium / personal | Photography-led travel looks; good if the destination is the story | Not rental-specific; Hevor and Outland carry a €55 price in the API |
| Natural Block | partner | Local business and adventure operators | Retail / blog leaning |
| **Free plan:** Adventurer, Calyx, Azur | free | Zero cost. Calyx is a one-page layout (address, contact, social icons); Adventurer a typographic travel blog; Azur a coastal, type-only page | No custom domain, no plugins; the look is far from a booking page |

**Default advice.** With an advanced Figma design, don't force a catalogue theme: use the kit theme and match the design (route A of `rental-site`). Without one, start from **The Shore** (a single hotel-like property) or **Vibrrrant** (several units, bold brand), and expect to spend time moving the sections into the owner's order.

## Re-run the search

```
https://public-api.wordpress.com/rest/v1.2/themes?search=<term>&number=60
```

Terms that gave results: hotel, rental, vacation, travel, resort, villa, stay, booking, real estate. Useful fields: `theme_tier`, `taxonomies.theme_feature` (look for `block-themes`), `taxonomies.theme_subject`, `demo_uri`, `screenshot`. Theme downloads need authentication; the source for most themes is github.com/Automattic/themes.
