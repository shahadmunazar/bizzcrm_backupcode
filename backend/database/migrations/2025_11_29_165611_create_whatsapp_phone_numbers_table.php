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
        Schema::create('whatsapp_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->foreignId('whatsapp_business_account_id')->constrained('whatsapp_business_accounts')->restrictOnDelete();
            $table->string('phone_number_id')->unique(); // WhatsApp Phone Number ID
            $table->string('display_phone_number')->nullable(); // Human-readable phone number
            $table->string('verified_name')->nullable();
            $table->enum('status', ['PENDING', 'CONNECTED', 'DISCONNECTED'])->default('PENDING');
            $table->enum('code_verification_status', ['VERIFIED', 'UNVERIFIED'])->nullable();
            $table->enum('quality_rating', ['GREEN', 'YELLOW', 'RED', 'UNKNOWN'])->nullable();
            $table->json('capabilities')->nullable(); // SMS, VOICE, etc.
            $table->json('metadata')->nullable(); // Store additional phone number data
            $table->timestamps();
            
            $table->index('box_id');
            $table->index('whatsapp_business_account_id');
            $table->index('phone_number_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_phone_numbers');
    }
};
