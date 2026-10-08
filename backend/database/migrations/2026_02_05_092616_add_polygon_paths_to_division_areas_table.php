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
        Schema::table('division_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('division_areas', 'polygon_paths')) {
                $table->longText('polygon_paths')->nullable()->after('radius');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('division_areas', function (Blueprint $table) {
            if (Schema::hasColumn('division_areas', 'polygon_paths')) {
                $table->dropColumn('polygon_paths');
            }
        });
    }
};
