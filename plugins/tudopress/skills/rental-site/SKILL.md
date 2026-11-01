---
name: rental-site
description: Spins up a WordPress website for a holiday rental (a house, apartment or small guesthouse) from a Figma design, a brief and the booking links (Airbnb, Booking.com, direct), starting on a free or Personal WordPress.com plan, with a shortlist of themes. Use when the user wants a site for a property to rent, "casa para alugar", "alojamento local", "holiday home website", "Airbnb / Booking site", or asks which WordPress.com theme suits a rental. Builds on the TudoPress kit's new-site and figma-to-site skills.
---

# Rental site

A one-property (or few-unit) rental site: a page that makes a guest trust the place and sends them to book, with the facts, the photos and the booking links in one place the owner can edit. Local and reversible until `/tudopress:launch`.

KIT = `${TUDOPRESS_KIT:-~/Documents/Github-Repository/tudopress}`. Read `KIT/PLAYBOOK.md` and `KIT/docs/rental-themes.md` first. The steps below add the rental specifics; the mechanics (Studio site, images, checks, hand-over) are those of `/tudopress:new-site` and `/tudopress:figma-to-site`: follow them, don't repeat them.

## 1. Brief (one message, skip what's known)

1. The property: name, place, how many units (one house / several rooms or apartments), sleeps, bedrooms, bathrooms.
2. **How do guests book?** (a) only on platforms: Airbnb, Booking.com, others (their links); (b) platforms + a direct enquiry form; (c) an availability calendar. Default (a)+(b).
3. Languages (a rental usually needs at least two; the translation is a separate step, see the backlog) and the time zone.
4. Figma file and the frames or pages that hold the content (links), photos not in Figma, the logo, colours.
5. The domain they want, or none yet. Who owns the account (the owner's, route A of `handoff`), and who answers guests.
6. Portugal: the **Alojamento Local registration number** and the complaints-book link (see § 6). Elsewhere: the local registration or licence rules.

Write the answers to `~/Studio/<slug>/BRIEF.md` with a "Property facts" table: each fact with its **source** (Figma node ID or the owner's message). **Never invent a fact**: capacity, beds, amenities, distances, prices, rules, check-in times, cancellation terms. Unknown → ask, or leave out and list under "To ask the owner".

## 2. Plan and route

Plan (WordPress.com catalogue read 2026-10-31; prices change, re-check with the `wpcom-plans-list` tool before quoting):

| | Free | **Personal, €48 a year** |
|---|---|---|
| Address | a wordpress.com subdomain | custom domain (a first-year domain credit is included) |
| Themes | free-tier themes only | free, personal, premium and partner themes; uploaded themes |
| Plugins | none | install and upload; Studio Sync |
| Ads | may show WordPress.com ads (the "no adverts" feature starts at Personal) | none |

**Recommend Personal**: guests trust a real address, and the kit's theme and tools need uploads. Free is fine for a throwaway test. **Not on Personal (Premium and up):** the Cover and Video blocks, Calendly, payment buttons, Google Analytics, advanced SEO. Consequences: build the hero as a Group with a background image or as an Image block (test Cover on the first site before relying on it either way), use a plain `https://wa.me/<number>` link for WhatsApp, embed film from YouTube / Vimeo, and measure with what Personal has.

Route (decide after reading the design):

| Situation | Route |
|---|---|
| An advanced Figma design that has to be matched | **A.** The kit theme (`KIT/bin/new-site.sh`), `theme.json` from the Figma, the rental sections as patterns in the site's theme |
| No finished design, or speed matters more | **B.** A catalogue theme from `KIT/docs/rental-themes.md` (The Shore first), the content poured into its patterns |
| Zero cost test | **C.** Free plan with a free theme (Adventurer, Calyx, Azur): no custom domain, no plugins |

Say the choice and the reason in one line; the user decides.

## 3. Read the Figma (read-only)

As `figma-to-site` §1–3, plus:
- **Collect every external link** (Airbnb, Booking.com, Google Maps, WhatsApp, Instagram, reviews, email, phone) into a table in BRIEF.md with the frame it came from. Check each opens (`curl -sIL`; booking sites may block bots: then open it in the browser pane). Wrong or old links are the commonest rental-site fault.
- Pull the facts into the "Property facts" table, the texts verbatim (typography fixes only, as `figma-to-site` §6) and the images per section.
- Name frames → pages: usually Home (one long page), Gallery, The house / spaces, Location, Rates and rules, Contact / book. A one-property site works as one long page plus Contact; don't make five thin pages.

## 4. Build the sections

Each section is native blocks, named groups, presets only; order is the owner's design. Typical set (create the patterns in the site's theme by copying the nearest kit pattern; once two rental sites exist, move the set into the kit):

| Section | Blocks | Facts it needs |
|---|---|---|
| Hero | Group with background image (or Image), H1 name, one-line promise, **book buttons** | name, hero photo, booking links |
| Facts bar | Columns of number + label (guests, bedrooms, beds, bathrooms, size) | the facts, from the source |
| About the house | Heading + text + one image | the owner's words |
| Gallery | Gallery or Columns of Images, in the owner's order, real alt text ("Double bedroom with a sea view") | photos, credits |
| Spaces | A card per room or space | per-room facts |
| Amenities | A list with icons or a plain two-column list | the owner's list, not a generic one |
| Location | Text, distances **only if given**, a map link, or Jetpack's Map block | address or coordinates |
| Rates and rules | A table, check-in / check-out, house rules, cancellation | all from the owner |
| Reviews | Short quotes with first name and platform, **with permission**; otherwise a link to the listing's reviews | check each platform's terms |
| Book / contact | Buttons to the platforms, an enquiry form (Jetpack Forms), phone, WhatsApp link, email | links, owner's email |

**Booking buttons:** `core/buttons`, label "Book on Airbnb" / "Book on Booking.com" / "Book direct", links opened in a new tab (`rel="noopener"`). Put "Book direct" first if the owner prefers it (no platform fee for them); that is their call. Wording in the site's language.

**Availability:** the platforms' calendars can't be embedded. Options, in order of safety: link to the listing; an enquiry form with dates; a booking or iCal plugin, **tested on a Personal site first** (don't promise one). Use native Jetpack Forms for the enquiry, to the owner's email, with a privacy note.

## 5. Photos

Export from Figma at 2× display size into `~/Tudotype-media/<slug>/` (outside Dropbox), WebP or JPEG ≤ 2400 px, import with real alt text (what a guest sees, in the site's language). Photographers' and platform photos: ask whose they are before publishing; list credits in BRIEF.md. People in photos: the owner's consent.

## 6. Checks specific to rentals

- **Portugal, Alojamento Local:** the registration number belongs in the advertising, and the complaints-book (Livro de Reclamações) link is expected on the site. The rules change: confirm the current ones with the owner or the official portal before launch; don't state them as fact on the site without checking.
- Every link in BRIEF.md's link table opens the right listing.
- Prices carry their currency and what they include; a rule or policy appears only if the owner gave it.
- A privacy page and a note on the enquiry form (it collects names, emails, dates); a cookie notice only if analytics or embeds that set cookies are added (YouTube in privacy mode; a Google Maps iframe sets cookies).
- Phone first: tap targets ≥ 24 px, hero text readable over the photo, buttons reachable without scrolling.
- Then `KIT/bin/check-theme.py`, the editor validity check from `new-site` §6, `KIT/bin/measure-page.js` at desktop and 390 px, and the Ready to launch? list.

## 7. Hand over

As `figma-to-site` §9: the owner makes the account and buys the plan (Personal, €48 a year at the 2026-10-31 catalogue price, plus the domain), invites the user as Administrator, the user pushes with Studio Sync (test it on the first rental site: not yet proven on Personal), the owner presses Launch. End with the numbered list for the owner: copy to approve, photos to confirm, links to check, the AL number, who answers enquiries.

## Track the cost

Mark each phase with `KIT/bin/token-tracker.py` (`start` and `mark`) so the user knows what a site costs to produce; a session started in the project's own folder is much cheaper than one started in a big folder (see the `lean-context` repository).
