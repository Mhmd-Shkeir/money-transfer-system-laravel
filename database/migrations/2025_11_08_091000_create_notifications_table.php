<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type',['email','sms','app']);
            $table->string('title')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('notifications');
    }
};
