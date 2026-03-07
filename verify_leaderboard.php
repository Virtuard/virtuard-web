<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Modules\Api\Controllers\MemberController;
use Illuminate\Http\Request;

echo "--- VERIFYING LEADERBOARD FILTER (App Users Only) ---\n";
try {
    $memberController = app(MemberController::class);
    $response = $memberController->allMembers(new Request(['per_page' => 100]));
    $content = json_decode($response->getContent(), true);

    echo "Top-level keys: " . implode(', ', array_keys($content)) . "\n";
    if (isset($content['data'])) {
        echo "Data keys: " . implode(', ', array_keys($content['data'])) . "\n";
    }

    $members = [];
    if (isset($content['data']['members'])) {
        $members = $content['data']['members'];
    } elseif (isset($content['members'])) {
        $members = $content['members'];
    }

    echo "Total members in leaderboard: " . count($members) . "\n";

    foreach ($members as $m) {
        $id = $m['id'] ?? $m['id_user'] ?? null;
        if (!$id) {
            echo "User: " . ($m['user_name'] ?? 'unknown') . " | NO ID FOUND\n";
            continue;
        }
        $hasProgress = \App\Models\UserGameProgress::where('user_id', $id)->exists();
        echo "User: " . ($m['user_name'] ?? 'unknown') . " | ID: " . $id . " | Has Progress Entry: " . ($hasProgress ? 'YES' : 'NO') . "\n";
        if (!$hasProgress) {
            echo "❌ ERROR: Found user without progress in leaderboard!\n";
        }
    }

    $totalUsersCount = User::where('role_id', '!=', 1)->where('status', 'publish')->count();
    $totalAppUsers = User::where('role_id', '!=', 1)->where('status', 'publish')->has('gameProgress')->count();
    echo "\nTotal publish-users in DB (all): " . $totalUsersCount . "\n";
    echo "Total app-users (with progress): " . $totalAppUsers . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
