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
        Schema::table('bravo_hotels', function (Blueprint $table) {
            $table->integer('view_count')->default(0)->after('status');
        });

        Schema::table('bravo_spaces', function (Blueprint $table) {
            $table->integer('view_count')->default(0)->after('status');
        });

        Schema::table('bravo_businesses', function (Blueprint $table) {
            $table->integer('view_count')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bravo_hotels', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });

        Schema::table('bravo_spaces', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });

        Schema::table('bravo_businesses', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });
    }
};
