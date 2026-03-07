<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    \Illuminate\Support\Facades\Schema::table('user_game_progress', function ($table) {
        $table->text('puzzle_details')->nullable();
    });
    file_put_contents(__DIR__ . '/err.txt', "SUCCESS");
} catch (\Exception $e) {
    file_put_contents(__DIR__ . '/err.txt', "ERROR: " . $e->getMessage());
}
