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
        if (!Schema::hasTable('whats_app_portfolios')) {
            Schema::create('whats_app_portfolios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
                $table->string('portfolio_id')->unique(); // Facebook Business Portfolio ID
                $table->string('name')->nullable();
                $table->string('timezone')->nullable();
                $table->string('currency')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index('box_id');
                $table->index('portfolio_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_portfolios');
    }
};
