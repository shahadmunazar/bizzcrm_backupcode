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
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('bulk_upload_id')->nullable()->after('box_id');
            $table->foreign('bulk_upload_id')->references('id')->on('bulk_uploads')->onDelete('set null');
            $table->index('bulk_upload_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['bulk_upload_id']);
            $table->dropIndex(['bulk_upload_id']);
            $table->dropColumn('bulk_upload_id');
        });
    }
};
