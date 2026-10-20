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
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'referred_by_vendor_id')) {
                $table->unsignedBigInteger('referred_by_vendor_id')->nullable()->after('referral_code')->index();
                $table->foreign('referred_by_vendor_id')->references('id')->on('vendors')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'referred_by_vendor_id')) {
                $table->dropForeign(['referred_by_vendor_id']);
                $table->dropColumn('referred_by_vendor_id');
            }
        });
    }
};
