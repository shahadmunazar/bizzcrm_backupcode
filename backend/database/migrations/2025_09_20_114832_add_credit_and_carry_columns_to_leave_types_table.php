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
        Schema::table('leave_types', function (Blueprint $table) {
            $table->string('leave_credit_type')->nullable()->after('leave_code');
            $table->string('carry_forward')->default('no')->nullable()->after('leave_credit_type');
            $table->integer('carry_forward_days')->nullable()->after('carry_forward');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
                        $table->dropColumn(['leave_credit_type', 'carry_forward', 'carry_forward_days']);
        });
    }
};
