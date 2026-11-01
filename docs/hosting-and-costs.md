# Hosting and costs

Checked on the dates given. Check again before quoting a friend: plans and prices change.

## Why WordPress.com for friends

- **One bill, nothing to maintain.** Hosting, security, core and plugin updates, a CDN, SSL and spam protection are included. No server, no FTP, no hosting company to chase.
- **The friend owns it.** Their account, their card, their domain. You join as an administrator.
- **It's cheaper than the usual small-business stack.** A website builder subscription plus a domain plus a form service usually costs more per year than Personal plus a domain.
- **Studio fits it.** Build locally, show on a preview link, push with Studio Sync.

## Plans (checked 2026-10-06)

| Plan | Roughly | Enough when |
|---|---|---|
| Free | €0 | Never for a friend's real site: a wordpress.com address, no custom theme or plugins |
| **Personal** | about $4/month paid yearly ($9 monthly) | Almost every friend site. Since 2 April 2026 it allows uploaded themes, plugins and Studio Sync |
| Premium | more | Video uploads beyond a few files, advanced design tools, earning from ads |
| Business | more | Shops with heavy plugins, staging, SFTP/SSH, WP-CLI, daily server backups |
| Commerce | most | A real shop with many products |

The Studio docs say Sync works on every paid plan; an older page still says Business. **Test Sync on Personal with the first friend before promising it.**

Video: the plan catalogue puts video uploads on Premium and above (tudotype.com issue #2). For one or two short loops, embed from Vimeo or YouTube (privacy mode) instead.

## Domains

- Register in the **friend's** name and account (WordPress.com, or any registrar they already use).
- A first year is often included with a yearly plan.
- **Email:** WordPress.com plans don't include mailboxes. Options: Google Workspace or Microsoft 365 (paid per person), WordPress.com's own email offer, or forwarding to their existing Gmail (free with most registrars). Copy MX, SPF, DKIM and DMARC records before you move nameservers, or their email stops.
- Transfers need the domain to be unlocked and an authorisation code; most registrars won't transfer within 60 days of a registration or transfer.

## Free tools you'll use

- **WordPress Studio** (local sites, previews, Sync).
- **Studio preview sites** (`*.wp.build`): free, public to anyone with the link, expire 7 days after the last update.
- **WordPress Playground** for a throwaway demo in a browser tab.

## What to tell the friend

"Your website will cost about €X a year for hosting and €Y for the address. That includes updates, security and backups of your content. You own the account; I'm an administrator so I can help. If we stop working together, nothing breaks."
