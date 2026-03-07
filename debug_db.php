<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$output = "DB Connection: " . config('database.default') . "\n";
if (config('database.default') == 'sqlite') {
    $output .= "SQLite Path: " . config('database.connections.sqlite.database') . "\n";
}

try {
    $count = DB::table('core_settings')->count();
    $output .= "Core Settings Count: $count\n";

    $settings = DB::table('core_settings')->get();
    foreach ($settings as $s) {
        $output .= "[$s->group] $s->name: $s->val\n";
    }
} catch (\Exception $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

file_put_contents('db_debug.txt', $output);
echo "Done. Check db_debug.txt\n";
