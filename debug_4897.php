<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$user = DB::table('users')->where('id', 4897)->first();
if ($user) {
    echo "User 4897 found.\n";
    echo "Bio: " . $user->bio . "\n";
    echo "Current Level: " . ($user->current_level ?? 'NULL') . "\n";
    echo "Coins: " . ($user->coins ?? 'NULL') . "\n";
} else {
    echo "User 4897 NOT found in local DB.\n";
    $count = DB::table('users')->count();
    echo "Total users in DB: $count\n";
}
