<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Set up temporary directory
$tempDir = '/tmp/laravel-' . uniqid();
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

foreach ([
    $tempDir.'/framework/cache',
    $tempDir.'/framework/sessions',
    $tempDir.'/framework/testing',
    $tempDir.'/framework/views',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

putenv("TMPDIR=$tempDir");
putenv("TEMP=$tempDir");
putenv("TMP=$tempDir");

// Load Composer autoloader
$composerAutoload = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($composerAutoload)) {
    http_response_code(500);
    echo json_encode(['error' => 'Composer autoload not found.']);
    exit;
}

require $composerAutoload;

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Create Laravel application
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Vercel's filesystem is read-only except for /tmp, so point Laravel storage there.
if (method_exists($app, 'useStoragePath')) {
    $app->useStoragePath($tempDir);
}

$app->handleRequest(Request::capture());
