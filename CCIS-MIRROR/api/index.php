<?php
// 1. Force errors to display
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Vercel PHP Diagnostic</h1>";
echo "PHP Version: " . PHP_VERSION . "<br>";

// 2. Check for critical files
$files = [
    'Vendor Autoload' => __DIR__ . '/../vendor/autoload.php',
    'Bootstrap App' => __DIR__ . '/../bootstrap/app.php',
    'Public Index' => __DIR__ . '/../public/index.php'
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name found.<br>";
    } else {
        echo "❌ $name NOT FOUND at $path<br>";
    }
}

// 3. Test Composer Load
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    echo "✅ Composer Autoloader loaded successfully.<br>";
}

echo "<h2>If you see this, PHP is working. The crash happens inside Laravel.</h2>";