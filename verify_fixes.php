<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Modules\Api\Controllers\MemberController;
use Modules\Api\Controllers\PostController;
use Illuminate\Http\Request;

echo "--- VERIFYING MEMBER STATS ---\n";
try {
    $memberController = app(MemberController::class);
    $user = User::where('user_name', 'alice_agus')->first();
    if ($user) {
        $response = $memberController->detailMember(new Request(), $user->id);
        $data = $response->getData();
        echo "User: " . $data->data->member->user_name . "\n";
        echo "Level: " . $data->data->member->current_level . "\n";
        echo "Coins: " . $data->data->member->coins . "\n";
        echo "Trophies: " . $data->data->member->trophies . "\n";
    } else {
        echo "User alice_agus not found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- VERIFYING POST FEED (NULL AUTHOR CHECK) ---\n";
try {
    $postController = app(PostController::class);
    $response = $postController->index(new Request());
    echo "Status Code: " . $response->getStatusCode() . "\n";
    $content = json_decode($response->getContent(), true);
    print_r($content);
} catch (Exception $e) {
    echo "Exception Caught: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
