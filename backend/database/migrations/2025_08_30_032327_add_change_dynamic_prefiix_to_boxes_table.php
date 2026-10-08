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
                $table->boolean('change_dynamic_prefix')->default(false)->nullable()->after('dynamic_employee_id');
                $table->string('custom_employee_id')->nullable()->after('change_dynamic_prefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn('change_dynamic_prefix');
            $table->dropColumn('custom_employee_id');
        });
    }
};
