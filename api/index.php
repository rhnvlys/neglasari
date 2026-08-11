<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$storagePath = '/tmp/storage';

$directories = [
    $storagePath,
    $storagePath.'/framework',
    $storagePath.'/framework/cache',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Remove stale bootstrap cache if present
foreach (['packages.php', 'services.php', 'config.php', 'routes.php'] as $cacheFile) {
    $filePath = __DIR__.'/../bootstrap/cache/'.$cacheFile;
    if (file_exists($filePath)) {
        @unlink($filePath);
    }
}

// Setup SQLite DB if DB_CONNECTION is sqlite or mysql host is invalid
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '');

if ($dbConnection === 'sqlite' || empty($dbHost) || str_contains($dbHost, 'aivencloud.com')) {
    $targetDb = '/tmp/database.sqlite';
    $sourceDb = __DIR__ . '/../database/database.sqlite';

    if (!file_exists($targetDb) || filesize($targetDb) === 0) {
        if (file_exists($sourceDb)) {
            @copy($sourceDb, $targetDb);
        } else {
            @touch($targetDb);
        }
    }

    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$targetDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $targetDb;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $targetDb;
}

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath($storagePath);
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>Error on Vercel</h1>";
    echo "<pre>" . (string) $e . "</pre>";
}
