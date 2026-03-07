<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('user_game_progress')) {
            Schema::table('user_game_progress', function (Blueprint $table) {
                if (!Schema::hasColumn('user_game_progress', 'puzzle_details')) {
                    $table->text('puzzle_details')->nullable();
                }
            });
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_game_progress', function (Blueprint $table) {
            $table->dropColumn('puzzle_details');
        });
    }
};
