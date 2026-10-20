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
        Schema::table('vendor_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_commissions', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('vendor_commissions', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('vendor_commissions', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('vendor_commissions', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('paid_by');
            }
            if (!Schema::hasColumn('vendor_commissions', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_commissions', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'verified_by', 'paid_by', 'payment_reference', 'payment_notes']);
        });
    }
};
