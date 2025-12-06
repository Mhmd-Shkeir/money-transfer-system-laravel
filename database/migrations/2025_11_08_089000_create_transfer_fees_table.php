<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transfer_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')->constrained('currencies');
            $table->foreignId('to_currency_id')->constrained('currencies');
            $table->decimal('percentage_fee',5,2)->default(0);
            $table->decimal('fixed_fee',10,2)->default(0);
            $table->date('effective_date')->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transfer_fees');
    }
};
