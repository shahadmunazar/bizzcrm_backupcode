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
            $table->boolean('is_location_tracking')->default(1)->after('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
            $table->dropColumn('is_location_tracking');
        });
    }
};
