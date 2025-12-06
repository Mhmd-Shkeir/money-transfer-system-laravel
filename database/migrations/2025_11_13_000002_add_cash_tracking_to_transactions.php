<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_type')->default('transfer')->after('status'); // transfer, cash_in, cash_out
            $table->string('cash_reference')->nullable()->after('transaction_type'); // Reference for cash operations
            $table->text('notes')->nullable()->after('cash_reference'); // Notes for agent
        });
    }

    public function down(): void {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'cash_reference', 'notes']);
        });
    }
};
