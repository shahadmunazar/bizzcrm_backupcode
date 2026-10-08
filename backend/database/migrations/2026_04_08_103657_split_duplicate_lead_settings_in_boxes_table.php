<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            if (!Schema::hasColumn('boxes', 'allow_duplicate_leads_auto')) {
                $table->boolean('allow_duplicate_leads_auto')
                    ->default(0)
                    ->after('allow_duplicate_leads');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            if (Schema::hasColumn('boxes', 'allow_duplicate_leads_auto')) {
                $table->dropColumn('allow_duplicate_leads_auto');
            }
        });
    }
};
