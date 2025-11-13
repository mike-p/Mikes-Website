<?php

require __DIR__ . '/journal-content/functions.php';

$postsDirectory = __DIR__ . '/journal-content/posts';
$entries = loadJournalEntries($postsDirectory);
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : null;

// Ensure sitemap.xml remains up to date whenever the journal is viewed.
$baseUrl = sitemapBaseUrl();
$sitemapPath = __DIR__ . '/sitemap.xml';
$sitemapXml = buildSitemapXml($baseUrl, $entries);

if (is_writable(__DIR__) || (file_exists($sitemapPath) && is_writable($sitemapPath))) {
    @file_put_contents($sitemapPath, $sitemapXml);
}

if (($slug === null || $slug === '') && ($_SERVER['REQUEST_URI'] ?? '') !== '') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
    if (preg_match('#^/journal/([^/]+)$#', rtrim($requestPath, '/'), $matches)) {
        $slug = trim($matches[1]);
    }
}
$currentEntry = null;

if ($slug !== null && $slug !== '') {
    foreach ($entries as $entry) {
        if ($entry['slug'] === $slug) {
            $currentEntry = $entry;
            break;
        }
    }

    if ($currentEntry === null) {
        http_response_code(404);
    }
}

$pageMeta = [
    'title' => 'Journal | Mike Smith',
    'description' => 'Notes and musings on product leadership, AI, and the craft of building teams.',
    'og_type' => 'website',
];

if ($currentEntry !== null) {
    $pageMeta['title'] = $currentEntry['title'] . ' | Journal | Mike Smith';
    $pageMeta['description'] = journalExcerpt($currentEntry);
    $pageMeta['og_type'] = 'article';
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <?php include __DIR__ . '/includes/header-includes.php'; ?>
</head>
<body>
    <div class="inner-body">
        <?php include __DIR__ . '/includes/header.php'; ?>
        <main id="main" aria-label="Main content">
            <div class="main-content" role="main">
                <?php if ($currentEntry === null): ?>
                    <section class="page-header" aria-labelledby="journal-page-title">
                        <h1 id="journal-page-title" class="title">Journal</h1>
                        <p class="page-intro">Random musings and working notes from the product trenches.</p>
                    </section>

                    <?php if (empty($entries)): ?>
                        <p>No entries yet. Check back soon.</p>
                    <?php else: ?>
                        <ul class="journal-list">
                            <?php foreach ($entries as $entry): ?>
                                <li class="journal-list-item">
                                    <div class="journal-meta">
                                        <time datetime="<?= htmlspecialchars($entry['date']->format('Y-m-d'), ENT_QUOTES) ?>">
                                            <?= htmlspecialchars($entry['date']->format('j M Y'), ENT_QUOTES) ?>
                                        </time>
                                    </div>
                                    <div class="journal-content">
                                        <h2 class="journal-title">
                                            <a href="/journal/<?= htmlspecialchars($entry['slug'], ENT_QUOTES) ?>">
                                                <?= htmlspecialchars($entry['title'], ENT_QUOTES) ?>
                                            </a>
                                        </h2>
                                        <?php $excerpt = journalExcerpt($entry, 200); ?>
                                        <?php if ($excerpt !== ''): ?>
                                            <p class="journal-excerpt"><?= htmlspecialchars($excerpt, ENT_QUOTES) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php elseif ($currentEntry !== null): ?>
                    <article class="journal-entry">
                        <section class="journal-entry-header" aria-labelledby="journal-entry-title">
                            <p class="journal-entry-meta">
                                <a class="journal-back-link" href="/journal">← Journal</a>
                                <time datetime="<?= htmlspecialchars($currentEntry['date']->format('Y-m-d'), ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($currentEntry['date']->format('j M Y'), ENT_QUOTES) ?>
                                </time>
                            </p>
                            <h1 id="journal-entry-title" class="title"><?= htmlspecialchars($currentEntry['title'], ENT_QUOTES) ?></h1>
                        </section>
                        <div class="journal-entry-body">
                            <?= renderJournalMarkdown($currentEntry['content']); ?>
                        </div>
                    </article>
                <?php endif; ?>

                <div class="journal-connect">
                    <?php include __DIR__ . '/includes/about.php'; ?>
                </div>
            </div>
        </main>
        <footer>
            <?php include __DIR__ . '/includes/colophon.php'; ?>
        </footer>
    </div>
</body>
</html>


