<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\UserGameProgress;

echo "--- STARTING SYNC ---\n";

$users = DB::table('users')->where('bio', 'like', '%[VARD:%')->get();
echo "Found " . count($users) . " users with tags.\n";

foreach ($users as $user) {
    preg_match('/\[VARD:LV:(\d+),TR:(\d+),C:(\d+),P:(\d+)\]/', $user->bio, $matches);
    if ($matches) {
        $level = (int) $matches[1];
        $trophiesCount = (int) $matches[2];
        $coins = (int) $matches[3];
        $playTime = (int) $matches[4];

        echo "Syncing User #{$user->id} ({$user->first_name}): LV=$level, TR=$trophiesCount, C=$coins, P=$playTime\n";

        // 1. Update users table
        DB::table('users')->where('id', $user->id)->update([
            'current_level' => $level,
            'level' => $level,
            'coins' => $coins,
            'total_score' => $coins,
            'trophies' => $trophiesCount,
            'total_play_time' => $playTime
        ]);

        // 2. Update or Create user_game_progress table
        $progress = DB::table('user_game_progress')->where('user_id', $user->id)->first();
        if ($progress) {
            DB::table('user_game_progress')->where('user_id', $user->id)->update([
                'current_level' => $level,
                'coins' => $coins,
                'total_score' => $coins,
                'total_play_time' => $playTime,
                'updated_at' => now()
            ]);
        } else {
            DB::table('user_game_progress')->insert([
                'user_id' => $user->id,
                'current_level' => $level,
                'coins' => $coins,
                'total_score' => $coins,
                'total_play_time' => $playTime,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

echo "--- SYNC COMPLETED ---\n";
