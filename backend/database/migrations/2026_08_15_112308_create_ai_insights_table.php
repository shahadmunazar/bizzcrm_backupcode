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
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable()->index();
            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
            $table->morphs('analyzable'); 
            
            $table->string('category')->nullable()->index();
            $table->string('intent')->nullable()->index();
            $table->string('sentiment')->nullable()->index();
            $table->string('priority')->nullable()->index();
            
            $table->boolean('action_required')->default(false)->index();
            $table->string('suggested_action')->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            
            $table->json('vector_embedding')->nullable(); 
            $table->json('matched_smart_views')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
