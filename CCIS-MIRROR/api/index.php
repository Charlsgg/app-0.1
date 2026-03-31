<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

// 1. Load Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot the Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Set Storage Path to Writable /tmp
$app->useStoragePath('/tmp/storage');

// 4. Handle the Request via the Kernel (The most stable method for Vercel)
$kernel = $app->make(Kernel::class);

// 5. IMPORTANT: Bootstrap the Kernel
// This registers 'config', 'view', and other core services so helpers won't crash
$kernel->bootstrap();

// 6. Ensure Required Folders exist in /tmp
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

// 7. Initialize SQLite if needed
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
}

// 8. Capture and Handle Request
$request = Request::capture();
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);