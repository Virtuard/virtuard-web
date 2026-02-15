<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePuzzleTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('puzzle_tracking')) {
            Schema::create('puzzle_tracking', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('session_id', 255)->nullable();
                $table->string('event_type', 50)->default('view'); // view, click, download, app_open
                $table->string('platform', 50)->nullable(); // android, ios, desktop, web
                $table->string('user_agent', 500)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('referrer', 500)->nullable();
                $table->string('img_url', 500)->nullable();
                $table->string('title', 255)->nullable();
                $table->text('query_params')->nullable(); // JSON
                $table->string('deep_link_used', 500)->nullable();
                $table->string('redirect_url', 500)->nullable();
                $table->boolean('app_installed')->default(false);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->integer('create_user')->nullable();
                $table->integer('update_user')->nullable();
                $table->timestamps();

                // Indexes for better query performance
                $table->index('session_id');
                $table->index('event_type');
                $table->index('platform');
                $table->index('created_at');
                $table->index('user_id');
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
        Schema::dropIfExists('puzzle_tracking');
    }
}
