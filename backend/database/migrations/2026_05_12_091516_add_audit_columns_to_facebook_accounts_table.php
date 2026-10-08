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
        Schema::table('facebook_accounts', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('added_by')->nullable()->after('api_params');
            $table->unsignedBigInteger('updated_by')->nullable()->after('added_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');

            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_accounts', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['added_by', 'updated_by', 'deleted_by', 'deleted_at']);
        });
    }
};
