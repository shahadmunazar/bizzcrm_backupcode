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
        Schema::table('pharma_khabar_leads', function (Blueprint $table) {
            $table->string('pincode')->nullable()->after('state');
            $table->string('location')->nullable()->after('pincode');
            $table->string('drugs_license')->nullable()->after('location');
            $table->string('have_gst_number')->nullable()->after('drugs_license');
            $table->string('purchase_period')->nullable()->after('have_gst_number');
            $table->string('call_back_time')->nullable()->after('purchase_period');
            $table->string('profession')->nullable()->after('call_back_time');
            $table->string('minimum_investment')->nullable()->after('profession');
            $table->string('qtype')->nullable()->after('minimum_investment');
            $table->string('submit_ip')->nullable()->after('qtype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharma_khabar_leads', function (Blueprint $table) {
            $table->dropColumn([
                'pincode', 'location', 'drugs_license', 'have_gst_number',
                'purchase_period', 'call_back_time', 'profession',
                'minimum_investment', 'qtype', 'submit_ip'
            ]);
        });
    }
};
