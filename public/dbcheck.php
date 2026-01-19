<?php

header('Content-Type: text/plain');

$dbPath = dirname(__DIR__).'/database/database.sqlite';

echo "Database Check\n";
echo "==============\n\n";

if (! file_exists($dbPath)) {
    echo "❌ Database file not found at: $dbPath\n";
    exit(1);
}

echo "✅ Database file exists\n";
echo "   Path: $dbPath\n";
echo '   Size: '.filesize($dbPath)." bytes\n";
echo '   Writable: '.(is_writable($dbPath) ? 'Yes' : 'No')."\n\n";

try {
    $db = new PDO('sqlite:'.$dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful\n\n";

    // List all tables
    echo "Tables:\n";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "  - $table ($count rows)\n";
    }

} catch (Exception $e) {
    echo '❌ Database error: '.$e->getMessage()."\n";
}
