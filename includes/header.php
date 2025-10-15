<?php
// Get the current filename without extension
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Define your nav items (filename => display name)
$navItems = [
    'product-strategy' => 'Product Strategy',
    'product-team-AI-vibe-coding' => 'Vibe Coding',
    'hire-me' => 'How Can I Help'
];
?>

            
<a class="skip-link" href="#main">Skip to content</a>
<header>
    <div class="nav-container">                
        <div class="back-home">
            <a href="/" aria-label="Home">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24" role="img" aria-hidden="true">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg><span>Mike Smith</span>
            </a>
        </div>

        <nav class="site-nav" aria-label="Primary">
            <ul>
                <?php foreach ($navItems as $file => $name): ?>
                    <li>
                        <a href="<?= $file ?>" title="<?= $name ?>" class="<?= ($currentPage === $file) ? 'active' : '' ?>">
                            <?= $name ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul> 
        </nav>
    </div>
</header>