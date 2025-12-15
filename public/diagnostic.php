<?php
// Simple diagnostic page
header('Content-Type: text/plain');

echo "=== HouseKeepr Diagnostic ===\n\n";

echo "✓ PHP is working\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Current Dir: " . getcwd() . "\n\n";

$basePath = dirname(__DIR__);
echo "Base Path: " . $basePath . "\n\n";

echo "=== File Structure Check ===\n";
$files = [
    '.env',
    'index.php',
    '.htaccess',
    'public/index.php',
    'public/.htaccess',
    'bootstrap/app.php',
    'vendor/autoload.php',
];

foreach ($files as $file) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);
    echo ($exists ? "✓" : "✗") . " $file\n";
    if ($exists && in_array($file, ['index.php', '.htaccess'])) {
        echo "  Size: " . filesize($fullPath) . " bytes\n";
    }
}

echo "\n=== Directory Check ===\n";
$dirs = ['public', 'storage', 'bootstrap', 'vendor', 'database'];
foreach ($dirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    echo (is_dir($fullPath) ? "✓" : "✗") . " $dir/\n";
}

echo "\n=== Laravel Bootstrap Test ===\n";
try {
    if (file_exists($basePath . '/vendor/autoload.php')) {
        require $basePath . '/vendor/autoload.php';
        echo "✓ Autoloader loaded\n";

        if (file_exists($basePath . '/bootstrap/app.php')) {
            $app = require_once $basePath . '/bootstrap/app.php';
            echo "✓ Laravel app bootstrapped\n";
            echo "✓ Laravel is READY\n";
        } else {
            echo "✗ bootstrap/app.php not found\n";
        }
    } else {
        echo "✗ vendor/autoload.php not found\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
