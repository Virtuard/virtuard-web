<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Api\Controllers\MemberController;
use Illuminate\Http\Request;

echo "--- TESTING API MEMBERS LIST ---\n";
try {
    $memberController = app(MemberController::class);
    $response = $memberController->allMembers(new Request(['per_page' => 10]));
    $content = json_decode($response->getContent(), true);

    if (isset($content['status']) && $content['status'] == false) {
        echo "❌ API ERROR: " . ($content['message'] ?? 'Unknown error') . "\n";
        exit;
    }

    $members = $content['data']['members'] ?? [];
    echo "Total members returned: " . count($members) . "\n";

    foreach ($members as $m) {
        echo "User: " . ($m['user_name'] ?? 'none') . " | ID: " . ($m['id'] ?? 'none') . "\n";
        echo "  Level: " . ($m['current_level'] ?? 'MISSING') . "\n";
        echo "  Coins: " . ($m['coins'] ?? 'MISSING') . "\n";
        echo "  Trophies: " . (isset($m['trophies']) ? (is_array($m['trophies']) ? count($m['trophies']) : 'present') : 'MISSING') . "\n";
    }
} catch (Exception $e) {
    echo "❌ CRASH: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
