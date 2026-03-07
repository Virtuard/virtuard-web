<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("PRAGMA table_info(users)");
$names = array_map(function ($c) {
    return $c->name; }, $columns);
echo implode(", ", $names) . "\n";
