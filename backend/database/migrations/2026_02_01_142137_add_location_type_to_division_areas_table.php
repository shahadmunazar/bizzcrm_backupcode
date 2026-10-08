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
            $table->string('location_type')->default('radius')->comment('radius or area')->after('radius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('division_areas', function (Blueprint $table) {
            $table->dropColumn('location_type');
        });
    }
};
