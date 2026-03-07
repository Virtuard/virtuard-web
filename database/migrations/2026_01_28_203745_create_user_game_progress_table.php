<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGameProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_game_progress')) {
            Schema::create('user_game_progress', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->integer('current_level')->default(1);
                $table->integer('current_stage')->nullable();
                $table->integer('current_checkpoint')->nullable();
                $table->bigInteger('total_score')->default(0);
                $table->bigInteger('experience')->default(0);
                $table->integer('coins')->default(0);
                $table->integer('lives')->default(5);
                $table->text('completed_levels_data')->nullable(); // JSON
                $table->text('trophies')->nullable(); // JSON
                $table->bigInteger('total_play_time')->default(0); // in seconds
                $table->integer('create_user')->nullable();
                $table->integer('update_user')->nullable();
                $table->softDeletes();
                $table->timestamps();

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_game_progress');
    }
}
