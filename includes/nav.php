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
        foreach ($navItems as $path => $name):
            $isActive = ($currentSegment === $path);
            $isJournal = ($path === 'journal');
            $titleAttr = htmlspecialchars($name, ENT_QUOTES);
            $href = htmlspecialchars($buildUrl($path), ENT_QUOTES);
            $liClasses = $isJournal ? 'nav-item--journal' : '';
            $liClassAttr = $liClasses === '' ? '' : ' class="' . htmlspecialchars($liClasses, ENT_QUOTES) . '"';
        ?>
            <li<?= $liClassAttr ?>>
                <?php
                $linkClasses = [];
                if ($isActive) {
                    $linkClasses[] = 'active';
                }
                if ($isJournal) {
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
