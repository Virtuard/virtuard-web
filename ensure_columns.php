<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Verifying database schema for user_game_progress...\n";

try {
    if (!Schema::hasTable('user_game_progress')) {
        echo "❌ Table 'user_game_progress' does not exist. Please run migrations first.\n";
        exit(1);
    }

    Schema::table('user_game_progress', function (Blueprint $table) {
        if (!Schema::hasColumn('user_game_progress', 'puzzle_details')) {
            $table->text('puzzle_details')->nullable()->after('completed_levels_data');
            echo "✅ Added column: puzzle_details\n";
        } else {
            echo "ℹ️ Column already exists: puzzle_details\n";
        }

        if (!Schema::hasColumn('user_game_progress', 'total_play_time')) {
            $table->integer('total_play_time')->default(0)->after('coins');
            echo "✅ Added column: total_play_time\n";
        } else {
            echo "ℹ️ Column already exists: total_play_time\n";
        }

        // Ensure trophies is a text column for JSON
        if (Schema::hasColumn('user_game_progress', 'trophies')) {
            // In SQLite we can't easily change column type, but we at least verify it's there
            echo "ℹ️ Column verified: trophies\n";
        }
    });

    echo "🚀 Database repair completed successfully!\n";

} catch (\Exception $e) {
    echo "❌ Error during database repair: " . $e->getMessage() . "\n";
}
