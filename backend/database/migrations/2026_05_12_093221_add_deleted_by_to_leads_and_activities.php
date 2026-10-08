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
            if (!Schema::hasColumn('leads', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('last_updated_by');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('last_updated_by');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
        });
    }
};
