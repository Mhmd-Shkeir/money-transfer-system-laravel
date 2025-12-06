<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->foreignId('user_id')->constrained('users');
            $table->text('reason');
            $table->enum('status',['pending','approved','rejected','resolved'])->default('pending');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('disputes');
    }
};
