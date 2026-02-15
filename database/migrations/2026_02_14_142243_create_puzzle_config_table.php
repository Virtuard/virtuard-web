<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePuzzleConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('puzzle_config')) {
            Schema::create('puzzle_config', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('android_package', 255)->nullable();
                $table->string('android_store_link', 500)->nullable();
                $table->string('android_deep_link_scheme', 100)->default('https');
                $table->string('ios_app_id', 100)->nullable();
                $table->string('ios_store_link', 500)->nullable();
                $table->string('ios_deep_link_scheme', 100)->nullable();
                $table->string('web_game_url', 500)->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('create_user')->nullable();
                $table->integer('update_user')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('puzzle_config');
    }
}
