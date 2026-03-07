<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$progress = DB::table('user_game_progress')->get();
$data = [];
foreach ($progress as $p) {
    $data[] = (array) $p;
}

file_put_contents('full_progress_dump.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Dumped " . count($data) . " records to full_progress_dump.json\n";
