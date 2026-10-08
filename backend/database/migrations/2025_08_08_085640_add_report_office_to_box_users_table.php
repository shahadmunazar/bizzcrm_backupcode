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
        Schema::table('box_users', function (Blueprint $table) {
            $table->string('report_office')->nullable()->after('designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
            $table->dropColumn('report_office');
        });
    }
};
