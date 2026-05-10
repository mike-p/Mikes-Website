<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$segments = array_values(array_filter(explode('/', trim($requestPath, '/'))));
$currentSegment = $segments[0] ?? '';

$navItems = [
    'product-strategy' => 'Product strategy',
    'product-team-AI-vibe-coding' => 'Vibe coding',
    'work' => 'Work',
    'hire-me' => 'How I can help',
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
                <?php
                $linkClasses = [];
                if ($isActive) {
                    $linkClasses[] = 'active';
                }
                if ($path === 'journal') {
                    $linkClasses[] = 'nav-link--journal';
                }
                $classAttr = $linkClasses === [] ? '' : ' class="' . htmlspecialchars(implode(' ', $linkClasses), ENT_QUOTES) . '"';
                ?>
                <a href="<?= $href ?>" title="<?= $titleAttr ?>"<?= $classAttr ?>>
                    <?= $titleAttr ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul> 
</nav>
