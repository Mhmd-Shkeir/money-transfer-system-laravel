<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_profile_id');
            $table->unsignedBigInteger('user_id');        // agent user
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->nullable(); // optional, can store agent currency
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable(); // admin user id
            $table->timestamp('approved_at')->nullable();
            $table->text('agent_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->foreign('agent_profile_id')->references('id')->on('agent_profiles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payout_requests');
    }
};
