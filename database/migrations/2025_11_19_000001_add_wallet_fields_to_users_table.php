<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 15, 2)->default(0.00)->after('social_provider');
            $table->decimal('total_deposits', 15, 2)->default(0.00)->after('wallet_balance');
            $table->decimal('total_withdrawals', 15, 2)->default(0.00)->after('total_deposits');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_balance', 'total_deposits', 'total_withdrawals']);
        });
    }
};