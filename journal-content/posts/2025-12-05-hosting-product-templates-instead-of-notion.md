---
title: Hosting Product Templates Instead of Notion
date: 2025-12-05
summary: Moving templates from Notion to self-hosted markdown with a Notion-like UI, plus adding table and horizontal rule support to the markdown renderer.
---

I had a handful of product templates living in Notion that I wanted to bring onto my site. So I/AI/Cursor built a template system that looks like Notion but lives on my domain.

## 1. The setup

-   **Template structure (≈30 mins):** Created a `templates-content/templates/` folder, mirroring the journal structure. Set up markdown files with front matter (title, description, slug) for each of the 5 templates: Product Strategy, Product Review Process, 1-Pager, PRD, and Impact Assessment.
-   **Template viewer (≈90 mins):** Built `template.php` with a Notion-like UI — clean typography, minimal navigation (just "Back to site"), and a download button. Styled it to feel like Notion but match my site's aesthetic.
-   **Routing & sitemap (≈20 mins):** Added template routes to the router (`/template` and `/template/{slug}`) and updated the sitemap to include all templates for SEO.

## 2. Bugs introduced & fixed (2 in total)

1. **Function redeclaration error** – `generateTitleFromSlug()` was duplicated in both `templates-content/functions.php` and `journal-content/functions.php`. Removed the duplicate and reordered the requires so the journal functions load first (5 mins).
2. **Sitemap file path issue** – Template file paths weren't being checked properly when generating lastmod dates. Added proper file existence checks (5 mins).

Total debugging time: **~10 mins**. Much cleaner than the journal build!

## 3. Feature additions

-   **Table support** – Added markdown table parsing to the renderer. Tables now render with proper headers, borders, and hover effects. Works in both templates and journal entries.
-   **Horizontal rules** – Added support for `---` (and `***`, `___`) as visual dividers. Renders as a clean horizontal line with proper spacing.
-   **Download functionality** – Each template has a "Download Markdown" button that serves the raw `.md` file with proper headers.

## 4. The Notion-like UI

The template viewer uses:

-   Clean, minimal header with just a "Back to site" link
-   Sticky header that stays visible while scrolling
-   Typography matching Notion's style (Inter font, proper line heights)
-   Subtle hover effects on interactive elements
-   Responsive design that works on mobile (tables scroll horizontally)

The goal was to make it feel like Notion but clearly be part of my site. No full navigation, just focused content viewing.

## 5. SEO benefits

All templates are now:

-   Indexed in the sitemap with priority 0.7
-   Accessible via clean URLs (`/template/product-strategy-template`)
-   Properly meta-tagged for social sharing
-   Crawlable by search engines

The templates should perform much better for SEO than Notion links ever would.

## 6. What's next

The markdown renderer now supports:

-   Headers (h1-h6)
-   Lists (unordered)
-   Links, bold, italic, inline code
-   Blockquotes
-   Tables
-   Horizontal rules

I'm considering adding:

-   Ordered lists
-   Code blocks with syntax highlighting
-   Nested lists
-   Task lists (checkboxes)

But for now, the templates work great and people can download them as markdown files.

---

**TL;DR:** Moved 5 product templates from Notion to self-hosted markdown files with a Notion-like UI. Added table and horizontal rule support to the markdown renderer. The whole thing took about 2.5 hours, with only 2 small bugs to fix. Templates are now SEO-friendly, downloadable, and look great. The system is extensible — just add a markdown file to the templates folder and it appears automatically.
