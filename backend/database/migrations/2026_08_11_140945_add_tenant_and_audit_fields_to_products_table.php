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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('box_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('box_user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['box_id']);
            $table->dropForeign(['box_user_id']);
            $table->dropColumn(['box_id', 'box_user_id', 'created_by', 'updated_by', 'deleted_by', 'deleted_at']);
        });
    }
};
