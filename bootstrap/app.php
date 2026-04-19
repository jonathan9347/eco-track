<?php

// Force Laravel to use /tmp for temporary files
if (function_exists('sys_get_temp_dir')) {
    $tempDir = '/tmp';
    if (is_writable($tempDir)) {
        ini_set('upload_tmp_dir', $tempDir);
        putenv("TMPDIR=$tempDir");
        putenv("TEMP=$tempDir");
        putenv("TMP=$tempDir");
    }
}

// ... rest of your bootstrap file

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
