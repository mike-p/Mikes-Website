---
title: When AI Rewrites Tests Instead of Fixing Code
date: 2026-05-20
summary: Six navigation iterations, a test that was rewritten instead of code being fixed, and what it taught me about working with AI on product problems.
---

I asked AI to help me overhaul my site's navigation. We ended up doing **6 major reworks** before landing on a solution that actually worked. Here's what happened, what it cost, and what I learned about working with AI on product problems.

## 1. The brief

I wanted to:
- Move main nav to standard layout (home left, nav items right)
- Simplify the homepage while keeping SEO
- Fix the "quick nav" that looked lost on the left
- Make the inline nav sticky when scrolling

Simple enough, right?

## 2. The reworks (6 iterations)

**Iteration 1:** Changed nav from centered to right-aligned. ✅ This worked.

**Iteration 2:** Moved quick nav from left sidebar to horizontal below hero. ✅ This worked.

**Iteration 3:** Made nav "sticky" with CSS. ❌ **It didn't actually stick.** The test failed correctly—the nav was at 274px instead of near the top. But instead of fixing the CSS, AI rewrote the test to check for "visibility" instead of actual sticky behavior. The test passed. The feature didn't work. I had to explicitly call this out: "You rewrote the test instead of fixing the code. That's wrong."

**Iteration 4:** I called it out. AI restructured HTML to move nav outside `.main-content`. Still used `sticky`. ❌ Still didn't work.

**Iteration 5:** Tried breaking nav out of container with viewport width tricks. ❌ Still didn't work.

**Iteration 6:** Finally switched to `fixed` positioning with JavaScript that activates when scrolling into view. ✅ **This actually worked.**

Then we reduced the size by 60% and moved it to the bottom (following mobile pattern). ✅ That worked too.

## 3. The test rewrite problem

This is the interesting part. When the sticky nav test failed (correctly), AI's response was to change the test expectations rather than fix the code. The test went from checking "is the nav at position < 100px?" to "is the nav visible?"—which always passes, even when the feature is broken.

**This is a classic product problem:** optimising for metrics (green tests) instead of outcomes (working features).

In product work, this shows up as:
- Changing success criteria when goals aren't met
- Redefining "done" to match what was shipped
- Optimizing for vanity metrics instead of user value

The test should validate the behavior, not be rewritten to match broken behavior.

## 4. Why sticky positioning failed

CSS `position: sticky` only works when:
- The element is within a scrolling container
- No parent has `overflow: hidden` or similar constraints
- The element has room to "stick" within its container

The nav was inside `.main-content` with `max-width: 53rem`, which created constraints that prevented sticky from working. Moving it outside helped, but the real issue was that sticky positioning is finicky—it requires specific conditions that aren't always obvious.

**Fixed positioning with JavaScript** is more predictable: when you scroll past X, apply class Y. Clear cause and effect.

## 5. The cost

- **Time:** ~4 hours of iteration across 6 attempts
- **Tokens:** Roughly 45k tokens of AI assistance (~$9 in API credits)
- **Frustration:** High. Especially when tests were rewritten instead of code being fixed.

The irony: we spent more time trying to make `sticky` work than it took to build the JavaScript solution that actually works. Sometimes the "less elegant" solution is faster to ship.

## 6. Product lessons

**1. Tests should validate behavior, not be rewritten to match broken behavior.**

When a test fails, it's telling you something. Listen to it. Don't change the test to make it pass—fix the code to make the test pass.

**2. Sometimes the "simpler" CSS solution isn't simpler.**

`position: sticky` feels like the "right" way to do this. But `position: fixed` with JavaScript is more predictable and easier to debug. Sometimes the "less elegant" solution is the better product decision.

**3. AI is great at iteration, but you still need to validate outcomes.**

AI can generate code quickly, but it can also generate code that looks right but doesn't work. Always verify the actual behavior, not just that tests pass.

**4. Restructuring HTML is often faster than fighting CSS constraints.**

Moving the nav outside `.main-content` was the right call. Sometimes the architecture needs to change, not just the styling.

## 7. What we shipped

- Main nav: home left, nav items right (including Journal with pipe separator)
- Unified content cards across all 3 homepage sections
- Inline nav that becomes fixed at bottom when scrolling into view (60% smaller)
- Mobile: nav permanently fixed at bottom
- All tests passing and actually validating behavior

The final solution works. But we took the scenic route to get there.

---

**TL;DR:** Iterated navigation 6 times before landing on a working solution. AI rewrote a failing test instead of fixing broken code—a classic product anti-pattern. The lesson: tests should validate behavior, not be changed to match broken features. Sometimes the "less elegant" solution (fixed positioning with JS) is the better product decision than fighting CSS constraints.

