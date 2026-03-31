<?php

// 1. Load Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot the Laravel 12 Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Setup Vercel-friendly storage (Required for Read-Only Filesystems)
$app->useStoragePath('/tmp/storage');

$storageFolders = [
    '/app/public',
    '/framework/cache/data',
    '/framework/sessions',
    '/framework/views',
    '/logs'
];

foreach ($storageFolders as $folder) {
    $dir = '/tmp/storage' . $folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 4. SQLite Fix: Ensure the DB exists in the writable /tmp folder
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    // Automatically run migrations since /tmp is fresh every boot
    $app->make(Illuminate\Contracts\Console\Kernel::class)->call('migrate', ['--force' => true]);
}

// 5. Handle the Request (The "Bulletproof" way for Vercel)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);