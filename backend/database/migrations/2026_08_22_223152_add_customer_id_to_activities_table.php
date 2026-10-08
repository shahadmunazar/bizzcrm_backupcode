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
        if (!Schema::hasColumn('activities', 'customer_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('activity_id')->comment('References id in customers table');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('activities', 'customer_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('customer_id');
            });
        }
    }
};
