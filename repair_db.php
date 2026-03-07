<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Checking user_game_progress table...\n";

if (!Schema::hasTable('user_game_progress')) {
    echo "Table user_game_progress does NOT exist. Creating it...\n";
    Schema::create('user_game_progress', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->unique();
        $table->integer('current_level')->default(1);
        $table->integer('current_stage')->default(0);
        $table->integer('current_checkpoint')->default(0);
        $table->integer('total_score')->default(0);
        $table->integer('experience')->default(0);
        $table->integer('coins')->default(0);
        $table->integer('lives')->default(5);
        $table->text('completed_levels_data')->nullable();
        $table->text('puzzle_details')->nullable();
        $table->text('trophies')->nullable();
        $table->integer('total_play_time')->default(0);
        $table->unsignedBigInteger('create_user')->nullable();
        $table->unsignedBigInteger('update_user')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });
    echo "Table created successfully.\n";
} else {
    echo "Table exists. Checking columns...\n";

    if (!Schema::hasColumn('user_game_progress', 'puzzle_details')) {
        echo "Adding puzzle_details column...\n";
        Schema::table('user_game_progress', function (Blueprint $table) {
            $table->text('puzzle_details')->nullable();
        });
        echo "Column puzzle_details added.\n";
    } else {
        echo "Column puzzle_details already exists.\n";
    }

    if (!Schema::hasColumn('user_game_progress', 'total_play_time')) {
        echo "Adding total_play_time column...\n";
        Schema::table('user_game_progress', function (Blueprint $table) {
            $table->integer('total_play_time')->default(0)->after('trophies');
        });
        echo "Column total_play_time added.\n";
    } else {
        echo "Column total_play_time already exists.\n";
    }
}

echo "Final Schema:\n";
$results = DB::select("PRAGMA table_info(user_game_progress)");
print_r($results);
