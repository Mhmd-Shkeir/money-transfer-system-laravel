<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->string('source')->nullable(); // 'user' or 'agent' (duplicate of role)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('support_tickets');
    }
};
