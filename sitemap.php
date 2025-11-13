<?php
declare(strict_types=1);

require __DIR__ . '/journal-content/functions.php';

$baseUrl = sitemapBaseUrl();

$postsDirectory = __DIR__ . '/journal-content/posts';
$entries = loadJournalEntries($postsDirectory);

header('Content-Type: application/xml; charset=UTF-8');
echo buildSitemapXml($baseUrl, $entries);

