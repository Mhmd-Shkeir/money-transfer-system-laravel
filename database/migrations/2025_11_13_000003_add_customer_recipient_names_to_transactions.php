<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('notes'); // Customer name for cash-in
            $table->string('recipient_name')->nullable()->after('customer_name'); // Recipient name for cash-out
        });
    }

    public function down(): void {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'recipient_name']);
        });
    }
};
