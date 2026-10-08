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
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();

            // Reference to employee in box_users
            $table->foreignId('box_user_id')->constrained('box_users')->restrictOnDelete();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();

            // Auditable fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('company_name');
            $table->string('position');
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('current_working')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_experiences');
    }
};
