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
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('invoice_logo')->nullable();
            $table->string('invoice_prefix')->default('INV-');
            $table->integer('invoice_due_days')->default(5);
            $table->boolean('enable_round_off')->default(true);
            $table->string('round_off_type')->default('Up'); // Up or Down
            $table->boolean('show_company_details')->default(true);
            $table->text('header_terms')->nullable();
            $table->text('footer_terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
