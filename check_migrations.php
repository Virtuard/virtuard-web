<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $results = \Illuminate\Support\Facades\DB::table('migrations')->get();
    foreach ($results as $row) {
        echo $row->migration . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
