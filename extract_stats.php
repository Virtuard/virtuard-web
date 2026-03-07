<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- SCHEMA CHECK ---\n";
$userColumns = Schema::getColumnListing('users');
$statColumns = ['current_level', 'level', 'coins', 'total_score', 'trophies', 'total_play_time'];
foreach ($statColumns as $col) {
    if (in_array($col, $userColumns)) {
        echo "Column '$col' EXISTS in users table.\n";
    } else {
        echo "Column '$col' is MISSING in users table.\n";
    }
}

echo "\n--- STATS EXTRACTION ---\n";
$users = DB::table('users')->where('bio', 'like', '%[VARD:%')->get();
echo "Found " . count($users) . " users with VARD tags in bio.\n";

$report = [];
foreach ($users as $user) {
    preg_match('/\[VARD:LV:(\d+),TR:(\d+),C:(\d+),P:(\d+)\]/', $user->bio, $matches);
    if ($matches) {
        $stats = [
            'user_id' => $user->id,
            'name' => $user->first_name . ' ' . $user->last_name,
            'vard_level' => (int) $matches[1],
            'vard_trophies' => (int) $matches[2],
            'vard_coins' => (int) $matches[3],
            'vard_play_time' => (int) $matches[4],
            'db_level' => $user->current_level ?? $user->level ?? null,
            'db_coins' => $user->coins ?? null,
        ];
        $report[] = $stats;

        echo "User #{$user->id} ({$user->first_name}): LV={$stats['vard_level']}, COINS={$stats['vard_coins']}";
        if ($stats['vard_level'] != $stats['db_level'] || $stats['vard_coins'] != $stats['db_coins']) {
            echo " [MISMATCH/MISSING in DB]";
        }
        echo "\n";
    }
}

file_put_contents('user_stats_report.json', json_encode($report, JSON_PRETTY_PRINT));
echo "\nReport saved to user_stats_report.json\n";
