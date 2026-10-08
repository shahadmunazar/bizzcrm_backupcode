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
        Schema::table('salaries', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_settings_id')->nullable();
            $table->foreign('salary_settings_id')->references('id')->on('salary_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropForeign(['salary_settings_id']);
            $table->dropColumn('salary_settings_id');
        });
    }
};
