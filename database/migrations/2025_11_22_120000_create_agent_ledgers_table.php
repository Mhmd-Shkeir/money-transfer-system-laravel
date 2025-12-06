<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_profile_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('balance_after', 15, 2)->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('agent_profile_id')->references('id')->on('agent_profiles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_ledgers');
    }
};
