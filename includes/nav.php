<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$segments = array_values(array_filter(explode('/', trim($requestPath, '/'))));
$currentSegment = $segments[0] ?? '';

$navItems = [
    'product-strategy' => 'Product Strategy',
    'product-team-AI-vibe-coding' => 'Vibe Coding',
    'hire-me' => 'How Can I Help',
];

$buildUrl = function (string $path): string {
    return '/' . ltrim($path, '/');
};
?>

<nav class="site-nav" aria-label="Primary">
    <ul>
        <?php foreach ($navItems as $path => $name): ?>
            <li>
                <?php
                $isActive = ($currentSegment === $path);
                $titleAttr = htmlspecialchars($name, ENT_QUOTES);
                $href = htmlspecialchars($buildUrl($path), ENT_QUOTES);
                ?>
                <a href="<?= $href ?>" title="<?= $titleAttr ?>" class="<?= $isActive ? 'active' : '' ?>">
                    <?= $titleAttr ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul> 
</nav>
