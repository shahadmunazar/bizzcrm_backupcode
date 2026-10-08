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
        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();

            // Reference to employee in box_users
            $table->foreignId('box_user_id')->constrained('box_users')->restrictOnDelete();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();

            // Auditable fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name');
            $table->string('relation')->nullable();
            $table->string('phone');
            $table->string('phone_alt')->nullable();
            $table->enum('type', ['primary', 'secondary'])->default('primary');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_emergency_contacts');
    }
};
