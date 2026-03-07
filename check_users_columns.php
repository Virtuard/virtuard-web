<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("PRAGMA table_info(users)");
echo "--- USERS TABLE COLUMNS ---\n";
foreach ($columns as $c) {
    echo $c->name . " (" . $c->type . ")\n";
}
