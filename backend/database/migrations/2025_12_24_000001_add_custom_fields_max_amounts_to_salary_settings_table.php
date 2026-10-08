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
        Schema::table('salary_settings', function (Blueprint $table) {
            // Add JSON column to store max amounts for custom fields dynamically
            // This avoids needing to create columns for every custom field
            if (!Schema::hasColumn('salary_settings', 'custom_fields_max_amounts')) {
                $table->json('custom_fields_max_amounts')->nullable()->after('component_types');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            if (Schema::hasColumn('salary_settings', 'custom_fields_max_amounts')) {
                $table->dropColumn('custom_fields_max_amounts');
            }
        });
    }
};
