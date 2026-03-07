<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$columns = [
    'current_level' => 'integer',
    'level' => 'integer',
    'coins' => 'integer',
    'total_score' => 'integer',
    'trophies' => 'integer',
    'total_play_time' => 'integer',
];

echo "Checking columns in users table...\n";

Schema::table('users', function (Blueprint $table) use ($columns) {
    foreach ($columns as $column => $type) {
        if (!Schema::hasColumn('users', $column)) {
            echo "Adding column '$column' type '$type'...\n";
            if ($type == 'integer') {
                $table->integer($column)->default(0)->nullable();
            }
        } else {
            echo "Column '$column' already exists.\n";
        }
    }
});

echo "Success.\n";
