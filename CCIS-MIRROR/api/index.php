<?php
use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

// 1. Load Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Set Storage to Writable /tmp (Essential for Vercel)
$app->useStoragePath('/tmp/storage');

// 4. Ensure ALL required subfolders exist in /tmp
$storageFolders = [
    '/app/public',
    '/framework/cache/data',
    '/framework/sessions',
    '/framework/testing',
    '/framework/views', // This fixes the [view] does not exist error
    '/logs'
];
foreach ($storageFolders as $folder) {
    $dir = '/tmp/storage' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// 5. Force the View Compiled Path to the writable directory
config(['view.compiled' => '/tmp/storage/framework/views']);

// 6. SQLite Initialization & Auto-Migration
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    // Boot the kernel and run migrations
    $kernel = $app->make(Kernel::class);
    $kernel->bootstrap();
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
}

// 7. Handle the Request
$kernel = $app->make(Kernel::class);
$request = Request::capture();
$app->instance('request', $request); 

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);