<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 4897;
$bio = "🧩 Puzzle Gamer [VARD:LV:71,TR:8,C:68221,P:5784]";

DB::table('users')->updateOrInsert(
    ['id' => $id],
    [
        'name' => 'Alice Test',
        'first_name' => 'Alice',
        'last_name' => 'Test',
        'email' => 'alice@example.com',
        'bio' => $bio,
        'status' => 'publish',
        'role_id' => 2,
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now()
    ]
);

echo "Created mock user 4897 with VARD tag.\n";

$user = \App\User::find($id);
echo "Resulting Stats:\n";
echo "Level: " . $user->current_level . "\n";
echo "Coins: " . $user->coins . "\n";
echo "Trophies: " . $user->trophies . "\n";
echo "Play Time: " . $user->total_play_time . "\n";
echo "Clean Bio: " . $user->clean_bio . "\n";
