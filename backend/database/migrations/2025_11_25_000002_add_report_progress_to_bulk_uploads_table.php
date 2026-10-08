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
        Schema::table('bulk_uploads', function (Blueprint $table) {
            $table->decimal('report_progress', 5, 2)->default(0)->after('report_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_uploads', function (Blueprint $table) {
            $table->dropColumn('report_progress');
        });
    }
};

