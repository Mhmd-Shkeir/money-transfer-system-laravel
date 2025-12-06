<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'social_id')) {
                $table->dropColumn('social_id');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'social_id')) {
                $table->string('social_id')->nullable()->after('social_provider');
            }
        });
    }
};