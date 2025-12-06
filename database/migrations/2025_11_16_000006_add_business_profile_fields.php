<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('agent_profiles', function (Blueprint $table) {
            // Business Information (if not exists)
            if (!Schema::hasColumn('agent_profiles', 'business_name')) {
                $table->string('business_name')->nullable();
            }
            if (!Schema::hasColumn('agent_profiles', 'business_registration_number')) {
                $table->string('business_registration_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('agent_profiles', 'tax_id')) {
                $table->string('tax_id')->nullable();
            }
            if (!Schema::hasColumn('agent_profiles', 'business_description')) {
                $table->text('business_description')->nullable();
            }
            if (!Schema::hasColumn('agent_profiles', 'office_address')) {
                $table->string('office_address')->nullable();
            }
            
            // Currency and Country
            if (!Schema::hasColumn('agent_profiles', 'country_id')) {
                $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            }
            if (!Schema::hasColumn('agent_profiles', 'currency_id')) {
                $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            }
            
            // Timezone (skip if exists)
            if (!Schema::hasColumn('agent_profiles', 'timezone')) {
                $table->string('timezone')->default('UTC');
            }
            
            // Business Contact
            if (!Schema::hasColumn('agent_profiles', 'business_phone')) {
                $table->string('business_phone')->nullable();
            }
            if (!Schema::hasColumn('agent_profiles', 'business_email')) {
                $table->string('business_email')->nullable();
            }
            if (!Schema::hasColumn('agent_profiles', 'website')) {
                $table->string('website')->nullable();
            }
            if (!Schema::hasColumn('agent_profiles', 'service_description')) {
                $table->text('service_description')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['currency_id']);
            $table->dropColumn([
                'business_name',
                'business_registration_number',
                'tax_id',
                'business_description',
                'office_address',
                'country_id',
                'currency_id',
                'timezone',
                'business_phone',
                'business_email',
                'website',
                'service_description'
            ]);
        });
    }
};
