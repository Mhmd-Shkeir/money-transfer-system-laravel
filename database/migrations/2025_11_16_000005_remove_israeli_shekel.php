<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Delete Israeli Shekel
        DB::table('currencies')->where('code', 'ILS')->delete();
    }

    public function down(): void {
        // Restore if needed
        DB::table('currencies')->insert([
            'code' => 'ILS',
            'name' => 'Israeli Shekel',
            'symbol' => '₪',
        ]);
    }
};
