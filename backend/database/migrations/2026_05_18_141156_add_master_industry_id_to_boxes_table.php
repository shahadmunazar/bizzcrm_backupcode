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
        Schema::table('boxes', function (Blueprint $table) {
            $table->unsignedBigInteger('master_industry_id')->nullable()->after('industry');
            $table->foreign('master_industry_id')
                  ->references('id')
                  ->on('master_industry')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropForeign(['master_industry_id']);
            $table->dropColumn('master_industry_id');
        });
    }
};
