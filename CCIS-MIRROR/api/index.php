<?php

// 1. Load Composer
require __DIR__ . '/../vendor/autoload.php';

// 2. Boot Laravel 12
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Set Storage to Writable /tmp
$app->useStoragePath('/tmp/storage');

// 4. Ensure folders exist
$storageFolders = ['/app/public', '/framework/cache/data', '/framework/sessions', '/framework/views', '/logs'];
foreach ($storageFolders as $folder) {
    $dir = '/tmp/storage' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// 5. SQLite Initialization
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}

// 6. FORCE BOOTSTRAP (Fixes the "Target class [view] does not exist" error)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request); // Bind the request manually

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);