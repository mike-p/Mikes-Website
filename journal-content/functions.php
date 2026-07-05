<?php

/**
 * Load and parse journal entries stored as Markdown files with optional front matter.
 *
 * @param string $directory Absolute path to the posts directory.
 * @param bool   $includeFuture Include entries with a date in the future.
 * @return array<int, array<string, mixed>> Sorted entries (newest first).
 */
function loadJournalEntries(string $directory, bool $includeFuture = false): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.md');
    $entries = [];

    foreach ($files as $file) {
        $entry = parseJournalEntry($file);
        if ($entry !== null) {
            if (!$includeFuture && ($entry['is_future'] ?? false)) {
                continue;
            }
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
    $timezone = journalTimezone();

    try {
        $date = new DateTimeImmutable($dateString, $timezone);
    } catch (Exception $e) {
        $date = (new DateTimeImmutable('@' . (filemtime($file) ?: time())))->setTimezone($timezone);
    }

    $normalizedDate = $date->setTime(0, 0);
    $today = new DateTimeImmutable('today', $timezone);
    $isFuture = $normalizedDate > $today;

    $charterFlag = strtolower($frontMatter['charter'] ?? '');

    return [
        'slug' => $slug,
        'title' => $title,
        'summary' => $frontMatter['summary'] ?? '',
        'date' => $normalizedDate,
        'is_future' => $isFuture,
        'content' => $content,
        'has_charter' => in_array($charterFlag, ['true', 'yes', '1'], true),
    ];
}

/**
 * Split journal content into intro and charter sections (first --- divider).
 *
 * @return array{intro: string, charter: string}
 */
function splitJournalIntroAndCharter(string $content): array
{
    $parts = preg_split('/\n---\n/', trim($content), 2);

    if (count($parts) < 2) {
        return ['intro' => trim($content), 'charter' => ''];
    }

    return [
        'intro' => trim($parts[0]),
        'charter' => trim($parts[1]),
    ];
}

/**
 * Pull footnote definitions out of markdown before rendering.
 *
 * Syntax: [^id]: Footnote text with [links](url)
 *
 * @return array{content: string, footnotes: array<string, array{content: string, number: int|null}>}
 */
function extractJournalFootnotes(string $markdown): array
{
    $footnotes = [];

    $content = preg_replace_callback(
        '/^\[\^([^\]]+)\]:\s*(.+)$/m',
        static function (array $matches) use (&$footnotes): string {
            $footnotes[$matches[1]] = [
                'content' => trim($matches[2]),
                'number' => null,
            ];

            return '';
        },
        $markdown
    );

    return [
        'content' => trim(preg_replace("/\n{3,}/", "\n\n", $content ?? '')),
        'footnotes' => $footnotes,
    ];
}

/**
 * Render collected footnotes as an ordered list.
 *
 * @param array<string, array{content: string, number: int|null}> $footnotes
 */
function renderJournalFootnotesHtml(array $footnotes): string
{
    $used = array_filter(
        $footnotes,
        static fn (array $footnote): bool => $footnote['number'] !== null
    );

    if ($used === []) {
        return '';
    }

    uasort($used, static fn (array $a, array $b): int => $a['number'] <=> $b['number']);

    $html = '<aside class="journal-footnotes" aria-label="Footnotes"><ol>';

    foreach ($used as $id => $footnote) {
        $noNestedFootnotes = [];
        $content = formatInlineMarkdown($footnote['content'], $noNestedFootnotes);
        $safeId = htmlspecialchars($id, ENT_QUOTES);

        $html .= sprintf(
            '<li id="fn-%1$s" value="%2$d"><a class="journal-footnote-back" href="#fnref-%1$s" aria-label="Back to reference">↩</a> %3$s</li>',
            $safeId,
            $footnote['number'],
            $content
        );
    }

    $html .= '</ol></aside>';

    return $html;
}

/**
 * Convert Markdown into basic HTML with lightweight formatting support.
 *
 * @param string $markdown
 * @return string
 */
function renderJournalMarkdown(string $markdown): string
{
    $extracted = extractJournalFootnotes(trim($markdown));
    $markdown = $extracted['content'];
    $footnotes = $extracted['footnotes'];

    if ($markdown === '') {
        return $footnotes === [] ? '' : renderJournalFootnotesHtml($footnotes);
    }

    $lines = preg_split("/(\r\n|\n|\r)/", $markdown);
    $html = '';
    $paragraphLines = [];
    $inList = false; // false, 'ul', or 'ol'
    $inTable = false;
    $tableRows = [];
    $tableHeader = null;
    $tableSeparator = null;

    $flushParagraph = function () use (&$paragraphLines, &$html, &$footnotes) {
        if (empty($paragraphLines)) {
            return;
        }

        $text = implode(' ', $paragraphLines);
        $html .= '<p>' . formatInlineMarkdown($text, $footnotes) . '</p>';
        $paragraphLines = [];
    };

    $closeList = function () use (&$inList, &$html) {
        if ($inList === 'ul') {
            $html .= '</ul>';
            $inList = false;
        } elseif ($inList === 'ol') {
            $html .= '</ol>';
            $inList = false;
        }
    };

    $flushTable = function () use (&$inTable, &$tableRows, &$tableHeader, &$tableSeparator, &$html, &$footnotes) {
        if (!$inTable || $tableHeader === null) {
            return;
        }

        $html .= '<table>';
        $html .= '<thead><tr>';
        foreach ($tableHeader as $cell) {
            $html .= '<th>' . formatInlineMarkdown(trim($cell), $footnotes) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        foreach ($tableRows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . formatInlineMarkdown(trim($cell), $footnotes) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $inTable = false;
        $tableRows = [];
        $tableHeader = null;
        $tableSeparator = null;
    };

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Check if this is a table row (starts with | and has multiple |)
        $isTableRow = preg_match('/^\|.+\|$/', $trimmed) && substr_count($trimmed, '|') >= 2;

        if ($isTableRow) {
            // Parse table row
            $cells = array_map('trim', explode('|', $trimmed));
            // Remove empty first/last elements from split
            $cells = array_filter($cells, fn($cell) => $cell !== '');
            $cells = array_values($cells);

            // Check if this is a separator row (contains only dashes, colons, spaces, and pipes)
            $isSeparator = preg_match('/^[\|\s:\-]+$/', $trimmed);

            if ($isSeparator) {
                // This is the separator row, header should already be set
                if ($tableHeader !== null) {
                    $tableSeparator = $cells;
                    $inTable = true;
                }
            } elseif ($tableHeader === null) {
                // This is the header row
                $tableHeader = $cells;
                $inTable = true;
            } else {
                // This is a data row
                $tableRows[] = $cells;
            }
            continue;
        } else {
            // Not a table row, flush any open table
            $flushTable();
        }

        if ($trimmed === '') {
            $flushParagraph();
            $closeList();
            continue;
        }

        // Check for horizontal rule (---, ***, or ___ with at least 3 characters)
        if (preg_match('/^([-*_])(\1{2,})$/', $trimmed, $matches)) {
            $flushParagraph();
            $closeList();
            $html .= '<hr>';
            continue;
        }

        if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $matches)) {
            $flushParagraph();
            $closeList();
            $level = strlen($matches[1]);
            $text = formatInlineMarkdown($matches[2], $footnotes);
            $html .= sprintf('<h%d>%s</h%d>', $level, $text, $level);
            continue;
        }

        // Check for numbered list (1. 2. 3. etc.)
        if (preg_match('/^\d+\.\s+(.*)$/', $trimmed, $matches)) {
            $flushParagraph();
            if ($inList !== 'ol') {
                $closeList();
                $html .= '<ol>';
                $inList = 'ol';
            }
            $html .= '<li>' . formatInlineMarkdown($matches[1], $footnotes) . '</li>';
            continue;
        }

        // Check for unordered list (-, *, +)
        if (preg_match('/^[-*+]\s+(.*)$/', $trimmed, $matches)) {
            $flushParagraph();
            if ($inList !== 'ul') {
                $closeList();
                $html .= '<ul>';
                $inList = 'ul';
            }
            $html .= '<li>' . formatInlineMarkdown($matches[1], $footnotes) . '</li>';
            continue;
        }

        // Check for blockquote
        if (preg_match('/^>\s+(.*)$/', $trimmed, $matches)) {
            $flushParagraph();
            $closeList();
            $html .= '<blockquote>' . formatInlineMarkdown($matches[1], $footnotes) . '</blockquote>';
            continue;
        }

        $paragraphLines[] = $trimmed;
    }

    $flushParagraph();
    $closeList();
    $flushTable();

    $html .= renderJournalFootnotesHtml($footnotes);

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
 * @param array<string, array{content: string, number: int|null}> $footnotes
 */
function formatInlineMarkdown(string $text, array &$footnotes = []): string
{
    $codePlaceholders = [];
    $linkPlaceholders = [];
    $footnoteRefPlaceholders = [];
    $placeholderIndex = 0;

    $text = preg_replace_callback(
        '/`(.+?)`/s',
        function ($matches) use (&$codePlaceholders, &$placeholderIndex) {
            $placeholder = '<!--CODE' . $placeholderIndex . '-->';
            $codePlaceholders[$placeholder] = '<code>' . htmlspecialchars($matches[1], ENT_QUOTES) . '</code>';
            $placeholderIndex++;
            return $placeholder;
        },
        $text
    );

    // Links before escape so placeholders survive htmlspecialchars (same as code blocks)
    $text = preg_replace_callback(
        '/\[(.+?)\]\(((?:https?:\/\/|\/)[^\s)]+)\)/',
        static function ($matches) use (&$linkPlaceholders, &$placeholderIndex) {
            $url = $matches[2];
            $label = $matches[1];
            $escapedUrl = htmlspecialchars($url, ENT_QUOTES);
            $escapedLabel = htmlspecialchars($label, ENT_QUOTES);
            $isExternal = preg_match('/^https?:\/\//i', $url)
                && !preg_match('/^https?:\/\/(www\.)?mike-p\.co\.uk(\/|$)/i', $url);

            if ($isExternal) {
                $html = sprintf(
                    '<a href="%s" target="_blank" rel="noopener noreferrer" class="external-link">%s</a>',
                    $escapedUrl,
                    $escapedLabel
                );
            } else {
                $html = sprintf('<a href="%s">%s</a>', $escapedUrl, $escapedLabel);
            }

            $placeholder = '<!--LINK' . $placeholderIndex . '-->';
            $linkPlaceholders[$placeholder] = $html;
            $placeholderIndex++;

            return $placeholder;
        },
        $text
    );

    if ($footnotes !== []) {
        $text = preg_replace_callback(
            '/\[\^([^\]]+)\]/',
            static function (array $matches) use (&$footnotes, &$footnoteRefPlaceholders, &$placeholderIndex): string {
                $id = $matches[1];

                if (!isset($footnotes[$id])) {
                    return $matches[0];
                }

                if ($footnotes[$id]['number'] === null) {
                    $assigned = array_filter(
                        $footnotes,
                        static fn (array $footnote): bool => $footnote['number'] !== null
                    );
                    $footnotes[$id]['number'] = count($assigned) + 1;
                }

                $number = $footnotes[$id]['number'];
                $safeId = htmlspecialchars($id, ENT_QUOTES);
                $placeholder = '<!--FNREF' . $placeholderIndex . '-->';
                $footnoteRefPlaceholders[$placeholder] = sprintf(
                    '<sup class="journal-footnote-ref"><a href="#fn-%1$s" id="fnref-%1$s" aria-label="Footnote %2$d">%2$d</a></sup>',
                    $safeId,
                    $number
                );
                $placeholderIndex++;

                return $placeholder;
            },
            $text
        );
    }

    $escaped = htmlspecialchars($text, ENT_QUOTES);

    // Bold **text** or __text__
    $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
    $escaped = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $escaped);

    // Italic *text* or _text_
    $escaped = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $escaped);
    $escaped = preg_replace('/(?<!_)_(?!_)(.+?)(?<!_)_(?!_)/s', '<em>$1</em>', $escaped);

    foreach ($linkPlaceholders as $placeholder => $linkHtml) {
        $escapedPlaceholder = htmlspecialchars($placeholder, ENT_QUOTES);
        $escaped = str_replace($escapedPlaceholder, $linkHtml, $escaped);
    }

    foreach ($footnoteRefPlaceholders as $placeholder => $footnoteRefHtml) {
        $escapedPlaceholder = htmlspecialchars($placeholder, ENT_QUOTES);
        $escaped = str_replace($escapedPlaceholder, $footnoteRefHtml, $escaped);
    }

    foreach ($codePlaceholders as $placeholder => $codeHtml) {
        $escapedPlaceholder = htmlspecialchars($placeholder, ENT_QUOTES);
        $escaped = str_replace($escapedPlaceholder, $codeHtml, $escaped);
    }

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
 * Estimated reading time in minutes from entry body (markdown source).
 *
 * @param array<string, mixed> $entry
 */
function journalReadingMinutes(array $entry): int
{
    $text = trim((string) ($entry['content'] ?? ''));
    if ($text === '') {
        return 1;
    }

    $words = preg_split('/\s+/u', strip_tags($text), -1, PREG_SPLIT_NO_EMPTY);
    $count = is_array($words) ? count($words) : 0;
    $minutes = (int) ceil($count / 200);

    return max(1, $minutes);
}

/**
 * Fingerprint for cache busting on journal-aware pages.
 * Changes when a scheduled post goes live or published content is edited.
 */
function journalCacheFingerprint(string $directory, bool $includeFuture = false): string
{
    $entries = loadJournalEntries($directory, $includeFuture);
    $parts = [];

    foreach ($entries as $entry) {
        $file = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $entry['slug'] . '.md';
        $mtime = is_file($file) ? (int) filemtime($file) : 0;
        $parts[] = $entry['slug'] . ':' . $entry['date']->format('Y-m-d') . ':' . $mtime;
    }

    return implode('|', $parts);
}

/**
 * Return the timezone used for journal scheduling.
 */
function journalTimezone(): DateTimeZone
{
    static $tz = null;
    if ($tz === null) {
        $tz = new DateTimeZone('Europe/London');
    }

    return $tz;
}

/**
 * True on production hosts — drafts must never appear here.
 */
function journalIsProductionHost(string $host): bool
{
    $host = preg_replace('/:\d+$/', '', strtolower($host)) ?? strtolower($host);

    return in_array($host, ['mike-p.co.uk', 'www.mike-p.co.uk'], true);
}

/**
 * Optional override: JOURNAL_PREVIEW=1 in .env.local (repo root).
 */
function journalPreviewEnabledInEnv(): bool
{
    static $checked = false;
    static $enabled = false;

    if ($checked) {
        return $enabled;
    }

    $checked = true;
    $envFile = dirname(__DIR__) . '/.env.local';

    if (!is_file($envFile)) {
        return false;
    }

    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (strtoupper($key) === 'JOURNAL_PREVIEW' && in_array(strtolower($value), ['1', 'true', 'yes'], true)) {
            $enabled = true;
            break;
        }
    }

    return $enabled;
}

/**
 * True when running locally (not production). Works with any host/port when
 * using the PHP built-in server, e.g. php -S localhost:8000 router.php
 */
function journalIsLocalRequest(): bool
{
    if (php_sapi_name() === 'cli') {
        return false;
    }

    if (journalPreviewEnabledInEnv()) {
        return true;
    }

    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    $hostWithoutPort = preg_replace('/:\d+$/', '', $host) ?? $host;

    if (journalIsProductionHost($hostWithoutPort)) {
        return false;
    }

    // Any address when using `php -S … router.php` (see README)
    if (php_sapi_name() === 'cli-server') {
        return true;
    }

    return $hostWithoutPort === 'localhost'
        || str_starts_with($hostWithoutPort, '127.')
        || $hostWithoutPort === '[::1]'
        || (bool) preg_match('/\.(local|test|localhost|dev)$/', $hostWithoutPort);
}

/**
 * Include scheduled (future-dated) posts. Enabled on local dev only.
 */
function journalIncludeScheduledPosts(): bool
{
    return journalIsLocalRequest();
}

/**
 * Draft badge for scheduled posts (local preview only).
 */
function journalDraftBadge(array $entry): string
{
    if (empty($entry['is_future'])) {
        return '';
    }

    $date = $entry['date'] instanceof DateTimeInterface
        ? $entry['date']->format('j M Y')
        : '';

    if ($date === '') {
        return '';
    }

    return '<span class="journal-draft-badge">Draft — publishes ' . htmlspecialchars($date, ENT_QUOTES) . '</span>';
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
 * JSON-LD BlogPosting schema for a journal entry.
 */
function buildJournalArticleJsonLd(array $entry, string $baseUrl): string
{
    $slug = $entry['slug'] ?? '';
    $url = rtrim($baseUrl, '/') . '/journal/' . $slug;
    $postFile = dirname(__DIR__) . '/journal-content/posts/' . $slug . '.md';
    $timezone = journalTimezone();

    $datePublished = '';
    if ($entry['date'] instanceof DateTimeInterface) {
        $datePublished = DateTimeImmutable::createFromInterface($entry['date'])
            ->setTimezone($timezone)
            ->format(DateTimeInterface::ATOM);
    }

    $dateModified = $datePublished;
    if (is_file($postFile)) {
        $dateModified = (new DateTimeImmutable('@' . (int) filemtime($postFile)))
            ->setTimezone($timezone)
            ->format(DateTimeInterface::ATOM);
    }

    $description = trim((string) ($entry['summary'] ?? ''));
    if ($description === '') {
        $description = journalExcerpt($entry);
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $entry['title'] ?? '',
        'description' => $description,
        'datePublished' => $datePublished,
        'dateModified' => $dateModified,
        'url' => $url,
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $url,
        ],
        'author' => [
            '@type' => 'Person',
            'name' => 'Mike Smith',
            'url' => 'https://mike-p.co.uk',
        ],
        'publisher' => [
            '@type' => 'Person',
            'name' => 'Mike Smith',
        ],
        'image' => rtrim($baseUrl, '/') . '/i/logo.png',
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

/**
 * Y-m-d lastmod from the newest filemtime in a list of paths.
 *
 * @param list<string> $paths
 */
function sitemapLastModFromFiles(array $paths): string
{
    $latest = 0;
    foreach ($paths as $path) {
        if (is_file($path)) {
            $latest = max($latest, (int) filemtime($path));
        }
    }

    return $latest > 0 ? date('Y-m-d', $latest) : '';
}

/**
 * Latest Y-m-d from one or more date strings.
 *
 * @param list<string> $dates
 */
function sitemapMaxLastModDate(array $dates): string
{
    $latest = 0;
    foreach ($dates as $date) {
        if ($date === '') {
            continue;
        }
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            $latest = max($latest, $timestamp);
        }
    }

    return $latest > 0 ? date('Y-m-d', $latest) : '';
}

/**
 * Newest publish or file change among journal posts (for index / journal list pages).
 *
 * @param array<int, array<string, mixed>> $entries
 */
function sitemapLatestJournalLastMod(string $postsDirectory, array $entries): string
{
    $latest = 0;

    foreach ($entries as $entry) {
        if ($entry['is_future'] ?? false) {
            continue;
        }

        $postFile = rtrim($postsDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($entry['slug'] ?? '') . '.md';
        if (is_file($postFile)) {
            $latest = max($latest, (int) filemtime($postFile));
        }

        if (($entry['date'] ?? null) instanceof DateTimeInterface) {
            $latest = max($latest, $entry['date']->getTimestamp());
        }
    }

    return $latest > 0 ? date('Y-m-d', $latest) : '';
}

/**
 * Build the XML sitemap contents.
 *
 * @param string $baseUrl
 * @param array<int, array<string, mixed>> $entries
 * @param array<int, array<string, mixed>> $templates
 * @return string
 */
function buildSitemapXml(string $baseUrl, array $entries, array $templates = []): string
{
    $repoRoot = dirname(__DIR__);
    $postsDirectory = $repoRoot . '/journal-content/posts';
    $journalFeedLastMod = sitemapLatestJournalLastMod($postsDirectory, $entries);

    $staticUrls = [
        [
            'loc' => $baseUrl,
            'lastmod' => sitemapMaxLastModDate([
                sitemapLastModFromFiles([$repoRoot . '/index.php']),
                $journalFeedLastMod,
            ]),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
        [
            'loc' => $baseUrl . '/hire-me',
            'lastmod' => sitemapLastModFromFiles([$repoRoot . '/hire-me.php']),
            'changefreq' => 'monthly',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/product-strategy',
            'lastmod' => sitemapLastModFromFiles([$repoRoot . '/product-strategy.php']),
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/product-team-AI-vibe-coding',
            'lastmod' => sitemapLastModFromFiles([$repoRoot . '/product-team-AI-vibe-coding.php']),
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/work',
            'lastmod' => sitemapLastModFromFiles([$repoRoot . '/work.php']),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ],
        [
            'loc' => $baseUrl . '/journal',
            'lastmod' => sitemapMaxLastModDate([
                sitemapLastModFromFiles([$repoRoot . '/journal.php']),
                $journalFeedLastMod,
            ]),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ],
        [
            'loc' => $baseUrl . '/template',
            'lastmod' => sitemapLastModFromFiles([$repoRoot . '/template.php']),
            'changefreq' => 'monthly',
            'priority' => '0.8',
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
        if (!empty($url['lastmod'])) {
            $lines[] = '  <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1) . '</lastmod>';
        }
        $lines[] = '  <changefreq>' . htmlspecialchars($url['changefreq'], ENT_XML1) . '</changefreq>';
        $lines[] = '  <priority>' . htmlspecialchars($url['priority'], ENT_XML1) . '</priority>';
        $lines[] = '</url>';
    }

    if (!empty($entries)) {
        $lines[] = '';
    }

    foreach ($entries as $entry) {
        if (($entry['is_future'] ?? false)) {
            continue;
        }

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

    // Add templates to sitemap
    if (!empty($templates)) {
        $lines[] = '';
    }

    foreach ($templates as $template) {
        $loc = $baseUrl . '/template/' . $template['slug'];
        $templateFile = $template['file'] ?? '';
        $lastMod = ($templateFile !== '' && file_exists($templateFile) && filemtime($templateFile))
            ? date('Y-m-d', filemtime($templateFile))
            : '';

        $lines[] = '<url>';
        $lines[] = '  <loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>';
        if ($lastMod !== '') {
            $lines[] = '  <lastmod>' . htmlspecialchars($lastMod, ENT_XML1) . '</lastmod>';
        }
        $lines[] = '  <changefreq>monthly</changefreq>';
        $lines[] = '  <priority>0.7</priority>';
        $lines[] = '</url>';
    }

    $lines[] = '';
    $lines[] = '</urlset>';
    $lines[] = '';

    return implode(PHP_EOL, $lines);
}


