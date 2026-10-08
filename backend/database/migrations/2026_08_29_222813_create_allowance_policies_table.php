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
        Schema::create('allowance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->cascadeOnDelete();
            $table->foreignId('boxuser_id')->nullable()->constrained('box_users')->cascadeOnDelete();
            $table->string('role_name')->nullable();
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->cascadeOnDelete();
            $table->json('rules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_policies');
    }
};
