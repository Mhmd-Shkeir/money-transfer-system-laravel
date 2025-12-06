<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('compliances', function (Blueprint $table) {
        $table->id();
        $table->string('type'); // KYC, AML, Risk, Policy, etc.
        $table->text('description');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliances');
    }
};
