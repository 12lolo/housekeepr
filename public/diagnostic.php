<?php
/**
 * Laravel Production Diagnostic Script
 * Access this file directly via browser: https://housekeepr.nl/diagnostic.php
 * DELETE THIS FILE after fixing issues!
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>HouseKeepr Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .pass { color: #059669; font-weight: bold; }
        .fail { color: #dc2626; font-weight: bold; }
        .warn { color: #d97706; font-weight: bold; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        h2 { margin-top: 0; color: #1f2937; }
        pre { background: #f9fafb; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>🔍 HouseKeepr Production Diagnostic</h1>

<div class="section">
    <h2>PHP Environment</h2>
    <?php
    echo 'PHP Version: <strong>'.phpversion().'</strong>';
echo (version_compare(phpversion(), '8.2.0', '>='))
    ? ' <span class="pass">✓ PASS</span>'
    : ' <span class="fail">✗ FAIL (Requires PHP 8.2+)</span>';
echo '<br>';

echo 'Memory Limit: <strong>'.ini_get('memory_limit').'</strong><br>';
echo 'Max Execution Time: <strong>'.ini_get('max_execution_time').'s</strong><br>';
echo 'Upload Max: <strong>'.ini_get('upload_max_filesize').'</strong><br>';
echo 'Post Max: <strong>'.ini_get('post_max_size').'</strong><br>';
?>
</div>

<div class="section">
    <h2>Required PHP Extensions</h2>
    <?php
$required = ['mbstring', 'pdo', 'pdo_sqlite', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
foreach ($required as $ext) {
    echo "$ext: ";
    echo extension_loaded($ext)
        ? '<span class="pass">✓ INSTALLED</span>'
        : '<span class="fail">✗ MISSING</span>';
    echo '<br>';
}
?>
</div>

<div class="section">
    <h2>File System Checks</h2>
    <?php
$base = dirname(__DIR__);

function checkPath($path, $name, $shouldExist = true, $checkWritable = false)
{
    echo "$name: ";
    if (file_exists($path)) {
        echo '<span class="pass">✓ EXISTS</span>';
        if ($checkWritable) {
            echo is_writable($path)
                ? ' <span class="pass">✓ WRITABLE</span>'
                : ' <span class="fail">✗ NOT WRITABLE</span>';
        }
        echo ' <code>('.substr(sprintf('%o', fileperms($path)), -4).')</code>';
    } else {
        echo $shouldExist
            ? '<span class="fail">✗ MISSING</span>'
            : '<span class="warn">⚠ NOT FOUND</span>';
    }
    echo '<br>';
}

checkPath($base.'/.env', '.env file', true);
checkPath($base.'/vendor', 'vendor directory', true);
checkPath($base.'/vendor/autoload.php', 'Composer autoload', true);
checkPath($base.'/database', 'database directory', true, true);
checkPath($base.'/database/database.sqlite', 'SQLite database', true, true);
checkPath($base.'/storage', 'storage directory', true, true);
checkPath($base.'/storage/logs', 'storage/logs', true, true);
checkPath($base.'/storage/framework', 'storage/framework', true, true);
checkPath($base.'/storage/framework/cache', 'storage/framework/cache', true, true);
checkPath($base.'/storage/framework/sessions', 'storage/framework/sessions', true, true);
checkPath($base.'/storage/framework/views', 'storage/framework/views', true, true);
checkPath($base.'/bootstrap/cache', 'bootstrap/cache', true, true);
checkPath($base.'/public/build', 'public/build (Vite assets)', true);
?>
</div>

<div class="section">
    <h2>.env Configuration</h2>
    <?php
$envPath = $base.'/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);

    // Check for APP_KEY
    echo 'APP_KEY: ';
    if (preg_match('/^APP_KEY=base64:.+$/m', $envContent)) {
        echo '<span class="pass">✓ SET</span><br>';
    } else {
        echo '<span class="fail">✗ MISSING OR INVALID</span><br>';
        echo '<pre>Run: php artisan key:generate --force</pre>';
    }

    // Check APP_DEBUG
    echo 'APP_DEBUG: ';
    if (preg_match('/^APP_DEBUG=false$/m', $envContent)) {
        echo '<span class="pass">✓ false (production)</span><br>';
    } else {
        echo '<span class="warn">⚠ Should be "false" in production</span><br>';
    }

    // Check DB_DATABASE path
    if (preg_match('/^DB_DATABASE=(.+)$/m', $envContent, $matches)) {
        $dbPath = trim($matches[1]);
        echo "DB_DATABASE: <code>$dbPath</code><br>";

        // Check if it's absolute path
        if (substr($dbPath, 0, 1) !== '/') {
            echo '<span class="warn">⚠ WARNING: Using relative path. Consider using absolute path.</span><br>';
        }
    }
} else {
    echo '<span class="fail">✗ .env file not found!</span><br>';
    echo '<pre>Copy .env.production to .env or run: php artisan key:generate --force</pre>';
}
?>
</div>

<div class="section">
    <h2>Laravel Bootstrap Test</h2>
    <?php
try {
    require $base.'/vendor/autoload.php';
    echo '<span class="pass">✓ Composer autoloader loaded</span><br>';

    $app = require_once $base.'/bootstrap/app.php';
    echo '<span class="pass">✓ Laravel application bootstrapped</span><br>';

    echo 'Laravel Version: <strong>'.$app->version().'</strong><br>';

} catch (Exception $e) {
    echo '<span class="fail">✗ BOOTSTRAP FAILED</span><br>';
    echo '<pre>'.htmlspecialchars($e->getMessage()).'</pre>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
}
?>
</div>

<div class="section">
    <h2>Recent Laravel Logs</h2>
    <?php
$logPath = $base.'/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $logs = file($logPath);
    $recentLogs = array_slice($logs, -50); // Last 50 lines
    echo '<pre>'.htmlspecialchars(implode('', $recentLogs)).'</pre>';
} else {
    echo '<span class="warn">⚠ No log file found (may be good if fresh install)</span>';
}
?>
</div>

<div class="section">
    <h2>Recommendations</h2>
    <ul>
        <li>If APP_KEY is missing: SSH in and run <code>php artisan key:generate --force</code></li>
        <li>If permissions are wrong: <code>chmod 775 storage bootstrap/cache -R</code> and <code>chmod 666 database/database.sqlite</code></li>
        <li>If vendor is missing: <code>composer install --no-dev --optimize-autoloader</code></li>
        <li>Check <code>storage/logs/laravel.log</code> for detailed error messages</li>
        <li><strong>DELETE THIS FILE after diagnostics!</strong></li>
    </ul>
</div>

</body>
</html>
