<?php
// Development router for PHP built-in server: maps extensionless paths to matching .php files

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$trimmed = trim($requestUri, '/');
$segments = $trimmed === '' ? [] : explode('/', $trimmed);
$uri = '/' . implode('/', $segments);
if ($uri === '/') {
    $uri = '/index';
}

$docRoot = __DIR__;
$filePath = realpath($docRoot . $uri);

// Serve static files if they exist (css, js, images, etc.)
if ($filePath && is_file($filePath)) {
    return false; // let the built-in server handle it
}

// Serve dynamic sitemap
if ($uri === '/sitemap.xml') {
    require $docRoot . '/sitemap.php';
    exit;
}

// Handle journal routes: /journal and /journal/{slug}
if (!empty($segments) && $segments[0] === 'journal') {
    $journalEntry = $docRoot . '/journal.php';
    if (is_file($journalEntry)) {
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
        }
        require $journalEntry;
        exit;
    }
}

// Handle template routes: /template and /template/{slug}
if (!empty($segments) && $segments[0] === 'template') {
    $templateEntry = $docRoot . '/template.php';
    if (is_file($templateEntry)) {
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
        }
        require $templateEntry;
        exit;
    }
}

// Try matching a .php file for extensionless route
$candidate = $docRoot . $uri . '.php';
if (is_file($candidate)) {
    require $candidate;
    exit;
}

// Also try directory index.php (e.g., /blog -> /blog/index.php)
$dirIndex = $docRoot . $uri . '/index.php';
if (is_file($dirIndex)) {
    require $dirIndex;
    exit;
}

// Fallback to 404 page
http_response_code(404);
require $docRoot . '/404.php';
exit;
 
