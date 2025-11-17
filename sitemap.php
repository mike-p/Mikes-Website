<?php
declare(strict_types=1);

require __DIR__ . '/journal-content/functions.php';
require __DIR__ . '/templates-content/functions.php';

$baseUrl = sitemapBaseUrl();

$postsDirectory = __DIR__ . '/journal-content/posts';
$entries = loadJournalEntries($postsDirectory, includeFuture: true);

$templatesDirectory = __DIR__ . '/templates-content/templates';
$templates = loadTemplates($templatesDirectory);

header('Content-Type: application/xml; charset=UTF-8');
echo buildSitemapXml($baseUrl, $entries, $templates);

