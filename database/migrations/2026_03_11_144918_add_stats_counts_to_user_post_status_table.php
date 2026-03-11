<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_post_status', function (Blueprint $table) {
            if (!Schema::hasColumn('user_post_status', 'plays_count')) {
                $table->integer('plays_count')->default(0)->after('public');
            }
            if (!Schema::hasColumn('user_post_status', 'completions_count')) {
                $table->integer('completions_count')->default(0)->after('plays_count');
            }
            if (!Schema::hasColumn('user_post_status', 'views_count')) {
                $table->integer('views_count')->default(0)->after('completions_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_post_status', function (Blueprint $table) {
            //
        });
    }
};
