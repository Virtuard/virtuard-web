<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\User;
use Illuminate\Support\Facades\Auth;

// Mock context
$query = User::where('role_id', '!=', 1)->where('status', 'publish')->with('gameProgress')->limit(5);
$players = $query->get();

$players->transform(function ($user) {
    $user->avatar_url = $user->getAvatarUrl();
    $user->bio = $user->clean_bio;
    $user->game_progress = $user->gameProgress;

    // Explicitly add fields that our new logic should have synced
    $user->current_level = (int) $user->current_level;
    $user->coins = (int) $user->coins;

    return $user;
});

echo json_encode(['players' => $players], JSON_PRETTY_PRINT);
