<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('store_name');
            $table->string('address');
            $table->decimal('latitude',10,6)->nullable();
            $table->decimal('longitude',10,6)->nullable();
            $table->string('working_hours')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->decimal('commission_rate',5,2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('agent_profiles');
    }
};
