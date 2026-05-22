# CSS audit — Mikes-Website

**Date:** 2026-05-22 (post–Alpine Tactical + nav unification)  
**Scope:** `css/layout.css` (~3,043 lines), `css/reset.css`, body classes in PHP, `includes/header.php`, `includes/about.php`

This audit is stricter than the earlier pass. It explicitly checks **cross-page behaviour** (same HTML/chrome, different `body` classes) and **regression vectors** (scoped rules that silently diverge).

---

## Executive summary

| Area | Status | Risk |
|------|--------|------|
| Breakpoints (720 / 721–1024 / 1025) | Good | Low — documented in `:root` comment + `DESIGN.md` |
| Global header/nav | Good (after unification) | Low — was **High** when nav was `body.page-home` only |
| Body-class architecture | Weak | **Medium** — `page-home` vs `page-site` vs `page-template` |
| Monolithic `layout.css` | Weak | Medium — ~3k lines, 3 page “modes” in one file |
| Tokens vs legacy aliases | Partial | Low–medium |
| Dead CSS | Present | Low (noise + false confidence) |
| Footer variants | Fragmented | Medium |
| Journal nav hack | Brittle | Medium |

**What the first audit missed:** Treating “files synced” and “tokens exist” as sufficient, without verifying that **every page uses the same chrome selectors**. The nav regression happened because header layout lived under `body.page-home` while interior pages used `body.page-site` + `body:not(.page-home)` overrides — behaviour diverged though markup was identical.

---

## 1. Body classes (architecture)

### Current mapping

| `body` class | Pages | Purpose |
|--------------|-------|---------|
| `page-home` | `index.php` only | Homepage sections (`main-content--home`, `home-*`) |
| `page-site` | strategy, vibe, work, hire-me, journal, 404 | Interior marketing + journal shell |
| `page-template` | `template.php` | Template viewer (sticky sub-header, different footer) |
| `layout-split` | `work.php` (+ `page-site`) | CV two-column timeline (desktop ≥1025) |

### Debt

1. **`page-site` is a grab-bag** — Figures, errors, footer cedar, and colophon share one class with no semantic link to “interior page.”
2. **Duplicated selectors** — Many rules repeat `body.page-home, body.page-site` (footer). Should be `:where(.page-site, .page-home)` or a single `.site-chrome` on `<body>`.
3. **`page-home` was overloaded historically** — Used on all pages for “warm theme”; split to `page-site` fixed overlap bugs but left mental overhead.
4. **`page-template` is a third chrome line** — Footer padding/width differ from `page-site`; easy to forget when changing footer tokens.

### Recommendation

```html
<body class="site-chrome page-site">   <!-- interior -->
<body class="site-chrome page-home">   <!-- index only -->
<body class="site-chrome page-template"><!-- templates -->
```

- Shared chrome (footer cedar, `inner-body`, header clearance) → `.site-chrome` or `:where(.page-home, .page-site)`.
- Page-specific blocks stay on `.page-home` / `.page-template` only.

---

## 2. Header / navigation

### Current (correct) behaviour

- **All pages:** Grid `nav-wrapper` → brand | full-width `nav-container`.
- **All pages:** Primary links centered; Journal `li.nav-item--journal` absolutely pinned right with `padding-right: 7rem` on `ul`.
- **Tablet 721–1024:** Tighter gaps, 11px caps links.
- **Mobile ≤720:** Horizontal scroll; Journal `margin-left: auto` when absolute positioning off.

### Remaining debt

| Issue | Detail |
|-------|--------|
| `nav-style-centered` | Misleading name; used on every page via `header.php`. Rename to `nav-primary` or drop extra class. |
| Magic number `7rem` / `5.5rem` | Reserves space for Journal; not tokenized. Breaks if label or font changes. |
| Journal positioning | `position: absolute` on last item + padding compensation — prefer CSS grid areas on `nav-wrapper` (brand \| links \| journal). |
| `padding-right: 0` on mobile | Clears desktop reserve; verify overlap on narrow widths. |

### Regression test checklist (manual)

- [ ] `/` — centered nav, Journal right, Mike Smith visible (≥721px)
- [ ] `/product-team-AI-vibe-coding` — **matches** `/` nav at 823px, 1280px
- [ ] `/work` — same nav; timeline only affects main
- [ ] `/journal` + entry — nav unchanged
- [ ] `/template/...` — nav + sticky template header do not collide

---

## 3. Breakpoints

### Declared bands (correct)

```
Mobile:  max-width 720px
Tablet:  721px – 1024px
Desktop: min-width 1025px
```

### Scattered `@media (min-width: 1025px)` blocks (consolidation opportunity)

Six separate desktop blocks (grep count) — same breakpoint, harder to see full desktop picture:

- ~180 — `header .nav-wrapper` horizontal padding  
- ~1277 — CV `.layout-split` timeline  
- ~2664 — footer colophon (home + site)  
- ~2697 — `page-template` footer  
- ~2841 — `main-content--home` horizontal padding  
- ~2856 — `home-split` 4fr/8fr  
- ~2884 — `home-help-section` 5fr/7fr  

**Recommendation:** One `/* Desktop ≥1025 */` section with sub-comments, or postcss/custom media (only if you add a build step).

### Two `@media (max-width: 1024px)` blocks

- ~1434 — interior typography / section layout (large block)  
- ~2209 — title/section-title scale only  

Merge or cross-reference in comments to avoid editing one and missing the other.

---

## 4. Spacing tokens

| Token | Value | Used for |
|-------|-------|----------|
| `--section-gap` | 10rem | Home split, help section |
| `--section-gap-after-hero` | 7rem | Below hero CTAs |
| `--header-clearance` | 6.5rem | `.main-content--site` top (fixed header) |
| (hardcoded) | 10rem | `.main-content--home` top only |

**Debt:** Homepage uses `10rem` main padding; interior uses `6.5rem`. Intentional but undocumented in token comments. Consider `--header-clearance-home: 10rem` for parity.

---

## 5. Dead / legacy CSS (safe to remove)

No matching markup in repo (verified grep):

```css
footer .content-section:not(.connect-warm-card) { ... }
footer .content-section:not(.connect-warm-card):last-of-type { ... }
footer .connect-section .section-content { ... }
```

Also grep `connect-section` in layout — line 765 may be orphaned; verify before delete.

---

## 6. Footer (three behaviours)

| Context | Rule source | Visual |
|---------|-------------|--------|
| Default `footer` | L831 | `max-width: 53rem`, cedar, smaller padding |
| `page-home` + `page-site` | L2647 | Full bleed, `5rem` vertical padding, colophon in grid |
| `page-template` | L2689 | `max-width: container`, different padding |

**Debt:** Base `footer` rules still apply then get overridden — cascade is hard to reason about. Template pages never get full-bleed cedar band unless intentional.

---

## 7. Contact CTA (`about.php`)

- Renders in `<main>` as `.site-connect` (fixed 2026-05-22).
- Excluded on `index` and `hire-me` via URI segment check in `about.php`.
- **Debt:** Page detection uses `REQUEST_URI` — correct for `router.php`; document in `about.php` comment.

---

## 8. Token hygiene

Legacy aliases still used alongside Alpine tokens:

- `--terra` (black CTAs)
- `--warm-bg`, `--warm-border`, `--warm-text`, `--warm-callout-border`
- `--dn-coral` (duplicate of accent in places)

**Recommendation:** Map aliases to semantic tokens in `:root` only; grep phase-out in components.

---

## 9. File structure

| File | Lines | Role |
|------|-------|------|
| `layout.css` | ~3043 | Everything |
| `reset.css` | ~43 | Meyer reset |

**Recommendation (no build step):** Split imports in `header-includes.php`:

```
css/tokens.css      → :root + breakpoints comment
css/chrome.css      → header, footer, nav, colophon
css/home.css        → .main-content--home, .home-*
css/site.css        → .main-content--site, .page-section
css/journal.css     → journal-* (optional)
css/work.css        → .layout-split, .cv-* (optional)
```

---

## 10. Priority remediation plan

### P0 — Prevent nav/chrome regressions ✅ (2026-05-22)

1. Optional note: [PRE-DEPLOY.md](./PRE-DEPLOY.md) (only if you changed header/footer)
2. **`site-chrome` on `<body>`** on all pages; header uses bare `header` selectors only

### P1 — Quick wins ✅ (2026-05-22)

1. Removed dead `footer .content-section` / `connect-section` rules
2. Footer cedar/colophon: `:where(.page-home, .page-site)`
3. Tokens: `--header-clearance-home`, `--nav-journal-reserve` (+ tablet)
4. Renamed `nav-style-centered` → `nav-primary` in PHP + CSS

### P2 — Half day

1. Consolidate six `min-width: 1025px` blocks into one section.
2. Merge or document twin `max-width: 1024px` blocks.
3. Refactor Journal nav to grid columns (drop `7rem` padding hack).

### P3 — Larger

1. Split `layout.css` per section 9.
2. Introduce `.site-chrome` body class.
3. Align `page-template` footer with site cedar band (product decision).

---

## 11. Why the first audit felt “fine” but wasn’t

| First audit checked | Missed |
|-------------------|--------|
| Tokens present | Same nav markup, **different** CSS paths per `body` class |
| File hash / deploy | **Behaviour** at 823px viewport |
| Dead `nav-separator` | **Active** `body:not(.page-home)` block fighting homepage grid |
| `layout.css` size | **Ownership** — which selectors apply to which pages |
| Stitch visual diff | Interior pages never matched home **chrome**, only colours |

**Rule for next audit:** For each global component (header, footer, main padding), list **selector → pages affected** in a table. If selectors mention `page-home` or `:not(.page-home)`, flag immediately.

---

## Appendix: selector → pages matrix (header)

| Selector | index | page-site | page-template |
|----------|-------|-----------|---------------|
| `header .nav-wrapper` (grid) | Yes | Yes | Yes |
| `body.page-home .main-content--home` | Yes | No | No |
| `.main-content--site` + `--header-clearance` | No | Yes | Partial |
| `body.page-template .template-header` sticky | No | No | Yes |

*After 2026-05-22 nav unification, header row is aligned; main padding row still differs by design.*
