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
            if (!Schema::hasColumn('bulk_uploads', 'report_file_url')) {
                $table->text('report_file_url')->nullable()->after('report_file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_uploads', function (Blueprint $table) {
            if (Schema::hasColumn('bulk_uploads', 'report_file_url')) {
                $table->dropColumn('report_file_url');
            }
        });
    }
};
