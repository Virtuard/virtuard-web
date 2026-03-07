<?php
// debug_profile_v5.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$output = "--- ALL TABLES LIST ---\n";

try {
    $res = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    foreach ($res as $row) {
        $output .= "- {$row->name}\n";
    }

    $output .= "\n--- RECENT USERS ---\n";
    $users = DB::table('users')->orderBy('id', 'desc')->limit(5)->get();
    foreach ($users as $u) {
        $output .= "ID: {$u->id}, Name: {$u->name}, Username: " . ($u->user_name ?? 'NULL') . "\n";
    }

} catch (\Exception $e) {
    $output .= "CRITICAL ERROR: " . $e->getMessage() . "\n";
}

file_put_contents('profile_debug_output.txt', $output);
echo "Debug complete.\n";
