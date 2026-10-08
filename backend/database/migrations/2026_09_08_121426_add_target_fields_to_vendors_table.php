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
        Schema::table('vendors', function (Blueprint $table) {
            $table->enum('target_type', ['monthly', 'custom'])->default('monthly')->after('monthly_target');
            $table->date('target_start_date')->nullable()->after('target_type');
            $table->date('target_end_date')->nullable()->after('target_start_date');
            
            // Note: SQLite doesn't fully support changing defaults easily, but for MySQL/Postgres:
            $table->decimal('monthly_target', 15, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'target_start_date', 'target_end_date']);
            $table->decimal('monthly_target', 15, 2)->default(50000.00)->change();
        });
    }
};
