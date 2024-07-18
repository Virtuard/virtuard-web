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
        Schema::table('bravo_bookings', function (Blueprint $table) {
            $table->integer('ref_id')->nullable();
            $table->decimal('ref_commission', 10, 2)->default(0.00);
            $table->string('ref_commission_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            $table->dropColumn('referral_id');
            $table->dropColumn('referral_commission');
            $table->dropColumn('referral_commission_type');
        });
    }
};
