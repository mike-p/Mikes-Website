<?php
/**
 * Set proper HTTP headers for all pages
 * This should be included at the very top of pages, before any output
 */

// Set Content-Type header
header('Content-Type: text/html; charset=utf-8');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Determine if this is a 404 page
$is404 = (http_response_code() === 404 || 
          (isset($_SERVER['REQUEST_URI']) && 
           (strpos($_SERVER['REQUEST_URI'], '/404') !== false || 
            basename($_SERVER['PHP_SELF']) === '404.php')));

// Set caching headers based on page type
if ($is404) {
    // 404 pages should not be cached
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
} else {
    // Regular pages: allow caching but with revalidation
    // Public pages can be cached by CDNs and browsers, but should revalidate
    header('Cache-Control: public, max-age=3600, must-revalidate');
    header('Vary: Accept-Encoding');
}

// Set ETag for better caching (optional, but helps with conditional requests)
if (!$is404 && isset($_SERVER['REQUEST_URI'])) {
    $etag = md5($_SERVER['REQUEST_URI'] . filemtime(__FILE__));
    header('ETag: "' . $etag . '"');
    
    // Handle If-None-Match requests (304 Not Modified)
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
        trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === $etag) {
        http_response_code(304);
        exit;
    }
}

