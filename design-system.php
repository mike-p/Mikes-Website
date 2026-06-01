<?php
include __DIR__ . '/includes/http-headers.php';

$pageMeta = [
    'title' => 'Design system | Mike Smith',
    'description' => 'Alpine Tactical design tokens, typography, spacing, and UI components used on mike-p.co.uk.',
    'robots' => 'noindex,nofollow',
];

$colorTokens = [
    'Core text & surfaces' => [
        ['--dn-bg', '#fbf9f6', 'Page / nav background'],
        ['--page-bg', '#fbf9f6', 'Body background alias'],
        ['--dn-ink', '#1b1c1a', 'Primary text'],
        ['--dn-muted', '#454743', 'Secondary text'],
        ['--dn-accent', '#5a5f68', 'Links, labels, nav'],
        ['--dn-accent-hover', '#424750', 'Link hover'],
        ['--dn-card', '#f5f3f0', 'Cards, thinking blocks'],
        ['--dn-rule', '#cfc5b6', 'Dividers (alias of oatmeal)'],
        ['--dn-nav-surface', '#fbf9f6', 'Header surface'],
        ['--dn-header-surface', 'var(--dn-nav-surface)', 'Header (alias)'],
    ],
    'Brand accents' => [
        ['--color-forest', '#1b2d24', 'Footer, connect panels'],
        ['--color-snow', '#f8f7f4', 'Text on forest'],
        ['--color-cedar', '#4a2c2a', 'Secondary accent'],
        ['--color-mulled-wine', '#5d2b2b', 'Draft badge, emphasis'],
        ['--color-oatmeal', '#cfc5b6', 'Borders, rules'],
        ['--terra', '#000000', 'Primary black'],
    ],
    'Warm & containers' => [
        ['--warm-bg', '#f2ede9', 'Warm panels, card hover'],
        ['--warm-border', '#cfc5b6', 'Warm borders'],
        ['--warm-text', 'var(--dn-ink)', 'Warm panel text'],
        ['--warm-callout-border', '#cfc5b6', 'Callout borders'],
        ['--bg-secondary', '#efeeeb', 'Secondary fills'],
        ['--surface-container-high', '#eae8e5', 'Highlights, markers'],
        ['--outline-variant', '#c6c7c1', 'Marker borders'],
        ['--avatar-placeholder', '#e4e2df', 'Avatar fallback'],
    ],
];

$spacingTokens = [
    ['--margin-mobile', '1.5rem', '24px', 'Horizontal padding (mobile)'],
    ['--margin-desktop', '5rem', '80px', 'Horizontal padding (desktop ≥1025px)'],
    ['--gutter', '2rem', '32px', 'Grid / column gap'],
    ['--container-max', '68.75rem', '1100px', 'Max content width'],
    ['--section-gap', '6rem', '96px on homepage', 'Between major sections (home)'],
    ['--section-gap-after-hero', '7rem', '112px', 'Below homepage hero'],
    ['--header-clearance', '6.5rem', '104px', 'Main top padding (interior)'],
    ['--header-clearance-home', '10rem', '160px', 'Main top padding (home)'],
    ['--nav-journal-reserve', '7rem', '112px', 'Nav space for Journal link'],
    ['--nav-journal-reserve-tablet', '5.5rem', '88px', 'Nav Journal reserve (tablet)'],
    ['--home-section-label-size', '12px', '12px', 'Homepage section labels'],
    ['--home-section-label-margin-bottom', '2rem', '32px', 'Label margin (ruled)'],
    ['--home-section-label-margin-bottom-plain', '1.25rem', '20px', 'Label margin (no rule)'],
];

$typographyTokens = [
    ['Display', 'var(--font-serif)', 'clamp(2.25rem, 6vw, 5rem)', '600', '-0.04em', 'home-hero__title'],
    ['Headline', 'var(--font-serif)', 'clamp(1.75rem, 4vw, 3rem)', '500', '-0.02em', 'home-section-heading'],
    ['Body', 'var(--font-sans)', '1.125rem', '400', 'normal', 'home-hero__lead'],
    ['Home section label', 'var(--font-label)', 'var(--home-section-label-size)', 'var(--home-section-label-weight)', 'var(--home-section-label-tracking)', 'home-section-label'],
    ['Label / meta', 'var(--font-label)', '13px', '400', '0.15em', 'home-hero__eyebrow'],
];

$breakpoints = [
    ['Mobile', 'max-width: 720px', 'Stacked layouts, scrollable nav'],
    ['Tablet', '721px – 1024px', 'Tighter nav gap, single-column home split'],
    ['Desktop', 'min-width: 1025px', '5rem gutters, two-column home sections'],
];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <?php include __DIR__ . '/includes/header-includes.php'; ?>
</head>
<body class="site-chrome page-site page-design-system">
    <div class="inner-body">
        <?php include __DIR__ . '/includes/header.php'; ?>
        <main id="main" aria-label="Main content">
            <div class="main-content main-content--site ds-page">
                <header class="ds-page__header page-hero hero-section">
                    <p class="section-label">Alpine Tactical</p>
                    <h1 id="design-system-title" class="title heading-serif">Design system</h1>
                    <p class="page-intro">Live reference for CSS custom properties and UI patterns on this site. Source of truth: <code>:root</code> in <code>css/layout.css</code> and <code>docs/DESIGN.md</code> in the repo.</p>
                </header>

                <section class="ds-section" aria-labelledby="ds-colors-heading">
                    <h2 id="ds-colors-heading" class="ds-section__title heading-serif">Colour tokens</h2>
                    <?php foreach ($colorTokens as $groupName => $tokens): ?>
                        <div class="ds-subsection">
                            <h3 class="ds-subsection__title"><?= htmlspecialchars($groupName, ENT_QUOTES) ?></h3>
                            <ul class="ds-swatch-grid">
                                <?php foreach ($tokens as [$name, $value, $note]): ?>
                                    <?php
                                    $swatchBg = preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) ? $value : 'var(--dn-card)';
                                    $isLight = in_array($value, ['#fbf9f6', '#f5f3f0', '#f2ede9', '#efeeeb', '#eae8e5', '#e4e2df', '#f8f7f4', '#ffffff'], true)
                                        || str_starts_with($value, 'var(--dn-bg)')
                                        || str_starts_with($value, 'var(--page-bg)');
                                    ?>
                                    <li class="ds-swatch">
                                        <span class="ds-swatch__chip<?= $isLight ? ' ds-swatch__chip--bordered' : '' ?>" style="background: <?= htmlspecialchars($swatchBg, ENT_QUOTES) ?>"></span>
                                        <div class="ds-swatch__meta">
                                            <code class="ds-token"><?= htmlspecialchars($name, ENT_QUOTES) ?></code>
                                            <span class="ds-swatch__value"><?= htmlspecialchars($value, ENT_QUOTES) ?></span>
                                            <?php if ($note !== ''): ?>
                                                <span class="ds-swatch__note"><?= htmlspecialchars($note, ENT_QUOTES) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                    <p class="ds-note">Grain overlay: <code>--grain-opacity: 0.04</code> on <code>body::after</code> (not a fill colour).</p>
                </section>

                <section class="ds-section" aria-labelledby="ds-type-heading">
                    <h2 id="ds-type-heading" class="ds-section__title heading-serif">Typography</h2>
                    <div class="ds-subsection">
                        <h3 class="ds-subsection__title">Font stacks</h3>
                        <ul class="ds-token-list">
                            <li><code>--font-serif</code> — Fraunces, Georgia, serif</li>
                            <li><code>--font-sans</code> — Inter, system-ui</li>
                            <li><code>--font-label</code> — Geist Sans, Inter, system-ui</li>
                        </ul>
                    </div>
                    <div class="ds-subsection">
                        <h3 class="ds-subsection__title">Scale (specimens)</h3>
                        <ul class="ds-type-specimens">
                            <?php foreach ($typographyTokens as [$role, $family, $size, $weight, $spacing, $class]): ?>
                                <li class="ds-type-specimen">
                                    <div class="ds-type-specimen__meta">
                                        <span class="ds-type-specimen__role"><?= htmlspecialchars($role, ENT_QUOTES) ?></span>
                                        <code class="ds-token">.<?= htmlspecialchars($class, ENT_QUOTES) ?></code>
                                    </div>
                                    <p class="ds-type-specimen__sample ds-type-specimen__sample--<?= htmlspecialchars($class, ENT_QUOTES) ?>">
                                        The quick brown fox jumps over the lazy dog.
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>

                <section class="ds-section" aria-labelledby="ds-space-heading">
                    <h2 id="ds-space-heading" class="ds-section__title heading-serif">Spacing & layout</h2>
                    <ul class="ds-space-list">
                        <?php foreach ($spacingTokens as [$name, $value, $px, $note]): ?>
                            <li class="ds-space-row">
                                <div class="ds-space-row__label">
                                    <code class="ds-token"><?= htmlspecialchars($name, ENT_QUOTES) ?></code>
                                    <span><?= htmlspecialchars($value, ENT_QUOTES) ?> <span class="ds-muted">(<?= htmlspecialchars($px, ENT_QUOTES) ?>)</span></span>
                                    <span class="ds-swatch__note"><?= htmlspecialchars($note, ENT_QUOTES) ?></span>
                                </div>
                                <div class="ds-space-row__bar" style="width: min(<?= htmlspecialchars($value, ENT_QUOTES) ?>, 100%);" aria-hidden="true"></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="ds-section" aria-labelledby="ds-bp-heading">
                    <h2 id="ds-bp-heading" class="ds-section__title heading-serif">Breakpoints</h2>
                    <p class="ds-note">Use only these three bands in new CSS — see <code>css/layout.css</code> header comment.</p>
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th scope="col">Band</th>
                                <th scope="col">Media query</th>
                                <th scope="col">Typical use</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($breakpoints as [$band, $mq, $use]): ?>
                                <tr>
                                    <td><?= htmlspecialchars($band, ENT_QUOTES) ?></td>
                                    <td><code><?= htmlspecialchars($mq, ENT_QUOTES) ?></code></td>
                                    <td><?= htmlspecialchars($use, ENT_QUOTES) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

                <section class="ds-section" aria-labelledby="ds-components-heading">
                    <h2 id="ds-components-heading" class="ds-section__title heading-serif">Components</h2>
                    <p class="ds-note">Sharp corners (0px radius). Depth via borders and tonal layers, not shadows.</p>

                    <div class="ds-subsection">
                        <h3 class="ds-subsection__title">Buttons</h3>
                        <div class="ds-component-row cta-group">
                            <a class="primary-cta" href="#ds-components-heading">Primary CTA</a>
                            <a class="secondary-cta" href="#ds-components-heading">Secondary CTA</a>
                            <a class="primary-cta primary-cta--quiet" href="#ds-components-heading">Primary quiet</a>
                            <a class="secondary-cta secondary-cta--quiet" href="#ds-components-heading">Secondary quiet</a>
                        </div>
                    </div>

                    <div class="ds-subsection">
                        <h3 class="ds-subsection__title">Labels & highlights</h3>
                        <p class="section-label">Section label</p>
                        <p>Body with <strong class="marker-highlight">marker highlight</strong> for emphasis.</p>
                        <span class="journal-draft-badge">Draft — publishes 8 Jun 2026</span>
                    </div>

                    <div class="ds-subsection">
                        <h3 class="ds-subsection__title">Connect panel (forest)</h3>
                        <div class="ds-forest-demo home-connect-warm">
                            <h3 class="home-connect-warm__title heading-serif">Say hi — I reply soon(ish).</h3>
                            <p class="home-connect-warm__lead">Forest background uses <code>--color-forest</code> with snow text.</p>
                            <div class="cta-group home-connect-warm__cta">
                                <a class="primary-cta primary-cta--quiet" href="#ds-components-heading">Email me</a>
                                <a class="secondary-cta secondary-cta--quiet" href="#ds-components-heading">LinkedIn</a>
                            </div>
                        </div>
                    </div>

                    <div class="ds-subsection">
                        <h3 class="ds-subsection__title">Cards</h3>
                        <div class="ds-card-demo home-thinking-card">
                            <span class="home-thinking-num">01</span>
                            <p class="home-thinking-text">Thinking card — <code>--dn-card</code> fill, oatmeal border.</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
        <footer>
            <?php include __DIR__ . '/includes/colophon.php'; ?>
        </footer>
    </div>
</body>
</html>
