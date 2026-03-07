<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;

echo "Ricerca Alice Agus...\n";
$users = User::where('name', 'like', '%Alice%')
    ->orWhere('first_name', 'like', '%Alice%')
    ->orWhere('last_name', 'like', '%Agus%')
    ->get();

if ($users->isEmpty()) {
    echo "UTENTE NON TROVATO.\n";
} else {
    foreach ($users as $user) {
        echo "ID: " . $user->id . "\n";
        echo "Nome: " . $user->name . "\n";
        echo "User Name: " . $user->user_name . "\n";
        echo "Bio: " . ($user->bio ?? 'EMPTY') . "\n";
        echo "Coins: " . $user->coins . "\n";
        echo "Level: " . $user->current_level . "\n";
        echo "Total Score: " . $user->total_score . "\n";
        echo "-------------------\n";
    }
}
