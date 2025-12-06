<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            $table->enum('role', ['admin','user','agent'])->default('user');
            $table->enum('status', ['active','suspended'])->default('active');
            $table->boolean('is_email_verified')->default(false);
            $table->boolean('is_phone_verified')->default(false);
            $table->string('social_provider')->nullable();
            $table->string('social_id')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
