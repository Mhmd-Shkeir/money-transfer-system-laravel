<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('agent_profiles', function (Blueprint $table) {
            // Add business hours and location information
            $table->json('business_hours')->nullable()->comment('JSON format: {"Monday": {"open": "09:00", "close": "18:00"}, ...}');
            $table->string('timezone')->nullable()->default('UTC')->comment('e.g., America/New_York, Europe/London');
            $table->string('business_phone')->nullable();
            $table->string('business_email')->nullable();
            $table->string('website')->nullable();
            $table->text('service_description')->nullable();
            $table->json('services_offered')->nullable()->comment('JSON array of services');
            $table->decimal('latitude', 10, 8)->nullable()->change();
            $table->decimal('longitude', 10, 8)->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'business_hours',
                'timezone',
                'business_phone',
                'business_email',
                'website',
                'service_description',
                'services_offered'
            ]);
        });
    }
};
