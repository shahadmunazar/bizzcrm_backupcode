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
        Schema::create('facebook_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->foreignId('facebook_page_id')->constrained('facebook_pages')->restrictOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete(); // Linked to CRM lead
            $table->string('adgroup_id')->nullable();
            $table->string('ad_id')->nullable();
            $table->timestamp('created_time')->nullable(); // Lead creation timestamp from Facebook
            $table->string('leadgen_id')->nullable(); // Facebook Lead ID
            $table->string('page_id')->nullable(); // Facebook Page ID
            $table->string('form_id')->nullable(); // Facebook Form ID
            $table->enum('status', ['Pending', 'Imported', 'Skipped', 'Updated'])->default('Pending');
            $table->longText('data')->nullable(); // JSON: full lead data from Facebook
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_leads');
    }
};
