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
            $table->unsignedBigInteger('lead_sub_status_id')->nullable()->after('lead_status_id');
            $table->foreign('lead_sub_status_id')->references('id')->on('lead_sub_status')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_sub_status_id']);
            $table->dropColumn('lead_sub_status_id');
        });
    }
};
