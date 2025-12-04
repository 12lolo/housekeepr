<?php
/**
 * Laravel Public Directory Proxy
 * This file allows Laravel to work when the public directory is not the document root
 */

// Set the public path
$publicPath = __DIR__ . '/public';

// Change the current directory to public
chdir($publicPath);

// Require the actual Laravel entry point
require $publicPath . '/index.php';
