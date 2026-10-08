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
        Schema::create('facebook_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->foreignId('facebook_page_id')->constrained('facebook_pages')->restrictOnDelete();
            $table->string('form_id')->nullable(); // Facebook Form ID
            $table->string('name')->nullable(); // Form name
            $table->string('status')->nullable(); // Form status
            $table->longText('mapping')->nullable(); // JSON: field mappings to CRM fields
            $table->enum('is_mapped', ['Yes', 'No'])->default('No');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_forms');
    }
};
