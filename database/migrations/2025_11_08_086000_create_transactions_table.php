<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('beneficiary_id')->constrained('beneficiaries');
            $table->foreignId('agent_id')->nullable()->constrained('agent_profiles');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods');
            $table->foreignId('from_currency_id')->nullable()->constrained('currencies');
            $table->foreignId('to_currency_id')->nullable()->constrained('currencies');
            $table->decimal('amount_sent',12,2);
            $table->decimal('exchange_rate',12,6)->nullable();
            $table->decimal('fee',12,2)->default(0);
            $table->decimal('total_paid',12,2);
            $table->decimal('amount_received',12,2)->nullable();
            $table->enum('payout_method',['bank_deposit','cash_pickup','mobile_wallet'])->nullable();
            $table->enum('status',['pending','processing','completed','cancelled','refunded'])->default('pending');
            $table->string('reference_code')->unique();
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
