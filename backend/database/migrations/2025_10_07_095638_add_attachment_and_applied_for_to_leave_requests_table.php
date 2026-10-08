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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('applied_for')->nullable()->after('leave_balance'); // or another existing column
            $table->string('attachment')->nullable()->after('applied_for');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
                        $table->dropColumn(['applied_for', 'attachment']);

        });
    }
};
