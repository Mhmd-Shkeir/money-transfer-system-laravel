<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('admin_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_commissions');
    }
};
