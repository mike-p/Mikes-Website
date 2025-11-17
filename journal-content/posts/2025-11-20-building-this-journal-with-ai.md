---
title: Building This Journal With AI
date: 2025-11-20
summary: A friendly retro on spinning up the journal with GPT-5 Codex in a single afternoon.
---

I asked GPT-5 Codex to help me stand up this mini-journal, and we sprinted from “blank repo” to “live posts” in a single afternoon. Below is a quick retro for anyone curious how much work (and rework!) was involved.

## 1. Standing it up

-   **Initial scaffolding (≈45 mins):** Markdown loader, excerpt helper, simple list/detail template, and light SEO metadata. By the end of the first hour `/journal` listed posts and deep links rendered markdown nicely.
-   **Styling pass (≈60 mins):** Added typography, spacing, list styling, and responsive layout for both the list and article view. This also included the contact module that now appears beneath each journal page.

## 2. Bugs introduced & fixed (7 in total)

1. **Header nav missing on `/journal`** – PHP include path was relative; fixed with `__DIR__` (5 mins).
2. **Static asset URLs broken** – CSS reset/layout paths needed to be root-relative (10 mins).
3. **Journal slug routing** – Built-in PHP router fell back to the homepage. Added explicit slug handling (15 mins).
4. **Trailing slash redirect loop** – `.htaccess` wanted `/journal/`. Disabled `DirectorySlash` and later routed through `journal.php` (20 mins).
5. **Sitemap returning 404** – Apache rewrite wasn’t honoured; introduced `sitemap.php`, debugging helper, and an auto-regenerated static file (40 mins total).
6. **Navigation duplication** – Two “Journal” links fighting for attention; simplified to one handwritten badge (5 mins).
7. **Mobile nav collapse** – Centered desktop layout blew up on small screens; rewrote the grid and stack behaviour (20 mins).

Total debugging time: **~1 hour 55 mins**, spread across the afternoon.

## 3. Changing my mind (a lot)

-   **Navigation style** – We tried pill nav, centered nav, stacked nav, and finally landed on centered links with a handwritten journal marker on the right (4 iterations).
-   **Journal spacing & width** – Adjusted article widths, contact module spacing, and emphasis styling multiple times to match the rest of the site (3 tweaks).
-   **Sitemap strategy** – Went from “static file” ➜ “dynamic PHP” ➜ “dynamic + static fallback” as production realities surfaced.

## 4. Environment hiccups

-   PHP built-in router vs. Apache `.htaccess` behaved differently (slug routing + sitemap).
-   Trailing-slash rules on production forced real directories to shadow routes; renaming the journal folder and adding rewrites solved it.
-   Needed a quick debug endpoint (`sitemap-debug.php`) to confirm rewrites were actually reaching PHP.

## 5. Credits burned

-   Roughly **35k tokens** of GPT-5 Codex usage (about **$7** in API credits) to iterate, debug, and polish.
-   All work completed in a single sitting (~4.5 hours including writing this retro).

---

**TL;DR:** GPT-5 Codex paired programming let me ship a fully functional, styled journal (with routing, SEO, sitemap, and a custom nav experience) in an afternoon. The rework wasn’t zero — seven bugs, multiple design pivots, and some classic environment gremlins — but the velocity made it absolutely worth the credits. Now that the scaffolding is in place, future posts (like this one) are just a markdown file away.
