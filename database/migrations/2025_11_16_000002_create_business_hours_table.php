<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_profile_id')->constrained('agent_profiles')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['agent_profile_id', 'day_of_week']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('business_hours');
    }
};
