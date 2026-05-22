<?php
// Include HTTP headers (must be before any output)
include __DIR__ . '/includes/http-headers.php';

require __DIR__ . '/journal-content/functions.php'; // For markdown rendering and generateTitleFromSlug()
require __DIR__ . '/templates-content/functions.php';

$templatesDirectory = __DIR__ . '/templates-content/templates';
$templates = loadTemplates($templatesDirectory);
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : null;

if (($slug === null || $slug === '') && ($_SERVER['REQUEST_URI'] ?? '') !== '') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
    if (preg_match('#^/template/([^/]+)$#', rtrim($requestPath, '/'), $matches)) {
        $slug = trim($matches[1]);
    }
}

$currentTemplate = null;

if ($slug !== null && $slug !== '') {
    $currentTemplate = getTemplateBySlug($templatesDirectory, $slug);

    if ($currentTemplate === null) {
        http_response_code(404);
    }
} 

// Handle download request
if (isset($_GET['download']) && $currentTemplate !== null) {
    $filename = $currentTemplate['slug'] . '.md';
    $content = file_get_contents($currentTemplate['file']);
    
    header('Content-Type: text/markdown; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($content));
    
    echo $content;
    exit;
}

$pageMeta = [
    'title' => 'Product Templates | Mike Smith',
    'description' => 'Free product management templates and frameworks for product teams. Download PRD templates, product strategy frameworks, 1-pagers, and impact assessments to align teams and execute with clarity.',
    'og_type' => 'website',
];

if ($currentTemplate !== null) {
    $pageMeta['title'] = $currentTemplate['title'] . ' | Templates | Mike Smith';
    $pageMeta['description'] = $currentTemplate['description'] ?: 'Product template by Mike Smith';
    $pageMeta['og_type'] = 'article';
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <?php include __DIR__ . '/includes/header-includes.php'; ?>
</head>
<body class="site-chrome page-template">
<div class="inner-body">
<?php include __DIR__ . '/includes/header.php'; ?>
<main id="main" aria-label="Main content">
<div class="main-content main-content--site template-route">
    <div class="template-viewer">
        <?php if ($currentTemplate === null): ?>
            <!-- Template listing page -->
            <div class="template-header">
                <div class="template-header-content">
                    <a href="/product-strategy" class="template-back-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to site
                    </a>
                </div>
            </div>
            <div class="template-content-wrapper">
                <h1 class="template-title">Product Templates</h1>
                <p class="template-description">Templates and frameworks to help teams align on strategy and execute with clarity.</p>
                
                <?php if (empty($templates)): ?>
                    <p>No templates available yet.</p>
                <?php else: ?>
                    <ul class="template-list">
                        <?php foreach ($templates as $template): ?>
                            <li class="template-list-item">
                                <h3>
                                    <a href="/template/<?= htmlspecialchars($template['slug'], ENT_QUOTES) ?>">
                                        <?= htmlspecialchars($template['title'], ENT_QUOTES) ?>
                                    </a>
                                </h3>
                                <?php if ($template['description']): ?>
                                    <p><?= htmlspecialchars($template['description'], ENT_QUOTES) ?></p>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php elseif ($currentTemplate !== null): ?>
            <!-- Individual template page -->
            <div class="template-header">
                <div class="template-header-content">
                    <a href="/product-strategy" class="template-back-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to site
                    </a>
                    <div class="template-actions">
                        <a href="/template/<?= htmlspecialchars($currentTemplate['slug'], ENT_QUOTES) ?>?download=1" class="template-download-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Markdown
                        </a>
                    </div>
                </div>
            </div>
            <div class="template-content-wrapper">
                <h1 class="template-title"><?= htmlspecialchars($currentTemplate['title'], ENT_QUOTES) ?></h1>
                <?php if ($currentTemplate['description']): ?>
                    <p class="template-description"><?= htmlspecialchars($currentTemplate['description'], ENT_QUOTES) ?></p>
                <?php endif; ?>
                <div class="template-body">
                    <?= renderJournalMarkdown($currentTemplate['content']); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/about.php'; ?>
</main>
<footer>
<?php include __DIR__ . '/includes/colophon.php'; ?>
</footer>
</div>
</body>
</html>

