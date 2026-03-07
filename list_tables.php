<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
echo "--- ALL TABLES ---\n";
foreach ($tables as $t) {
    echo $t->name . "\n";
}
echo "--- END TABLES ---\n";
