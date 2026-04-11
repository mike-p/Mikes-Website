<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$segments = array_values(array_filter(explode('/', trim($requestPath, '/'))));
$currentSegment = $segments[0] ?? '';

$navItems = [
    'product-strategy' => 'Product Strategy',
    'product-team-AI-vibe-coding' => 'Vibe Coding',
    'work' => 'Work',
    'hire-me' => 'How Can I Help',
    'journal' => 'Journal',
];

$buildUrl = function (string $path): string {
    return '/' . ltrim($path, '/');
};
?>

<nav class="site-nav" aria-label="Primary">
    <ul>
        <?php 
        $itemCount = count($navItems);
        $index = 0;
        foreach ($navItems as $path => $name): 
            $index++;
            $isLast = ($index === $itemCount);
            $isActive = ($currentSegment === $path);
            $titleAttr = htmlspecialchars($name, ENT_QUOTES);
            $href = htmlspecialchars($buildUrl($path), ENT_QUOTES);
        ?>
            <?php if ($isLast): ?>
                <li class="nav-separator">|</li>
            <?php endif; ?>
            <li>
                <a href="<?= $href ?>" title="<?= $titleAttr ?>" class="<?= $isActive ? 'active' : '' ?>">
                    <?= $titleAttr ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul> 
</nav>
