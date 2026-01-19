<?php

header('Content-Type: text/plain');
echo 'PHP Version: '.phpversion()."\n";
echo 'Document Root: '.__DIR__."\n";
echo 'Base Dir: '.dirname(__DIR__)."\n\n";

// Test autoloader
if (file_exists(dirname(__DIR__).'/vendor/autoload.php')) {
    echo "✓ Autoloader exists\n";
    require dirname(__DIR__).'/vendor/autoload.php';
    echo "✓ Autoloader loaded\n";
} else {
    echo "✗ Autoloader not found\n";
    exit(1);
}

// Test .env
if (file_exists(dirname(__DIR__).'/.env')) {
    echo "✓ .env file exists\n";
} else {
    echo "✗ .env file not found\n";
}

// Test database
$dbPath = dirname(__DIR__).'/database/database.sqlite';
if (file_exists($dbPath)) {
    echo "✓ Database file exists: $dbPath\n";
    echo '  Permissions: '.substr(sprintf('%o', fileperms($dbPath)), -4)."\n";
    echo '  Writable: '.(is_writable($dbPath) ? 'Yes' : 'No')."\n";
} else {
    echo "✗ Database file not found\n";
}

// Try to bootstrap Laravel
try {
    $app = require_once dirname(__DIR__).'/bootstrap/app.php';
    echo "✓ Laravel bootstrapped\n";
    echo '  Laravel Version: '.$app->version()."\n";
} catch (Exception $e) {
    echo '✗ Laravel bootstrap failed: '.$e->getMessage()."\n";
    echo '  File: '.$e->getFile().':'.$e->getLine()."\n";
}
