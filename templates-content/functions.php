<?php

/**
 * Load and parse template files stored as Markdown files with optional front matter.
 *
 * @param string $directory Absolute path to the templates directory.
 * @return array<int, array<string, mixed>> Array of templates.
 */
function loadTemplates(string $directory): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.md');
    $templates = [];

    foreach ($files as $file) {
        $template = parseTemplate($file);
        if ($template !== null) {
            $templates[] = $template;
        }
    }

    // Sort by title
    usort($templates, static fn ($a, $b) => strcmp($a['title'], $b['title']));

    return $templates;
}

/**
 * Parse an individual template Markdown file into structured data.
 *
 * Supported front matter fields:
 * - title (string)
 * - description (string)
 * - slug (string)
 *
 * @param string $file Absolute path to the Markdown file.
 * @return array<string, mixed>|null
 */
function parseTemplate(string $file): ?array
{
    $slug = basename($file, '.md');
    $raw = trim((string) file_get_contents($file));

    if ($raw === '') {
        return null;
    }

    $frontMatter = [];
    $content = $raw;

    if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $raw, $matches)) {
        $frontMatter = parseTemplateFrontMatter($matches[1]);
        $content = trim($matches[2]);
    }

    $title = $frontMatter['title'] ?? generateTitleFromSlug($slug);
    $description = $frontMatter['description'] ?? '';
    $templateSlug = $frontMatter['slug'] ?? $slug;

    return [
        'slug' => $templateSlug,
        'title' => $title,
        'description' => $description,
        'content' => $content,
        'file' => $file,
    ];
}

/**
 * Get a single template by slug.
 *
 * @param string $directory Absolute path to the templates directory.
 * @param string $slug Template slug.
 * @return array<string, mixed>|null
 */
function getTemplateBySlug(string $directory, string $slug): ?array
{
    $templates = loadTemplates($directory);
    
    foreach ($templates as $template) {
        if ($template['slug'] === $slug) {
            return $template;
        }
    }
    
    return null;
}

/**
 * Parse simple key/value front matter blocks for templates.
 *
 * @param string $frontMatter
 * @return array<string, string>
 */
function parseTemplateFrontMatter(string $frontMatter): array
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

