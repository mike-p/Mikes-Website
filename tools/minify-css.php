<?php
// Simple CSS minifier script to generate css/layout.min.css from css/layout.css

$root = dirname(__DIR__);
$source = $root . '/css/layout.css';
$target = $root . '/css/layout.min.css';

if (!file_exists($source)) {
    fwrite(STDERR, "Source CSS not found: {$source}\n");
    exit(1);
}

$css = file_get_contents($source);
if ($css === false) {
    fwrite(STDERR, "Failed to read: {$source}\n");
    exit(1);
}

// Remove comments
$min = preg_replace('!\/\*.*?\*\!/s', '', $css);
// Collapse whitespace
$min = preg_replace('/\s+/', ' ', $min);
// Trim spaces around CSS punctuation
$min = preg_replace('/\s*([{};:>,])\s*/', '$1', $min);
// Remove last semicolon before closing brace
$min = preg_replace('/;\}/', '}', $min);
// Final trim
$min = trim($min);

$ok = file_put_contents($target, $min);
if ($ok === false) {
    fwrite(STDERR, "Failed to write: {$target}\n");
    exit(1);
}

fwrite(STDOUT, "Minified CSS written to: {$target}\n");
exit(0);
 