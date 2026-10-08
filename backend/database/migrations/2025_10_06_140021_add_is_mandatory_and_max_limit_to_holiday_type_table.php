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
        Schema::table('holiday_type', function (Blueprint $table) {
            $table->string('is_mandatory')->nullable()->after('holiday_date');
            $table->integer('max_limit')->nullable()->after('is_mandatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holiday_type', function (Blueprint $table) {
            $table->dropColumn(['is_mandatory', 'max_limit']);
        });
    }
};
