<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("PRAGMA table_info(users)");
$search = ['level', 'coins', 'trophies', 'current_level', 'points'];
foreach ($columns as $c) {
    if (in_array(strtolower($c->name), $search)) {
        echo "FOUND: " . $c->name . " (" . $c->type . ")\n";
    }
}
