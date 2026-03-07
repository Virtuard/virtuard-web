<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Illuminate\Support\Facades\DB;

echo "--- DEBUGGING LEADERBOARD QUERY ---\n";

DB::enableQueryLog();

$query = User::where('role_id', '!=', 1)
    ->where('status', 'publish')
    ->whereHas('gameProgress', function ($q) {
        $q->where('current_level', '>', 0)
            ->orWhere('coins', '>', 0)
            ->orWhere('total_score', '>', 0);
    })
    ->with(['gameProgress'])
    ->limit(20);

$users = $query->get();

echo "SQL Query executed:\n";
print_r(DB::getQueryLog());

echo "\nResults found: " . count($users) . "\n";
foreach ($users as $u) {
    echo "User: {$u->user_name} (ID: {$u->id})\n";
    echo "  Lvl: {$u->current_level} | Coins: {$u->coins}\n";
    if ($u->gameProgress) {
        echo "  DB Progress: Lvl={$u->gameProgress->current_level}, Coins={$u->gameProgress->coins}\n";
    } else {
        echo "  ❌ NO gameProgress record loaded!\n";
    }
}

$allUsersCount = User::count();
$publishUsersCount = User::where('role_id', '!=', 1)->where('status', 'publish')->count();
echo "\nTotal users in DB: $allUsersCount\n";
echo "Total publish users (non-admin): $publishUsersCount\n";
