---
name: launch
description: Takes a friend's local Studio site (built with the TudoPress kit) live on WordPress.com: choosing a plan, the friend's own account, pushing with Studio Sync or uploading the theme and plugin, connecting the domain without breaking email, and the launch-day checklist. Use when the user says a friend's site is ready, wants to "put it online", "launch", "go live", "connect the domain", "push to WordPress.com", or asks what plan a friend needs.
---

# Launch

Everything in this skill touches money, accounts or the public web. **Each outward step needs the user's explicit go**, and steps that need the friend's card or login are theirs to do; give exact click paths instead.

KIT = `${TUDOPRESS_KIT:-~/Documents/Github-Repository/tudopress}`. Background: `KIT/PLAYBOOK.md` §1, §7, and `KIT/docs/hosting-and-costs.md`.

## 0. Ready?

1. Dashboard → **Ready to launch?** on the local site: every item green except "Search engines may list the site" (that's launch day).
2. `KIT/bin/check-theme.py` → `CHECK_OK`; no invalid blocks (see `/tudopress:new-site` step 6).
3. Copy and photos approved by the friend (BRIEF.md "Copy to approve" empty). No DRAFT left on a page: `studio wp post list --post_type=page,post --s=DRAFT --fields=ID,post_title`.
4. The friend has seen it on a phone (Studio preview).

## 1. Account and plan (the friend does this)

- The friend creates **their own** WordPress.com account and site, and buys the plan with their card. Usually **Personal**; see the hosting doc for when to go higher. Re-check current plan limits on wordpress.com/pricing before advising.
- The friend invites the user: their site → Users → Add new user → the user's email, role **Administrator**.
- If the WordPress.com MCP tools are available, you may read plans (`wpcom-plans-list`) and the user's sites (`wpcom-user-sites`) to confirm the invite worked. Never buy, change DNS or nameservers through them without the user's explicit go for that exact action.

## 2. Move the site

**A. Studio Sync (preferred):** in the Studio app → the site → Sync → connect WordPress.com → choose the friend's site → Push. Or `studio push` from the site folder (`studio push --help` for options). Before the first push, make sure `~/Studio/<slug>/.deployignore` exists (`new-site.sh` writes it) with at least:
```
*.md
*.log
wp-content/upgrade/
```
A push **replaces** the remote site's content. Fine for the first launch; never after the friend has started editing online (pull first instead).

**B. Manual:** `KIT/bin/package.sh ~/Studio/<slug>` → upload the theme zip (Appearance → Themes → Add Theme → Upload) and the plugin zip (Plugins → Add New → Upload), then Tools → Import → WordPress with an export from the local site (`studio wp export`), including media.

After either: Settings → Permalinks → Save (flushes rewrites), Settings → TudoPress (builder = the user, guide page set), visit every page.

## 3. Domain

- **New domain:** the friend registers it in their WordPress.com account (often free for the first year with a yearly plan); it connects itself.
- **Existing domain elsewhere:** decide with the user between connecting (change nameservers or A records at the current registrar) and transferring (unlock + auth code; not within 60 days of the last registration or transfer).
- **Email first.** Before changing nameservers, list the current DNS records (`dig +short MX <domain>`, `dig +short TXT <domain>`, and DKIM selectors from the mail provider) and recreate MX, SPF, DKIM and DMARC in WordPress.com → Domains → DNS records **before** switching. Then send a test email both ways.
- Make the custom domain primary; check https works and www redirects.

## 4. Launch day

- Settings → Reading → untick "Discourage search engines" (or use the launch checklist link).
- Privacy policy published; linked in the footer.
- Forms (Jetpack Forms): send a test submission; it arrives in Feedback and by email.
- Newsletter (Jetpack): subscribe with a test address.
- Site icon shows in a browser tab; a link preview shows title, description and image (paste the URL in a chat app).
- Check four widths and a phone. Check one colour variation if the friend may switch.
- Dashboard → Ready to launch? all green.

## 5. Report

What's live (URL), what the friend still has to do (with click paths), renewal dates (plan, domain) to put in the Site guide, then offer `/tudopress:handoff`.
