<?php
use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

// 1. Load Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Setup Vercel Writable Storage
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
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// 4. Force SQLite DB creation in /tmp
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    // Explicitly run migrations because the DB is fresh
    $app->make(Kernel::class)->bootstrap(); // Ensure app is bootstrapped
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
}

// 5. Handle Request with explicit binding
$kernel = $app->make(Kernel::class);
$request = Request::capture();
$app->instance('request', $request); // Manually bind request to avoid "class not found"

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);