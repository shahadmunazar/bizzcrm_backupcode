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
        Schema::table('box_users', function (Blueprint $table) {
            $table->unsignedBigInteger('emp_type')->nullable()->after('staff_role');
            $table->foreign('emp_type')->references('id')->on('employee_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
            $table->dropForeign(['emp_type']);
            $table->dropColumn('emp_type');
        });
    }
};
