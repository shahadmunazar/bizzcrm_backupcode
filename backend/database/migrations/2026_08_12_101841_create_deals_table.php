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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->onDelete('cascade');
            $table->string('name');
            $table->unsignedBigInteger('pipeline_id')->nullable();
            $table->string('stage')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->json('assignees')->nullable();
            $table->decimal('deal_value', 15, 2)->nullable();
            $table->string('currency')->nullable();
            $table->unsignedBigInteger('period_id')->nullable();
            $table->string('period_value')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('contact_id')->nullable()->constrained('project_contacts')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
