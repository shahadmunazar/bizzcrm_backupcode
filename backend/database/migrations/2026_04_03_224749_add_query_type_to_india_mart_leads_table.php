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
        Schema::table('india_mart_leads', function (Blueprint $table) {
            $table->string('query_type')->nullable()->after('mcenquiry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('india_mart_leads', function (Blueprint $table) {
            $table->dropColumn('query_type');
        });
    }
};
