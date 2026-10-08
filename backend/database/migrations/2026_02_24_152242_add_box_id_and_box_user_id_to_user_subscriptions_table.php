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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('box_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('box_user_id')->nullable()->after('box_id');
            
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['box_id']);
            $table->dropForeign(['box_user_id']);
            $table->dropColumn(['box_id', 'box_user_id']);
        });
    }
};
