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
        Schema::create('whatsapp_campaign_numbers', function (Blueprint $row) {
            $row->id();
            $row->foreignId('box_id')->constrained('boxes')->onDelete('cascade');
            $row->foreignId('whatsapp_campaign_id')->constrained('whatsapp_campaigns')->onDelete('cascade');
            $row->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $row->string('phone');
            $row->string('name')->nullable();
            $row->string('status')->default('PENDING'); // PENDING, SENT, FAILED
            $row->text('error_message')->nullable();
            $row->json('metadata')->nullable();
            
            // Audit columns
            $row->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            $row->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $row->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            
            $row->timestamps();
            $row->softDeletes();
            
            $row->index('phone');
            $row->index('status');
            $row->index('box_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campaign_numbers');
    }
};
