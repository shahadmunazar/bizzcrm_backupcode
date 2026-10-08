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
        Schema::table('pharma_vend_leads', function (Blueprint $table) {
            $table->text('location')->nullable()->change();
            $table->text('message')->nullable()->change();
            $table->text('requirment_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharma_vend_leads', function (Blueprint $table) {
            $table->string('location')->nullable()->change();
            $table->string('message')->nullable()->change();
            $table->string('requirment_type')->nullable()->change();
        });
    }
};
