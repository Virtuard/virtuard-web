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
            $table->tinyInteger('bed')->nullable()->after('room');
            $table->tinyInteger('bathroom')->nullable()->after('bed');
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
            $table->dropColumn(['bed', 'bathroom']);
        });
    }
};

