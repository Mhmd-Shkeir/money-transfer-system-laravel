<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add the foreign key only if it does not already exist (use information_schema)
        $exists = \Illuminate\Support\Facades\DB::selectOne(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = database() AND TABLE_NAME = 'agent_profiles' AND CONSTRAINT_NAME = 'agent_profiles_user_id_foreign'"
        );

        if (!$exists) {
            Schema::table('agent_profiles', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            \Illuminate\Support\Facades\Log::info('add_fk_agent_profiles_user_id: foreign key already exists, skipping');
        }
    }

    public function down()
    {
        // Drop foreign key only if it exists
        $exists = \Illuminate\Support\Facades\DB::selectOne(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = database() AND TABLE_NAME = 'agent_profiles' AND CONSTRAINT_NAME = 'agent_profiles_user_id_foreign'"
        );
        if ($exists) {
            Schema::table('agent_profiles', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } else {
            \Illuminate\Support\Facades\Log::info('add_fk_agent_profiles_user_id down: foreign key not present, skipping');
        }
    }
};
