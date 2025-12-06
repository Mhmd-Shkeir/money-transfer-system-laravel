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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->nullable()->after('fee');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('offer_id')->comment('Amount discounted from fee due to offer');
            
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['offer_id']);
            $table->dropColumn(['offer_id', 'discount_amount']);
        });
    }
};
