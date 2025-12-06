<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code',3)->unique();
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->string('region')->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('countries');
    }
};
