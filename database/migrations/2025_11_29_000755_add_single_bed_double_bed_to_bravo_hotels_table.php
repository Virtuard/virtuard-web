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
            $table->tinyInteger('bedroom')->nullable()->after('bed');
            $table->tinyInteger('single_bed')->nullable()->after('bedroom');
            $table->tinyInteger('double_bed')->nullable()->after('single_bed');
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
            $table->dropColumn(['single_bed', 'double_bed']);
        });
    }
};

