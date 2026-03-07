<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Api\Controllers\MemberController;
use Illuminate\Http\Request;

$controller = new MemberController();
$request = Request::create('/api/members', 'GET', ['per_page' => 5]);

$response = $controller->allMembers($request);
$content = $response->getContent();

echo "API RESPONSE PREVIEW:\n";
$data = json_decode($content, true);
if (isset($data['data']['members'])) {
    foreach ($data['data']['members'] as $u) {
        echo "User: " . ($u['user_name'] ?? $u['name']) . "\n";
        echo "  - current_level: " . ($u['current_level'] ?? 'MISSING') . "\n";
        echo "  - coins: " . ($u['coins'] ?? 'MISSING') . "\n";
        echo "  - total_score: " . ($u['total_score'] ?? 'MISSING') . "\n";
        echo "  - clean_bio: " . ($u['clean_bio'] ?? 'MISSING') . "\n";
        echo "  - bio: " . ($u['bio'] ?? 'MISSING') . "\n";
    }
} else {
    echo "ERROR: Member list not found in response.\n";
    echo $content;
}
