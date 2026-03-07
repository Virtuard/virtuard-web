<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$hasUserName = Schema::hasColumn('users', 'user_name');
echo "Has user_name: " . ($hasUserName ? "YES" : "NO") . "\n";

$columns = Schema::getColumnListing('users');
echo "Total columns: " . count($columns) . "\n";
print_r($columns);
