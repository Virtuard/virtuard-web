<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Api\Controllers\MemberController;
use Illuminate\Http\Request;

echo "Inizializzazione controller...\n";
$controller = new MemberController();
$request = Request::create('/api/members', 'GET', ['per_page' => 10]);

echo "Chiamata allMembers...\n";
try {
    $response = $controller->allMembers($request);
    $content = $response->getContent();

    file_put_contents('api_output.json', $content);
    echo "Output salvato in api_output.json\n";

    $data = json_decode($content, true);
    if ($data && isset($data['data']['members'])) {
        echo "Trovati " . count($data['data']['members']) . " utenti.\n";
        foreach ($data['data']['members'] as $u) {
            $name = $u['user_name'] ?? $u['name'] ?? 'Unknown';
            $level = $u['current_level'] ?? 'N/A';
            $coins = $u['coins'] ?? 'N/A';
            $score = $u['total_score'] ?? 'N/A';
            echo " - $name | Lvl: $level | Coins: $coins | Score: $score\n";
        }
    } else {
        echo "Dati non validi o mancanti!\n";
    }
} catch (\Exception $e) {
    echo "ECCEZIONE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
