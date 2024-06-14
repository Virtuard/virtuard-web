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
            $table->integer('room')->nullable();
            $table->string('chain')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
        });

        Schema::table('bravo_spaces', function (Blueprint $table) {
            $table->integer('room')->nullable();
            $table->integer('square_land')->nullable();
            $table->integer('flooring')->nullable();
            $table->string('land_registry_category')->nullable();
            $table->string('agency')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
        });

        Schema::table('bravo_businesses', function (Blueprint $table) {
            $table->string('franchising')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
        });

        Schema::table('bravo_naturals', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
        });

        Schema::table('bravo_culturals', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
        });

        Schema::table('bravo_arts', function (Blueprint $table) {
            $table->integer('room')->nullable();
            $table->integer('bed')->nullable();
            $table->integer('bathroom')->nullable();
            $table->integer('square')->nullable();
            $table->string('engineering')->nullable();
            $table->string('software')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
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
            $table->dropColumn('room');
            $table->dropColumn('chain');
            $table->dropColumn('phone');
            $table->dropColumn('website');
        });

        Schema::table('bravo_spaces', function (Blueprint $table) {
            $table->dropColumn('room');
            $table->dropColumn('square_land');
            $table->dropColumn('flooring');
            $table->dropColumn('land_registry_category');
            $table->dropColumn('agency');
            $table->dropColumn('phone');
            $table->dropColumn('website');
        });

        Schema::table('bravo_businesses', function (Blueprint $table) {
            $table->dropColumn('franchising');
            $table->dropColumn('phone');
            $table->dropColumn('website');
        });

        Schema::table('bravo_naturals', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('website');
        });

        Schema::table('bravo_culturals', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('website');
        });

        Schema::table('bravo_arts', function (Blueprint $table) {
            $table->dropColumn('room');
            $table->dropColumn('bed');
            $table->dropColumn('bathroom');
            $table->dropColumn('square');
            $table->dropColumn('engineering');
            $table->dropColumn('software');
            $table->dropColumn('phone');
            $table->dropColumn('website');
        });
    }
};
