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
        Schema::table('product_hsns', function (Blueprint $table) {
            $table->string('short_name')->nullable()->after('code');
            $table->decimal('sgst', 8, 2)->default(0)->nullable()->after('short_name');
            $table->decimal('cgst', 8, 2)->default(0)->nullable()->after('sgst');
            $table->decimal('igst', 8, 2)->default(0)->nullable()->after('cgst');
            $table->string('type')->default('Goods')->nullable()->after('igst');
            $table->string('uqc')->nullable()->after('type');
            $table->decimal('cess', 8, 2)->default(0)->nullable()->after('uqc');
            $table->date('from_date')->nullable()->after('cess');
            $table->date('to_date')->nullable()->after('from_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_hsns', function (Blueprint $table) {
            $table->dropColumn(['short_name', 'sgst', 'cgst', 'igst', 'type', 'uqc', 'cess', 'from_date', 'to_date']);
        });
    }
};
