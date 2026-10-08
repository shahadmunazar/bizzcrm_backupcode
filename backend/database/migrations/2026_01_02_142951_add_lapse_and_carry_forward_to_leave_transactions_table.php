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
        Schema::table('leave_transaction', function (Blueprint $table) {
            $table->decimal('lapsed_leave', 5, 2)->default(0)->after('debit_leave');
            $table->decimal('carried_forward_leave', 5, 2)->default(0)->after('lapsed_leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_transaction', function (Blueprint $table) {
            $table->dropColumn(['lapsed_leave', 'carried_forward_leave']);
        });
    }
};
