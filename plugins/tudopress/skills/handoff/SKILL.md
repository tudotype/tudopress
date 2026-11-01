---
name: handoff
description: Hands a finished friend's WordPress site over so they can run it alone, using the TudoPress kit: their account and role, the simple admin menu, the private Site guide in their words, a short hand-off session script, and what to watch for afterwards. Use when the user says a friend's site is live or nearly live and asks to "hand it over", "teach <friend> to use it", "write the guide", "prepare the hand-off" or "make it easy for them to edit".
---

# Hand-off

The goal: the friend changes a text, a photo and the menu on their own, and knows who to ask. KIT = `${TUDOPRESS_KIT:-~/Documents/Github-Repository/tudopress}`. Principles: `KIT/docs/backoffice-for-humans.md`.

## 1. Accounts

- The friend logs in with **their own** account, real first name filled in (the dashboard greets them by it). Role: Administrator if they own the site; Editor for helpers who only write.
- Settings → TudoPress: the user ticked as **builder**, the friend **not** (so they get the simple menu), builder's name and email filled, Site guide page chosen.
- If the site runs locally only, do this on the local site; on WordPress.com the user does it (or you, through the admin, with their go).

## 2. The Site guide

Write it from `KIT/docs/site-guide-template.md`, in the friend's language, about **this** site:
- Their real tasks first ("Change this week's menu"), with the exact buttons from the Start here panel.
- Renewal dates and amounts (plan, domain) from the launch report.
- "Things only <builder> should change": the parts you'd rather they didn't touch.
Put it into the private Site guide page as blocks. Keep it under two screens.

If a task needs more than three steps, change the site instead: a Start here button (`hwh_start_tasks` filter), a synced pattern, a field with a help tip.

## 3. The session (30 minutes, the friend drives)

Give the user this script; they run it with the friend, on the friend's computer:
1. Log in. Find the Site guide in the toolbar.
2. Change one sentence on the home page; save; look at the site.
3. Replace one photo; write its description.
4. Write a short news post with a featured image; read the "Before you publish" list; publish; delete it again if it was a test.
5. Add a page from a layout; add it to the menu; remove it again.
6. Change the colours in Styles → Browse styles; undo.
7. Ask: "What would you want to change next month?" Write it down.

Ask the user to note every hesitation. Each one is a fix to the site (a label, a button, a help tip, a shorter path), not to the friend.

## 4. Follow-up

- A month later: what did they change, what did they ask about? Update the guide and the dashboard.
- Before the plan or domain renews: a reminder to the friend.
- Record lessons that apply to every friend site in `KIT/PLAYBOOK.md` (ask the user before editing it).
