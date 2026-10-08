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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('box_user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('project_custom_id')->nullable();
            $table->foreignId('project_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->string('project_timing')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->default('Active');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
