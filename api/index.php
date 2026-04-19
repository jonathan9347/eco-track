<?php

// Set up temporary directory
$tempDir = '/tmp/laravel-' . uniqid();
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
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

// Set custom view compiled path
if (function_exists('config')) {
    config(['view.compiled' => $tempDir . '/views']);
}

// Create Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);