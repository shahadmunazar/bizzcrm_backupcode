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
        Schema::create('lead_source_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('indetifier'); // keeping User's typo
            $table->text('system_fields');
            $table->text('source_fields');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('from')->nullable();
            $table->text('has_word')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_source_templates');
    }
};
