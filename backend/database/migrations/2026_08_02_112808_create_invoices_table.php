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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->enum('type', ['performa','invoice'])->default('performa');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('lead_id')->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_with_tax', 15, 2);
            $table->date('invoice_date');
            $table->string('tax_type')->nullable();
            $table->string('code')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('added_by')->index();
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->unsignedBigInteger('last_updated_by')->index();
            $table->string('invoice_no')->nullable();
            $table->string('system_number')->nullable();
            $table->string('payment_option')->nullable();
            $table->enum('status', ['Pending','Approved','DisApproved'])->default('Pending');
            $table->dateTime('status_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
