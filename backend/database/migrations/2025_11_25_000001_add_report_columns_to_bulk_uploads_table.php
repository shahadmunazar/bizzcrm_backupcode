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
            if (! Schema::hasColumn('bulk_uploads', 'report_file_path')) {
                $table->string('report_file_path')->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('bulk_uploads', 'report_status')) {
                $table->enum('report_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('report_file_path');
            }
            if (! Schema::hasColumn('bulk_uploads', 'report_requested_at')) {
                $table->timestamp('report_requested_at')->nullable()->after('report_status');
            }
            if (! Schema::hasColumn('bulk_uploads', 'report_generated_at')) {
                $table->timestamp('report_generated_at')->nullable()->after('report_requested_at');
            }
            if (! Schema::hasColumn('bulk_uploads', 'report_requested_by')) {
                $table->unsignedBigInteger('report_requested_by')->nullable()->after('report_generated_at');
                $table->foreign('report_requested_by')->references('id')->on('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('bulk_uploads', 'report_error')) {
                $table->text('report_error')->nullable()->after('report_requested_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_uploads', function (Blueprint $table) {
            if (Schema::hasColumn('bulk_uploads', 'report_requested_by')) {
                $table->dropForeign(['report_requested_by']);
            }
            $table->dropColumn([
                'report_file_path',
                'report_status',
                'report_requested_at',
                'report_generated_at',
                'report_requested_by',
                'report_error',
            ]);
        });
    }
};
