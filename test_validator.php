<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$validator = \Illuminate\Support\Facades\Validator::make([
    'current_level' => 1,
    'coins' => 10,
    'total_play_time' => 500,
    'completed_levels_data' => ['p1', 'p2'],
    'puzzle_details' => ['p1' => ['time' => 10], 'p2' => ['time' => 20]],
    'trophies' => ['t1']
], [
    'current_level' => 'nullable|integer|min:1',
    'coins' => 'nullable|integer|min:0',
    'completed_levels_data' => 'nullable|array',
    'puzzle_details' => 'nullable|array',
    'trophies' => 'nullable|array',
    'total_play_time' => 'nullable|integer|min:0',
]);

if ($validator->fails()) {
    echo "FAILED\n";
    print_r($validator->errors()->toArray());
} else {
    echo "PASSED\n";
}
