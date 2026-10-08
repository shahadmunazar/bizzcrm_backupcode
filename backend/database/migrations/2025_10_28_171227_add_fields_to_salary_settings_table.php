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
            $table->enum('monthly_option', ['calendar', 'specific'])->nullable()->after('name');
            $table->date('start_date')->nullable()->after('monthly_option');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->dropColumn(['monthly_option', 'start_date', 'end_date']);

        });
    }
};
