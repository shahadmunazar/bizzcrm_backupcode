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
        Schema::table('boxes', function (Blueprint $table) {
            $table->integer('attendace_module_from_date')->nullable()->after('attendace_module_date');
            $table->integer('attendace_module_to_date')->nullable()->after('attendace_module_from_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn([
                'attendace_module_from_date',
                'attendace_module_to_date'
            ]);
        });
    }
};

