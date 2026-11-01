# Design basics

Widely published, basic rules every site built with this kit meets. Sources at the end. Two tools measure them: `bin/contrast.py` (palette contrast from theme.json) and `bin/measure-page.js` (a rendered page, pasted into a browser console).

## Type

- **Few typefaces.** One or two is usually enough; every extra family adds font files to download and styles to keep consistent.
- **A small set of sizes**, defined once as presets in `theme.json`. Blocks use the presets; avoid one-off sizes in the editor.
- **Only the weights you load.** If the font files declare 400 and 700, a 500 in the editor shows as one of them.
- **Body text at least 16 px**, line height around 1.5 (WCAG 1.4.12 requires that people can increase it without breaking the page).
- **Line length** of roughly 45–75 characters for reading text.
- **Flush-left body text.** Centre only short lines.
- **No letterspacing on lowercase text.** Small capitals and all-caps labels may take a little.
- **Bold or italic for emphasis, not both, and not underline** (underline means a link).
- **Curly quotes and apostrophes** (“ ” ’), en dash for ranges (10–12), em dash for breaks. WordPress converts straight quotes in post content automatically; check titles and template text.

## Colour and contrast (WCAG 2.2 AA)

- Text: **4.5:1** against its background; large text (24 px, or 18.66 px bold) **3:1**.
- Borders of form fields, icons, focus rings: **3:1** (WCAG 1.4.11).
- Light text on mid-tone colour usually fails. Check every text colour on every background the site uses: `bin/contrast.py theme.json`.
- Compute ratios; don't judge by eye.

## Structure

- **One H1 per page**, then H2, H3 in order. Choose a heading level for structure, a size for looks.
- Headings only for headings: a copyright line or a price is a paragraph.
- **Enter makes a paragraph.** No empty paragraphs for spacing; spacing comes from the design.
- Alt text that says what an image shows; empty alt on decoration.
- Links that say where they go, never "click here" or a bare web address.

## Phone

- Check the phone view separately: stacking order, what covers what, buttons that fit their label.
- **Tap targets at least 24 × 24 px** (WCAG 2.5.8), 44 px is better.
- One floating element at most; nothing that covers text.
- Anything that moves for more than 5 seconds can be paused, and nothing moves for people who ask for reduced motion.

## Performance

- Fewer font files and smaller images before anything clever. Resize photos before upload (≈2400 px long side).
- The largest image on the first screen should load first (not lazy-loaded).

## Sources

- WCAG 2.2: w3.org/TR/WCAG22 (1.1.1, 1.3.1, 1.4.3, 1.4.11, 1.4.12, 2.2.2, 2.4.4, 2.4.6, 2.5.8, 4.1.2).
- Matthew Butterick, *Practical Typography*: practicaltypography.com (line length, line spacing, centred text, letterspacing, bold or italic, straight and curly quotes, hyphens and dashes, carriage returns).
- WordPress Theme Handbook: developer.wordpress.org/themes (theme.json, presets).
