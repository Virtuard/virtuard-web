<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$progress = DB::table('user_game_progress')->get();
echo "Total progress records: " . count($progress) . "\n";
foreach ($progress as $p) {
    echo "User ID: " . $p->user_id . " | Level: " . ($p->current_level ?? 'null') . "\n";
}
