---
name: Alpine Tactical
colors:
  surface: '#fbf9f6'
  surface-dim: '#dbdad7'
  surface-bright: '#fbf9f6'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3f0'
  surface-container: '#efeeeb'
  surface-container-high: '#eae8e5'
  surface-container-highest: '#e4e2df'
  on-surface: '#1b1c1a'
  on-surface-variant: '#454743'
  inverse-surface: '#30312f'
  inverse-on-surface: '#f2f0ed'
  outline: '#767872'
  outline-variant: '#c6c7c1'
  surface-tint: '#5e5e5e'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#1c1c1c'
  on-primary-container: '#848484'
  inverse-primary: '#c6c6c6'
  secondary: '#5a5f68'
  on-secondary: '#ffffff'
  secondary-container: '#dee2ed'
  on-secondary-container: '#60656e'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#201b12'
  on-tertiary-container: '#8b8376'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e2e2e2'
  primary-fixed-dim: '#c6c6c6'
  on-primary-fixed: '#1b1b1b'
  on-primary-fixed-variant: '#474747'
  secondary-fixed: '#dee2ed'
  secondary-fixed-dim: '#c2c6d1'
  on-secondary-fixed: '#171c23'
  on-secondary-fixed-variant: '#424750'
  tertiary-fixed: '#ece1d1'
  tertiary-fixed-dim: '#cfc5b6'
  on-tertiary-fixed: '#201b11'
  on-tertiary-fixed-variant: '#4c463b'
  background: '#fbf9f6'
  on-background: '#1b1c1a'
  surface-variant: '#e4e2df'
  cedar: '#4a2c2a'
  forest: '#1b2d24'
  snow: '#f8f7f4'
  mulled-wine: '#5d2b2b'
  oatmeal: '#cfc5b6'
  surface-warm: '#f2ede9'
typography:
  display-xl:
    fontFamily: Fraunces
    fontSize: 80px
    fontWeight: '600'
    lineHeight: '1.0'
    letterSpacing: -0.04em
  headline-lg:
    fontFamily: Fraunces
    fontSize: 48px
    fontWeight: '500'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Fraunces
    fontSize: 36px
    fontWeight: '500'
    lineHeight: '1.1'
  headline-md:
    fontFamily: Fraunces
    fontSize: 32px
    fontWeight: '500'
    lineHeight: '1.2'
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-caps:
    fontFamily: Geist
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1.0'
    letterSpacing: 0.1em
  label-mono:
    fontFamily: Geist
    fontSize: 13px
    fontWeight: '400'
    lineHeight: '1.4'
spacing:
  unit: 8px
  margin-mobile: 24px
  margin-desktop: 80px
  gutter: 32px
  section-gap: 160px
  container-max: 1100px
---

## Brand & Style

The brand identity, "Alpine Tactical," blends the warmth of a mountain lodge with the precision of a high-end editorial publication. It is a mix of **Minimalism** and **Tactile Brutalism**. The aesthetic prioritizes high-quality materials (wood, wool, wine) translated into digital form through the use of grain overlays, high-contrast borders, and sophisticated serif typography.

The target audience consists of discerning professionals who value "slow tech"—software that feels artisanal rather than industrial. The UI should evoke a sense of quiet authority, warmth, and intellectual clarity. It rejects the "Silicon Valley Slush" (generic rounded corners and vibrant gradients) in favor of sharp edges, deep natural tones, and intentional whitespace.

## Colors

The palette is rooted in an earthy, organic spectrum that mimics a rustic mountain environment. 

- **Primary & Neutral:** A high-contrast relationship between absolute Black (#000000) and Bone/Off-white (#fbf9f6) provides the structural foundation.
- **Natural Accents:** "Cedar" (deep mahogany) and "Forest" (dark evergreen) act as secondary backgrounds for high-impact sections.
- **Tonal Dividers:** "Oatmeal" and "Surface Warm" are used for subtle borders and background shifts to differentiate content blocks without relying on shadows.
- **Textural Highlights:** "Mulled Wine" is reserved for specific typographic emphasis or interactive highlights to add a layer of warmth.

A global `0.04` opacity grain overlay is applied across all surfaces to ensure no color feels "flat" or sterile.

## Typography

The typographic system relies on three distinct voices:
1. **The Editorial Voice (Fraunces):** Used for headlines and displays. It should frequently utilize italics to emphasize specific words or entire headers, creating a "literary journal" feel.
2. **The Functional Voice (Inter):** A neutral sans-serif used for body copy to ensure maximum readability and a modern edge against the traditional serif display.
3. **The Technical Voice (Geist):** A monospaced/technical sans used for metadata, labels, and "The Fireplace" notes. It provides the "Tactical" feel, suggesting precision and craftsmanship.

Large headlines (Display XL) should use aggressive negative letter-spacing to feel tight and impactful.

## Layout & Spacing

The system uses a **Fixed Grid** approach for desktop, centering a 1100px content container. 

- **Vertical Rhythm:** Extreme vertical spacing (`section-gap`) is used to give content "room to breathe," mimicking the vastness of an alpine landscape.
- **Bento Grid Logic:** For complex information architecture (Inventory), a 12-column grid is utilized with 32px gutters. Elements should span varying widths (e.g., 7-span, 5-span, 4-span, 8-span) to create a rhythmic, non-repetitive layout.
- **Responsive Behavior:** On mobile, margins reduce to 24px and grid columns stack vertically. Sections that use horizontal layouts (like Note entries) shift to a single-column flow while maintaining the generous 160px gap between major thematic sections.

### Site breakpoints (`css/layout.css`)

Use **only these three bands** when adding or changing layout rules:

| Band | Range | Typical use |
|------|--------|-------------|
| **Mobile** | `max-width: 720px` | Stacked hero, scrollable header nav, full-width CTAs |
| **Tablet** | `721px`–`1024px` | `1.5rem` gutters, centered nav cluster, single-column home split |
| **Desktop** | `min-width: 1025px` | `5rem` gutters, two-column home split / help section, CV timeline split |

Legacy widths (600, 640, 768, 900, 960px) were removed during CSS cleanup — do not reintroduce without a documented reason.

For maintenance debt, regressions, and a **selector → page** checklist, see [CSS-AUDIT.md](./CSS-AUDIT.md).

## Elevation & Depth

This system avoids shadows entirely. Depth is achieved through **Bold Borders** and **Tonal Layering**:

- **Layering:** Different surface colors (White, Bone, Cedar, Forest) indicate separate functional areas.
- **Borders:** 1px solid lines in "Oatmeal" or "Primary" colors define boundaries. This creates a "blueprint" or "architectural drawing" feel.
- **Interactions:** Depth is expressed through movement rather than Z-index. Hover states on cards utilize color shifts (e.g., White to Surface-Warm) and internal transformations (image sliding or font style changes to italics).
- **Overlays:** A persistent film grain texture sits at the highest Z-index with `pointer-events: none` to unify all layers under a tactile, physical "paper" feel.

## Shapes

The shape language is strictly **Sharp (0px roundedness)**. Every container, button, and image frame must have 90-degree corners to reinforce the "Carpenter" and "Architectural" themes. 

The only exception to this rule is the "Full" rounding (pill shape) used for specific decorative icons or functional tags, though these should be used sparingly to avoid breaking the structural rigidity of the design.

## Components

- **Buttons:** Sharp-edged boxes with 1px borders. Default state is transparent/outlined; hover state is a solid fill with inverted text. Labels are always uppercase Geist (Label-Caps).
- **Cards (Bento):** Defined by 1px "Oatmeal" borders. Images inside cards should use `object-cover` and have subtle hover transitions (e.g., grayscale to color, or slight scaling).
- **Input Fields:** Minimalist under-lines (border-bottom) rather than fully enclosed boxes. Use `label-mono` for field titles and `headline-md` for user input text.
- **Navigation:** A fixed top bar with a 1px bottom border. Links are uppercase Geist with a `decoration-secondary` underline on hover.
- **Images:** All imagery should carry a consistent treatment—either high-contrast grayscale or warm, tactile textures (wood grain, wool knit, snowy landscapes).
- **Badges/Tags:** Small rectangular blocks with solid primary backgrounds and inverted text, used for categories like "INVENTORY" or "CASE STUDY."