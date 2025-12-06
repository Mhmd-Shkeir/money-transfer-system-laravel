<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->json('documents')->nullable()->after('working_hours'); // Store doc file paths
            $table->decimal('cash_balance', 15, 2)->default(0)->after('total_transactions'); // Current cash held
            $table->decimal('total_cash_in', 15, 2)->default(0)->after('cash_balance'); // Total cash received
            $table->decimal('total_cash_out', 15, 2)->default(0)->after('total_cash_in'); // Total cash paid out
        });
    }

    public function down(): void {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->dropColumn(['documents', 'cash_balance', 'total_cash_in', 'total_cash_out']);
        });
    }
};
