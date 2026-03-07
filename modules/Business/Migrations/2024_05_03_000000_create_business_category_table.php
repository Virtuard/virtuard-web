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
        Schema::table('bravo_businesses', function (Blueprint $table) {
            $table->integer('category_id')->after('banner_image_id')->unsigned()->nullable();
        });
        
        Schema::create('bravo_business_category', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->text('content')->nullable();
            $table->string('status', 50)->nullable();
            $table->integer('_lft')->unsigned()->default(0);
            $table->integer('_rgt')->unsigned()->default(0);
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->bigInteger('origin_id')->nullable();
            $table->string('lang', 10)->nullable();
            $table->bigInteger('create_user')->nullable();
            $table->bigInteger('update_user')->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->index(['_lft', '_rgt', 'parent_id']);
        });
        
        Schema::create('bravo_business_category_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('origin_id')->nullable();
            $table->string('locale', 10)->nullable();
            $table->string('name', 255)->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('create_user')->nullable();
            $table->bigInteger('update_user')->nullable();
            $table->timestamps();
        
            $table->index(['origin_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('business_category');
        Schema::dropIfExists('business_category_translations');
    }
};
