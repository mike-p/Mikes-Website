<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');

echo "sitemap-debug.php reached\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? '(not set)') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? '(not set)') . "\n";
echo "Current directory contents:\n";

$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }
    $path = __DIR__ . DIRECTORY_SEPARATOR . $file;
    $type = is_dir($path) ? '[dir]' : '[file]';
    echo "  {$type} {$file}\n";
}

echo "\n";
echo "Rewrite debug complete.\n";

