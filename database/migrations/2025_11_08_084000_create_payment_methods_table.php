<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['bank_account','credit_card','e_wallet']);
            $table->string('provider')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('currency',3)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('nickname')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payment_methods');
    }
};
