<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$storagePath = '/tmp/storage';
$bootstrapPath = '/tmp/bootstrap';

$directories = [
    $storagePath,
    $storagePath.'/framework',
    $storagePath.'/framework/cache',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    $bootstrapPath,
    $bootstrapPath.'/cache',
];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Remove public/hot if present so Vite dev server URL is not injected in production
if (file_exists(__DIR__.'/../public/hot')) {
    @unlink(__DIR__.'/../public/hot');
}

// Ensure providers.php exists in /tmp/bootstrap
$sourceProviders = __DIR__.'/../bootstrap/providers.php';
$targetProviders = $bootstrapPath.'/providers.php';
if (file_exists($sourceProviders) && !file_exists($targetProviders)) {
    @copy($sourceProviders, $targetProviders);
}

// Setup SQLite DB in /tmp
$targetDb = '/tmp/database.sqlite';
$sourceDb = __DIR__ . '/../database/database.sqlite';

if (!file_exists($targetDb) || filesize($targetDb) === 0) {
    if (file_exists($sourceDb)) {
        @copy($sourceDb, $targetDb);
    } else {
        @touch($targetDb);
    }
}

// Explicit environment variables for Vercel
$envVars = [
    'APP_NAME' => 'SIAP Neglasari',
    'APP_ENV' => 'production',
    'APP_KEY' => 'base64:F1FdnJ57539kVNNfQR7L9+g4eVQMqUMzyjfi4LIbEs8=',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'https://neglasari-pi.vercel.app',
    'LOG_CHANNEL' => 'stderr',
    'SESSION_DRIVER' => 'database',
    'CACHE_STORE' => 'database',
    'QUEUE_CONNECTION' => 'sync',
    'FILESYSTEM_DISK' => 'public',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => $targetDb,
];

foreach ($envVars as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath($storagePath);
    $app->useBootstrapPath($bootstrapPath);
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>Error on Vercel</h1>";
    echo "<pre>" . (string) $e . "</pre>";
}
