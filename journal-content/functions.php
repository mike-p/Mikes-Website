<?php

/**
 * Load and parse journal entries stored as Markdown files with optional front matter.
 *
 * @param string $directory Absolute path to the posts directory.
 * @return array<int, array<string, mixed>> Sorted entries (newest first).
 */
function loadJournalEntries(string $directory): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.md');
    $entries = [];

    foreach ($files as $file) {
        $entry = parseJournalEntry($file);
        if ($entry !== null) {
            $entries[] = $entry;
        }
    }

    usort($entries, static fn ($a, $b) => $b['date']->getTimestamp() <=> $a['date']->getTimestamp());

    return $entries;
}

/**
 * Parse an individual Markdown file into structured data.
 *
 * Supported front matter fields:
 * - title (string)
 * - date  (YYYY-MM-DD)
 * - summary (string)
 *
 * @param string $file Absolute path to the Markdown file.
 * @return array<string, mixed>|null
 */
function parseJournalEntry(string $file): ?array
{
    $slug = basename($file, '.md');
    $raw = trim((string) file_get_contents($file));

    if ($raw === '') {
        return null;
    }

    $frontMatter = [];
    $content = $raw;

    if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $raw, $matches)) {
        $frontMatter = parseFrontMatter($matches[1]);
        $content = trim($matches[2]);
    }

    $title = $frontMatter['title'] ?? generateTitleFromSlug($slug);
    $dateString = $frontMatter['date'] ?? date('Y-m-d', filemtime($file) ?: time());

    try {
        $date = new DateTimeImmutable($dateString);
    } catch (Exception $e) {
        $date = new DateTimeImmutable('@' . (filemtime($file) ?: time()));
    }

    return [
        'slug' => $slug,
        'title' => $title,
        'summary' => $frontMatter['summary'] ?? '',
        'date' => $date,
        'content' => $content,
    ];
}

/**
 * Convert Markdown into basic HTML with lightweight formatting support.
 *
 * @param string $markdown
 * @return string
 */
function renderJournalMarkdown(string $markdown): string
{
    $markdown = trim($markdown);

    if ($markdown === '') {
        return '';
    }

    $lines = preg_split("/(\r\n|\n|\r)/", $markdown);
    $html = '';
    $paragraphLines = [];
    $inList = false;

    $flushParagraph = function () use (&$paragraphLines, &$html) {
        if (empty($paragraphLines)) {
            return;
        }

        $text = implode(' ', $paragraphLines);
        $html .= '<p>' . formatInlineMarkdown($text) . '</p>';
        $paragraphLines = [];
    };

    $closeList = function () use (&$inList, &$html) {
        if ($inList) {
            $html .= '</ul>';
            $inList = false;
        }
    };

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '') {
            $flushParagraph();
            $closeList();
            continue;
        }

        if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $matches)) {
            $flushParagraph();
            $closeList();
            $level = strlen($matches[1]);
            $text = formatInlineMarkdown($matches[2]);
            $html .= sprintf('<h%d>%s</h%d>', $level, $text, $level);
            continue;
        }

        if (preg_match('/^[-*+]\s+(.*)$/', $trimmed, $matches)) {
            $flushParagraph();
            if (!$inList) {
                $html .= '<ul>';
                $inList = true;
            }
            $html .= '<li>' . formatInlineMarkdown($matches[1]) . '</li>';
            continue;
        }

        $paragraphLines[] = $trimmed;
    }

    $flushParagraph();
    $closeList();

    return $html;
}

/**
 * Parse simple key/value front matter blocks.
 *
 * @param string $frontMatter
 * @return array<string, string>
 */
function parseFrontMatter(string $frontMatter): array
{
    $data = [];
    $lines = preg_split("/(\r\n|\n|\r)/", trim($frontMatter));

    foreach ($lines as $line) {
        if (strpos($line, ':') === false) {
            continue;
        }

        [$key, $value] = array_map('trim', explode(':', $line, 2));
        if ($key !== '') {
            $data[strtolower($key)] = $value;
        }
    }

    return $data;
}

/**
 * Basic inline Markdown formatter for emphasis and links.
 *
 * @param string $text
 * @return string
 */
function formatInlineMarkdown(string $text): string
{
    $escaped = htmlspecialchars($text, ENT_QUOTES);

    // Links [label](url)
    $escaped = preg_replace_callback(
        '/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/',
        static fn ($matches) => sprintf(
            '<a href="%s">%s</a>',
            htmlspecialchars($matches[2], ENT_QUOTES),
            htmlspecialchars($matches[1], ENT_QUOTES)
        ),
        $escaped
    );

    // Bold **text** or __text__
    $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
    $escaped = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $escaped);

    // Italic *text* or _text_
    $escaped = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $escaped);
    $escaped = preg_replace('/(?<!_)_(?!_)(.+?)(?<!_)_(?!_)/s', '<em>$1</em>', $escaped);

    // Inline code `code`
    $escaped = preg_replace_callback(
        '/`(.+?)`/s',
        static fn ($matches) => '<code>' . htmlspecialchars($matches[1], ENT_QUOTES) . '</code>',
        $escaped
    );

    return $escaped;
}

/**
 * Generate a readable title from a slug if none is supplied.
 *
 * @param string $slug
 * @return string
 */
function generateTitleFromSlug(string $slug): string
{
    $title = str_replace(['-', '_'], ' ', $slug);
    $title = preg_replace('/\s+/', ' ', $title ?? '');

    return ucwords(trim($title));
}

/**
 * Create a short excerpt for a journal entry.
 *
 * @param array<string, mixed> $entry
 * @param int $length
 * @return string
 */
function journalExcerpt(array $entry, int $length = 160): string
{
    $source = $entry['summary'] ?? '';

    if ($source === '') {
        $html = renderJournalMarkdown($entry['content'] ?? '');
        $source = strip_tags($html);
    }

    $source = preg_replace('/\s+/', ' ', trim($source ?? ''));

    if (strlen($source) <= $length) {
        return $source;
    }

    $excerpt = substr($source, 0, $length);
    $lastSpace = strrpos($excerpt, ' ');

    if ($lastSpace !== false) {
        $excerpt = substr($excerpt, 0, $lastSpace);
    }

    return rtrim($excerpt, '.,;:!') . '…';
}

/**
 * Determine the fully-qualified base URL for sitemap generation.
 */
function sitemapBaseUrl(): string
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return 'https://mike-p.co.uk';
    }

    $host = $_SERVER['HTTP_HOST'];
    $lowerHost = strtolower($host);
    $isLocalHost = str_contains($lowerHost, 'localhost') || str_starts_with($lowerHost, '127.');
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? null) === 443;

    $scheme = $isSecure || !$isLocalHost ? 'https' : 'http';

    return $scheme . '://' . $host;
}


/**
 * Build the XML sitemap contents.
 *
 * @param string $baseUrl
 * @param array<int, array<string, mixed>> $entries
 * @return string
 */
function buildSitemapXml(string $baseUrl, array $entries): string
{
    $staticUrls = [
        [
            'loc' => $baseUrl,
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
        [
            'loc' => $baseUrl . '/#ai-leadership',
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ],
        [
            'loc' => $baseUrl . '/#industry-expertise',
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ],
        [
            'loc' => $baseUrl . '/#product-approach',
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ],
        [
            'loc' => $baseUrl . '/#connect',
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ],
        [
            'loc' => $baseUrl . '/hire-me',
            'changefreq' => 'monthly',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/product-strategy',
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/product-team-AI-vibe-coding',
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/work',
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ],
        [
            'loc' => $baseUrl . '/journal',
            'changefreq' => 'daily',
            'priority' => '0.9',
        ],
    ];

    $lines = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
        '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
        '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9',
        '            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">',
        '',
    ];

    foreach ($staticUrls as $url) {
        $lines[] = '<url>';
        $lines[] = '  <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>';
        $lines[] = '  <changefreq>' . htmlspecialchars($url['changefreq'], ENT_XML1) . '</changefreq>';
        $lines[] = '  <priority>' . htmlspecialchars($url['priority'], ENT_XML1) . '</priority>';
        $lines[] = '</url>';
    }

    if (!empty($entries)) {
        $lines[] = '';
    }

    foreach ($entries as $entry) {
        $loc = $baseUrl . '/journal/' . $entry['slug'];
        $lastMod = $entry['date'] instanceof DateTimeInterface
            ? $entry['date']->format('Y-m-d')
            : '';

        $lines[] = '<url>';
        $lines[] = '  <loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>';
        if ($lastMod !== '') {
            $lines[] = '  <lastmod>' . htmlspecialchars($lastMod, ENT_XML1) . '</lastmod>';
        }
        $lines[] = '  <changefreq>monthly</changefreq>';
        $lines[] = '  <priority>0.6</priority>';
        $lines[] = '</url>';
    }

    $lines[] = '';
    $lines[] = '</urlset>';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}


