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
        Schema::table('holidays', function (Blueprint $table) {
            $table->unsignedBigInteger('holiday_type_id')->nullable()->after('holiday_code');
            $table->json('country_region')->nullable()->after('holiday_type_id');

            $table->foreign('holiday_type_id')
                  ->references('id')
                  ->on('holiday_type')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['holiday_type_id']);

            // Then drop the columns
            $table->dropColumn('holiday_type_id');
            $table->dropColumn('country_region');
        });
    }
};
