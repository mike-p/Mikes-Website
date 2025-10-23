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
