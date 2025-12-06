<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('wallet_provider')->nullable();
            $table->string('wallet_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('beneficiaries');
    }
};
