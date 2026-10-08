<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('show_into_leads')->default(false)->after('name');
            $table->boolean('show_into_accounts')->default(false)->after('show_into_leads');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('show_into_leads')->default(false)->after('name');
            $table->boolean('show_into_accounts')->default(false)->after('show_into_leads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['show_into_leads', 'show_into_accounts']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['show_into_leads', 'show_into_accounts']);
        });
    }
};
