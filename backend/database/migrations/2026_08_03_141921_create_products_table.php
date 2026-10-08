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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->boolean('hide')->default(false);
            $table->string('code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('packing')->nullable();
            $table->string('unit')->nullable();
            $table->boolean('decimal')->default(false);
            $table->string('fast_search')->nullable();
            $table->string('color_type')->nullable();
            $table->string('item_type')->nullable();
            $table->string('company')->nullable();
            $table->string('company_status')->nullable();
            $table->string('salt')->nullable();
            $table->string('salt_status')->nullable();
            $table->string('hsn_sac')->nullable();
            $table->string('hsn_status')->nullable();
            $table->string('erp_to_erp')->nullable();
            $table->string('local_tax')->nullable();
            $table->string('central_tax')->nullable();
            $table->decimal('sgst_percent', 5, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(0);
            $table->decimal('igst_percent', 5, 2)->default(0);
            $table->decimal('cess_percent', 5, 2)->default(0);
            $table->decimal('cess_amount', 10, 3)->default(0);
            $table->decimal('csr', 10, 2)->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('purchase_rate', 10, 2)->default(0);
            $table->decimal('cost', 10, 5)->default(0);
            $table->decimal('rate_a', 10, 2)->default(0);
            $table->decimal('rate_b', 10, 2)->default(0);
            $table->decimal('rate_c', 10, 2)->default(0);
            $table->decimal('conv_box', 10, 3)->default(0);
            $table->decimal('conv_cas', 10, 3)->default(0);
            $table->boolean('negative_stock')->default(false);
            $table->integer('minimum_qty')->default(0);
            $table->integer('maximum_qty')->default(0);
            $table->integer('reorder_qty')->default(0);
            $table->integer('reorder_days')->default(0);
            $table->decimal('volume_discount', 10, 3)->default(0);
            $table->decimal('on_qty_volume', 10, 1)->default(0);
            $table->decimal('item_discount', 10, 2)->default(0);
            $table->decimal('special_disc', 10, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->integer('on_quantity_disc')->default(0);
            $table->decimal('maximum_discount_percent', 5, 2)->default(0);
            $table->decimal('purchase_disc', 10, 2)->default(0);
            $table->string('f6_rate_plus_minus')->nullable();
            $table->string('free_scheme')->nullable();
            $table->string('valid_from')->nullable();
            $table->decimal('minimum_margin_percent', 5, 2)->default(0);
            $table->decimal('disc_less', 10, 2)->default(0);
            $table->string('manufacturer')->nullable();
            $table->boolean('narcotic')->default(false);
            $table->boolean('schedule_h')->default(false);
            $table->boolean('schedule_h1')->default(false);
            $table->string('schedule_h1_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
