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
        Schema::table('deals', function (Blueprint $table) {
            if (!Schema::hasColumn('deals', 'follow_up_date')) {
                $table->date('follow_up_date')->nullable()->after('expected_close_date');
            }
            if (!Schema::hasColumn('deals', 'description')) {
                $table->text('description')->nullable()->after('follow_up_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'follow_up_date')) {
                $table->dropColumn('follow_up_date');
            }
            if (Schema::hasColumn('deals', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
