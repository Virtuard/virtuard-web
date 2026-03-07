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
        // Table: user_post_status
        if (!Schema::hasTable('user_post_status')) {
            Schema::create('user_post_status', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('ipanorama_id')->nullable();
                $table->text('message')->nullable();
                $table->string('type_status', 50)->default('Status');
                $table->string('type_post', 50)->nullable();
                $table->string('tag', 255)->default('-');
                $table->integer('public')->default(1);
                $table->integer('plays_count')->default(0);
                $table->integer('completions_count')->default(0);
                $table->integer('plays')->default(0);
                $table->integer('completions')->default(0);
                $table->integer('views_count')->default(0);
                $table->string('media', 255)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Table: user_post_comment
        if (!Schema::hasTable('user_post_comment')) {
            Schema::create('user_post_comment', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->text('comment');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Table: user_post_like
        if (!Schema::hasTable('user_post_like')) {
            Schema::create('user_post_like', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
            });
        }

        // Table: user_post_media
        if (!Schema::hasTable('user_post_media')) {
            Schema::create('user_post_media', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->string('media', 255);
                $table->string('type', 50)->nullable();
                $table->boolean('is_360_media')->default(false);
                $table->timestamps();
            });
        }

        // Table: post_plays
        if (!Schema::hasTable('post_plays')) {
            Schema::create('post_plays', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // Table: post_completions (Leaderboard source)
        if (!Schema::hasTable('post_completions')) {
            Schema::create('post_completions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->integer('time_spent')->comment('In seconds');
                $table->integer('moves');
                $table->timestamps();
            });
        }

        // Table: post_shares
        if (!Schema::hasTable('post_shares')) {
            Schema::create('post_shares', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('post_shares');
        Schema::dropIfExists('post_completions');
        Schema::dropIfExists('post_plays');
        Schema::dropIfExists('user_post_media');
        Schema::dropIfExists('user_post_like');
        Schema::dropIfExists('user_post_comment');
        Schema::dropIfExists('user_post_status');
    }
};
