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
        Schema::create('whatsapp_business_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->unsignedBigInteger('whatsapp_business_portfolio_id')->nullable();
            $table->foreign('whatsapp_business_portfolio_id', 'waba_portfolio_fk')
                ->references('id')
                ->on('whatsapp_business_portfolios')
                ->onDelete('set null');
            $table->string('waba_id')->unique(); // WhatsApp Business Account ID
            $table->string('name')->nullable();
            $table->string('currency')->nullable();
            $table->string('timezone')->nullable();
            $table->text('access_token')->nullable(); // WABA access token
            $table->enum('account_status', ['PENDING', 'ACTIVE', 'SUSPENDED', 'DEACTIVATED'])->default('PENDING');
            $table->string('message_template_namespace')->nullable();
            $table->json('metadata')->nullable(); // Store additional account data
            $table->timestamps();
            
            $table->index('box_id');
            $table->index('waba_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_business_accounts');
    }
};
