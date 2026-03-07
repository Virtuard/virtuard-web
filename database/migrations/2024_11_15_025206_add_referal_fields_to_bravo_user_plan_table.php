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
        Schema::table('bravo_user_plan', function (Blueprint $table) {
            $table->integer('referal_user_id')->nullable();
            $table->decimal('referal_amount', 10, 2)->nullable()->after('referal_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bravo_user_plan', function (Blueprint $table) {
            $table->dropColumn(['referal_user_id', 'referal_amount']);
        });
    }
};
