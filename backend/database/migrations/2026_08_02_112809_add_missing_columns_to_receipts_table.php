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
        Schema::table('receipts', function (Blueprint $table) {
            $table->enum('tds_deducted', ['No', 'Yes'])->default('No')->after('reference');
            $table->decimal('tds_amount', 15, 2)->default(0)->after('tds_deducted');
            $table->string('remarks')->nullable()->after('tds_amount');
            $table->unsignedBigInteger('approved_by')->nullable()->after('remarks');
            $table->string('type')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['tds_deducted', 'tds_amount', 'remarks', 'approved_by', 'type']);
        });
    }
};
