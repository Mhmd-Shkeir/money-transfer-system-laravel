<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Add 'authorized' to the status enum
        DB::statement("ALTER TABLE `transactions` MODIFY `status` ENUM('pending','processing','completed','cancelled','refunded','authorized') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void {
        // Revert to original set without 'authorized' - be careful: this may fail if rows use 'authorized'
        DB::statement("ALTER TABLE `transactions` MODIFY `status` ENUM('pending','processing','completed','cancelled','refunded') NOT NULL DEFAULT 'pending'");
    }
};
