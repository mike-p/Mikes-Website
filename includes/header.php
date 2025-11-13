<?php
$navStyle = 'centered';
$navContainerClass = 'nav-container nav-style-centered';
$navStyleQueryString = '';
$homeHref = '/';
?>
<a class="skip-link" href="#main">Skip to content</a>
<header>
    <div class="nav-wrapper">
        <div class="back-home">
            <a href="<?= htmlspecialchars($homeHref, ENT_QUOTES) ?>" aria-label="Home">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24" role="img" aria-hidden="true">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg><span>Mike Smith</span>
            </a>
        </div>

        <div class="<?= htmlspecialchars($navContainerClass, ENT_QUOTES) ?>">
            <?php include __DIR__ . '/nav.php'; ?>
        </div>

        <a class="site-identifier" href="/journal" aria-label="Journal">
            <span class="site-identifier__subtitle">Notes & Musings</span>
            <span class="site-identifier__title">Journal</span>
        </a>
    </div>
</header>
