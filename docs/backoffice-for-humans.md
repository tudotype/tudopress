# The back office, for humans

The people who run a friend's site aren't developers and won't read documentation. They open the dashboard once a month to change the opening hours. Everything here is about that moment.

The **TudoPress Helper** plugin does the parts marked ⚙️. The rest is how you build.

## Principles

1. **Speak their language.** Labels name the result on the site, not the WordPress mechanism. "Highlight on the news page", not "Sticky". "Photos and files", not "Media Library". "Change the menu", not "Navigation".
2. **Fewer doors, none locked.** Hide what they never need; always leave a visible way back. A locked door becomes a phone call.
3. **Explain every field where it is.** One or two sentences: where it shows up, what happens when it's empty, an example value.
4. **Advise, never block.** Warn about missing alt text; don't stop the publish.
5. **Make the safe thing the easy thing.** Layouts to start from, sections that look right on a phone, colours that pass contrast, no free-image search with licence traps.
6. **One place for each thing.** If the address appears on four pages, it's a synced pattern edited once.

## Checklist

### Getting in
- [ ] ⚙️ The login screen shows the site's icon and name.
- [ ] The friend has their own account (never a shared login), with their real first name: the dashboard greets them by it.
- [ ] Their role: **Administrator** if they own the WordPress.com site (they do: see the playbook), **Editor** for anyone else who only writes.

### The dashboard
- [ ] ⚙️ "Start here" is the first panel: the tasks they come for, as large buttons.
- [ ] ⚙️ Add site-specific tasks with the `hwh_start_tasks` filter ("Add a recipe", "Change this week's menu").
- [ ] ⚙️ WordPress news, Quick Draft, the welcome panel and the Site Health widget are gone for them; "Ready to launch?" shows only to builders.
- [ ] ⚙️ The footer of every admin screen says "Need help? How this site works · Ask <you>".

### The menu
- [ ] ⚙️ Simple menu: Tools, Plugins, the theme and plugin file editors and (with comments off) Comments are hidden for non-builders.
- [ ] ⚙️ Users → Profile → "Show the full admin menu" brings everything back, per person.
- [ ] Custom post types get a plain menu name and an icon (`menu_icon`), and appear right under Pages (`menu_position` 21).

### The editor
- [ ] ⚙️ New pages offer the site's own page layouts ("Choose a pattern"), and only those.
- [ ] ⚙️ The inserter hides blocks they won't need (Classic, Verse, RSS, Tag Cloud…). Blocks already used keep working.
- [ ] ⚙️ WordPress.org's pattern directory and core's generic patterns are off: the Patterns tab shows the site's sections.
- [ ] ⚙️ Openverse is off in the Media tab.
- [ ] ⚙️ The code editor is off for them (Options → Code editor), so nobody pastes broken HTML by accident.
- [ ] ⚙️ "Before you publish" lists alt text, heading jumps, vague links, missing featured image and excerpt, each with "Show me".
- [ ] Every group in templates, parts and patterns has a name, so List View reads "Hero › Buttons", not "Group › Group".
- [ ] Sections that must keep their layout can be locked to content only (`"templateLock":"contentOnly"` on the section's group): text and images editable, structure not. Use it sparingly; the friend can still "Modify" it.
- [ ] Shared bits (address, opening hours, a footer call to action) are **synced patterns** (Patterns → Create → Synced), edited once.
- [ ] Nothing a visitor reads lives in PHP. If the friend can see it, they can edit it in the editor or in a field.

### Custom fields (when a site needs them)
- [ ] One box per subject ("Recipe details"), in the sidebar when short, under the content when long.
- [ ] ⚙️ Every field built with `hwh_field()`: a label, a help tip (where it shows, what empty means), an example as placeholder.
- [ ] Checkboxes named after the effect ("Show on the home page").
- [ ] Lists of things the friend orders (photos, team members) are reorderable by drag, not by typing numbers.
- [ ] The raw "Custom Fields" box is removed (`remove_post_type_support( $type, 'custom-fields' )` when you register fields yourself).
- [ ] Every field is registered with `register_post_meta()` and `show_in_rest`, so it works in the block editor and in Block Bindings.

### Settings
- [ ] ⚙️ Settings → TudoPress: who to ask, the site guide, and every helper as a checkbox with an explanation.
- [ ] Site-wide texts that don't belong to a page (shipping details, a banner message) go in one settings screen with help tips, or in a synced pattern. Never in the Customizer of a block theme: friends can't find it.

### Help
- [ ] ⚙️ A private **Site guide** page, linked from the toolbar and the dashboard, written from `docs/site-guide-template.md` in the friend's language.
- [ ] A 30-minute hand-off where the friend drives and you watch. Write down every place they hesitate; fix the dashboard, not the friend.

## What was learnt the hard way on tudotype.com

- A field without an explanation got filled with the wrong thing within a week. Help tips fixed it.
- WordPress's own "Sticky" checkbox meant nothing to anyone; "Highlight on the Gazette" did.
- An HTML comment left in a template made the whole page show "This block contains unexpected or invalid content" in the Site Editor.
- Hard-coded text in templates and PHP patterns meant every typo was a developer job. Content moved into pages and synced patterns.
- Hiding a panel without a way back produced confusion; a profile switch didn't.
- Settings in the Customizer weren't found by anyone in a block theme. A settings screen with help tips was.
