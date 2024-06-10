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
        Schema::table('bravo_plans', function (Blueprint $table) {
            $table->integer('max_ipanorama')->after('max_service')->default(0);
        });

        Schema::table('bravo_user_plan', function (Blueprint $table) {
            $table->integer('max_ipanorama')->after('max_service')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bravo_plans', function (Blueprint $table) {
            $table->dropColumn('max_ipanorama');
        });

        Schema::table('bravo_user_plan', function (Blueprint $table) {
            $table->dropColumn('max_ipanorama');
        });
    }
};
