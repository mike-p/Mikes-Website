<?php

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
    'description' => 'Product strategy and execution templates for teams.',
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
    <style>
        /* Notion-like template styles */
        .template-viewer {
            min-height: 100vh;
            background: #fff;
        }

        .template-header {
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e6e6e6;
            padding: 0.75rem 0;
            z-index: 100;
        }

        .template-header-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .template-back-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #37352f;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }

        .template-back-link:hover {
            opacity: 0.7;
        }

        .template-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .template-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .template-download-btn:hover {
            background: #333;
        }

        .template-content-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .template-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #37352f;
            margin: 0 0 0.5rem;
            line-height: 1.2;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, "Apple Color Emoji", Arial, sans-serif;
        }

        .template-description {
            font-size: 1.1rem;
            color: #787774;
            margin: 0 0 2rem;
            line-height: 1.5;
        }

        .template-body {
            font-size: 1rem;
            line-height: 1.6;
            color: #37352f;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, "Apple Color Emoji", Arial, sans-serif;
        }

        .template-body h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #37352f;
            margin: 2rem 0 0.75rem;
            line-height: 1.2;
        }

        .template-body h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #37352f;
            margin: 1.75rem 0 0.5rem;
            line-height: 1.3;
        }

        .template-body h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #37352f;
            margin: 1.5rem 0 0.5rem;
            line-height: 1.4;
        }

        .template-body p {
            margin: 0.75rem 0;
            color: #37352f;
        }

        .template-body ul,
        .template-body ol {
            margin: 0.75rem 0;
            padding-left: 1.5rem;
        }

        .template-body li {
            margin: 0.25rem 0;
            color: #37352f;
        }

        .template-body code {
            background: #f7f6f3;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.9em;
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
            color: #eb5757;
        }

        .template-body strong {
            font-weight: 600;
            color: #37352f;
        }

        .template-body em {
            font-style: italic;
            color: #37352f;
        }

        .template-body a {
            color: #37352f;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .template-body a.external-link {
            display: inline-flex;
            align-items: baseline;
            gap: 0.25em;
        }

        .template-body a.external-link::after {
            content: '';
            width: 0.75em;
            height: 0.75em;
            min-width: 0.75em;
            flex-shrink: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.6;
            display: inline-block;
            vertical-align: baseline;
        }

        .template-body a:hover {
            opacity: 0.7;
        }

        .template-body blockquote {
            border-left: 3px solid #e6e6e6;
            padding-left: 1rem;
            margin: 1rem 0;
            color: #787774;
            font-style: italic;
        }

        .template-body hr {
            border: none;
            border-top: 1px solid #e6e6e6;
            margin: 2rem 0;
            background: none;
        }

        .template-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            font-size: 0.95rem;
        }

        .template-body table thead {
            background: #f7f6f3;
        }

        .template-body table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #37352f;
            border-bottom: 2px solid #e6e6e6;
        }

        .template-body table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f1ef;
            color: #37352f;
        }

        .template-body table tbody tr:hover {
            background: #fafafa;
        }

        .template-body table tbody tr:last-child td {
            border-bottom: none;
        }

        .template-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .template-list-item {
            padding: 1.5rem;
            background: #f7f6f3;
            border-radius: 3px;
            margin-bottom: 1rem;
            transition: background 0.2s ease;
        }

        .template-list-item:hover {
            background: #f1f1ef;
        }

        .template-list-item h3 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        .template-list-item p {
            margin: 0.5rem 0 0;
            color: #787774;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .template-header-content {
                padding: 0 1rem;
            }

            .template-content-wrapper {
                padding: 2rem 1rem;
            }

            .template-title {
                font-size: 2rem;
            }

            .template-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .template-download-btn {
                width: 100%;
                justify-content: center;
            }

            .template-body table {
                font-size: 0.85rem;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .template-body table th,
            .template-body table td {
                padding: 0.5rem 0.75rem;
            }
        }
    </style>
</head>
<body>
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
                                    <a href="/template/<?= htmlspecialchars($template['slug'], ENT_QUOTES) ?>" style="color: #37352f; text-decoration: none;">
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
</body>
</html>

